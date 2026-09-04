<?php

const ORDER_STATUSES = ['nova', 'odoslana', 'dorucena', 'zrusena'];

const ORDER_STATUS_LABELS = [
    'nova'     => 'Nová',
    'odoslana' => 'Odoslaná',
    'dorucena' => 'Doručená',
    'zrusena'  => 'Zrušená',
];

// Potvrdenie objednavky uz rieši existujuci mail hned pri odoslani objednavky
// (sendOrderConfirmation) - samostatny stav "Potvrdena" by bol duplicitny.
const ORDER_STATUS_TRANSITIONS = [
    'nova'     => ['odoslana', 'zrusena'],
    'odoslana' => ['dorucena', 'zrusena'],
    'dorucena' => [],
    'zrusena'  => [],
];

function getDbConnection(): PDO
{
    static $pdo = null;
    static $tablesReady = false;

    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    if (!$tablesReady) {
        createOrdersTables($pdo);
        $tablesReady = true;
    }

    return $pdo;
}

function createOrdersTables(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_number    VARCHAR(32)  NOT NULL UNIQUE,
            status          VARCHAR(20)  NOT NULL DEFAULT 'nova',
            name            VARCHAR(150) NOT NULL,
            company         VARCHAR(150) NOT NULL DEFAULT '',
            ico             VARCHAR(20)  NOT NULL DEFAULT '',
            dic             VARCHAR(20)  NOT NULL DEFAULT '',
            phone           VARCHAR(40)  NOT NULL,
            email           VARCHAR(150) NOT NULL,
            street          VARCHAR(150) NOT NULL,
            city            VARCHAR(100) NOT NULL,
            zip             VARCHAR(10)  NOT NULL,
            note            TEXT NULL,
            total           DECIMAL(10,2) NOT NULL,
            created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id        INT UNSIGNED NOT NULL,
            product_id      VARCHAR(50)  NOT NULL,
            name            VARCHAR(200) NOT NULL,
            sku             VARCHAR(50)  NOT NULL DEFAULT '',
            price           DECIMAL(10,2) NOT NULL,
            quantity        INT UNSIGNED NOT NULL,
            CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            INDEX idx_order_id (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

/**
 * Ulozi objednavku (+ jej polozky ako snapshot z kosika, nie znova z MRP -
 * historicke objednavky tak ostanu presne aj po zmene cien/skladu v MRP).
 * Cislo objednavky sa generuje s retry pri kolizii (teoreticka, ale nie
 * nemozna zhoda pri rovnakej sekunde + rovnakom nahodnom 4-cislí).
 */
function createOrder(array $orderData, array $cart, float $total): array
{
    $pdo = getDbConnection();

    $maxAttempts = 10;
    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $orderNumber = 'OBJ-' . date('Ymd') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO orders (order_number, status, name, company, ico, dic, phone, email, street, city, zip, note, total)
                VALUES (:order_number, 'nova', :name, :company, :ico, :dic, :phone, :email, :street, :city, :zip, :note, :total)
            ");
            $stmt->execute([
                'order_number' => $orderNumber,
                'name'         => $orderData['name'],
                'company'      => $orderData['company'] ?? '',
                'ico'          => $orderData['ico'] ?? '',
                'dic'          => $orderData['dic'] ?? '',
                'phone'        => $orderData['phone'],
                'email'        => $orderData['email'],
                'street'       => $orderData['street'],
                'city'         => $orderData['city'],
                'zip'          => $orderData['zip'],
                'note'         => $orderData['note'] ?? '',
                'total'        => $total,
            ]);

            $orderId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, name, sku, price, quantity)
                VALUES (:order_id, :product_id, :name, :sku, :price, :quantity)
            ");
            foreach ($cart as $item) {
                $itemStmt->execute([
                    'order_id'   => $orderId,
                    'product_id' => $item['id'],
                    'name'       => $item['name'],
                    'sku'        => $item['sku'] ?? '',
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                ]);
            }

            $pdo->commit();

            return getOrder($orderId);
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // 23000 = integrity constraint violation (tu: duplicitne order_number) - skus znova s novym cislom.
            if ($e->getCode() === '23000' && $attempt < $maxAttempts) {
                continue;
            }
            throw $e;
        }
    }

    throw new RuntimeException('Nepodarilo sa vygenerovat unikatne cislo objednavky.');
}

function getOrders(array $filters = []): array
{
    $pdo = getDbConnection();

    if (!empty($filters['status'])) {
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE status = :status ORDER BY created_at DESC');
        $stmt->execute(['status' => $filters['status']]);
    } else {
        $stmt = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC');
    }

    return $stmt->fetchAll();
}

function getOrder(int $id): ?array
{
    $pdo = getDbConnection();

    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $order = $stmt->fetch();

    if ($order === false) {
        return null;
    }

    $itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id');
    $itemsStmt->execute(['order_id' => $id]);
    $order['items'] = $itemsStmt->fetchAll();

    return $order;
}

function updateOrderStatus(int $id, string $newStatus): bool
{
    if (!in_array($newStatus, ORDER_STATUSES, true)) {
        return false;
    }

    $pdo = getDbConnection();

    $stmt = $pdo->prepare('SELECT status FROM orders WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $current = $stmt->fetchColumn();

    if ($current === false || $current === $newStatus) {
        return false;
    }

    $update = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
    return $update->execute(['status' => $newStatus, 'id' => $id]);
}

function getValidNextStatuses(string $currentStatus): array
{
    return ORDER_STATUS_TRANSITIONS[$currentStatus] ?? [];
}
