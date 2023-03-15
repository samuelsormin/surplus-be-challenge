<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoryProduct;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(): JsonResponse
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

    public function getById($id): JsonResponse
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

    public function store(Request $request): JsonResponse
    {
        // validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'image' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            $errorMsg = $validator->errors()->all();

            return response()->json([
                "status" => false,
                "message" => implode(" | ", $errorMsg),
                "data" => []
            ], 400);
        }

        // save image
        $path = storage_path('app/public/images/');

        $file = $request->file('image');
        $filename = date('YmdHis') . $file->getClientOriginalName();
        $file->move($path, $filename);

        // insert data to db
        DB::beginTransaction();
        try {
            $productData = [
                'name' => $request->name,
                'description' => $request->description,
                'enable' => 1
            ];

            $product = Product::create($productData);

            $imageData = [
                'name' => $request->name,
                'file' => sprintf('%s%s', $path, $filename),
                'enable' => 1
            ];

            $image = Image::create($imageData);

            $categoryProductData = [
                'product_id' => $product->id,
                'category_id' => $request->category_id
            ];

            CategoryProduct::create($categoryProductData);

            $productImageData = [
                'product_id' => $product->id,
                'image_id' => $image->id
            ];

            ProductImage::create($productImageData);

            DB::commit();

            $category = $product->categoryProduct->category;
            $product->category = $category;

            $image = $product->productImage->image;
            $product->image = $image;

            unset($product->productImage);
            unset($product->categoryProduct);

            return response()->json([
                'status' => true,
                'message' => 'Data created succesfully.',
                'data' => $product
            ], 201);
            //
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Failed insert data', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                "status" => false,
                "message" => 'Failed insert data',
                "data" => []
            ], 500);
        }
    }
}
