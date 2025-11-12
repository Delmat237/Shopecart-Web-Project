<?php

namespace App\Http\Controllers\Swagger;

/**
 * @OA\Info(
 *     title="Shopecart E-commerce API",
 *     version="1.0.0",
 *     description="API Backend Laravel pour le projet Shopecart - TP4"
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Serveur de développement local"
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="Gestion des produits du catalogue"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum",
 *     description="Token d'authentification Sanctum (à obtenir via /api/v1/login)"
 * )
 */
class SwaggerConfig
{
    // Ce fichier contient uniquement les annotations OpenAPI de base
}
