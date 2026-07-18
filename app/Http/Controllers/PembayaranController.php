<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{

    private function rulesNilaiBayarHarusPas(?float $sisaBayar)
    {
        return function ($attribute, $value, $fail) use ($sisaBayar) {
            if ($sisaBayar === null) {
                return; // biar rule exists:penjualans,id yang nangkep kalau penjualan_id invalid
            }
            if ($value < $sisaBayar) {
                $kurang = $sisaBayar - $value;
                $fail('Nilai bayar kurang Rp ' . number_format($kurang, 0, ',', '.') . '. Sisa bayar yang harus dilunasi adalah Rp ' . number_format($sisaBayar, 0, ',', '.') . '.');
            } elseif ($value > $sisaBayar) {
                $lebih = $value - $sisaBayar;
                $fail('Nilai bayar lebih Rp ' . number_format($lebih, 0, ',', '.') . '. Sisa bayar yang harus dilunasi adalah Rp ' . number_format($sisaBayar, 0, ',', '.') . '.');
            }
        };
    }

    public function index(Request $request)
    {
        // Ambil data untuk tabel utama Pembayaran
        $pembayarans = Pembayaran::with(['penjualan' => function($q) {
            $q->where('deleted', 0)
            ->withSum(['pembayarans' => function($qq) {
                $qq->where('deleted', 0);
            }], 'nilai_bayar'); // <--- Hitung dari belakang lewat SQL
        }])->where('deleted', 0)->orderBy('created_at', 'desc');

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $pembayarans->whereBetween('tanggal_pembayaran', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $pembayarans = $pembayarans->paginate(10);

        // Ambil data untuk dropdown di DialogCreate
        $penjualans = Penjualan::withSum(['pembayarans' => function($q) {
            $q->where('deleted', 0);
        }], 'nilai_bayar') // <--- Hitung dari belakang lewat SQL
        ->with(['items.item'])
        ->where('deleted', 0)
        ->where('status', '!=', 'Sudah Dibayar')
        ->get();

        $kodePembayaran = 'PMB-' . date('Ymd') . '-' . str_pad(Pembayaran::where('deleted', 0)->count() + 1, 4, '0', STR_PAD_LEFT);

        return Inertia::render('Pembayaran/Index', compact('pembayarans', 'penjualans', 'kodePembayaran'));
    }

    public function create()
    {
        $penjualans = Penjualan::with(['pembayarans' => function($q) {
            $q->where('deleted', 0);
        }])->where('deleted', 0)->where('status', '!=', 'Sudah Dibayar')->get();
        $kodePembayaran = 'PMB-' . date('Ymd') . '-' . str_pad(Pembayaran::where('deleted', 0)->count() + 1, 4, '0', STR_PAD_LEFT);
        return Inertia::render('Pembayaran/Create', compact('penjualans', 'kodePembayaran'));
    }

    public function store(Request $request)
    {
        $penjualan = Penjualan::find($request->penjualan_id);
        $sisaBayar = $penjualan ? $penjualan->total - $penjualan->totalSudahDibayar() : null;

            $request->validate([
                'kode_pembayaran' => 'required|unique:pembayarans',
                'penjualan_id' => 'required|exists:penjualans,id',
                'tanggal_pembayaran' => 'required|date',
                'nilai_bayar' => ['required', 'numeric', $this->rulesNilaiBayarHarusPas($sisaBayar)],
            ], [
                'kode_pembayaran.required' => 'Kode pembayaran wajib diisi.',
                'kode_pembayaran.unique' => 'Kode pembayaran sudah digunakan.',
                'penjualan_id.required' => 'Pilih penjualan terlebih dahulu.',
                'penjualan_id.exists' => 'Penjualan yang dipilih tidak valid.',
                'tanggal_pembayaran.required' => 'Tanggal pembayaran wajib diisi.',
                'tanggal_pembayaran.date' => 'Format tanggal tidak valid.',
                'nilai_bayar.required' => 'Nilai bayar wajib diisi.',
                'nilai_bayar.numeric' => 'Nilai bayar harus berupa angka.',
            ]);

        $penjualan = Penjualan::findOrFail($request->penjualan_id);

        DB::transaction(function () use ($request, $penjualan) {
            Pembayaran::create([
                'kode_pembayaran' => $request->kode_pembayaran,
                'penjualan_id' => $request->penjualan_id,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'nilai_bayar' => $request->nilai_bayar
            ]);

            $penjualan->update(['status' => 'Sudah Dibayar']);
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
