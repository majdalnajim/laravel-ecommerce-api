<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories=Category::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'name'=>'required|string|max:255|unique:categories,name',
        ]);
        $category=Category::create($data);

        return response()->json([
            'message'=>'Category created successfully',
            'category'=>$category,
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
       $data=$request->validate([
        'name'=>'required|string|max:255|unique:categories,name,'.$category->id, //i gnore the regored i wann edit
       ]);
       $category->update($data);
       return response()->json([
        'massage'=>'Category updated successfuly',
        'category'=>$category
       ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
'massage'=>'successfuly delete'
        ]);
    }
}
