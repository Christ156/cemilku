<?php
namespace App\Http\Controllers;

use App\Exports\DecorationExport;
use App\Imports\DecorationImport;
use App\Models\Decoration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DecorationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role == "admin") {
            $decorations = Decoration::all();
            return view('admin.decoration.index', compact('decorations'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->role == "admin") {
            return view('admin.decoration.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role == "admin") {
            $validated = $request->validate([
                'name'  => 'required|string|max:255|unique:decorations,name',
                'price' => 'required|numeric|min:1',
                'stock' => 'required|integer|min:0',
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $file         = $request->file('image');
                $originalName = $file->getClientOriginalName();

                // Simpan ke folder public/assets/decoration_items
                $destinationPath = public_path('assets/decoration');
                $file->move($destinationPath, $originalName);

                // Simpan hanya nama file ke database
                $validated['image'] = $originalName;
            }

            Decoration::create($validated);

            return redirect()->route('admindecoration.index')->with('success', 'Decoration ditambahkan!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('admindecoration.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Decoration $decoration)
    {
        if (Auth::user()->role == "admin") {
            return view('admin.decoration.edit', compact('decoration'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Decoration $decoration)
    {
        if (Auth::user()->role == "admin") {
            $validated = $request->validate([
                'name'  => 'required|string|max:255',
                'price' => 'required|numeric|min:1',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($decoration->image && file_exists(public_path('assets/decoration/' . $decoration->image))) {
                    unlink(public_path('assets/decoration/' . $decoration->image));
                }

                $file         = $request->file('image');
                $originalName = $file->getClientOriginalName();

                // Simpan gambar baru ke folder public/assets/decoration_items
                $destinationPath = public_path('assets/decoration');
                $file->move($destinationPath, $originalName);

                // Simpan hanya nama file di database
                $validated['image'] = $originalName;
            }

            $decoration->update($validated);

            return redirect()->route('admindecoration.index')->with('success', 'Decoration diperbarui!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Decoration $decoration)
    {
        if (Auth::user()->role == "admin") {
            $decoration->delete();

            return redirect()->route('admindecoration.index')->with('success', 'Decoration dihapus!');
        }
    }

    public function export()
    {
        return Excel::download(new DecorationExport, 'decoration.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new DecorationImport;
        Excel::import($import, $request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            return back()->withErrors($import->failures())->with('warning', 'Beberapa baris gagal diimpor.');
        }

        return redirect()->route('admindecoration.index')->with('success', 'Data decoration berhasil diimpor!');
    }

    public function trash()
    {
        $trashedDecorations = Decoration::onlyTrashed()->get();
        return view('admin.decoration.trash', compact('trashedDecorations'));
    }

    public function restore($id)
    {
        $decoration = Decoration::withTrashed()->findOrFail($id);
        $decoration->restore();
        return redirect()->route('admindecoration.trash')->with('success', 'Decoration berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $decoration = Decoration::withTrashed()->findOrFail($id);
        $decoration->forceDelete();
        return redirect()->route('admindecoration.trash')->with('success', 'Decoration berhasil dihapus permanen.');
    }

    public function restoreAll()
    {
        Decoration::onlyTrashed()->restore();
        return redirect()->route('admindecoration.trash')->with('success', 'Semua decoration berhasil direstore.');
    }
}
