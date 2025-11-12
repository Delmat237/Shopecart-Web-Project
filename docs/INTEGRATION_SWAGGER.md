# Guide d'Intégration et de Configuration de Swagger (l5-swagger)

Ce guide décrit l'installation, la configuration et les corrections courantes pour l'intégration de la documentation OpenAPI (Swagger) dans un projet Laravel en utilisant le paquet darkaonline/l5-swagger.

## Étape 1 : Installation du Paquet

Utilisez Composer pour ajouter l5-swagger à votre projet.

```bash
composer require "darkaonline/l5-swagger"
```


## Étape 2 : Publication des Fichiers de Configuration et de Vue

Publiez les fichiers de configuration nécessaires et les vues d'interface de Swagger.

```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

Ceci créera le fichier de configuration principal : config/l5-swagger.php.

## Étape 3 : Définition de l'Hôte API (Correction du Problème de Port 8000)

C'est l'étape la plus critique pour éviter l'erreur Failed to fetch causée par la différence entre le port par défaut de Swagger (80) et le port de Laravel (8000).

### 3.1 Définir la variable d'environnement

Assurez-vous que votre fichier .env contient l'URL complète du serveur :

```bash
APP_URL=http://localhost:8000
```

### 3.2 Utiliser la Constante dans l'Annotation Globale

Modifiez votre contrôleur racine (souvent app/Http/Controllers/Controller.php ou app/Http/Controllers/HomeController.php) pour qu'il inclue l'annotation @OA\Server en utilisant la constante L5_SWAGGER_CONST_HOST :

(Dans app/Http/Controllers/HomeController.php ou équivalent)

```bash
// ... autres annotations de base
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Serveur de l'API Laravel (Port 8000)"
 * )
 * @OA\Tag(
// ...

```

## Étape 4 : Définition des Schémas de Sécurité (Sanctum)

Configurez Swagger pour comprendre comment gérer les jetons d'authentification Bearer (Sanctum).

### 4.1 Annotation Globale de Sécurité

Dans le même fichier que l'étape 3 (app/Http/Controllers/HomeController.php ou équivalent), assurez-vous que les schémas de sécurité sont définis :

```bash
/**
 * @OA\Info(
 * // ... autres infos
 * )
 * @OA\Components(
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="Sanctum",
 * description="Entrez le jeton Bearer (ex: Bearer 1|xxxxxxxx...)"
 * )
 * )
 * @OA\Server(
 * // ... Serveur
 * )
 */
// ...
```

### 4.2 Application de la Sécurité aux Routes Protégées

Pour toute route nécessitant une authentification (comme /user ou /logout), ajoutez l'annotation security :

(Exemple dans AuthController.php pour la route /logout)

```bash
    /**
     * @OA\Post(
     * path="/api/v1/logout", 
     * // ...
     * summary="Déconnexion de l'utilisateur",
     * security={{"bearerAuth": {}}}, // <-- C'EST ICI QUE VOUS L'APPLIQUEZ
     * @OA\Response(
     * // ...
```

## Étape 5 : Ajout des Annotations aux Contrôleurs

Ajoutez les annotations spécifiques à chaque méthode de contrôleur pour documenter les chemins, les paramètres et les réponses.

(Exemple pour la méthode login dans AuthController.php)

```bash
    /**
     * @OA\Post(
     * path="/api/v1/login", 
     * operationId="loginUser",
     * tags={"Auth"},
     * summary="Connexion de l'utilisateur",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email", "password"},
     * @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="secret123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Connexion réussie. Retourne le jeton d'accès.",
     * // ...
     * )
     * )
     */
    public function login(Request $request)
    // ...
```

## Étape 6 : Génération de la Documentation

Chaque fois que vous modifiez les annotations (@OA), vous devez regénérer le fichier JSON/YAML de documentation.

```bash
php artisan l5-swagger:generate
```

## Étape 7 : Accès à l'Interface

La documentation est accessible via l'URL par défaut :

http://localhost:8000/api/documentation


## 🚨 Correction de l'Erreur 422 (Identifiants Invalides)

Si vous recevez toujours l'erreur 422 Unprocessable Content (Invalid credentials) dans Swagger mais que cURL fonctionne :

Vérifiez le JSON :

Dans l'interface Swagger (après avoir cliqué sur Try it out), assurez-vous que le corps de la requête est exactement :

```json
{
  "email": "azangueleonel9@gmail.com",
  "password": "secret123"
}
```

Évitez les espaces blancs accidentels ou les guillemets simples (qui ne sont pas du JSON valide).

Videz le cache (pour éliminer tout problème de configuration ou de routes obsolètes) :

```bash
php artisan optimize:clear
```

Vérifiez les données de la base de données : Confirmez que l'utilisateur existe dans votre base de données avec ce mot de passe exact. Le mot de passe dans la base de données doit être haché (ex: $2y$10$xxxxxxxx...) mais Hash::check doit fonctionner avec la valeur secret123 si elle a été enregistrée correctement.