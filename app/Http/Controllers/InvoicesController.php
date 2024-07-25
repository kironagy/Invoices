<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use App\Models\sections;
use Illuminate\Http\Request;
use App\Exports\InvoicesExport;
use App\Models\invoices_details;
use Illuminate\Support\Facades\DB;
use App\Models\invoice_attachments;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = invoices::all();
        return view("invoices.invoices", compact("invoices"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = sections::all();
        return view("invoices.add_invoice", compact("sections"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // insert invoice data
        invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);
        // insert invoices_details
        $invoice_id = invoices::latest()->first()->id;
        invoices_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);
        // upload file
        if ($request->hasFile('img')) {

            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('img');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new invoice_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move file
            $imageName = $request->img->getClientOriginalName();
            $request->img->move(public_path('Attachments/' . $invoice_number), $imageName);
            // return back()->with('success', "تم اضافة الفاتورة بنجاح");
        }



        // Notification::send($user, new AddInvoice($invoice_id));

        return redirect()->route("invoices.index")->with('success', "تم اضافة الفاتورة بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoices = invoices::where('id', $id)->first();
        return view("invoices.status_update", compact("invoices"));
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id)
    {
        $invoices = invoices::where("id", $id)->first();
        $sections = sections::all();
        return view("invoices.edit_invoice", compact("invoices", "sections"));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, invoices $invoices)
    {
        $invoices = invoices::findOrFail($request->invoice_id);

        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);
        return back()->with('success', 'تم تعديل الفاتورة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = invoices::where('id', $id)->first();
        $Details = invoice_attachments::where('invoice_id', $id)->first();

        $id_page = $request->id_page;

        if (!$id_page == 2) {

            if (!empty($Details->invoice_number)) {

                Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
            }

            $invoices->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/invoices');
        } else {

            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('/Archive');
        }
    }

    // public function getProducts($id)
    // {
    //      $products = products::where("section_id", $id)->pluck("Product_name", "id");
    //      return $products->toJson();
    ///////////////////////
    //     $products = DB::table("products")->where("section_id ", $id)->pluck("Product_name", "id");
    //     return json_encode($products);
    // }
    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("Product_name", "id");
        return json_encode($products);
    }

    public function Status_Update($id, Request $request)
    {
        $invoices = invoices::findOrFail($id);

        if ($request->Status === 'مدفوعة') {

            $invoices->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        } else {
            $invoices->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('success', 'تم تغير حالة الدفع بنجاح');
        return redirect('/invoices');
    }

    public function Invoice_Paid()
    {
        $invoices = invoices::where("Value_Status", 1)->get();
        return view("invoices.invoices_Paid", compact("invoices"));
    }

    public function Invoice_UnPaid()
    {
        $invoices = invoices::where("Value_Status", 2)->get();
        return view("invoices.invoices_upPaid", compact("invoices"));
    }

    public function Invoice_Partial()
    {
        $invoices = invoices::where("Value_Status", 3)->get();
        return view("invoices.invoice_Partial", compact("invoices"));
    }
    public function Print_invoice($id)
    {
        $invoices = invoices::where("id", $id)->first();
        return view("invoices.Print_invoice", compact("invoices"));
    }

    //export Excle
    public function export()
    {
        return Excel::download(new InvoicesExport, 'Invoices.xlsx');
    }
}
