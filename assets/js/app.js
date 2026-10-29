document.addEventListener('DOMContentLoaded', function () {
    initMobileMenu();
    initCartButtons();
    initQuantityInputs();
    initCartPage();
    initProductGallery();
    initFilterSidebar();
    initPriceRange();
    initCategoryFilterToggle();
    initOrderForm();
    initContactForm();
    initVerigaForm();
    initVerigaPopup();
    initLiveSearch();
    initBackToTop();
    initStickyCta();
    initScrollReveal();
    initHeaderScroll();
    initCardTilt();
    initHeroGlow();
    initHeroShowcase();
    initHeroSlider();
    initSmoothHashArrival();
    initProductsSlider();
    initCookieConsent();
});

// === MOBILE MENU ===
function initMobileMenu() {
    const hamburger = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('mainMenu');
    const searchToggle = document.getElementById('searchToggle');
    const searchWrap = document.getElementById('headerSearchWrap');
    const searchInput = document.getElementById('headerSearchInput');
    const submenuToggles = document.querySelectorAll('[data-menu-submenu-toggle]');

    function closeSubmenus() {
        document.querySelectorAll('.header__menu-item--dropdown.is-open').forEach(function (item) {
            item.classList.remove('is-open');
            var toggle = item.querySelector('[data-menu-submenu-toggle]');
            var link = item.querySelector('.header__menu-link-wrap > a');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
            if (link) link.setAttribute('aria-expanded', 'false');
        });
    }

    submenuToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var item = toggle.closest('.header__menu-item--dropdown');
            if (!item) return;

            var isOpen = item.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            var link = item.querySelector('.header__menu-link-wrap > a');
            if (link) link.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    if (hamburger && menu) {
        hamburger.addEventListener('click', function () {
            hamburger.classList.toggle('active');
            menu.classList.toggle('open');
            if (!menu.classList.contains('open')) closeSubmenus();
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
                closeSubmenus();
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (hamburger && menu && !hamburger.contains(e.target) && !menu.contains(e.target)) {
            hamburger.classList.remove('active');
            menu.classList.remove('open');
            closeSubmenus();
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
            dropdown.innerHTML = '<div class="search-dropdown__empty">Katalógové číslo zadávajte bez pomlčiek</div>';
            dropdown.classList.add('open');
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
            html += '<a href="' + p.url + '" class="search-dropdown__item">';
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
        if (input.value.trim().length < 3) {
            dropdown.innerHTML = '<div class="search-dropdown__empty">Katalógové číslo zadávajte bez pomlčiek</div>';
            dropdown.classList.add('open');
        } else if (dropdown.innerHTML) {
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

// === PRICE RANGE FILTER ===
function initPriceRange() {
    var range = document.querySelector('[data-price-range]');
    if (!range) return;

    var fromInput = range.querySelector('#priceFrom');
    var toInput = range.querySelector('#priceTo');
    var fill = range.querySelector('[data-price-fill]');
    var fromLabel = range.querySelector('[data-price-from]');
    var toLabel = range.querySelector('[data-price-to]');
    if (!fromInput || !toInput || !fill) return;

    var min = Number(range.dataset.min) || 0;
    var max = Number(range.dataset.max) || 1;
    var step = Number(fromInput.step) || 1;
    var formatter = new Intl.NumberFormat('sk-SK', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });

    function formatPrice(value) {
        return formatter.format(value) + ' €';
    }

    function update(source) {
        var from = Number(fromInput.value);
        var to = Number(toInput.value);

        if (source === fromInput && from > to - step) {
            fromInput.value = String(Math.max(min, to - step));
        }
        if (source === toInput && to < from + step) {
            toInput.value = String(Math.min(max, from + step));
        }

        from = Number(fromInput.value);
        to = Number(toInput.value);
        var span = Math.max(1, max - min);
        var fromPercent = ((from - min) / span) * 100;
        var toPercent = ((to - min) / span) * 100;

        fill.style.left = fromPercent + '%';
        fill.style.width = Math.max(0, toPercent - fromPercent) + '%';
        if (fromLabel) fromLabel.textContent = formatPrice(from);
        if (toLabel) toLabel.textContent = formatPrice(to);
    }

    fromInput.addEventListener('input', function () {
        update(fromInput);
    });
    toInput.addEventListener('input', function () {
        update(toInput);
    });
    update();
}

// === CATEGORY FILTER TOGGLE ===
function initCategoryFilterToggle() {
    document.querySelectorAll('[data-category-toggle]').forEach(function (toggle) {
        var list = toggle.previousElementSibling;
        var label = toggle.querySelector('span');
        if (!list || !label) return;

        toggle.addEventListener('click', function () {
            var isExpanded = list.classList.toggle('is-expanded');
            toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
            label.textContent = isExpanded
                ? toggle.dataset.labelLess
                : toggle.dataset.labelMore;
        });
    });
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

// === HERO SHOWCASE ===
function initHeroShowcase() {
    var showcase = document.querySelector('[data-hero-showcase]');
    if (!showcase) return;

    var slides = showcase.querySelectorAll('[data-hero-slide]');
    var dots = showcase.querySelectorAll('[data-hero-go]');
    var currentLabel = showcase.querySelector('[data-hero-current]');
    var current = 0;
    var timer;

    if (!slides.length) return;

    function setSlide(index) {
        current = (index + slides.length) % slides.length;

        slides.forEach(function (slide, i) {
            slide.classList.toggle('is-active', i === current);
        });

        dots.forEach(function (dot, i) {
            dot.classList.toggle('is-active', i === current);
            dot.setAttribute('aria-current', i === current ? 'true' : 'false');
        });

        if (currentLabel) {
            currentLabel.textContent = String(current + 1).padStart(2, '0');
        }
    }

    function startTimer() {
        window.clearInterval(timer);
        timer = window.setInterval(function () {
            setSlide(current + 1);
        }, 6000);
    }

    var prev = showcase.querySelector('[data-hero-prev]');
    var next = showcase.querySelector('[data-hero-next]');

    if (prev) prev.addEventListener('click', function () {
        setSlide(current - 1);
        startTimer();
    });

    if (next) next.addEventListener('click', function () {
        setSlide(current + 1);
        startTimer();
    });

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            setSlide(Number(dot.getAttribute('data-hero-go')) || 0);
            startTimer();
        });
    });

    showcase.addEventListener('mouseenter', function () {
        window.clearInterval(timer);
    });

    showcase.addEventListener('mouseleave', startTimer);
    showcase.addEventListener('focusin', function () {
        window.clearInterval(timer);
    });
    showcase.addEventListener('focusout', function (event) {
        if (!showcase.contains(event.relatedTarget)) startTimer();
    });

    setSlide(0);
    startTimer();
}

// === HOMEPAGE PRODUCTS SLIDER ===
function initProductsSlider() {
    var slider = document.querySelector('[data-products-slider]');
    var track = slider ? slider.querySelector('[data-products-track]') : null;
    if (!slider || !track) return;

    var cards = track.querySelectorAll('.product-card');
    var prev = slider.querySelector('[data-products-prev]');
    var next = slider.querySelector('[data-products-next]');
    var timer;

    if (cards.length < 2) return;

    function getStep() {
        var gap = parseFloat(window.getComputedStyle(track).gap) || 24;
        return cards[0].getBoundingClientRect().width + gap;
    }

    function scrollNext() {
        var maxScroll = track.scrollWidth - track.clientWidth;
        var nextPosition = track.scrollLeft + getStep();

        if (nextPosition >= maxScroll - 4) {
            track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            track.scrollBy({ left: getStep(), behavior: 'smooth' });
        }
    }

    function scrollPrev() {
        if (track.scrollLeft <= 4) {
            track.scrollTo({
                left: Math.max(0, track.scrollWidth - track.clientWidth),
                behavior: 'smooth'
            });
        } else {
            track.scrollBy({ left: -getStep(), behavior: 'smooth' });
        }
    }

    function startTimer() {
        window.clearInterval(timer);
        timer = window.setInterval(function () {
            if (!document.hidden) scrollNext();
        }, 6500);
    }

    if (prev) prev.addEventListener('click', function () {
        scrollPrev();
        startTimer();
    });

    if (next) next.addEventListener('click', function () {
        scrollNext();
        startTimer();
    });

    slider.addEventListener('mouseenter', function () {
        window.clearInterval(timer);
    });

    slider.addEventListener('mouseleave', startTimer);
    slider.addEventListener('touchstart', function () {
        window.clearInterval(timer);
    }, { passive: true });
    slider.addEventListener('touchend', startTimer, { passive: true });

    startTimer();
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

// === COOKIE CONSENT ===
var COOKIE_CONSENT_KEY = 'orvex_cookie_consent';

function getCookieConsent() {
    try {
        var raw = localStorage.getItem(COOKIE_CONSENT_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch (e) {
        return null;
    }
}

function saveCookieConsent(analytics, marketing) {
    var consent = { necessary: true, analytics: !!analytics, marketing: !!marketing, ts: Date.now() };
    try {
        localStorage.setItem(COOKIE_CONSENT_KEY, JSON.stringify(consent));
    } catch (e) {}
    document.dispatchEvent(new CustomEvent('cookieconsentchange', { detail: consent }));
    return consent;
}

// window.cookieConsent.has('analytics'|'marketing') - pre buduce skripty (napr. GA),
// ktore sa maju nacitat len so suhlasom uzivatela.
window.cookieConsent = {
    get: getCookieConsent,
    has: function (category) {
        var consent = getCookieConsent();
        return !!(consent && consent[category]);
    }
};

function initCookieConsent() {
    var banner = document.getElementById('cookieBanner');
    var overlay = document.getElementById('cookieModalOverlay');
    var fab = document.getElementById('cookieFab');
    if (!banner || !overlay || !fab) return;

    var acceptBtn = document.getElementById('cookieAcceptBtn');
    var declineBtn = document.getElementById('cookieDeclineBtn');
    var settingsBtn = document.getElementById('cookieSettingsBtn');
    var footerLink = document.getElementById('cookieSettingsFooterLink');
    var closeBtn = document.getElementById('cookieModalClose');
    var saveBtn = document.getElementById('cookieSaveBtn');
    var declineAllBtn = document.getElementById('cookieDeclineAllBtn');
    var analyticsToggle = document.getElementById('cookieAnalyticsToggle');
    var marketingToggle = document.getElementById('cookieMarketingToggle');

    function showBanner() {
        banner.hidden = false;
        fab.hidden = true;
    }

    function hideBanner() {
        banner.hidden = true;
        fab.hidden = false;
    }

    function openModal() {
        var consent = getCookieConsent();
        analyticsToggle.checked = !!(consent && consent.analytics);
        marketingToggle.checked = !!(consent && consent.marketing);
        overlay.hidden = false;
    }

    function closeModal() {
        overlay.hidden = true;
    }

    var existingConsent = getCookieConsent();
    if (existingConsent) {
        hideBanner();
    } else {
        showBanner();
    }

    if (acceptBtn) acceptBtn.addEventListener('click', function () {
        saveCookieConsent(true, true);
        hideBanner();
    });

    if (declineBtn) declineBtn.addEventListener('click', function () {
        saveCookieConsent(false, false);
        hideBanner();
    });

    if (settingsBtn) settingsBtn.addEventListener('click', openModal);
    if (footerLink) footerLink.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !overlay.hidden) closeModal();
    });

    if (saveBtn) saveBtn.addEventListener('click', function () {
        saveCookieConsent(analyticsToggle.checked, marketingToggle.checked);
        closeModal();
        hideBanner();
    });

    if (declineAllBtn) declineAllBtn.addEventListener('click', function () {
        saveCookieConsent(false, false);
        closeModal();
        hideBanner();
    });

    if (fab) fab.addEventListener('click', openModal);
}


// === VERIGA: DOPYTOVY FORMULAR ===
function initVerigaForm() {
    var form = document.getElementById('verigaForm');
    if (!form) return;

    // Kliknutie na dlazdicu typu predvyplni typ retazi vo formulari.
    document.querySelectorAll('[data-veriga-type]').forEach(function (tile) {
        tile.addEventListener('click', function () {
            var radio = form.querySelector('input[name="type"][value="' + tile.getAttribute('data-veriga-type') + '"]');
            if (radio) radio.checked = true;
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var hasError = false;
        form.querySelectorAll('.error').forEach(function (el) { el.classList.remove('error'); });

        form.querySelectorAll('input[required]:not([type="radio"]):not([type="checkbox"]), textarea[required]').forEach(function (field) {
            if (!field.value.trim()) {
                field.classList.add('error');
                hasError = true;
            }
        });

        var email = form.querySelector('#vf_email');
        if (email && email.value && !isValidEmail(email.value)) {
            email.classList.add('error');
            hasError = true;
        }

        if (!form.querySelector('input[name="type"]:checked')) {
            form.querySelector('.vform-types').classList.add('error');
            hasError = true;
        }

        var consent = form.querySelector('input[name="consent"]');
        if (!consent.checked) {
            hasError = true;
            showToast('Potvrďte súhlas so spracovaním osobných údajov');
        }

        if (hasError) {
            var firstError = form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            showToast('Vyplňte všetky povinné polia');
            return;
        }

        var btn = document.getElementById('verigaSubmitBtn');
        var origText = btn.innerHTML;
        btn.disabled = true;
        btn.textContent = 'Odosielam...';

        var formData = new FormData(form);
        formData.append('action', 'veriga_inquiry');

        fetch('/api/ajax.php', { method: 'POST', body: formData })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    form.innerHTML = '<div class="cform-success"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><h3>Dopyt bol odoslaný</h3><p>' + String(data.message).replace(/[escapeHtml(data.message)<>"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }) + '</p></div>';
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

// === VERIGA: MALE OKNO (NIE CELOOBRAZOVKOVE - Google penalizuje rusive interstitialy) ===
function initVerigaPopup() {
    var popup = document.getElementById('verigaPopup');
    if (!popup) return;

    var KEY = 'orvex_veriga_popup';
    var HIDE_DAYS = 14;

    try {
        var last = parseInt(localStorage.getItem(KEY), 10);
        if (last && Date.now() - last < HIDE_DAYS * 86400000) return;
    } catch (e) { /* localStorage nedostupny - okno sa ukaze */ }

    function dismiss() {
        popup.hidden = true;
        try { localStorage.setItem(KEY, String(Date.now())); } catch (e) { /* ignore */ }
    }

    document.getElementById('verigaPopupClose').addEventListener('click', dismiss);
    popup.querySelector('a').addEventListener('click', dismiss);

    // Ukaz az ked sa zavrie cookie lista, aby sa okna neprekryvali.
    function show() {
        var banner = document.getElementById('cookieBanner');
        if (banner && !banner.hidden) {
            document.addEventListener('cookieconsentchange', function () { setTimeout(show, 1500); }, { once: true });
            return;
        }
        popup.hidden = false;
    }
    setTimeout(show, 6000);
}

// === HOMEPAGE: SLIDER CELYCH SEKCII (hero) ===
function initHeroSlider() {
    var root = document.querySelector('[data-hslider]');
    if (!root) return;

    var track = root.querySelector('[data-hslider-track]');
    var slides = root.querySelectorAll('[data-hslide]');
    var dots = root.querySelectorAll('[data-hslider-go]');
    var total = slides.length;
    if (total < 2) return;

    var index = 0;
    var timer = null;
    var AUTOPLAY_MS = 8000;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function render() {
        track.style.transform = 'translateX(' + (-index * 100) + '%)';
        slides.forEach(function (slide, i) {
            var active = i === index;
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
            // inert = neaktivny slide sa nedostane do tab-poradia ani ku čitačkám obrazovky
            if (active) slide.removeAttribute('inert'); else slide.setAttribute('inert', '');
        });
        dots.forEach(function (dot, i) {
            dot.classList.toggle('is-active', i === index);
            if (i === index) dot.setAttribute('aria-current', 'true'); else dot.removeAttribute('aria-current');
        });
    }

    function go(i) {
        index = (i + total) % total;
        render();
    }

    function stop() {
        if (timer) { clearInterval(timer); timer = null; }
    }

    function start() {
        if (reduceMotion || timer) return;
        timer = setInterval(function () { go(index + 1); }, AUTOPLAY_MS);
    }

    root.querySelector('[data-hslider-prev]').addEventListener('click', function () { go(index - 1); stop(); });
    root.querySelector('[data-hslider-next]').addEventListener('click', function () { go(index + 1); stop(); });
    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            go(Number(dot.getAttribute('data-hslider-go')) || 0);
            stop();
        });
    });

    // Pauza pri prejdení myšou / zameraní klávesnicou, aby sa slide nemenil pod rukami.
    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', start);
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) stop(); else start();
    });

    // Swipe na dotyku
    var startX = null;
    root.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; stop(); }, { passive: true });
    root.addEventListener('touchend', function (e) {
        if (startX === null) return;
        var dx = e.changedTouches[0].clientX - startX;
        startX = null;
        if (Math.abs(dx) > 50) go(dx < 0 ? index + 1 : index - 1);
    }, { passive: true });

    root.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') go(index - 1);
        if (e.key === 'ArrowRight') go(index + 1);
    });

    render();
    start();
}

// === PLYNULY PRICHOD NA SEKCIU INEJ STRANKY ===
// Odkaz s data-goto="id-sekcie" otvori stranku BEZ #hash (prehliadac by inak najprv skocil na sekciu a
// az potom by sa nasa animacia rozbehla - posek/sekanie). Id si zapamata a po nacitani stranky sa k sekcii
// plynule doroluje od vrchu. Bez JS odkaz jednoducho otvori stranku.
function initSmoothHashArrival() {
    var HEADER_OFFSET = 100;
    var KEY = 'orvex_goto';

    function easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    // Scroll s animaciou. Ciel sa prepocitava v kazdom kroku, takze neprekazaju dodatocne nacitane obrazky.
    function scrollToTarget(target) {
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var targetY = function () {
            return Math.max(0, target.getBoundingClientRect().top + window.pageYOffset - HEADER_OFFSET);
        };
        if (reduceMotion) { window.scrollTo({ top: targetY(), behavior: 'instant' }); return; }

        var startY = window.pageYOffset;
        var duration = Math.min(1400, Math.max(700, Math.abs(targetY() - startY) * 0.35));
        var startTime = null;

        function step(now) {
            if (startTime === null) startTime = now;
            var t = Math.min(1, (now - startTime) / duration);
            var y = startY + (targetY() - startY) * easeInOutCubic(t);
            window.scrollTo({ top: y, behavior: 'instant' });
            if (t < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // 1) klik na odkaz s data-goto -> zapamataj cielovu sekciu
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[data-goto]');
        if (!link) return;
        try { sessionStorage.setItem(KEY, link.getAttribute('data-goto')); } catch (err) { /* ignore */ }
    });

    // 2) po nacitani stranky: zapamatana sekcia, prip. klasicky #hash
    var id = null;
    try {
        id = sessionStorage.getItem(KEY);
        if (id) sessionStorage.removeItem(KEY);
    } catch (err) { /* ignore */ }
    var fromHash = false;
    if (!id && window.location.hash.length > 1) { id = window.location.hash.slice(1); fromHash = true; }
    if (!id) return;

    var target = document.getElementById(id);
    if (!target) return;

    if (fromHash) {
        // prehliadac uz skocil na #hash - vratime sa hore a doroluje sa plynulo
        if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
        window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
    }

    var go = function () { setTimeout(function () { scrollToTarget(target); }, 200); };
    if (document.readyState === 'complete') go(); else window.addEventListener('load', go);
}
