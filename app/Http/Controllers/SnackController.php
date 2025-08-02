<?php
namespace App\Http\Controllers;

use App\Exports\SnackExport;
use App\Imports\SnackImport;
use App\Models\Snack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SnackController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $search = $request->input('search');
        $snacks = Snack::query();

        if ($search) {
            $snacks->where('name', 'like', '%' . $search . '%');
        }

        $snacks = $snacks->get();

        return view('admin.snack.index', compact('snacks'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin') {
            return view('admin.snack.create');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:snacks,name',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file         = $request->file('image');
            $originalName = $file->getClientOriginalName();

            $destinationPath = public_path('assets/snack_items');
            $file->move($destinationPath, $originalName);

            $validated['image'] = $originalName;
        }

        Snack::create($validated);

        return redirect()->route('adminsnack.index')->with('success', 'Snack ditambahkan!');
    }

    public function show(string $id)
    {
        return redirect()->route('adminsnack.index');
    }

    public function edit(Snack $snack)
    {
        if (Auth::user()->role === 'admin') {
            return view('admin.snack.edit', compact('snack'));
        }
    }

    public function update(Request $request, Snack $snack)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($snack->image && file_exists(public_path('assets/snack_items/' . $snack->image))) {
                unlink(public_path('assets/snack_items/' . $snack->image));
            }

            $file            = $request->file('image');
            $filename        = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/snack_items');
            $file->move($destinationPath, $filename);

            $validated['image'] = $filename;
        }

        $snack->update($validated);

        return redirect()->route('adminsnack.index')->with('success', 'Snack diperbarui!');
    }

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
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new SnackImport, $request->file('file'));
            return redirect()->route('adminsnack.index')->with('success', 'Data snack berhasil diimpor!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage()]);
        }
    }

    public function trash()
    {
        $trashedSnacks = Snack::onlyTrashed()->get();
        return view('admin.snack.trash', compact('trashedSnacks'));
    }

    public function restore($id)
    {
        $snack = Snack::withTrashed()->findOrFail($id);
        $snack->restore();

        return redirect()->route('adminsnack.trash')->with('success', 'Snack berhasil dipulihkan.');
    }

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
