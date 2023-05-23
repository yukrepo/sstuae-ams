<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductDetails;
use App\Models\Categories;
class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Products::select('products.*', 'categories.name as category_name')
                        ->join('categories', 'categories.id', '=', 'products.category_id')
                        ->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
             'name' => 'required|string|max:190',
             'image' => 'nullable',
             'model_no' => 'required|string|max:50|unique:products',
             'description' => 'nullable',
             'category_id' => 'required'
        ]);

        if($request->image){
            $photo = strtolower($request['model_no']).'-'.time().'.'.explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
            \Image::make($request->image)->save(public_path('images/products/').$photo)->resize(300,300);
        }
        else{
            $photo = '';
        }

        $product =  Products::create([
                        'name' => $request['name'],
                        'description' => $request['description'],
                        'model_no' => strtoupper($request['model_no']),
                        'category_id' => $request['category_id'],
                        'image' => $photo
        ]);

        return ['message' => 'product created'];
    }

    public function show($id)
    {
        //$product = Products::findOrFail($id);
        $product = Products::select('products.*', 'categories.name as category_name')
                        ->join('categories', 'categories.id', '=', 'products.category_id')
                        ->where('products.id', $id)->get();
        return $product;
    }

    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:190',
            'image' => 'nullable',
            'model_no' => 'required|string|max:50|unique:products,model_no,'.$product->id,
            'description' => 'nullable',
            'category_id' => 'required'
       ]);
       if($request->image != $product->image){
           $photo = strtolower($request['model_no']).'-'.time().'.'.explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
           \Image::make($request->image)->save(public_path('images/products/').$photo)->resize(300,300);
       }
       else{
           $photo = $product->image;
       }

       $product->update([
                       'name' => $request['name'],
                       'description' => $request['description'],
                       'model_no' => strtoupper($request['model_no']),
                       'category_id' => $request['category_id'],
                       'image' => $photo
       ]);

       return ['message' => 'product details updated'];
    }

    public function destroy($id)
    {
        $customer = Products::findOrFail($id);
        $customer->delete();
        return ['message' => 'record deleted'];
    }

    public function listItems()
    {
        $products = Products::select('products.*', 'categories.name as category_name')
           ->join('categories', 'categories.id', '=', 'products.category_id')
           ->get();
        $result = [];
        foreach($products as $product):
            $result[] =['value' => $product->id, 'text' => $product->name.' - '.$product->model_no.' - IN '.$product->category_name];
        endforeach;
        return $result;
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $product = Products::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")->orwhere('email','LIKE',"%$search%");
            })->withCount('sales')->paginate(10);
        }
        else{
           $product = Products::withCount('sales')->latest()->paginate(10);
        }
        return $product;
    }

}
