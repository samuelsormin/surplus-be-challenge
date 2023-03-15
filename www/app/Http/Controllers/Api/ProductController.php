<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        $products = Product::all();

        if (empty($products)) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => []
            ], 200);
        }

        foreach ($products as $product) {
            $category = $product->categoryProduct->category;
            $product->category = $category;

            $image = $product->productImage->image;
            $product->image = $image;

            unset($product->productImage);
            unset($product->categoryProduct);
        }

        return response()->json([
            'status' => true,
            'message' => 'Inquiry data success',
            'data' => $products
        ], 200);
    }

    public function getById($id)
    {
        $product = Product::find($id);

        if (empty($product)) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => []
            ], 200);
        }

        $category = $product->categoryProduct->category;
        $product->category = $category;

        $image = $product->productImage->image;
        $product->image = $image;

        unset($product->productImage);
        unset($product->categoryProduct);

        return response()->json([
            'status' => true,
            'message' => 'Inquiry data success',
            'data' => $product
        ], 200);
    }
}
