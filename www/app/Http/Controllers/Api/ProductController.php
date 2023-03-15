<?php

namespace App\Http\Controllers\Api;

use App\Models\CategoryProduct;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    public function __construct()
    {
        //
    }

    /**
     * Show products data
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::all();

        if (empty($products)) return $this->sendError('Data not found.', 200);

        foreach ($products as $product) {
            $category = $product->categoryProduct->category;
            $product->category = $category;

            $image = $product->productImage->image;
            $product->image = $image;

            unset($product->productImage);
            unset($product->categoryProduct);
        }

        return $this->sendResponse($products, 'Inquiry data success.');
    }

    /**
     * Show product by id
     * 
     * @param integer $id
     * 
     * @return JsonResponse
     */
    public function getById($id): JsonResponse
    {
        $product = Product::find($id);

        if (empty($product)) return $this->sendError('Data not found.', 200);

        $category = $product->categoryProduct->category;
        $product->category = $category;

        $image = $product->productImage->image;
        $product->image = $image;

        unset($product->productImage);
        unset($product->categoryProduct);

        return $this->sendResponse($product, 'Inquiry data success.');
    }

    /**
     * Store data to db
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
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
            $errorMsg = implode(' | ', $validator->errors()->all());

            return $this->sendError($errorMsg);
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

            return $this->sendResponse($product, 'Data created successfully.', 201);
            //
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Failed insert data', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return $this->sendError('Failed insert data', 500);
        }
    }

    /**
     * Update data by id
     * 
     * @param Request $request
     * @param integer $id
     * 
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        // validate request
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'description' => 'string',
            'enable' => 'boolean',
            'category_id' => 'integer',
            'image' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            $errorMsg = implode(' | ', $validator->errors()->all());

            return $this->sendError($errorMsg);
        }

        // inquiry product data
        $product = Product::find($id);

        if (empty($product)) return $this->sendError('Data not found.', 200);

        // update data to db
        DB::beginTransaction();

        try {
            $productData = [
                'name' => empty($request->name) ? $product->name : $request->name,
                'description' => empty($request->description) ? $product->description : $request->description,
                'enable' => ($request->enable == '') ? $product->enable : $request->enable,
            ];

            if (!empty($productData)) $product->update($productData);

            if ($request->hasFile('image')) {
                // save image
                $path = storage_path('app/public/images/');

                $file = $request->file('image');
                $filename = date('YmdHis') . $file->getClientOriginalName();
                $file->move($path, $filename);

                $imageData = [
                    'name' => $request->name,
                    'file' => sprintf('%s%s', $path, $filename),
                    'enable' => 1
                ];

                $image = Image::find($product->productImage->image->id);

                $image->update($imageData);
            }

            if (!empty($request->category_id)) {
                $categoryProductData = [
                    'category_id' => $request->category_id
                ];

                $categoryProduct = CategoryProduct::find($product->categoryProduct->id);

                $categoryProduct->update($categoryProductData);
            }

            DB::commit();

            $product = Product::find($id);

            $category = $product->categoryProduct->category;
            $product->category = $category;

            $image = $product->productImage->image;
            $product->image = $image;

            unset($product->productImage);
            unset($product->categoryProduct);

            return $this->sendResponse($product, 'Data updated successfully.');
            //
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Failed update data', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return $this->sendError('Failed update data', 500);
        }
    }

    /**
     * Delete data from db
     * 
     * @param integer $id
     * 
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::find($id);

            CategoryProduct::find($product->categoryProduct->id)->delete();

            Image::find($product->productImage->image->id)->delete();

            ProductImage::find($product->productImage->id)->delete();

            $product->delete();

            DB::commit();

            return $this->sendResponse([], 'Data deleted successfully.');
            //
        } catch (\Throwable $th) {
            DB::rollBack();
            
            Log::error('Failed delete data', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return $this->sendError('Failed delete data', 500);
        }
    }
}
