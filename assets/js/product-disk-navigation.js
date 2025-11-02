// ============================================
// product-disk-navigation.js - VERSION RENOUVELÉE 2025
// Navigation optimisée pour produits de stockage
// ============================================

let isUpdating = false;
let observer = null;

// === FONCTION PRINCIPALE : ATTACHE LES CLICS ===
function attachProductNavigation() {
    if (isUpdating) return;
    isUpdating = true;

    try {
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            // Nettoyer anciens événements
            const handler = card._clickHandler;
            if (handler) {
                card.removeEventListener('click', handler);
            }

            const handleClick = function (e) {
                // Ne pas intercepter les clics sur le bouton panier
                if (e.target.closest('.add-to-cart-btn')) return;
                e.preventDefault();

                let productInfo = {};

                // 1. Priorité : data-product
                const dataProduct = this.getAttribute('data-product');
                if (dataProduct) {
                    try {
                        productInfo = JSON.parse(dataProduct);
                    } catch (err) {
                        console.error('Erreur data-product:', err);
                    }
                }

                // 2. Extraire du DOM
                if (!productInfo.title) {
                    const brandEl = this.querySelector('.product-brand');
                    const titleEl = this.querySelector('.product-title-card');
                    const priceEl = this.querySelector('.product-price');
                    const ratingEl = this.querySelector('.rating-text');
                    const imageEl = this.querySelector('.product-image');
                    const tagEl = this.querySelector('.product-tag');
                    
                    const brand = brandEl?.textContent.trim() || '';
                    const title = titleEl?.textContent.trim() || '';
                    const capacity = title.match(/\d+\s?(TB|GB|To|Go)/i)?.[0] || '';
                    const type = title.includes('NVMe') ? 'NVMe' : 
                                 title.includes('SSD') ? 'SSD' : 
                                 title.includes('Externe') || title.includes('Portable') ? 'Externe' : 'HDD';
                    
                    productInfo = {
                        brand: brand,
                        title: title,
                        price: priceEl?.textContent.trim() || '',
                        rating: ratingEl?.textContent.trim() || '',
                        image: imageEl?.src || '',
                        tag: tagEl?.textContent.trim() || 'PREMIUM',
                        stars: Array.from(this.querySelectorAll('.stars-list i'))
                            .filter(i => i.classList.contains('fas') && !i.classList.contains('fa-star-half-alt')).length,
                        subtitle: `${brand} | ${capacity} | ${type} | Stockage fiable et performant`
                    };
                }

                // Sauvegarder dans sessionStorage
                sessionStorage.setItem('selectedProduct', JSON.stringify(productInfo));
                window.location.href = 'product_disk-detail.html';
            };

            card._clickHandler = handleClick;
            card.addEventListener('click', handleClick);
            card.style.cursor = 'pointer';
        });
    } finally {
        setTimeout(() => { isUpdating = false; }, 100);
    }
}

// Exporter pour pagination_disk.js
window.attachProductNavigation = attachProductNavigation;

// === INITIALISATION + OBSERVATION ===
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

// === AFFICHAGE DÉTAILS SUR product_disk-detail.html ===
window.addEventListener('load', function () {
    if (!window.location.pathname.includes('product_disk-detail.html')) return;

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

    // Mise à jour du titre de la page
    document.title = `${product.title} | Shopcart`;

    // Mise à jour des informations
    updateElement('.product-title', product.title);
    updateElement('.new-tag', product.tag);
    updateElement('.main-price', product.price);
    updateElement('.rating-text', product.rating);
    updateElement('.product-subtitle', product.subtitle || `${product.brand} | Stockage haute performance`);

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
    if (mainImage && product.image) {
        mainImage.src = product.image;
        mainImage.alt = product.title;
    }

    // Miniatures (utiliser l'image du produit pour toutes)
    document.querySelectorAll('.thumbnail img').forEach(thumb => {
        if (product.image) {
            thumb.src = product.image;
            thumb.dataset.image = product.image;
        }
    });
});

// === GESTION GALERIE D'IMAGES ===
document.addEventListener('DOMContentLoaded', function () {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('#main-image');
    if (!mainImage) return;

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function () {
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const src = this.querySelector('img')?.getAttribute('data-image') || this.querySelector('img')?.src;
            if (src) {
                mainImage.style.opacity = '0.5';
                setTimeout(() => {
                    mainImage.src = src;
                    mainImage.style.opacity = '1';
                }, 150);
            }
        });
    });
});

// === GESTION COULEURS/FORMATS & QUANTITÉ ===
document.addEventListener('DOMContentLoaded', function () {
    // Sélection de format (couleurs)
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function () {
            document.querySelectorAll('.color-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            const format = this.getAttribute('data-color');
            if (format) {
                const selectedEl = document.querySelector('#selected-color');
                if (selectedEl) selectedEl.textContent = format;
            }
        });
    });

    // Gestion quantité
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const display = document.querySelector('.quantity-display');
            if (!display) return;
            
            let qty = parseInt(display.textContent) || 1;
            const isPlus = this.querySelector('i')?.classList.contains('fa-plus');
            
            if (isPlus) {
                qty++;
            } else if (qty > 1) {
                qty--;
            }
            
            display.textContent = qty;
            
            // Animation
            display.style.transform = 'scale(1.2)';
            setTimeout(() => { display.style.transform = 'scale(1)'; }, 150);
        });
    });
});


origin/tp/2-js-dynamics

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