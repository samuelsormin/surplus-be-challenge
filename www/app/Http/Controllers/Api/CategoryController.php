<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends BaseController
{
    public function __construct()
    {
        //
    }

    /**
     * Show categories data
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();

        if (empty($categories)) return $this->sendError('Data not found.', 200);

        return $this->sendResponse($categories, 'Inquiry data success.');
    }

    /**
     * Show category by id
     * 
     * @param integer $id
     * 
     * @return JsonResponse
     */
    public function getById($id): JsonResponse
    {
        $category = Category::find($id);

        if (empty($category)) return $this->sendError('Data not found.', 200);

        return $this->sendResponse($category, 'Inquiry data success.');
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
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            $errorMsg = implode(' | ', $validator->errors()->all());

            return $this->sendError($errorMsg);
        }

        // save data
        try {
            $categoryData = [
                'name' => $request->name,
                'enable' => 1,
            ];

            $category = Category::create($categoryData);

            return $this->sendResponse($category, 'Data created successfully.', 201);
            //
        } catch (\Throwable $th) {
            Log::error('Failed insert data', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return $this->sendError('Failed insert data', 500);
        }
    }

    /**
     * Update data
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
            'enable' => 'boolean'
        ]);

        if ($validator->fails()) {
            $errorMsg = implode(' | ', $validator->errors()->all());

            return $this->sendError($errorMsg);
        }

        // update data
        try {
            $category = Category::find($id);

            $categoryData = [
                'name' => empty($request->name) ? $category->name : $request->name,
                'enable' => ($request->enable == '') ? $category->enable : $request->enable,
            ];

            $category->update($categoryData);

            return $this->sendResponse($category, 'Data updated successfully.');
            //
        } catch (\Throwable $th) {
            Log::error('Failed update data', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return $this->sendError('Failed update data', 500);
        }
    }

    /**
     * Delete data
     * 
     * @param integer $id
     * 
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            Category::find($id)->delete();

            return $this->sendResponse([], 'Data deleted successfully.');
            //
        } catch (\Throwable $th) {
            Log::error('Failed delete data', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return $this->sendError('Failed delete data', 500);
        }
    }
}
