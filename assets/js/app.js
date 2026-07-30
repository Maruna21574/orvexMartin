document.addEventListener('DOMContentLoaded', function () {
    initMobileMenu();
    initCartButtons();
    initQuantityInputs();
    initCartPage();
    initProductGallery();
    initFilterSidebar();
    initOrderForm();
    initContactForm();
    initLiveSearch();
    initBackToTop();
    initStickyCta();
    initScrollReveal();
    initHeaderScroll();
    initCardTilt();
    initHeroGlow();
});

// === MOBILE MENU ===
function initMobileMenu() {
    const hamburger = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('mainMenu');
    const searchToggle = document.getElementById('searchToggle');
    const searchWrap = document.getElementById('headerSearchWrap');
    const searchInput = document.getElementById('headerSearchInput');

    if (hamburger && menu) {
        hamburger.addEventListener('click', function () {
            hamburger.classList.toggle('active');
            menu.classList.toggle('open');
            if (searchWrap) searchWrap.classList.remove('open');
        });
    }

    if (searchToggle && searchWrap && searchInput) {
        searchToggle.addEventListener('click', function () {
            searchWrap.classList.toggle('open');
            if (searchWrap.classList.contains('open')) {
                searchInput.focus();
                if (hamburger) hamburger.classList.remove('active');
                if (menu) menu.classList.remove('open');
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (hamburger && menu && !hamburger.contains(e.target) && !menu.contains(e.target)) {
            hamburger.classList.remove('active');
            menu.classList.remove('open');
        }
        if (searchToggle && searchWrap && !searchToggle.contains(e.target) && !searchWrap.contains(e.target)) {
            searchWrap.classList.remove('open');
        }
    });
}

// === LIVE SEARCH ===
function initLiveSearch() {
    var input = document.getElementById('headerSearchInput');
    var dropdown = document.getElementById('searchDropdown');
    var wrap = document.getElementById('headerSearchWrap');
    if (!input || !dropdown) return;

    var timer = null;
    var currentQuery = '';

    input.addEventListener('input', function () {
        var q = input.value.trim();
        clearTimeout(timer);

        if (q.length < 3) {
            dropdown.classList.remove('open');
            dropdown.innerHTML = '';
            currentQuery = '';
            return;
        }

        timer = setTimeout(function () {
            if (q === currentQuery) return;
            currentQuery = q;

            fetch('/api/ajax.php?action=search&q=' + encodeURIComponent(q))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data.success || input.value.trim() !== q) return;
                    renderDropdown(data, q);
                })
                .catch(function () {
                    dropdown.classList.remove('open');
                });
        }, 250);
    });

    function renderDropdown(data, q) {
        if (data.products.length === 0) {
            dropdown.innerHTML = '<div class="search-dropdown__empty">Žiadne výsledky pre „' + escapeHtml(q) + '"</div>';
            dropdown.classList.add('open');
            return;
        }

        var html = '';
        data.products.forEach(function (p) {
            html += '<a href="/produkt?id=' + encodeURIComponent(p.id) + '" class="search-dropdown__item">';
            html += '<div class="search-dropdown__img">';
            if (p.image) {
                html += '<img src="' + escapeHtml(p.image) + '" alt="">';
            } else {
                html += '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
            }
            html += '</div>';
            html += '<div class="search-dropdown__info">';
            html += '<span class="search-dropdown__name">' + highlightMatch(p.name, q) + '</span>';
            html += '<span class="search-dropdown__meta">' + escapeHtml(p.sku) + ' &middot; ' + escapeHtml(p.category) + '</span>';
            html += '</div>';
            html += '<span class="search-dropdown__price">' + escapeHtml(p.price_vat) + '</span>';
            html += '</a>';
        });

        if (data.total > 5) {
            html += '<a href="/produkty?hladaj=' + encodeURIComponent(q) + '" class="search-dropdown__all">Zobraziť všetkých ' + data.total + ' výsledkov</a>';
        }

        dropdown.innerHTML = html;
        dropdown.classList.add('open');
    }

    function highlightMatch(text, query) {
        var escaped = escapeHtml(text);
        var q = escapeHtml(query);
        var regex = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        return escaped.replace(regex, '<mark>$1</mark>');
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    document.addEventListener('click', function (e) {
        if (wrap && !wrap.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });

    input.addEventListener('focus', function () {
        if (dropdown.innerHTML && input.value.trim().length >= 3) {
            dropdown.classList.add('open');
        }
    });
}

// === ADD TO CART ===
function initCartButtons() {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn--add-to-cart');
        if (!btn) return;

        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const price = btn.dataset.price;
        const image = btn.dataset.image || '';
        const sku = btn.dataset.sku || '';

        let quantity = 1;
        if (btn.dataset.qtyInput) {
            const qtyInput = document.getElementById(btn.dataset.qtyInput);
            if (qtyInput) quantity = parseInt(qtyInput.value) || 1;
        }

        const formData = new FormData();
        formData.append('action', 'add_to_cart');
        formData.append('id', id);
        formData.append('name', name);
        formData.append('price', price);
        formData.append('quantity', quantity);
        formData.append('image', image);
        formData.append('sku', sku);

        fetch('/api/ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    updateCartCount(data.cartCount);
                    showToast('Produkt bol pridaný do košíka');
                    animateCartIcon();
                }
            })
            .catch(function () {
                showToast('Chyba pri pridávaní do košíka');
            });
    });
}

// === QUANTITY INPUTS ===
function initQuantityInputs() {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.quantity-input__btn');
        if (!btn) return;

        var input = btn.parentElement.querySelector('input');
        if (!input) return;

        var value = parseInt(input.value) || 1;
        var min = parseInt(input.min) || 1;
        var max = parseInt(input.max) || 999;

        if (btn.dataset.action === 'increase') {
            if (value < max) input.value = value + 1;
        } else if (btn.dataset.action === 'decrease') {
            if (value > min) input.value = value - 1;
        }

        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

// === CART PAGE ===
function initCartPage() {
    var cartItems = document.getElementById('cartItems');
    if (!cartItems) return;

    cartItems.addEventListener('click', function (e) {
        var removeBtn = e.target.closest('.btn-remove');
        if (removeBtn) {
            var id = removeBtn.dataset.id;
            removeCartItem(id);
        }
    });

    cartItems.addEventListener('change', function (e) {
        var input = e.target.closest('.cart-qty-input');
        if (!input) return;

        var id = input.dataset.id;
        var qty = parseInt(input.value) || 1;
        if (qty < 1) {
            qty = 1;
            input.value = 1;
        }
        updateCartItem(id, qty);
    });

    cartItems.addEventListener('click', function (e) {
        var btn = e.target.closest('.quantity-input__btn');
        if (!btn || !btn.dataset.id) return;

        var id = btn.dataset.id;
        var input = btn.parentElement.querySelector('input');
        if (!input) return;

        setTimeout(function () {
            updateCartItem(id, parseInt(input.value) || 1);
        }, 50);
    });
}

function updateCartItem(id, quantity) {
    var formData = new FormData();
    formData.append('action', 'update_cart');
    formData.append('id', id);
    formData.append('quantity', quantity);

    fetch('/api/ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                updateCartCount(data.cartCount);
                var item = document.querySelector('.cart-item[data-id="' + id + '"]');
                if (item) {
                    var totalEl = item.querySelector('.cart-item__total');
                    if (totalEl) totalEl.textContent = data.itemTotal;
                }
                var summaryCount = document.getElementById('summaryCount');
                var summaryTotal = document.getElementById('summaryTotal');
                if (summaryCount) summaryCount.textContent = data.cartCount;
                if (summaryTotal) summaryTotal.textContent = data.cartTotal;
            }
        });
}

function removeCartItem(id) {
    var formData = new FormData();
    formData.append('action', 'remove_from_cart');
    formData.append('id', id);

    fetch('/api/ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                updateCartCount(data.cartCount);
                var item = document.querySelector('.cart-item[data-id="' + id + '"]');
                if (item) {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-20px)';
                    item.style.transition = 'all 0.3s ease';
                    setTimeout(function () {
                        item.remove();
                        if (data.cartEmpty) {
                            location.reload();
                        } else {
                            var summaryCount = document.getElementById('summaryCount');
                            var summaryTotal = document.getElementById('summaryTotal');
                            if (summaryCount) summaryCount.textContent = data.cartCount;
                            if (summaryTotal) summaryTotal.textContent = data.cartTotal;
                        }
                    }, 300);
                }
                showToast('Produkt bol odstránený z košíka');
            }
        });
}

// === PRODUCT GALLERY ===
function initProductGallery() {
    var thumbs = document.querySelectorAll('.product-detail__thumb');
    var mainImage = document.getElementById('mainImage');
    if (!thumbs.length || !mainImage) return;

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            thumbs.forEach(function (t) { t.classList.remove('active'); });
            thumb.classList.add('active');

            var img = mainImage.querySelector('img');
            if (img) {
                img.src = thumb.dataset.image;
            }
        });
    });
}

// === FILTER SIDEBAR ===
function initFilterSidebar() {
    var toggle = document.getElementById('filterToggle');
    var sidebar = document.getElementById('filterSidebar');
    var close = document.getElementById('sidebarClose');
    var overlay = document.getElementById('sidebarOverlay');

    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', function () {
        sidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    });

    function closeSidebar() {
        sidebar.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (close) close.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
}

// === ORDER FORM VALIDATION ===
function initOrderForm() {
    var form = document.getElementById('orderForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        var requiredFields = form.querySelectorAll('[required]');
        var hasError = false;

        requiredFields.forEach(function (field) {
            field.classList.remove('error');
            if (!field.value.trim()) {
                field.classList.add('error');
                hasError = true;
            }
        });

        var email = form.querySelector('#email');
        if (email && email.value && !isValidEmail(email.value)) {
            email.classList.add('error');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            var firstError = form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            showToast('Vyplňte všetky povinné polia');
        }
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// === CONTACT FORM ===
function initContactForm() {
    var form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var requiredFields = form.querySelectorAll('[required]');
        var hasError = false;

        requiredFields.forEach(function (field) {
            field.classList.remove('error');
            if (!field.value.trim()) {
                field.classList.add('error');
                hasError = true;
            }
        });

        var email = form.querySelector('#cf_email');
        if (email && email.value && !isValidEmail(email.value)) {
            email.classList.add('error');
            hasError = true;
        }

        if (hasError) {
            var firstError = form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            showToast('Vyplňte všetky povinné polia');
            return;
        }

        var btn = document.getElementById('contactSubmitBtn');
        var origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="spin"><circle cx="12" cy="12" r="10"/></svg> Odosielam...';

        var formData = new FormData(form);
        formData.append('action', 'contact_form');

        fetch('/api/ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    form.innerHTML = '<div class="cform-success"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><h3>Správa bola odoslaná</h3><p>' + data.message + '</p></div>';
                } else {
                    showToast(data.message || 'Chyba pri odosielaní');
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
            })
            .catch(function () {
                showToast('Chyba pri odosielaní formulára');
                btn.disabled = false;
                btn.innerHTML = origText;
            });
    });
}

// === CARD TILT ===
function initCardTilt() {
    if (window.matchMedia('(pointer: coarse)').matches) return;

    document.addEventListener('mousemove', function (e) {
        var card = e.target.closest('.product-card, .category-card, .feature, .contact-box');
        if (!card) return;

        var rect = card.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        var centerX = rect.width / 2;
        var centerY = rect.height / 2;
        var rotateX = ((y - centerY) / centerY) * -6;
        var rotateY = ((x - centerX) / centerX) * 6;

        card.style.transform = 'perspective(600px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-4px)';
    });

    document.addEventListener('mouseleave', function (e) {
        var card = e.target.closest('.product-card, .category-card, .feature, .contact-box');
        if (card) {
            card.style.transform = '';
        }
    }, true);

    document.addEventListener('mouseout', function (e) {
        var card = e.target.closest('.product-card, .category-card, .feature, .contact-box');
        if (card && !card.contains(e.relatedTarget)) {
            card.style.transform = '';
        }
    });
}

// === HERO GLOW ===
function initHeroGlow() {
    var hero = document.getElementById('heroSection');
    var glow = document.getElementById('heroGlow');
    if (!hero || !glow || window.matchMedia('(pointer: coarse)').matches) return;

    hero.addEventListener('mousemove', function (e) {
        var rect = hero.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        glow.style.opacity = '1';
        glow.style.left = x + 'px';
        glow.style.top = y + 'px';
    });

    hero.addEventListener('mouseleave', function () {
        glow.style.opacity = '0';
    });
}

// === HEADER SCROLL ===
function initHeaderScroll() {
    var header = document.getElementById('siteHeader');
    if (!header) return;

    var last = 0;

    window.addEventListener('scroll', function () {
        var y = window.scrollY;
        if (y > 60) {
            header.classList.add('header--scrolled');
        } else {
            header.classList.remove('header--scrolled');
        }
        last = y;
    }, { passive: true });
}

// === SCROLL REVEAL ===
function initScrollReveal() {
    var items = document.querySelectorAll('.section__header, .category-card, .product-card, .feature, .contact-box, .contact-card, .about-service, .about-stat, .about-values__list li, .cta-banner__content, .cform-layout, .product-inquiry, .legal-content h2, .error-page');

    if (!items.length) return;

    items.forEach(function (el) {
        el.classList.add('sr');
    });

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                var delay = 0;
                var parent = entry.target.parentElement;
                if (parent) {
                    var siblings = parent.querySelectorAll(':scope > .sr');
                    siblings.forEach(function (sib, i) {
                        if (sib === entry.target) delay = i * 80;
                    });
                }
                setTimeout(function () {
                    entry.target.classList.add('sr--visible');
                }, Math.min(delay, 400));
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    items.forEach(function (el) {
        observer.observe(el);
    });
}

// === BACK TO TOP ===
function initBackToTop() {
    var btn = document.getElementById('backToTop');
    if (!btn) return;

    window.addEventListener('scroll', function () {
        if (window.scrollY > 400) {
            btn.classList.add('show');
        } else {
            btn.classList.remove('show');
        }
    });

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// === STICKY CTA ===
function initStickyCta() {
    var bar = document.getElementById('stickyCta');
    var actions = document.querySelector('.product-detail__actions');
    if (!bar || !actions) return;

    function checkSticky() {
        var rect = actions.getBoundingClientRect();
        if (rect.bottom < 0) {
            bar.classList.add('show');
        } else {
            bar.classList.remove('show');
        }
    }

    window.addEventListener('scroll', checkSticky, { passive: true });
    checkSticky();
}

// === SORT ===
function applySort(value) {
    var url = new URL(window.location.href);
    if (value) {
        url.searchParams.set('zoradit', value);
    } else {
        url.searchParams.delete('zoradit');
    }
    window.location.href = url.toString();
}

// === HELPERS ===
function updateCartCount(count) {
    var counters = document.querySelectorAll('#cartCount');
    counters.forEach(function (el) {
        el.textContent = count;
        if (count > 0) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    });
}

function animateCartIcon() {
    var cart = document.querySelector('.header__cart');
    if (!cart) return;
    cart.style.transform = 'scale(1.2)';
    setTimeout(function () {
        cart.style.transform = 'scale(1)';
        cart.style.transition = 'transform 0.2s ease';
    }, 200);
}

function showToast(message) {
    var toast = document.getElementById('toast');
    if (!toast) return;

    toast.textContent = message;
    toast.classList.add('show');

    setTimeout(function () {
        toast.classList.remove('show');
    }, 3000);
}
