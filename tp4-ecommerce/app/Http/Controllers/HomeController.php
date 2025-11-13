<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Shopecart E-commerce API Documentation",
 * description="Documentation des points d'accès (endpoints) de l'API Shopecart.",
 * @OA\Contact(
 * email="contact@shopecart.com"
 * )
 * )
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Serveur de développement"
 * )
 * @OA\Tag(
 * name="Home",
 * description="Points d'accès pour la page d'accueil et les informations générales."
 * )
 */
class HomeController extends Controller
{
    /**
     * @OA\Get(
     * path="/",
     * operationId="getFeaturedProducts",
     * tags={"Home"},
     * summary="Récupérer les produits phares pour la page d'accueil",
     * description="Retourne une liste limitée (max 8) des produits visibles, en stock et marqués comme 'featured'.",
     * @OA\Response(
     * response=200,
     * description="Opération réussie",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Product")
     * )
     * )
     * )
     */
    
    public function index()
    {
        $featuredProducts = Product::with('category')
            ->where('is_visible', true)
            ->where('is_featured', true)
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $categories = Category::where('is_visible', true)
            ->orderBy('position')
            ->take(6)
            ->get();

        return view('pages.home', compact('featuredProducts', 'categories'));
    }

    /**
     * Afficher la page À propos
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Afficher la page Contact
     */
    public function contact()
    {
        return view('pages.contact');
    }
}
