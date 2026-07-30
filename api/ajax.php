<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add_to_cart':
        $id = trim($_POST['id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $image = trim($_POST['image'] ?? '');
        $sku = trim($_POST['sku'] ?? '');

        if ($id === '' || $name === '' || $price <= 0) {
            echo json_encode(['success' => false, 'message' => 'Neplatné údaje produktu']);
            exit;
        }

        addToCart($id, $name, $price, $quantity, $image, $sku);
        echo json_encode([
            'success' => true,
            'message' => 'Produkt bol pridaný do košíka',
            'cartCount' => getCartCount(),
            'cartTotal' => formatPrice(getCartTotal()),
        ]);
        break;

    case 'update_cart':
        $id = trim($_POST['id'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($id === '') {
            echo json_encode(['success' => false, 'message' => 'Neplatné ID produktu']);
            exit;
        }

        updateCartQuantity($id, $quantity);
        $cart = getCart();
        $itemTotal = 0;
        if (isset($cart[$id])) {
            $itemTotal = $cart[$id]['price'] * $cart[$id]['quantity'];
        }

        echo json_encode([
            'success' => true,
            'cartCount' => getCartCount(),
            'cartTotal' => formatPrice(getCartTotal()),
            'itemTotal' => formatPrice($itemTotal),
        ]);
        break;

    case 'remove_from_cart':
        $id = trim($_POST['id'] ?? '');

        if ($id === '') {
            echo json_encode(['success' => false, 'message' => 'Neplatné ID produktu']);
            exit;
        }

        removeFromCart($id);
        echo json_encode([
            'success' => true,
            'message' => 'Produkt bol odstránený z košíka',
            'cartCount' => getCartCount(),
            'cartTotal' => formatPrice(getCartTotal()),
            'cartEmpty' => empty(getCart()),
        ]);
        break;

    case 'search':
        $q = trim($_GET['q'] ?? '');
        if (mb_strlen($q) < 3) {
            echo json_encode(['success' => true, 'products' => []]);
            exit;
        }

        $all = getProducts(['search' => $q]);
        $results = array_slice($all, 0, 5);

        $out = [];
        foreach ($results as $p) {
            $out[] = [
                'id' => $p['id'],
                'name' => $p['name'],
                'sku' => $p['sku'],
                'price_vat' => formatPrice($p['price_vat']),
                'category' => $p['category'],
                'image' => $p['image'],
            ];
        }

        echo json_encode([
            'success' => true,
            'products' => $out,
            'total' => count($all),
            'query' => $q,
        ]);
        break;

    case 'get_cart':
        echo json_encode([
            'success' => true,
            'cart' => getCart(),
            'cartCount' => getCartCount(),
            'cartTotal' => formatPrice(getCartTotal()),
        ]);
        break;

    case 'contact_form':
        $csrfToken = trim($_POST['csrf_token'] ?? '');
        if (!verifyCsrfToken($csrfToken)) {
            echo json_encode(['success' => false, 'message' => 'Neplatný bezpečnostný token. Skúste obnoviť stránku.']);
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $message === '') {
            echo json_encode(['success' => false, 'message' => 'Vyplňte všetky povinné polia.']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Zadajte platnú e-mailovú adresu.']);
            exit;
        }

        sendContactConfirmation($name, $email, $subject, $message);
        echo json_encode(['success' => true, 'message' => 'Správa bola odoslaná. Potvrdenie sme vám poslali na e-mail.']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Neplatná akcia']);
        break;
}
