document.addEventListener('DOMContentLoaded', () => {
    const productCards = document.querySelectorAll('#products-for-you-grid .product-card');
    const filterDropdownLinks = document.querySelectorAll('.tag-buttons-group .dropdown-content a');
    const allFiltersButton = document.querySelector('.filter-button.primary-filter');
    const filterNone = document.getElementById('none');
    
    // Sélectionner tous les inputs radio des filtres
    const filterToggles = document.querySelectorAll('input[name="filters"]');

    let activeFilters = {
        price: null,
        brand: null,
        rating: null
    };

    const applyFilters = () => {
        let matchingCards = [];
        
        productCards.forEach(card => {
            let matches = true;

            const brand = card.dataset.brand ? card.dataset.brand.toLowerCase() : '';
            const price = parseFloat(card.dataset.priceRaw) || 0;
            const rating = parseFloat(card.dataset.ratingRaw) || 0;

            // 1. Filtrage par Marque
            if (activeFilters.brand) {
                const filterBrandNormalized = activeFilters.brand.toLowerCase();
                if (!brand.includes(filterBrandNormalized)) {
                    matches = false;
                }
            }

            // 2. Filtrage par Prix
            if (matches && activeFilters.price) {
                const [minStr, maxStr] = activeFilters.price.split('-');
                const minPrice = parseFloat(minStr);
                const maxPrice = maxStr === '+' ? Infinity : parseFloat(maxStr);

                if (price < minPrice || price > maxPrice) {
                    matches = false;
                }
            }

            // 3. Filtrage par Note (LOGIQUE STRICTE)
            if (matches && activeFilters.rating) {
                const minRating = parseFloat(activeFilters.rating);

                // Détermine la fourchette stricte
                let lowerBound, upperBound;
                
                if (minRating === 5) {
                    lowerBound = 4.75;
                    upperBound = 5.1;
                } else if (minRating === 4.5) {
                    lowerBound = 4.25;
                    upperBound = 4.75; 
                } else {
                    lowerBound = minRating;
                    upperBound = minRating + 0.5;
                }
                
                if (rating < lowerBound || rating >= upperBound) {
                    matches = false;
                }
            }
            
            // Masquer/Afficher l'élément
            if (matches) {
                matchingCards.push(card);
                card.classList.remove('filtered-out');
            } else {
                card.classList.add('filtered-out');
            }
            
            // Applique le style pour masquer/afficher
            card.style.display = matches ? '' : 'none';
        });
        
        // Optionnel : Afficher un message si aucun produit ne correspond
        const productGrid = document.getElementById('products-for-you-grid');
        let noResultsMessage = productGrid.querySelector('.no-results-message');
        
        if (matchingCards.length === 0) {
            if (!noResultsMessage) {
                noResultsMessage = document.createElement('p');
                noResultsMessage.classList.add('no-results-message');
                noResultsMessage.textContent = "Aucun produit ne correspond à ces critères de filtre.";
                productGrid.appendChild(noResultsMessage);
            }
            noResultsMessage.style.display = '';
        } else {
            if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
        }
    };

    const resetFilters = () => {
        activeFilters = { price: null, brand: null, rating: null };
        productCards.forEach(card => {
            card.style.display = '';
            card.classList.remove('filtered-out');
        });
        
        const noResultsMessage = document.querySelector('#products-for-you-grid .no-results-message');
        if (noResultsMessage) {
            noResultsMessage.style.display = 'none';
        }

        filterNone.checked = true; 
    };

    // --- Écouteurs d'Événements ---

    filterDropdownLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();

            // Identifie le type de filtre et met à jour l'état
            const dataAttributes = e.currentTarget.dataset;
            
            // Un seul filtre est actif à la fois (marque, prix ou note)
            activeFilters.price = dataAttributes.price || null;
            activeFilters.brand = dataAttributes.brand || null;
            activeFilters.rating = dataAttributes.rating || null;
            
            applyFilters();

            // IMPORTANT : Fermer TOUS les dropdowns après avoir cliqué
            filterNone.checked = true;
        });
    });

    // Gérer le bouton "Tous les filtres" pour réinitialiser
    allFiltersButton.addEventListener('click', () => {
        resetFilters();
    });
    
    // Optionnel : Fermer le dropdown si on clique en dehors
    document.addEventListener('click', (e) => {
        // Vérifier si le clic est en dehors des filtres
        const isClickInsideFilter = e.target.closest('.filter-dropdown') || 
                                    e.target.closest('.tag-buttons-group') ||
                                    e.target.closest('.filter-button');
        
        if (!isClickInsideFilter) {
            filterNone.checked = true;
        }
    });
});