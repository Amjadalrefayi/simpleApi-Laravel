<?php

namespace App\Http\Controllers\API;


use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\ProductResources;

/**
 * @group Product Management
 *
 * APIs to manage the products
 */
class ProductController extends BaseController
{
    /**
     * Display a listing of Products.
     *
     * @urlParam pages int required for paginate.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pages){
        $products = Product::paginate($pages);
        return $this->sendResponse(ProductResources::collection($products), [
            'current_page' => $products->currentPage(),
            'nextPageUrl' => $products->nextPageUrl(),
            'previousPageUrl' => $products->previousPageUrl(),
        ]);
    }

    /**
     * Store a newly product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Please validate error', $validator->errors());
        }
        $image = $request->image;
        $newImage = time() . $image->getClientOriginalName();
        $image->move('uploads/products', $newImage);
        $input = $request->all();
        $input['user_id'] = Auth::id();
        $input['image'] = 'uploads/products/' . $newImage;
        $product = Product::create($input);

        return $this->sendResponse(new ProductResources($product), 'Products Store successfully');
    }

    /**
     * Display a specified product.
     *
     * @urlParam id int required product ID.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $product = Product::find($id);

        if (!$product) {
            return $this->sendError('Poduct Not Found', 404);
        }
        return $this->sendResponse(new ProductResources($product), 'Specific Product');
    }

    /**
     * Update a specified product in storage.
     *
     * @urlParam id int required product ID.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $product = Product::find($id);

        if (!$product) {
            return $this->sendError('Poduct Not Found', 404);
        }
        if (Auth::id() != $product->user_id) {
            return $this->sendError('Not Valid to update', 'This product for another user');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Please validate error', $validator->errors());
        }

        $image = $request->image;
        $newImage = time() . $image->getClientOriginalName();
        $image->move('uploads/products', $newImage);

        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = 'uploads/products/' . $newImage;
        $product->update();

        return $this->sendResponse(new ProductResources($product), 'Product Updated successfully');
    }

    /**
     * SoftDelete a specified product from storage.
     *
     * @urlParam id int required product ID.
     *
     * @return \Illuminate\Http\Response
     */
    public function softDelete($id){
        $product = Product::find($id);

        if (!$product) {
            return $this->sendError('Poduct Not Found', 404);
        }
        if (Auth::id() != $product->user_id) {
            return $this->sendError('Not Valid to delete', 'This product for another user');
        }
        $product->delete();

        return $this->sendResponse('', 'Product SoftDeleted successfully');
    }

    /**
     * Restore a specified product from storage.
     *
     * @urlParam id int required product ID.
     *
     * @return \Illuminate\Http\Response
     */
    public function restore($id){
        $product = Product::onlyTrashed()->where('id', $id)->first();

        if (!$product) {
            return $this->sendError('Poduct Not Found', 404);
        }
        if (Auth::id() != $product->user_id) {
            return $this->sendError('Not Valid to delete', 'This product for another user');
        }
        $product->restore();
        return $this->sendResponse('', 'Product Restored successfully');
    }

    /**
     * ForceDelete a specified product from storage.
     *
     * @urlParam id int required product ID.
     *
     * @return \Illuminate\Http\Response
     */

    public function forceDelete($id){

        $product = Product::onlyTrashed()->where('id', $id)->first();

        if (!$product) {
            return $this->sendError('Poduct Not Found', 404);
        }
        if (Auth::id() != $product->user_id) {
            return $this->sendError('Not Valid to delete', 'This product for another user');
        }

        $product->forceDelete();
        return $this->sendResponse('', 'Product ForceDeleted successfully');
    }
}
