<?php

namespace App\Http\Controllers;

use App\Models\invoices;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //total of Invoices
        $totalInvoices = number_format(invoices::sum('Total'), 2);
        //Count of invoices
        $invoicesCount = invoices::count();

        ///////////////////////////////////
        //total of invoicesNotPay
        $invoicesNotPay = invoices::where('Value_Status', '2')->sum('Total');
        $CountInvoicesNotPay = invoices::where('Value_Status', '2')->count();
        ///////////////////////////////////
        //percentage of NotPay Invoices
        /* notPayInvoices / countTotal Invoices *100  */
        $percentNotPay = $CountInvoicesNotPay > 0 ? round($CountInvoicesNotPay / $invoicesCount * 100, 2) : 0;
        ///////////////////////////////////

        //Payment Invoices
        $CountPayInvoices = invoices::where('Value_Status', '1')->count();
        $SumPayMentInvoices = invoices::where('Value_Status', '1')->sum('Total');
        /* PayInvoices / countTotal Invoices *100  */
        $percentPay = $CountPayInvoices > 0 ? round($CountPayInvoices / $invoicesCount * 100, 2) : 0;

        ////////////////////////////////////////////////////////
        //Payment Invoices
        $CountPartiallypaid = invoices::where('Value_Status', '3')->count();
        $SumPartiallypaid = invoices::where('Value_Status', '3')->sum('Total');
        /* PayInvoices / countTotal Invoices *100  */
        $percentPartiallypaid = $CountPartiallypaid > 0 ? round($CountPartiallypaid / $invoicesCount * 100, 2) : 0;

        // chart
        $chartjs = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 400, 'height' => 200])
            ->labels(['فواتير المدفوعة ', 'الفواتير غير مدفوعة ', 'الفواتير المدفوعة جزئيا']) // Added 'Label z' to match the number of colors and data points
            ->datasets([
                [
                    'backgroundColor' => ['#008000', '#FF0000', '#FFA500'],
                    'hoverBackgroundColor' => ['#008000', '#FF0000', '#FFA500'],
                    'data' => [$percentPay, $percentNotPay, $percentPartiallypaid], // Added a third data point to match the number of colors
                ],
            ])
            ->options([]);
        $chartjs2 = app()->chartjs
            ->name('barChartTest')
            ->type('bar')
            ->size(['width' => 400, 'height' => 200])
            ->labels(['احصائيات الزوار لشهر يناير ', 'احضائيات الزوار لشهر فبراير'])
            ->datasets([
                [
                    'label' => 'احصائيات الزوار لشهر يناير ',
                    'backgroundColor' => ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                    'data' => [69, 59],
                ],
                [
                    'label' => 'احصائيات الزوار لشهر فبراير ',
                    'backgroundColor' => ['rgba(255, 99, 132, 0.3)', 'rgba(54, 162, 235, 0.3)'],
                    'data' => [65, 12],
                ],
            ])
            ->options([]);

        return view('home', compact(
            'totalInvoices',
            'invoicesCount',
            'invoicesNotPay',
            'CountInvoicesNotPay',
            'percentNotPay',
            'CountPayInvoices',
            'SumPayMentInvoices',
            'percentPay',
            'CountPartiallypaid',
            'SumPartiallypaid',
            'percentPartiallypaid',
            'chartjs',
            'chartjs2'
        ));
    }
}
