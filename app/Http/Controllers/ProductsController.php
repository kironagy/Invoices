<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\sections;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $sections = sections::all();
        $products = products::all();
        return view("products.products", compact("sections", "products"));
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
        $validated = $request->validate(
            [
                'Product_name' => 'required|unique:products|max:255',
                'description' => 'required',
                'section_id' => 'required',
            ],
            [
                'Product_name.required' => 'اسم المنتج مطلوب ',
                'Product_name.unique' => 'اسم المنتج موجود مسبقا ',
                'description' => 'الملاحظات مطلوبة ',
                'section_id' => 'لابد من اختيار قسم  ',
            ]
        );
        $products = products::create([
            "Product_name" => $request->Product_name,
            "description" => $request->description,
            "section_id" => $request->section_id,
        ]);
        return back()->with("success", "تم اضافة القسم بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, products $products)
    {
        $validated = $request->validate(
            [
                'Product_name' => 'required|max:255|unique:products,Product_name,' . $request->pro_id,
                'description' => 'required',
            ],
            [
                'Product_name.required' => 'اسم المنتج مطلوب ',
                'description.required' => 'الملاحظات مطلوبة ',
            ]
        );

        $id = sections::where('section_name', $request->section_name)->first()->id;

        $products = Products::findOrFail($request->pro_id);

        $products->update([
            'Product_name' => $request->Product_name,
            'description' => $request->description,
            'section_id' => $id,
        ]);

        return back()->with('success', "تم تعديل المنتج بنجاح");
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Request $request)
    {
        $products = products::findOrFail($request->pro_id);
        $products->delete();
        return back()->with('success', "تم حذف المنتج بنجاح");
    }
}
