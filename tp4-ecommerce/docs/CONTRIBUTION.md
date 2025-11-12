# Guide de Contribution - TP4 E-commerce

Merci de contribuer au projet ! Ce guide vous aidera à travailler efficacement en équipe.

## 🔄 Workflow Git

### 1. Récupérer les dernières modifications

Avant de commencer à travailler, assurez-vous d'avoir la dernière version :
```bash
git checkout tp/4-laravel-full
git pull origin tp/4-laravel-full
```

### 2. Créer une branche pour votre tâche
```bash
git checkout -b feature/nom-de-votre-fonctionnalite
```

**Convention de nommage des branches** :
- `feature/` - Nouvelle fonctionnalité (ex: `feature/cart-system`)
- `fix/` - Correction de bug (ex: `fix/payment-validation`)
- `docs/` - Documentation (ex: `docs/readme-update`)
- `refactor/` - Refactorisation (ex: `refactor/product-controller`)

### 3. Faire vos modifications

- Travaillez sur votre tâche assignée
- Testez votre code avant de commiter
- Écrivez du code clair et commenté

### 4. Commits réguliers

Faites des commits atomiques avec des messages clairs :
```bash
git add .
git commit -m "feat: ajout du système de panier"
```

**Convention de commits** :
- `feat:` - Nouvelle fonctionnalité
- `fix:` - Correction de bug
- `docs:` - Documentation
- `style:` - Formatage (pas de changement de logique)
- `refactor:` - Refactorisation
- `test:` - Ajout/modification de tests
- `chore:` - Tâches de maintenance

**Exemples** :
```
feat: ajout de la validation du formulaire de paiement
fix: correction du calcul du total dans le panier
docs: mise à jour du README avec les instructions d'installation
refactor: amélioration de la structure du ProductController
```

### 5. Pousser votre branche
```bash
git push origin feature/nom-de-votre-fonctionnalite
```

### 6. Créer une Pull Request

1. Allez sur GitHub
2. Cliquez sur "New Pull Request"
3. Sélectionnez votre branche
4. Décrivez vos modifications
5. Assignez un reviewer
6. Attendez l'approbation avant de merge

## 📝 Standards de code

### PHP / Laravel

- Respectez le style PSR-12
- Utilisez le type hinting
- Documentez les méthodes complexes
- Nommage en camelCase pour les variables et méthodes
- Nommage en PascalCase pour les classes

**Exemple** :
```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::paginate(12);
        return view('products.index', compact('products'));
    }
}
```

### Blade

- Indentation de 4 espaces
- Utilisez `@foreach`, `@if`, etc. au lieu de PHP pur
- Échappez toujours les variables : `{{ $var }}`

### JavaScript

- Utilisez `const` et `let`, pas `var`
- Nommage en camelCase
- Commentez le code complexe

### CSS

- Utilisez des classes descriptives
- Organisez par composants
- Évitez les `!important`

## 🧪 Tests

Avant de pousser votre code, testez :

1. **Fonctionnalités** : Testez manuellement votre feature
2. **Navigation** : Vérifiez que tous les liens fonctionnent
3. **Responsive** : Testez sur mobile/tablette/desktop
4. **Navigateurs** : Testez sur Chrome, Firefox, Safari

## 🚫 À éviter

- ❌ Pusher directement sur `main`
- ❌ Commiter des fichiers `.env`
- ❌ Commiter des fichiers de logs
- ❌ Commiter `node_modules` ou `vendor`
- ❌ Laisser du code commenté inutile
- ❌ Laisser des `console.log()` ou `dd()` de debug

## 🔍 Code Review

Quand vous reviewez une PR :

- ✅ Vérifiez que le code suit les standards
- ✅ Testez la fonctionnalité localement
- ✅ Vérifiez qu'il n'y a pas de régression
- ✅ Proposez des améliorations constructives
- ✅ Approuvez ou demandez des modifications

## 📞 Communication

- Utilisez Discord/Slack pour les questions rapides
- Créez des issues pour les bugs ou features
- Documentez vos décisions importantes
- Faites un daily stand-up (15min) pour synchroniser

## 🎯 Checklist avant de merge

- [ ] Code testé et fonctionnel
- [ ] Pas de conflits avec `main`
- [ ] Commits clairs et atomiques
- [ ] Code review approuvé
- [ ] Documentation à jour si nécessaire

## 🆘 Besoin d'aide ?

- Consultez la documentation Laravel : https://laravel.com/docs
- Demandez à l'équipe sur Discord
- Créez une issue sur GitHub
- Contactez le chef de projet

---

**Merci de respecter ces guidelines pour un projet de qualité ! 🚀**
