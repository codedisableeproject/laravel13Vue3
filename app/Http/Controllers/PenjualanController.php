<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Penjualan;
use App\Models\Item;
use App\Models\PenjualanItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('items.item', 'pembayarans')->where('deleted', 0)->orderBy('created_at', 'desc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tanggal_penjualan', [$request->start_date, $request->end_date]);
        }

        $penjualans = $query->paginate(10);
        $items = Item::where('deleted', 0)->get();
        $kodePenjualan = 'PJN-' . date('Ymd') . '-' . str_pad(Penjualan::where('deleted', 0)->count() + 1, 4, '0', STR_PAD_LEFT);
        
        $filters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ];
        
        return Inertia::render('Penjualan/Index', compact('penjualans', 'items', 'kodePenjualan', 'filters'));
    }

    public function create()
    {
        $items = Item::where('deleted', 0)->get();
        $kodePenjualan = 'PJN-' . date('Ymd') . '-' . str_pad(Penjualan::count() + 1, 4, '0', STR_PAD_LEFT);
        return Inertia::render('Penjualan/Create', compact('items', 'kodePenjualan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_penjualan' => 'required|unique:penjualans',
            'tanggal_penjualan' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            foreach ($request->items as $item) {
                $itemData = Item::find($item['item_id']);
                $total += $item['qty'] * $itemData->harga;
            }

            $penjualan = Penjualan::create([
                'kode_penjualan' => $request->kode_penjualan,
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'total' => $total,
                'status' => 'Belum Dibayar'
            ]);

            foreach ($request->items as $item) {
                $itemData = Item::find($item['item_id']);
                PenjualanItem::create([
                    'penjualan_id' => $penjualan->id,
                    'item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'price' => $itemData->harga,
                    'total_price' => $item['qty'] * $itemData->harga
                ]);
            }
        });

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dibuat!');
    }

    public function show($id)
    {
        $penjualan = Penjualan::with('items.item', 'pembayarans')->where('deleted', 0)->findOrFail($id);
        $totalSudahDibayar = $penjualan->totalSudahDibayar();
        return Inertia::render('Penjualan/Show', compact('penjualan', 'totalSudahDibayar'));
    }

    public function edit($id)
    {
        $penjualan = Penjualan::with('items.item')->where('deleted', 0)->findOrFail($id);
        if ($penjualan->status === 'Sudah Dibayar') {
            return redirect()->route('penjualan.index')->with('error', 'Penjualan yang sudah dibayar tidak bisa diedit!');
        }
        $items = Item::where('deleted', 0)->get();
        return Inertia::render('Penjualan/Edit', compact('penjualan', 'items'));
    }

    public function update(Request $request, $id)
    {
        $penjualan = Penjualan::where('deleted', 0)->findOrFail($id);
        if ($penjualan->status === 'Sudah Dibayar') {
            return redirect()->route('penjualan.index')->with('error', 'Penjualan yang sudah dibayar tidak bisa diedit!');
        }

        $request->validate([
            'tanggal_penjualan' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $penjualan) {
            $total = 0;
            foreach ($request->items as $item) {
                $itemData = Item::find($item['item_id']);
                $total += $item['qty'] * $itemData->harga;
            }

            $penjualan->update([
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'total' => $total
            ]);

            $penjualan->items()->delete();

            foreach ($request->items as $item) {
                $itemData = Item::find($item['item_id']);
                PenjualanItem::create([
                    'penjualan_id' => $penjualan->id,
                    'item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'price' => $itemData->harga,
                    'total_price' => $item['qty'] * $itemData->harga
                ]);
            }
        });

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diupdate!');
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::where('deleted', 0)->findOrFail($id);
        if ($penjualan->status === 'Sudah Dibayar') {
            return redirect()->route('penjualan.index')->with('error', 'Penjualan yang sudah dibayar tidak bisa dihapus!');
        }

        $penjualan->update([
            'deleted' => 1,
            'deleted_at' => now(),
            // 'deleted_by' => auth()->id()
            'deleted_by' => Auth::id() // Menggunakan Facade Auth
        ]);
        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus!');
    }
}
