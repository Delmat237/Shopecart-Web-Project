<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::where('is_visible', true)
            ->orderBy('position')
            ->get();

        return new CategoryCollection($categories);
    }

    /**
     * Display the specified category with its products.
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