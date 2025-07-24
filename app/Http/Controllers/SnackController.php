<?php

namespace App\Http\Controllers;

use App\Exports\SnackExport;
use App\Imports\SnackImport;
use App\Models\Snack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SnackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role == "admin") {
            $snacks = Snack::all();
            return view('admin.snack.index', compact('snacks'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->role == "admin") {
            return view('admin.snack.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role == "admin") {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('snacks', 'public');
            }

            Snack::create($validated);

            return redirect()->route('adminsnack.index')->with('success', 'Snack ditambahkan!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        if(Auth::user()->role == "admin"){
            return redirect()->route('adminsnack.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Snack $snack)
    {
        if (Auth::user()->role == "admin") {
            return view('admin.snack.edit', compact('snack'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Snack $snack)
    {
        if (Auth::user()->role == "admin") {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($snack->image && Storage::disk('public')->exists($snack->image)) {
                    Storage::disk('public')->delete($snack->image);
                }

                // Simpan gambar baru
                $validated['image'] = $request->file('image')->store('snacks', 'public');
            }

            $snack->update($validated);

            return redirect()->route('adminsnack.index')->with('success', 'Snack diperbarui!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Snack $snack)
    {
        $snack->delete();

        return redirect()->route('adminsnack.index')->with('success', 'Snack dihapus!');
    }

    public function export()
    {
        return Excel::download(new SnackExport, 'snack.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new SnackImport, $request->file('file'));

        return redirect()->route('adminsnack.index')->with('success', 'Data snack berhasil diimpor!');
    }

    // Menampilkan semua snack yang sudah dihapus
    public function trash()
    {
        $trashedSnacks = Snack::onlyTrashed()->get(); // Ambil data snack yang di-soft delete
        return view('admin.snack.trash', compact('trashedSnacks'));
    }

    // Restore soft-deleted snack
    public function restore($id)
    {
        $snack = Snack::withTrashed()->findOrFail($id);
        $snack->restore();
        return redirect()->route('adminsnack.trash')->with('success', 'Snack berhasil dipulihkan.');
    }

    // Hapus permanen snack
    public function forceDelete($id)
    {
        $snack = Snack::withTrashed()->findOrFail($id);
        $snack->forceDelete();
        return redirect()->route('adminsnack.trash')->with('success', 'Snack berhasil dihapus permanen.');
    }

    public function restoreAll()
    {
        Snack::onlyTrashed()->restore();
        return redirect()->route('adminsnack.trash')->with('success', 'Semua snack berhasil direstore.');
    }
}
