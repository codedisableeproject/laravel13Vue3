<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::where('deleted', 0)->paginate(10);
        $kodeItem = 'ITM-' . date('Ymd') . '-' . str_pad(Item::where('deleted', 0)->count() + 1, 4, '0', STR_PAD_LEFT);
        return Inertia::render('Item/Index', compact('items', 'kodeItem'));
    }

    public function create()
    {
        $kodeItem = 'ITM-' . date('Ymd') . '-' . str_pad(Item::where('deleted', 0)->count() + 1, 4, '0', STR_PAD_LEFT);
        return Inertia::render('Item/Create', compact('kodeItem'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:items',
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:10240',
        ], [
            'kode.required' => 'Kode Item wajib diisi',
            'nama.required' => 'Nama Item wajib diisi',
            'harga.required' => 'Harga Item wajib diisi',
            'image.max' => 'Gambar Item maksimal 10MB',
        ]);



        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        Item::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'image' => $imagePath,
            'deleted' => 0
        ]);

        return redirect()->route('item.index')->with('success', 'Item berhasil dibuat!');
    }

    public function edit($id)
    {
        $item = Item::where('deleted', 0)->findOrFail($id);
        return Inertia::render('Item/Edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::where('deleted', 0)->findOrFail($id);
        $request->validate([
            'kode' => 'required|unique:items,kode,' . $id,
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:10240',
        ], [
            'kode.required' => 'Kode Item wajib diisi',
            'nama.required' => 'Nama Item wajib diisi',
            'harga.required' => 'Harga Item wajib diisi',
            'image.max' => 'Gambar Item maksimal 10MB',
        ]);



        $imagePath = $item->image;
        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $imagePath = $request->file('image')->store('items', 'public');
        } elseif ($request->boolean('remove_image')) {
            // user minta hapus gambar tanpa ganti yang baru
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $imagePath = null;
        }

        $item->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'image' => $imagePath
        ]);
        return redirect()->route('item.index')->with('success', 'Item berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = Item::where('deleted', 0)->findOrFail($id);
        $item->update([
            'deleted' => 1,
            'deleted_at' => now(),
            'deleted_by' => auth()->id() // jika id bergaris merah biarin aja fungsi tetap jalan kok
        ]);
        return redirect()->route('item.index')->with('success', 'Item berhasil dihapus!');
    }
}
