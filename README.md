<<<<<<< HEAD
# TP1 - Site Web Statique from Scratch (HTML & CSS)

## Objectif
Construire un site vitrine e-commerce statique avec pages : accueil (landing), liste produits, détails produit, panier (statique), commande/paiement (formulaires), login/register, contact, about us. Respecter l'organisation en dossiers (/assets/css, /assets/images, etc.) et une charte graphique cohérente (mise en page, navigation, footer), selon la fiche TP jointe.

**Note** : Le tableau de bord (dashboard) n'est pas requis pour TP1, car il est hors du scope de ce TP statique. Il sera abordé dans les TPs ultérieurs, notamment TP4 avec Laravel.

## Livrable
Site statique fonctionnel, testable localement.

## Contributions par Équipe
- Équipe 1 (A, B, C) : Page landing (`index.html`) avec structure (Header, Footer, Navigation) et contenu de la section d'accueil.
- Équipe 2 (D, E, F) : Pages liste produits (`products.html`) et détails produit (`product-detail.html`) avec grille produits et fiche individuelle (photo, description, prix).
- Équipe 3 (G, H, I) : Pages login (`login.html`) et register (`register.html`) avec formulaires HTML et stylisation CSS.
- Équipe 4 (J, K, L) : Pages about us (`about.html`) et contact (`contact.html`) avec structure, contenu textuel et formulaire.
- Équipe 5 (M) : Intégration finale et setup Git, assurant la cohésion graphique et l'assemblage de toutes les pages avec placeholders pour panier, commande/paiement.

## Installation Spécifique
Ouvrez `index.html` dans un navigateur moderne (Chrome, Firefox).
=======
# 🛒 TP4 E-commerce - Application Laravel
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



<p align="center">
<a href="https://img.shields.io/badge/Laravel-10.x-red?logo=laravel"><img src="https://img.shields.io/badge/Laravel-10.x-red?logo=laravel" alt="Laravel"></a>
<a href="https://img.shields.io/badge/PHP-8.1+-blue?logo=php"><img src="https://img.shields.io/badge/PHP-8.1+-blue?logo=php" alt="php"></a>
<a href="https://img.shields.io/badge/MySQL-8.0+-orange?logo=mysql"><img src="https://img.shields.io/badge/MySQL-8.0+-orange?logo=mysql" alt="Laravel"></a>
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
Application e-commerce complète développée avec Laravel dans le cadre du TP4 de Programmation Web.

## 📋 Table des matières

- [Aperçu](#aperçu)
- [Fonctionnalités](#fonctionnalités)
- [Technologies utilisées](#technologies-utilisées)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Structure du projet](#structure-du-projet)
- [Équipe](#équipe)
- [Contribution](#contribution)
- [Documentation](#documentation)
- [License](#license)

## 🎯 Aperçu
>>>>>>> upstream/tp/4-laravel-full

Application e-commerce full-stack permettant aux utilisateurs de :
- Parcourir un catalogue de produits
- Ajouter des articles au panier
- Passer des commandes
- Suivre l'historique des achats

Les administrateurs peuvent :
- Gérer les produits (CRUD)
- Suivre les commandes
- Accéder aux statistiques

## ✨ Fonctionnalités

### Pour les clients

- ✅ Authentification (inscription/connexion)
- ✅ Catalogue de produits avec recherche et filtres
- ✅ Fiche produit détaillée
- ✅ Panier d'achat persistant
- ✅ Système de commande
- ✅ Paiement simulé
- ✅ Historique des commandes

<<<<<<< HEAD
## Licence
MIT - Projet éducatif.
## Fichiers Clés
- `index.html` : Page d'accueil.
- `/assets/css/main.css` : Styles globaux.
- `/assets/images/` : Répertoire des images produits.
- `/assets/css/style-login.css`, `/assets/css/style-contact.css` : Styles spécifiques.

## Arborescence Proposée
```
shopecart-web-project/
├── index.html
├── products.html
├── product-detail.html
├── login.html
├── register.html
├── about.html
├── contact.html
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   ├── style-login.css
│   │   ├── style-contact.css
│   │   └── style-product.css
│   └── images/
│       ├── product1.jpg
│       ├── product2.jpg
│       └── ...
├── README.md
└── .gitignore
```

Voir la répartition des tâches pour plus de détails.
=======
### Pour les administrateurs

- ✅ Dashboard avec statistiques
- ✅ Gestion des produits (ajout, modification, suppression)
- ✅ Gestion des commandes
- ✅ Mise à jour des statuts de commande

## 🛠️ Technologies utilisées

### Backend
- **Laravel 10.x** - Framework PHP
- **MySQL** - Base de données
- **Eloquent ORM** - Gestion des données
- **Blade** - Moteur de templates

### Frontend
- **HTML5 / CSS3**
- **JavaScript (Vanilla)**
- **Bootstrap 5** (optionnel)
- **Responsive Design**

### Outils

- **Composer** - Gestionnaire de dépendances PHP
- **NPM** - Gestionnaire de dépendances JS
- **Git** - Contrôle de version
- **Vite** - Bundler d'assets

## 📦 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- PHP >= 8.1
- Composer >= 2.5
- Node.js >= 18.x
- MySQL >= 8.0
- Git

## 🚀 Installation

### 1. Cloner le  

```bash
git clone https://github.com/Delmat237/Shopecart-Web-Project.git 
cd Shopecart-Web-Project
```

###  Acceder à la branche 
```bash
git checkout tp/4-laravel-full
```
### Acceder au projet
```bash
cd tp4-ecommerce
```
### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances JavaScript

```bash
npm install
```

### 4. Créer le fichier de configuration

```bash
cp .env.example .env
```

### 5. Générer la clé d'application

```bash
php artisan key:generate
```

### 6. Créer la base de données

Créez une base de données MySQL nommée `tp4_ecommerce` :

```sql
CREATE DATABASE tp4_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Configurer la base de données

Modifiez le fichier `.env` avec vos identifiants :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tp4_ecommerce
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### 8. Exécuter les migrations et seeders

```bash
php artisan migrate --seed
```

### 9. Créer le lien symbolique pour le stockage

```bash
php artisan storage:link
```

### 10. Compiler les assets

```bash
npm run dev
```

### 11. Lancer le serveur de développement

```bash
php artisan serve
```

L'application sera accessible sur : **http://localhost:8000**

## ⚙️ Configuration

### Comptes de test

Après avoir exécuté les seeders, vous pouvez utiliser ces comptes :

#### Administrateur
- **Email** : admin@ecommerce.com
- **Mot de passe** : password

#### Client
- **Email** : client@ecommerce.com
- **Mot de passe** : password

### Configuration du mail (optionnel)

Pour tester l'envoi d'emails en local, utilisez Mailtrap :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username
MAIL_PASSWORD=votre_password
```

## 📖 Utilisation

### Commandes Artisan utiles

```bash
# Vider tous les caches
php artisan optimize:clear

# Réinitialiser la base de données
php artisan migrate:fresh --seed

# Lister toutes les routes
php artisan route:list

# Créer un nouvel utilisateur admin
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password'), 'is_admin' => true])
```

### Compiler les assets pour la production

```bash
npm run build
```

## 📁 Structure du projet

```
tp4-ecommerce/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/           # Authentification
│   │   │   ├── Admin/          # Contrôleurs admin
│   │   │   ├── CartController.php
│   │   │   ├── OrderController.php
│   │   │   ├── PaymentController.php
│   │   │   └── ProductController.php
│   │   ├── Middleware/
│   │   └── Requests/
│   └── Models/
│       ├── User.php
│       ├── Product.php
│       ├── Category.php
│       ├── Cart.php
│       ├── CartItem.php
│       ├── Order.php
│       └── OrderItem.php
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── layouts/
│       ├── components/
│       ├── auth/
│       ├── products/
│       ├── cart/
│       ├── orders/
│       ├── payment/
│       └── admin/
│
├── routes/
│   └── web.php
│
└── public/
    ├── css/
    ├── js/
    └── images/
```

## 👥 Équipe

### Équipe 1 - Gestion Produits & BDD
- **A** - Setup & Architecture Laravel
- **C** - Migrations & Seeders
- **D** - Authentification

### Équipe 2 - Frontend Produits
- **E** - CRUD Produits (Backend)
- **F** - Routes & Controllers Produits (Frontend)

### Équipe 3 - Panier & Paiement
- **G** - Gestion Commandes
- **I** - Système de Paiement
- **L** - Panier (CRUD)

### Équipe 4 - Admin & Intégration
- **K** - Dashboard Admin
- **M** - Intégration Blade & Tests

### Équipe CMS (TP3)
- **H, J** - Travaillent sur le CMS (TP3)

## 🤝 Contribution

### Workflow Git

1. **Créer une branche pour votre fonctionnalité**
   ```bash
   git checkout -b feature/nom-fonctionnalite
   ```

2. **Faire vos modifications et commits**
   ```bash
   git add .
   git commit -m "feat: description de la fonctionnalité"
   ```

3. **Pousser votre branche**
   ```bash
   git push origin feature/nom-fonctionnalite
   ```

4. **Créer une Pull Request sur GitHub**

### Convention de commits

Utilisez les préfixes suivants :
- `feat:` - Nouvelle fonctionnalité
- `fix:` - Correction de bug
- `docs:` - Documentation
- `style:` - Formatage, style
- `refactor:` - Refactorisation de code
- `test:` - Ajout de tests
- `chore:` - Tâches de maintenance

**Exemples** :
```
feat: ajout du système de panier
fix: correction du calcul du total
docs: mise à jour du README
```

## 📚 Documentation

- [Guide d'installation complet](docs/INSTALLATION.md)
- [Documentation API](docs/API.md)
- [Guide de contribution](docs/CONTRIBUTING.md)
- [Architecture du projet](docs/ARCHITECTURE.md)

## 🐛 Résolution des problèmes

### Erreur de migration

```bash
php artisan migrate:fresh --seed
```

### Erreur de permission

```bash
chmod -R 775 storage bootstrap/cache
```

### Assets non chargés

```bash
npm run dev
php artisan storage:link
```

### Vider tous les caches

```bash
php artisan optimize:clear
```

## 📝 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 📞 Support

Pour toute question ou problème :
- Créer une [issue](https://github.com/Delmat237/Shopecart-Web-Project/issues)
- Contacter l'équipe via Discord/Slack

---

**Développé avec ❤️ par l'équipe - Programmation Web 2025**
>>>>>>> upstream/tp/4-laravel-full
