<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search=$request->search;
        $categoryId=$request->category_id;
        $products=Product::with('category') ->when($search,function($query) use ($search){
        $query->where('name','like',"%{$search}%");
        })->when($categoryId,function ($query) use ($categoryId){
            $query->where('category_id',$categoryId);
        } )->paginate(5);
        return ProductResource::collection($products);
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
    public function store(Request $request)
    {

        $data=$request->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string',
            'price'=>'required|numeric|min:1',
            'stock'=>'required|integer|min:0',
            'image'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category_id'=>'nullable|exists:categories,id',
        ]);
        
        $path=$request->file('image')->store('products','public');

        if($request->hasFile('image')){
            $data['image']=$path; 
        };
        $product=Product::create($data);
        return response()->json([
            'massage'=>'Producte Created Successfully',
            'product'=>$product
        ],201);
        }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
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
    public function update(Request $request, Product $product)
    {
           $data=$request->validate([
            'name'=>'sometimes|string|max:255',
            'description'=>'nullable|string',
            'price'=>'sometimes|numeric|min:1',
            'stock'=>'sometimes|integer|min:0',
            'image'=>'sometimes|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        
        $path=$request->file('image')->store('products','public');


        if($request->hasFile('image')){
            storage::disk('public')->delete($product->image);
            $data['image']=$path; 
            };
            $product->update($data);
        return response()->json([
            'massage'=>'product updated successfully',
            'product'=>$product
        ]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
         storage::disk('public')->delete($product->image);

        $product->delete();
        return response()->json([
            'massage'=>'product deleted successfully'
        ]);
     }
}
