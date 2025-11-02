// ============================================
// product-ordi-navigation.js - VERSION COMPLÈTE & CORRIGÉE
// ============================================

let isUpdating = false;
let observer = null;

// Fonction principale : attache les clics sur les cartes produits
function attachProductNavigation() {
    if (isUpdating) return;
    isUpdating = true;

    try {
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            // Nettoyer les anciens événements (sans cloner)
            const handler = card._clickHandler;
            if (handler) {
                card.removeEventListener('click', handler);
            }

            const handleClick = function (e) {
                if (e.target.closest('.add-to-cart-btn')) return;
                e.preventDefault();

                let productInfo = {};

                // 1. Priorité : data-product (recommandé)
                const dataProduct = this.getAttribute('data-product');
                if (dataProduct) {
                    try {
                        productInfo = JSON.parse(dataProduct);
                    } catch (err) {
                        console.error('Erreur data-product:', err);
                    }
                }

                // 2. Sinon : extraire du DOM
                if (!productInfo.title) {
                    productInfo = {
                        brand: this.querySelector('.product-brand')?.textContent.trim() || '',
                        title: this.querySelector('.product-title-card')?.textContent.trim() || '',
                        price: this.querySelector('.product-price')?.textContent.trim() || '',
                        rating: this.querySelector('.rating-text')?.textContent.trim() || '',
                        image: this.querySelector('.product-image')?.src || '',
                        tag: this.querySelector('.product-tag')?.textContent.trim() || 'PREMIUM',
                        stars: Array.from(this.querySelectorAll('.stars-list i'))
                            .filter(i => i.classList.contains('fas') && !i.classList.contains('fa-star-half-alt')).length
                    };
                }

                sessionStorage.setItem('selectedProduct', JSON.stringify(productInfo));
                window.location.href = 'product_ordi-detail.html';
            };

            // Stocker pour nettoyage futur
            card._clickHandler = handleClick;
            card.addEventListener('click', handleClick);
            card.style.cursor = 'pointer';
        });
    } finally {
        setTimeout(() => { isUpdating = false; }, 100);
    }
}

// Exporter pour pagination_ordi.js
window.attachProductNavigation = attachProductNavigation;

// ============================================
// INITIALISATION + OBSERVATION DES DEUX GRILLES
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    attachProductNavigation();

    // Observer les deux grilles
    const grids = ['grid1', 'grid2'].map(id => document.getElementById(id)).filter(Boolean);

    if (grids.length > 0 && !observer) {
        observer = new MutationObserver(function (mutations) {
            let shouldUpdate = false;
            mutations.forEach(mutation => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    shouldUpdate = true;
                }
            });

            if (shouldUpdate && !isUpdating) {
                observer.disconnect();
                setTimeout(() => {
                    attachProductNavigation();
                    grids.forEach(grid => observer.observe(grid, { childList: true, subtree: true }));
                }, 50);
            }
        });

        grids.forEach(grid => {
            observer.observe(grid, { childList: true, subtree: true });
        });
    }
});

// ============================================
// AFFICHAGE DES DÉTAILS SUR product_ordi-detail.html
// ============================================
window.addEventListener('load', function () {
    if (!window.location.pathname.includes('product_ordi-detail.html')) return;

    const productData = sessionStorage.getItem('selectedProduct');
    if (!productData) return;

    let product;
    try {
        product = JSON.parse(productData);
    } catch (err) {
        console.error('Erreur parsing produit:', err);
        return;
    }

    const updateElement = (selector, value) => {
        const el = document.querySelector(selector);
        if (el && value) el.textContent = value;
    };

    updateElement('.product-title', product.title);
    updateElement('.new-tag', product.tag);
    updateElement('.main-price', product.price);
    updateElement('.rating-text', product.rating);

    // Étoiles
    const starsDisplay = document.querySelector('.stars-list');
    if (starsDisplay && product.stars) {
        let starsHTML = '';
        for (let i = 0; i < 5; i++) {
            starsHTML += i < product.stars
                ? '<i class="fas fa-star"></i>'
                : '<i class="far fa-star"></i>';
        }
        starsDisplay.innerHTML = starsHTML;
    }

    // Image principale
    const mainImage = document.querySelector('#main-image');
    if (mainImage && product.image) mainImage.src = product.image;

    // Miniatures
    document.querySelectorAll('.thumbnail img').forEach(thumb => {
        if (product.image) thumb.src = product.image;
    });

    // Sous-titre
    updateElement('.product-subtitle', `${product.brand} | Intel Core i7 | 16 Go RAM | SSD 512 Go`);
});

// ============================================
// GESTION DE LA GALERIE D'IMAGES
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('#main-image');
    if (!mainImage) return;

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function () {
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const src = this.querySelector('img')?.getAttribute('data-image') || this.querySelector('img')?.src;
            if (src) mainImage.src = src;
        });
    });
});

// ============================================
// GESTION DES COULEURS & QUANTITÉ
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    // Couleurs
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function () {
            document.querySelectorAll('.color-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            const color = this.getAttribute('data-color');
            if (color) document.querySelector('#selected-color').textContent = color;
        });
    });

    // Quantité
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const display = document.querySelector('.quantity-display');
            if (!display) return;
            let qty = parseInt(display.textContent) || 1;
            const isPlus = this.querySelector('i')?.classList.contains('fa-plus');
            if (isPlus) qty++;
            else if (qty > 1) qty--;
            display.textContent = qty;
        });
    });
});

// ============================================
// PANIER - VERSION ROBUSTE AVEC EVENT DELEGATION
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    // --- debug : voir si le script est inclus plusieurs fois
    if (!document.body.dataset.cartScriptLoaded) {
        document.body.dataset.cartScriptLoaded = "true";
    } else {
        console.warn('cart script already loaded on this page (possible double include).');
    }

    // Event delegation pour tout le clic pertinent
    document.addEventListener('click', function (e) {
        // 1) boutons + / - (class .quantity-btn)
        const qtyBtn = e.target.closest('.quantity-btn');
        if (qtyBtn) {
            e.stopPropagation();
            handleQuantityClick.call(qtyBtn, e);
            return;
        }

        // 2) bouton "Ajouter au panier" sur page détail (class .btn-add)
        const addBtn = e.target.closest('.btn-add');
        if (addBtn) {
            e.preventDefault();
            e.stopPropagation();
            // protection anti-multi-clic rapide
            if (addBtn.disabled) return;
            addBtn.disabled = true;
            setTimeout(() => addBtn.disabled = false, 500); // 0.5s

            // collecte des infos à partir du conteneur le plus proche
            const container = addBtn.closest('.product-detail') || document;
            const title = container.querySelector('.product-title')?.textContent?.trim();
            const price = container.querySelector('.main-price')?.textContent?.trim();
            const image = container.querySelector('#main-image')?.src || container.querySelector('.main-product-image img')?.src || '';
            const color = container.querySelector('#selected-color')?.textContent?.trim() || '';
            const qty = parseInt(container.querySelector('.quantity-display')?.textContent) || 1;

            if (!title || !price) {
                console.warn('Impossible d\'ajouter : title ou price manquant');
                return;
            }

            // debug : afficher combien d'appels on réalise
            console.debug('addBtn clicked -> calling addToCart once for', title);

            addToCart({ title, price, quantity: qty, image, color });
            return;
        }

        // 3) boutons "add-to-cart" dans les cartes produits (liste)
        const quickBtn = e.target.closest('.add-to-cart-btn');
        if (quickBtn) {
            e.preventDefault();
            e.stopPropagation();
            if (quickBtn.disabled) return;
            quickBtn.disabled = true;
            setTimeout(() => quickBtn.disabled = false, 500);

            const card = quickBtn.closest('.product-card');
            if (!card) return;

            const product = {
                title: card.querySelector('.product-title-card')?.textContent?.trim() || '',
                price: card.querySelector('.product-price')?.textContent?.trim() || '',
                quantity: 1,
                image: card.querySelector('.product-image')?.src || ''
            };

            console.debug('quick add clicked -> calling addToCart for', product.title);
            addToCart(product);
            return;
        }
    });

    // initial cart count sync
    updateCartCount();
});

/* ----------------------
   Handlers & Utilities
   ---------------------- */

function handleQuantityClick(e) {
    // this = button (we used call above)
    const parent = this.closest('.product-detail, .product-card');
    const display = parent?.querySelector('.quantity-display');
    if (!display) return;

    let qty = parseInt(display.textContent) || 1;
    if (this.textContent.includes('-')) qty = Math.max(1, qty - 1);
    else qty += 1;
    display.textContent = qty;
}

function addToCart(product) {
    // DEBUG: compter combien d'appels addToCart arrivent
    window.___addToCartCallCount = (window.___addToCartCallCount || 0) + 1;
    console.debug('addToCart call #', window.___addToCartCallCount, 'product.quantity=', product.quantity, product.title);

    let cart = [];
    try {
        cart = JSON.parse(localStorage.getItem('cart') || '[]');
        if (!Array.isArray(cart)) cart = [];
    } catch (err) {
        console.error('localStorage cart parsing error, resetting cart', err);
        cart = [];
    }

    const existing = cart.find(item =>
        item.title === product.title &&
        item.image === product.image &&
        item.color === product.color
    );

    if (existing) {
        existing.quantity = (existing.quantity || 0) + (product.quantity || 0);
    } else {
        // ensure numeric quantity
        product.quantity = Number(product.quantity) || 1;
        cart.push(product);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    showNotification(`+${product.quantity} × ${product.title}`, 'success');
    updateCartCount();
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const total = cart.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0);

    const countEl = document.querySelector('.cart-count');
    if (countEl) {
        const prev = parseInt(countEl.textContent) || 0;
        countEl.textContent = total;

        if (total !== prev) {
            countEl.style.transform = 'scale(1.4)';
            setTimeout(() => countEl.style.transform = 'scale(1)', 200);
        }
    } else {
        // debug si élément absent
        console.debug('cart-count element not found on page; total would be', total);
    }
}

function showNotification(message, type = 'success') {
    const colors = { success: '#10b981', info: '#3b82f6' };

    const notif = document.createElement('div');
    notif.style.cssText = `
        position: fixed; top: 100px; right: 20px; 
        background: ${colors[type]}; color: white; 
        padding: 14px 24px; border-radius: 8px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
        z-index: 10000; animation: slideIn 0.3s ease; 
        font-weight: 500; font-size: 0.9rem; max-width: 300px;
    `;
    notif.textContent = message;
    document.body.appendChild(notif);

    setTimeout(() => {
        notif.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notif.remove(), 300);
    }, 2000);
}

// animation css (ajoute si nécessaire)
if (!document.getElementById('cart-notif-style')) {
    const style = document.createElement('style');
    style.id = 'cart-notif-style';
    style.textContent = `
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
    `;
    document.head.appendChild(style);
}
