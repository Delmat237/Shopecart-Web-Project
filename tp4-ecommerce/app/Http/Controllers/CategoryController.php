<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="API Endpoints for Categories"
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Get list of categories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = Category::where('is_visible', true)
            ->orderBy('position')
            ->get();

        return new CategoryCollection($categories);
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     summary="Get category details with products",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="category", ref="#/components/schemas/Category"),
     *             @OA\Property(
     *                 property="products",
     *                 type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *                 @OA\Property(property="links", type="object"),
     *                 @OA\Property(property="meta", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function show(Category $category)
    {
        if (!$category->is_visible) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $products = $category->products()
            ->where('is_visible', true)
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json([
            'category' => new CategoryResource($category),
            'products' => new \App\Http\Resources\ProductCollection($products)
        ]);
    }
}