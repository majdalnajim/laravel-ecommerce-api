<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd($request->category_id); 
        $search=$request->search;
        $categoryId=$request->category_id;
        $products=Product::with('category') ->when($search,function($query) use ($search){
        $query->where('name','like',"%{$search}%");
        })->when($categoryId,function ($query) use ($categoryId){
            $query->where('category_id',$categoryId);
        } )->paginate(5);
        return response()->json($products);
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
            'image'=>'nullable|string',
            'category_id'=>'numeric',
        ]);
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
        return response()->json($product);
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
            'image'=>'nullable|string'
        ]);

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
        $product->delete();
        return response()->json([
            'massage'=>'product deleted successfully'
        ]);
     }
}
