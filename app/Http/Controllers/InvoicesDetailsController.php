<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachments;
use App\Models\invoices;
use App\Models\invoices_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(invoices_details $invoices_details)
    {
        //
    }


    public function edit($id)
    {
        // هنا عاوز اجيب بيانات التفاصيل بتاع الفاتورة واعرضهم في الصفحة دي
        $invoices = invoices::where("id", $id)->first();
        $invoices_details = invoices_details::where("id_Invoice", $id)->get();
        $attachments = invoice_attachments::where("invoice_id", $id)->get();

        return view("invoices.invoices_details", compact("invoices", "invoices_details", "attachments"));
    }


    public function update(Request $request, invoices_details $invoices_details)
    {
    }

    public function destroy(Request $request)
    {
        $invoices = invoice_attachments::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number . '/' . $request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

    // download file
    public function download_file($invoice_number, $file_name)
    {
        $path = Storage::disk('public_uploads')->path($invoice_number . '/' . $file_name);
        return response()->download($path);
    }

    // show File
    public function open_file($invoice_number, $file_name)
    {
        $path = Storage::disk('public_uploads')->path($invoice_number . '/' . $file_name);

        return response()->file($path);
    }
}
