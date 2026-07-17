<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Default date range: this month
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Widgets
        $jumlahTransaksi = Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
        ->where('status', 'Sudah Dibayar')
        ->count();
        $jumlahPenjualan = Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
        ->where('status', 'Sudah Dibayar')
        ->sum('total');
        $jumlahQtyItemTerjual = PenjualanItem::whereHas('penjualan', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status', 'Sudah Dibayar');   // ← tambahin ini juga
        })->sum('qty');

        // Chart: Penjualan per Bulan
        $penjualanPerBulan = Penjualan::selectRaw('DATE_FORMAT(tanggal_penjualan, "%Y-%m") as bulan, SUM(total) as total')
            ->whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status', 'Sudah Dibayar')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Chart: Penjualan per Item
        $penjualanPerItem = PenjualanItem::selectRaw('item_id, SUM(qty) as total_qty')
            ->whereHas('penjualan', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_penjualan', [$startDate, $endDate])
                ->where('status', 'Sudah Dibayar');
            })
            ->with('item')
            ->groupBy('item_id')
            ->get();

        return Inertia::render('Dashboard', [
            'widgets' => [
                'jumlahTransaksi' => $jumlahTransaksi,
                'jumlahPenjualan' => $jumlahPenjualan,
                'jumlahQtyItemTerjual' => $jumlahQtyItemTerjual
            ],
            'charts' => [
                'penjualanPerBulan' => $penjualanPerBulan,
                'penjualanPerItem' => $penjualanPerItem
            ],
            'filters' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]
        ]);
    }
}
