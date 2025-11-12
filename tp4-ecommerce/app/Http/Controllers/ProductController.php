<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for Products"
 * )
 * 
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="iPhone 15 Pro"),
 *     @OA\Property(property="slug", type="string", example="iphone-15-pro"),
 *     @OA\Property(property="description", type="string", example="Latest iPhone with advanced features"),
 *     @OA\Property(property="price", type="number", format="float", example=999.99),
 *     @OA\Property(property="compare_price", type="number", format="float", example=1099.99),
 *     @OA\Property(property="stock", type="integer", example=50),
 *     @OA\Property(property="sku", type="string", example="IP15PRO256"),
 *     @OA\Property(property="is_visible", type="boolean", example=true),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="image", type="string", example="products/iphone15.jpg"),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(property="slug", type="string", example="electronics"),
 *     @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
 *     @OA\Property(property="is_visible", type="boolean", example=true),
 *     @OA\Property(property="position", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Cart",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="total", type="number", format="float", example=199.98),
 *     @OA\Property(property="items_count", type="integer", example=2),
 *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/CartItem"))
 * )
 * 
 * @OA\Schema(
 *     schema="CartItem",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="unit_price", type="number", format="float", example=99.99),
 *     @OA\Property(property="total", type="number", format="float", example=199.98),
 *     @OA\Property(property="product", ref="#/components/schemas/Product")
 * )
 * 
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_number", type="string", example="ORD-20231215-ABC123"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="subtotal", type="number", format="float", example=199.98),
 *     @OA\Property(property="shipping", type="number", format="float", example=10.00),
 *     @OA\Property(property="tax", type="number", format="float", example=20.00),
 *     @OA\Property(property="total", type="number", format="float", example=229.98),
 *     @OA\Property(property="customer_email", type="string", example="john@example.com"),
 *     @OA\Property(property="customer_first_name", type="string", example="John"),
 *     @OA\Property(property="customer_last_name", type="string", example="Doe"),
 *     @OA\Property(property="shipping_address", type="string", example="123 Main St"),
 *     @OA\Property(property="shipping_city", type="string", example="New York"),
 *     @OA\Property(property="shipping_zipcode", type="string", example="10001"),
 *     @OA\Property(property="shipping_country", type="string", example="USA"),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 

 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Get list of products",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search products by name or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Minimum price filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Maximum price filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort products (newest, price_asc, price_desc, name)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"newest", "price_asc", "price_desc", "name"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=12)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('is_visible', true)
            ->where('stock', '>', 0);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate($request->get('per_page', 12));

        return new ProductCollection($products);
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get product details",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="product", ref="#/components/schemas/Product"),
     *             @OA\Property(property="related_products", type="array", @OA\Items(ref="#/components/schemas/Product"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show(Product $product)
    {
        if (!$product->is_visible) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_visible', true)
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return response()->json([
            'product' => new ProductResource($product),
            'related_products' => ProductResource::collection($relatedProducts)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/products/featured",
     *     summary="Get featured products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     )
     * )
     */
    public function featured()
    {
        $products = Product::with('category')
            ->where('is_visible', true)
            ->where('is_featured', true)
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return ProductResource::collection($products);
    }
}