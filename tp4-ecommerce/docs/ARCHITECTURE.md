# Architecture du Projet TP4 E-commerce

## 📐 Vue d'ensemble

Ce document décrit l'architecture générale de l'application e-commerce.

## 🏗️ Architecture MVC

L'application suit le pattern MVC (Model-View-Controller) de Laravel :
```
User Request
     ↓
  Routes (web.php)
     ↓
  Controller
     ↓
  Model ←→ Database
     ↓
  View (Blade)
     ↓
  Response
```

## 🗄️ Schéma de Base de Données

### Tables principales

#### users
```sql
- id
- name
- email (unique)
- password
- is_admin (boolean)
- timestamps
```

#### categories
```sql
- id
- name
- description
- timestamps
```

#### products
```sql
- id
- name
- description
- price
- stock
- image
- category_id (FK)
- timestamps
```

#### carts
```sql
- id
- user_id (FK)
- timestamps
```

#### cart_items
```sql
- id
- cart_id (FK)
- product_id (FK)
- quantity
- timestamps
```

#### orders
```sql
- id
- user_id (FK)
- total
- status (enum)
- timestamps
```

#### order_items
```sql
- id
- order_id (FK)
- product_id (FK)
- quantity
- price
- timestamps
```

### Relations
```
User ─┬─ 1:1 ─→ Cart ─── 1:N ─→ CartItem ─── N:1 ─→ Product
      │
      └─ 1:N ─→ Order ─── 1:N ─→ OrderItem ─── N:1 ─→ Product

Category ─── 1:N ─→ Product
```

## 📂 Structure des dossiers

### app/Http/Controllers
```
Controllers/
├── Auth/                    # Authentification
│   ├── LoginController.php
│   ├── RegisterController.php
│   └── LogoutController.php
│
├── Admin/                   # Administration
│   ├── DashboardController.php
│   ├── ProductController.php
│   └── OrderController.php
│
├── ProductController.php    # Produits publics
├── CartController.php       # Gestion panier
├── OrderController.php      # Commandes client
└── PaymentController.php    # Paiement
```

### resources/views
```
views/
├── layouts/
│   ├── app.blade.php       # Layout principal
│   └── admin.blade.php     # Layout admin
│
├── components/
│   ├── navbar.blade.php
│   ├── footer.blade.php
│   └── product-card.blade.php
│
├── auth/                   # Pages d'authentification
├── products/               # Pages produits
├── cart/                   # Page panier
├── orders/                 # Pages commandes
├── payment/                # Pages paiement
└── admin/                  # Pages admin
```

## 🔄 Flux de données

### 1. Ajout au panier
```
User clique "Ajouter au panier"
     ↓
POST /cart/add
     ↓
CartController@add
     ↓
Vérifier stock produit
     ↓
Créer/Mettre à jour CartItem
     ↓
Calculer total
     ↓
Redirection avec message
```

### 2. Processus de commande
```
User valide panier
     ↓
GET /checkout
     ↓
PaymentController@showCheckout
     ↓
Afficher récapitulatif
     ↓
User soumet paiement
     ↓
POST /payment/process
     ↓
PaymentController@processPayment
     ↓
Créer Order + OrderItems
     ↓
Décrémenter stock
     ↓
Vider panier
     ↓
Redirection confirmation
```

## 🔐 Sécurité

### Middleware

- **auth** : Vérifie l'authentification
- **guest** : Autorise seulement les non-authentifiés
- **admin** : Vérifie le rôle admin

### Protection CSRF

Tous les formulaires utilisent `@csrf` pour la protection CSRF.

### Validation

Utilisation de Form Requests pour valider les données entrantes.

## 🎨 Frontend

### Assets
```
public/
├── css/
│   └── style.css
├── js/
│   └── app.js
└── images/
    ├── products/
    └── logo.png
```

### Compilation

Les assets sont compilés avec Vite :
- `npm run dev` : Développement
- `npm run build` : Production

## 🧪 Tests

Structure des tests (à implémenter) :
```
tests/
├── Feature/
│   ├── AuthTest.php
│   ├── CartTest.php
│   ├── OrderTest.php
│   └── ProductTest.php
└── Unit/
    ├── ProductModelTest.php
    └── CartServiceTest.php
```

## 📊 Performance

### Cache

- Config : `php artisan config:cache`
- Routes : `php artisan route:cache`
- Views : Compilées automatiquement

### Optimisation

- Pagination des listes
- Eager loading des relations
- Index sur colonnes fréquemment requêtées

## 🔄 Déploiement

### Checklist

1. `composer install --optimize-autoloader --no-dev`
2. `npm run build`
3. `php artisan config:cache`
4. `php artisan route:cache`
5. `php artisan view:cache`
6. Configurer `.env` pour production
7. `php artisan migrate --force`

---

**Dernière mise à jour** : 2025-11-04