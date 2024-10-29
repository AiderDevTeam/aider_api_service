<?php

namespace App\Http\Controllers;

use App\Actions\Product\AddProductImageAction;
use App\Actions\Product\DeleteProductAction;
use App\Actions\Product\DeleteProductImageAction;
use App\Actions\Product\DeleteProductPriceAction;
use App\Actions\Product\FilterProductsAction;
use App\Actions\Product\GetProductsAction;
use App\Actions\Product\GetProductsByCategoryAction;
use App\Actions\Product\GetProductsBySubCategoryAction;
use App\Actions\Product\GetProductsByVendorAction;
use App\Actions\Product\GetProductsByTagAction;
use App\Actions\Product\GetSimilarProductsAction;
use App\Actions\Product\GetSubCategoryProductsAction;
use App\Actions\Product\GetUserProductsAction;
use App\Actions\Product\ProductDetailsUpdateAction;
use App\Actions\Product\RejectProductAction;
use App\Actions\Product\StoreProductAction;
use App\Actions\WebActions\WebGetProductsByVendorAction;
use App\Http\Requests\AddProductImageRequest;
use App\Http\Requests\DeleteProductImageRequest;
use App\Http\Requests\ProductDetailsUpdateRequest;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\ProductPriceDeleteRequest;
use App\Http\Requests\RejectProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\WebGetProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductMediaRequest;
use App\Actions\Product\StoreProductMediaAction;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetProductsAction $action): JsonResponse
    {
        return $action->handle();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, StoreProductRequest $productRequest, StoreProductAction $action): JsonResponse
    {
        return $action->handle($request, $productRequest);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return successfulJsonResponse(data: new ProductResource($product->load(['photos', 'prices', 'user.statistics'])));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductDetailsUpdateRequest $request, Product $product, ProductDetailsUpdateAction $action): JsonResponse
    {
        return $action->handle($request, $product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteProductAction $action, Product $product): JsonResponse
    {
        return $action->handle($product);
    }

    public function getSubCategories(Category $category, GetSubCategoryProductsAction $action): JsonResponse
    {
        return $action->handle($category);
    }

    public function rejectProduct(RejectProductAction $action, Product $product, RejectProductRequest $request): JsonResponse
    {
        return $action->handle($request, $product);
    }

    public function getProductsByCategory(Request $request, Category $category, GetProductsByCategoryAction $action): JsonResponse
    {
        return $action->handle($request, $category);
    }

    public function getProductsByVendor(Request $request, GetProductsByVendorAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function webGetProductsByVendor(WebGetProductsRequest $request, WebGetProductsByVendorAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function getProductsBySubCategory(SubCategory $subCategory, GetProductsBySubCategoryAction $action): JsonResponse
    {
        return $action->handle($subCategory);
    }

    public function getProductsByTag(string $tag, GetProductsByTagAction $action): JsonResponse
    {
        return $action->handle($tag);
    }

    public function addProductImage(Product $product, AddProductImageRequest $request, AddProductImageAction $action): JsonResponse
    {
        return $action->handle($product, $request);
    }

    public function deleteProductImage(Product $product, DeleteProductImageRequest $request, DeleteProductImageAction $action): JsonResponse
    {
        return $action->handle($product, $request);
    }

    public function similarProducts(Request $request, Product $product, GetSimilarProductsAction $action): JsonResponse
    {
        return $action->handle($request, $product);
    }

    public function storeProductMedia(Product $product, StoreProductMediaRequest $request, StoreProductMediaAction $action): JsonResponse
    {
        return $action->handle($product, $request);
    }

    public function deletePrice(Product $product, ProductPriceDeleteRequest $request, DeleteProductPriceAction $action): JsonResponse
    {
        return $action->handle($product, $request);
    }

    public function getUserProducts(Request $request, User $user, GetUserProductsAction $action): JsonResponse
    {
        return $action->handle($request, $user);
    }

    public function filterProducts(ProductFilterRequest $request, FilterProductsAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
