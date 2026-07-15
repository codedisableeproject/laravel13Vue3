<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with('penjualan')->orderBy('created_at', 'desc');

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_pembayaran', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $pembayarans = $query->paginate(10);
        return Inertia::render('Pembayaran/Index', compact('pembayarans'));
    }

    public function create()
    {
        $penjualans = Penjualan::where('status', '!=', 'Sudah Dibayar')->get();
        $kodePembayaran = 'PMB-' . date('Ymd') . '-' . str_pad(Pembayaran::count() + 1, 4, '0', STR_PAD_LEFT);
        return Inertia::render('Pembayaran/Create', compact('penjualans', 'kodePembayaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_pembayaran' => 'required|unique:pembayarans',
            'penjualan_id' => 'required|exists:penjualans,id',
            'tanggal_pembayaran' => 'required|date',
            'nilai_bayar' => 'required|numeric|min:1',
        ]);

        $penjualan = Penjualan::findOrFail($request->penjualan_id);
        $totalSudahDibayar = $penjualan->totalSudahDibayar();
        if ($totalSudahDibayar + $request->nilai_bayar > $penjualan->total) {
            return back()->with('error', 'Nilai bayar melebihi total penjualan!');
        }

        DB::transaction(function () use ($request, $penjualan, $totalSudahDibayar) {
            Pembayaran::create([
                'kode_pembayaran' => $request->kode_pembayaran,
                'penjualan_id' => $request->penjualan_id,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'nilai_bayar' => $request->nilai_bayar
            ]);

            if ($totalSudahDibayar + $request->nilai_bayar == $penjualan->total) {
                $penjualan->update(['status' => 'Sudah Dibayar']);
            } else {
                $penjualan->update(['status' => 'Belum Dibayar Sepenuhnya']);
            }
        });

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dibuat!');
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with('penjualan')->findOrFail($id);
        return Inertia::render('Pembayaran/Show', compact('pembayaran'));
    }

    public function edit($id)
    {
        $pembayaran = Pembayaran::with('penjualan')->findOrFail($id);
        $penjualans = Penjualan::all();
        return Inertia::render('Pembayaran/Edit', compact('pembayaran', 'penjualans'));
    }

    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $penjualan = Penjualan::findOrFail($pembayaran->penjualan_id);

        $request->validate([
            'tanggal_pembayaran' => 'required|date',
            'nilai_bayar' => 'required|numeric|min:1',
        ]);

        $totalSudahDibayar = $penjualan->totalSudahDibayar() - $pembayaran->nilai_bayar;
        if ($totalSudahDibayar + $request->nilai_bayar > $penjualan->total) {
            return back()->with('error', 'Nilai bayar melebihi total penjualan!');
        }

        DB::transaction(function () use ($request, $pembayaran, $penjualan, $totalSudahDibayar) {
            $pembayaran->update([
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'nilai_bayar' => $request->nilai_bayar
            ]);

            if ($totalSudahDibayar + $request->nilai_bayar == $penjualan->total) {
                $penjualan->update(['status' => 'Sudah Dibayar']);
            } elseif ($totalSudahDibayar + $request->nilai_bayar > 0) {
                $penjualan->update(['status' => 'Belum Dibayar Sepenuhnya']);
            } else {
                $penjualan->update(['status' => 'Belum Dibayar']);
            }
        });

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil diupdate!');
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $penjualan = Penjualan::findOrFail($pembayaran->penjualan_id);

        DB::transaction(function () use ($pembayaran, $penjualan) {
            $pembayaran->update(['deleted' => 1]);
            $pembayaran->delete();

            $totalSudahDibayar = $penjualan->totalSudahDibayar();
            if ($totalSudahDibayar == $penjualan->total) {
                $penjualan->update(['status' => 'Sudah Dibayar']);
            } elseif ($totalSudahDibayar > 0) {
                $penjualan->update(['status' => 'Belum Dibayar Sepenuhnya']);
            } else {
                $penjualan->update(['status' => 'Belum Dibayar']);
            }
        });

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dihapus!');
    }
}
