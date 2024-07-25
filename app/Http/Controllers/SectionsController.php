<?php

namespace App\Http\Controllers;

use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = sections::all();
        return view("sections.sections", compact("sections"));
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
                'section_name' => 'required|unique:sections|max:255',
            ],
            [
                'section_name.required' => 'اسم القسم مطلوب ',
                'section_name.unique' => 'اسم القسم مسجل  مسبقا  ',
            ]
        );

        sections::create([
            "section_name" => $request->section_name,
            "description" => $request->description,
            "created_by" => (Auth::user()->name),
        ]);
        return back()->with('success', 'تم اضافة القسم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(sections $sections)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sections $sections)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $validated = $request->validate(
            [
                'section_name' => 'required|unique:sections,section_name,' . $id . '|max:255',
            ],
            [
                'section_name.required' => 'اسم القسم مطلوب ',
                'section_name.unique' => 'اسم القسم مسجل  مسبقا  ',
            ]
        );
        $sections = sections::find($id);
        $sections->update([
            "section_name" => $request->section_name,
            "description" => $request->description,
        ]);
        return back()->with('success', "تم تعديل القسم بنجاح");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        sections::destroy($id);
        return back()->with('success', "تم حذف القسم بنجاح");
    }
}
