# Répartition détaillée des tâches - TP4 Laravel

## ⚠️ Contraintes respectées
- **H et J** : Travaillent sur le CMS (TP3)


---

## 📋 Vue d'ensemble 

| Membre | Rôle Principal | Tâche Principale |
|--------|---------------|------------------|
| **A** | Setup & Architecture | Installation Laravel + Configuration Base |
| **C** | Base de données | Migrations & Seeders |
| **G** | Authentification | Système Login/Register |
| **D** | Gestion Produits | CRUD Produits (Backend) |
| **E** | Gestion Produits | Routes & Controllers Produits |
| **L** | Gestion Commandes | Tables orders & order_items |
| **I** | 💳 **Paiement** | Système de paiement & validation |
| **B** | Dashboard Admin | Interface de suivi commandes |
| **L** | 🛒 **Panier (CRUD)** | Système panier complet |
| **F** | serice de notification mail | Configuration d'envoie mail |
| **ALL** | Intégration & Tests | Blade templates + Tests finaux |

---

## 🔧 Répartition détaillée par membre

### ** - Setup & Architecture Laravel**
**Responsabilité** : Fondations du projet

#### Tâches :
1. **Installation & Configuration**
   - Installer Laravel via Composer
   - Configurer `.env` (base de données, APP_KEY, etc.)
   - Configurer la connexion à la base de données
   
2. **Structure du projet**
   - Organiser l'architecture MVC
   - Créer les dossiers nécessaires (resources/views, routes, etc.)
   - Mettre en place le système de routing de base

3. **Configuration initiale**
   - Configurer le middleware
   - Mettre en place la gestion des sessions
   - Préparer l'environnement de développement

**Livrables** :
- Projet Laravel fonctionnel et configuré
- Documentation d'installation dans README
- Fichier `.env.example` complété

---

### ** - Base de données (Migrations & Seeders)**
**Responsabilité** : Structure et données de test

#### Tâches :
1. **Migrations des tables**
   ```php
   - users (id, name, email, password, role, timestamps)
   - products (id, name, description, price, stock, image, category_id, timestamps)
   - categories (id, name, description, timestamps)
   - orders (id, user_id, total, status, timestamps)
   - order_items (id, order_id, product_id, quantity, price, timestamps)
   - carts (id, user_id, timestamps)
   - cart_items (id, cart_id, product_id, quantity, timestamps)
   ```

2. **Relations entre tables**
   - Définir les clés étrangères
   - Mettre en place les contraintes (CASCADE, etc.)

3. **Seeders (données fictives)**
   - Créer 20-30 produits fictifs
   - Créer 5-10 utilisateurs de test
   - Créer des catégories de produits
   - Peupler quelques commandes test

**Livrables** :
- Fichiers de migration complets
- Seeders fonctionnels
- Documentation du schéma de BDD

---

### ** - Authentification (Login/Register)**
**Responsabilité** : Système d'authentification complet

#### Tâches :
1. **Backend Authentification**
   - Controller `AuthController` avec méthodes :
     - `showLoginForm()` / `login()`
     - `showRegisterForm()` / `register()`
     - `logout()`
   - Validation des données (email valide, mot de passe fort)
   - Hashage des mots de passe
   - Gestion des sessions utilisateur

2. **Routes d'authentification**
   ```php
   GET  /login
   POST /login
   GET  /register
   POST /register
   POST /logout
   ```

3. **Middleware de protection**
   - Créer middleware `auth` pour protéger les routes
   - Redirection vers login si non authentifié
   - Gestion des rôles (admin/client)

4. **Blade Templates**
   - Vue `login.blade.php`
   - Vue `register.blade.php`
   - Messages d'erreur et de succès

**Livrables** :
- Système d'authentification fonctionnel
- Formulaires avec validation
- Protection des routes sensibles

---

### ** - Gestion Produits (CRUD Backend)**
**Responsabilité** : Logique métier des produits

#### Tâches :
1. **Model Product**
   - Définir les attributs fillables
   - Relations avec categories et order_items

2. **ProductController - Méthodes CRUD**
   ```php
   - index()      // Liste tous les produits
   - show($id)    // Affiche un produit
   - create()     // Formulaire ajout
   - store()      // Enregistrer produit
   - edit($id)    // Formulaire édition
   - update($id)  // Modifier produit
   - destroy($id) // Supprimer produit
   ```

3. **Validation des données**
   - Form Request pour création/modification
   - Règles de validation (nom requis, prix > 0, stock >= 0)
   - Messages d'erreur personnalisés

4. **Upload d'images**
   - Gestion upload image produit
   - Stockage dans `/public/storage/products`
   - Validation format (jpg, png, max 2MB)

**Livrables** :
- CRUD produits complet (backend)
- Validation robuste
- Gestion des images

---

### ** - Routes & Controllers Produits (Frontend)**
**Responsabilité** : Interface publique des produits

#### Tâches :
1. **Routes publiques**
   ```php
   GET /products           // Liste produits
   GET /products/{id}      // Détail produit
   GET /products/category/{id} // Produits par catégorie
   ```

2. **Vues Blade**
   - `products/index.blade.php` : Grille de produits
   - `products/show.blade.php` : Fiche produit détaillée
   - Pagination des produits (15 par page)
   - Filtres par catégorie

3. **Recherche & Filtres**
   - Barre de recherche (nom, description)
   - Tri (prix croissant/décroissant, nouveautés)
   - Filtrage par prix (min/max)

4. **Intégration avec le panier**
   - Bouton "Ajouter au panier" sur chaque produit
   - Vérification stock disponible
   - Messages de confirmation

**Livrables** :
- Pages produits publiques fonctionnelles
- Système de recherche/filtres
- Interface responsive

---

### ** - Gestion Commandes (Orders & Order_items)**
**Responsabilité** : Système de commandes

#### Tâches :
1. **Models & Relations**
   - Model `Order` avec relation `user` et `order_items`
   - Model `OrderItem` avec relation `order` et `product`

2. **OrderController**
   ```php
   - index()        // Liste commandes utilisateur
   - show($id)      // Détail commande
   - store()        // Créer commande depuis panier
   - updateStatus() // Modifier statut (admin)
   ```

3. **Logique de création commande**
   - Récupérer le panier actuel
   - Créer un `Order` avec total calculé
   - Créer les `OrderItem` associés
   - Vider le panier après validation
   - Décrémenter le stock produits

4. **Statuts de commande**
   - En attente, Payée, En préparation, Expédiée, Livrée, Annulée
   - Historique des statuts

**Livrables** :
- Système de commandes complet
- Gestion des statuts
- Historique utilisateur

---

### ** - 💳 Système de Paiement**
**Responsabilité** : Processus de paiement complet

#### Tâches :
1. **PaymentController**
   ```php
   - showCheckout()      // Page récapitulatif
   - processPayment()    // Traitement paiement
   - confirmPayment()    // Confirmation
   - cancel()            // Annulation
   ```

2. **Page de paiement**
   - Vue `checkout.blade.php` :
     - Récapitulatif panier
     - Formulaire adresse livraison
     - Choix mode de paiement (CB, PayPal simulé)
     - Calcul frais de port
     - Total final

3. **Validation & Sécurité**
   - Vérification disponibilité stock avant paiement
   - Validation formulaire (adresse complète, email)
   - Protection CSRF
   - Vérification montant côté serveur

4. **Simulation paiement** (pas d'API réelle)
   - Mock de paiement par carte bancaire
   - Numéros de test acceptés (4242 4242 4242 4242)
   - Messages de succès/erreur
   - Envoi email confirmation (simulé ou avec Mailtrap)

5. **Après paiement**
   - Créer la commande (collaboration avec G)
   - Mettre à jour le statut en "Payée"
   - Vider le panier
   - Redirection vers page confirmation

**Livrables** :
- Interface de paiement complète
- Validation robuste
- Simulation de paiement fonctionnelle
- Page de confirmation

---

### ** - Dashboard Admin (Suivi commandes)**
**Responsabilité** : Interface administrateur

#### Tâches :
1. **DashboardController**
   ```php
   - index()              // Vue d'ensemble
   - orders()             // Liste toutes commandes
   - orderDetails($id)    // Détail commande admin
   - updateOrderStatus()  // Changer statut
   ```

2. **Vue Dashboard** (`admin/dashboard.blade.php`)
   - Statistiques :
     - Nombre total de commandes
     - Chiffre d'affaires
     - Commandes du jour
     - Produits en rupture de stock
   - Graphiques simples (Chart.js ou similaire)

3. **Gestion des commandes admin**
   - Liste toutes les commandes (pagination)
   - Filtres (date, statut, client)
   - Détail commande avec :
     - Informations client
     - Liste produits commandés
     - Statut actuel
     - Modifier statut (dropdown)

4. **Protection des routes admin**
   - Middleware `admin` (vérifier role)
   - Redirection si non autorisé

**Livrables** :
- Dashboard administrateur fonctionnel
- Gestion complète des commandes
- Statistiques basiques

---

### ** - 🛒 Panier (CRUD complet)**
**Responsabilité** : Système de panier

#### Tâches :
1. **Models Cart & CartItem**
   - Relations avec User et Product
   - Méthodes utilitaires (getTotalPrice(), getItemCount())

2. **CartController**
   ```php
   - index()                    // Afficher panier
   - add(Request $request)      // Ajouter produit
   - update($id, Request $request) // Modifier quantité
   - remove($id)                // Supprimer article
   - clear()                    // Vider panier
   ```

3. **Logique métier**
   - Vérifier stock avant ajout
   - Calculer total automatiquement
   - Gérer quantités (min: 1, max: stock)
   - Empêcher ajout si stock insuffisant
   - Détecter changement de prix produit

4. **Vue Panier** (`cart/index.blade.php`)
   - Liste articles avec :
     - Image produit
     - Nom et prix
     - Quantité modifiable (+ / -)
     - Bouton supprimer
   - Sous-total par ligne
   - Total général
   - Bouton "Vider le panier"
   - Bouton "Passer commande" → vers paiement (I)

5. **API AJAX (optionnel mais recommandé)**
   - Ajouter/supprimer sans recharger page
   - Mise à jour quantité en temps réel
   - Notification toast (succès/erreur)

6. **Persistance**
   - Panier lié à l'utilisateur (table carts)
   - Persistance après déconnexion
   - Merge panier session → BDD au login

**Livrables** :
- Système de panier complet et robuste
- Interface utilisateur intuitive
- Calculs automatiques
- Gestion erreurs (stock, etc.)

---

### ** - Intégration Blade & Tests**
**Responsabilité** : Cohésion et qualité finale

#### Tâches :
1. **Templates Blade principaux**
   - Layout principal (`layouts/app.blade.php`) :
     - Header avec navigation
     - Menu (Accueil, Produits, Panier, Commandes)
     - Affichage utilisateur connecté
     - Footer
   - Composants réutilisables (@include, @component)

2. **Intégration CSS/JS**
   - Intégrer les assets du TP1 (CSS existant)
   - Utiliser Laravel Mix ou Vite
   - S'assurer du responsive design
   - Cohérence charte graphique

3. **Tests fonctionnels**
   - Tester tous les flux :
     - Inscription → Login
     - Navigation produits → Ajout panier
     - Panier → Checkout → Paiement → Commande
     - Dashboard admin
   - Tests multi-navigateurs (Chrome, Firefox, Safari)
   - Tests responsive (mobile, tablette, desktop)

4. **Messages flash & UX**
   - Notifications de succès/erreur
   - Messages de confirmation
   - Gestion des erreurs 404/500

5. **Documentation finale**
   - Mettre à jour README :
     - Installation détaillée
     - Configuration BDD
     - Lancement serveur
     - Comptes de test (admin/client)
   - Commentaires code si nécessaire

6. **Préparation démo**
   - Scénario de démonstration
   - Données de test cohérentes
   - Vérification fonctionnement global

**Livrables** :
- Application entièrement intégrée
- Tests complets effectués
- Documentation à jour
- Démo prête

---

## 🔄 Dépendances entre membres

```
 (Setup) 
  ↓
 (BDD) 
  ↓
├─→  (Auth) ────────────────┐
├─→  (Produits) ────────┤
├─→  (Panier) ──────────────┤
│     ↓                       │
│    (Paiement) ─────────────┤
│     ↓                       │
├─→  (Commandes) ────────────┤
└─→  (Dashboard Admin) ──────┤
                              ↓
                          (Intégration)
```

---

## 📊 Checklist finale avant livraison

### Backend
- [ ] Toutes les migrations fonctionnent
- [ ] Seeders peuplent la BDD correctement
- [ ] Auth (login/register/logout) fonctionnel
- [ ] CRUD produits complet
- [ ] Panier : ajout/modification/suppression
- [ ] Système de paiement simulé
- [ ] Création de commandes
- [ ] Dashboard admin opérationnel

### Frontend
- [ ] Toutes les pages Blade rendues
- [ ] Navigation fluide
- [ ] Design responsive
- [ ] Messages d'erreur/succès affichés
- [ ] Charte graphique respectée

### Git
- [ ] Commits réguliers de chaque membre
- [ ] Branches bien organisées
- [ ] README complet
- [ ] .gitignore correctement configuré

### Tests
- [ ] Flux complet testé (inscription → achat)
- [ ] Tests multi-navigateurs
- [ ] Pas d'erreurs 500 ou bugs bloquants

---

## 💡 Conseils pour la collaboration

1. **Communication** : Canal Discord/Slack actif
2. **Daily stand-ups** : Point quotidien de 15min
3. **Code reviews** : Relecture croisée des PR
4. **Commits atomiques** : 1 commit = 1 fonctionnalité
5. **Messages commits clairs** : `feat:`, `fix:`, `docs:`
6. **Tests fréquents** : Ne pas attendre la fin pour tester

---

## 🎯 Objectif final

Une **application e-commerce Laravel complète** avec :
- ✅ Authentification sécurisée
- ✅ Catalogue produits dynamique
- ✅ Panier fonctionnel persistant
- ✅ Système de paiement simulé
- ✅ Gestion des commandes
- ✅ Dashboard administrateur
- ✅ Interface responsive et élégante

**Bonne chance à toute l'équipe ! 🚀**