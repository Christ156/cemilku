<?php
namespace App\Http\Controllers;

use App\Exports\CollectionExport;
use App\Imports\CollectionImport;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role == "admin") {
            $collections = Collection::with('snacks')->get();
            return view('admin.collection.index', compact('collections'));
        } else {
            $cny        = Collection::where('category', 'Chinese New Year')->get();
            $ramadhan   = Collection::where('category', 'Ramadhan')->get();
            $valentine  = Collection::where('category', 'Valentine')->get();
            $christmas  = Collection::where('category', 'Christmas')->get();
            $birthday   = Collection::where('category', 'Birthday')->get();
            $graduation = Collection::where('category', 'Graduation')->get();

            return view('collections.index', compact('cny', 'ramadhan', 'valentine', 'christmas', 'birthday', 'graduation'));
        }


    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if (Auth::user()->role == "user") {
            $cny = Collection::where('category', 'Chinese New Year')->where('name', 'like', '%' . $search . '%')->get();
            $ramadhan = Collection::where('category', 'Ramadhan')->where('name', 'like', '%' . $search . '%')->get();
            $valentine = Collection::where('category', 'Valentine')->where('name', 'like', '%' . $search . '%')->get();
            $christmas = Collection::where('category', 'Christmas')->where('name', 'like', '%' . $search . '%')->get();
            $birthday = Collection::where('category', 'Birthday')->where('name', 'like', '%' . $search . '%')->get();
            $graduation = Collection::where('category', 'Graduation')->where('name', 'like', '%' . $search . '%')->get();

            return view('collections.index', compact('cny', 'ramadhan', 'valentine', 'christmas', 'birthday', 'graduation'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->role == "admin") {
            return view('admin.collection.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if (Auth::user()->role == "admin") {
            $validated = $request->validate([
                'name'        => 'required|string|max:255|unique:collections,name',
                'type'        => 'required|in:tower,bouquet',
                'category'    => 'required|in:Chinese New Year,Valentine,Ramadhan,Christmas,Birthday,Graduation',
                'description' => 'required|string',
                'price'       => 'required|numeric|min:1',
                'stock'       => 'required|integer|min:0',
                'image'       => 'required|image|mimes:jpg,jpeg,png|max:2048',

                // Snack dan quantity untuk 4 layer
                'snack_id_1'  => 'required|exists:snacks,id',
                'snack_id_2'  => 'required|exists:snacks,id',
                'snack_id_3'  => 'required|exists:snacks,id',
                'snack_id_4'  => 'required|exists:snacks,id',
            ]);

            $defaultQuantities = [
                'tower'   => [10, 12, 10, 8],
                'bouquet' => [5, 5, 5, 3],
            ];

            $quantities = $defaultQuantities[$validated['type']];

            // Simpan image jika ada
            $imageName = null;
            if ($request->hasFile('image')) {
                $imageName = $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('assets/collections'), $imageName);
            } else {
                $imageName = $collection->image ?? null;
            }
            // Simpan collection
            $collection = Collection::create([
                'name'        => $validated['name'],
                'type'        => $validated['type'],
                'category'    => $validated['category'],
                'description' => $validated['description'],
                'layer'       => '4',
                'price'       => $validated['price'],
                'stock'       => $validated['stock'],
                'image'       => $imageName,
            ]);

            // Simpan relasi snack
            for ($i = 1; $i <= 4; $i++) {
                $collection->snacks()->attach($validated["snack_id_$i"], [
                    'quantity' => $quantities[$i - 1], // gunakan default quantity sesuai type
                ]);
            }

            return redirect()->route('admincollection.index')->with('success', 'Collection berhasil ditambahkan!');
        } else {
            $request->validate([
                'collection_id' => 'required|exists:collections,id',
                'quantity'      => 'required|integer|min:1',
                'price'         => 'required|numeric|min:0',
            ]);

            $collection = Collection::findOrFail($request->collection_id);

            if ($request->quantity > $collection->stock) {
                return redirect()->back()->with('error', 'Quantity melebihi stok yang tersedia.');
            }

            $userId = Auth::user()->id;

            // Cari cart aktif milik user
            $cart = Cart::where('user_id', $userId)->where('is_active', true)->first();

            // Jika tidak ada, buat cart baru
            if (! $cart) {
                $cart = Cart::create([
                    'user_id'   => $userId,
                    'is_active' => true,
                ]);
            }

            // Simpan item ke cart
            $this->new_cart_item($cart->id, $request->collection_id, $request->quantity, $request->price, ($request->quantity * $request->price));

            return redirect()->route('collections.show', ['id' => $request->collection_id])->with('success', true);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->role == "admin") {
            return redirect()->route('admin.collection.index');
        } else {
            $detail = Collection::findOrFail($id);
            return view('collections.detail', compact('detail'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {
        if (Auth::user()->role == "admin") {
            return view('admin.collection.edit', compact('collection'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        if (Auth::user()->role == "admin") {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'type'        => 'required|in:tower,bouquet',
                'category'    => 'required|in:Chinese New Year,Valentine,Ramadhan,Christmas,Birthday,Graduation',
                'description' => 'required|string',
                'price'       => 'required|numeric||min:1',
                'stock'       => 'required|integer|min:0',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

                'snack_id_1'  => 'required|exists:snacks,id',
                'snack_id_2'  => 'required|exists:snacks,id',
                'snack_id_3'  => 'required|exists:snacks,id',
                'snack_id_4'  => 'required|exists:snacks,id',
            ]);

            // Simpan image baru jika ada
            if ($request->hasFile('image')) {
                if ($collection->image && file_exists(public_path('assets/collections/' . $collection->image))) {
                    unlink(public_path('assets/collections/' . $collection->image));
                }

                $imageName = $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('assets/collections'), $imageName);
            } else {
                $imageName = $collection->image;
            }

            // Update kolom-kolom utama
            $collection->update([
                'name'        => $validated['name'],
                'type'        => $validated['type'],
                'category'    => $validated['category'],
                'description' => $validated['description'],
                'price'       => $validated['price'],
                'stock'       => $validated['stock'],
                'image'       => $imageName,
            ]);

            // Tetapkan default quantity berdasarkan type
            $defaultQuantities = [
                'tower'   => [10, 12, 10, 8],
                'bouquet' => [5, 5, 5, 3],
            ];
            $quantities = $defaultQuantities[$validated['type']];

            // Sinkronisasi snack dan quantity
            $snackSync = [];
            for ($i = 1; $i <= 4; $i++) {
                $snackId             = $validated["snack_id_$i"];
                $quantity            = $quantities[$i - 1];
                $snackSync[$snackId] = ['quantity' => $quantity];
            }

            $collection->snacks()->sync($snackSync);

            return redirect()->route('admincollection.index')->with('success', 'Collection updated successfully!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        if (Auth::user()->role == "admin") {
            $collection->delete();
            return redirect()->route('admincollection.index')->with('success', 'Collection berhasil dihapus!');
        }
    }

    private function new_cart_item($cart_id, $collection_id, $quantity, $price, $total_price)
    {
        $cart_items = new CartItem();
        $cart_items->cart_id = $cart_id;
        $cart_items->collection_id = $collection_id;
        $cart_items->quantity = $quantity;
        $cart_items->price = $price;
        $cart_items->total_price = $total_price;
        $cart_items->created_at = now();
        $cart_items->save();
        return 0;
    }

    public function add_to_cart(Request $request, $collection_id, $quantity)
    {
        $user_id = Auth::user()->id;
        $checkCartUser = Cart::where('user_id', '=', $user_id)->where('is_active', '=', 1);
        $collection = Collection::findOrFail($collection_id);

        if($quantity == "custom"){
            $quantity = $request->input('quantity');
        }

        if ($checkCartUser->count() == 1) {
            $cart_id = $checkCartUser->first()->id;
            $sameItem = CartItem::where('collection_id', $collection_id);
            if($sameItem->count() > 0){
                $sameItem = $sameItem->first();
                $sameItem->quantity = $sameItem->quantity + $quantity;
                $sameItem->total_price = $sameItem->quantity * $sameItem->price;
                $sameItem->updated_at = now();
                $sameItem->save();
            }else{
                $this->new_cart_item($cart_id, $collection_id, 1, $collection->price, $collection->price);
            }
        } else {
            Cart::insert(['user_id' => $user_id, 'is_active' => 1, 'created_at' => now()]);
            $cart_id = Cart::where('user_id', '=', $user_id)->where('is_active', '=', 1)->first()->id;

            $this->new_cart_item($cart_id, $collection_id, 1, $collection->price, $collection->price);
        }

        return \redirect()->route('collections.index');
    }

    public function export()
    {
        return Excel::download(new CollectionExport, 'collection.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new CollectionImport, $request->file('file'));
        return redirect()->route('admincollection.index')->with('success', 'Data collection berhasil diimpor!');
    }

    public function trash()
    {
        $trashedCollections = Collection::onlyTrashed()->get();
        return view('admin.collection.trash', compact('trashedCollections'));
    }

    public function restore($id)
    {
        $collection = Collection::withTrashed()->findOrFail($id);
        $collection->restore();
        return redirect()->route('admincollection.trash')->with('success', 'Collection berhasil dipulihkan.');
    }

    public function restoreAll()
    {
        Collection::onlyTrashed()->restore();
        return redirect()->route('admincollection.trash')->with('success', 'Semua collection berhasil direstore.');
    }

    public function forceDelete($id)
    {
        $collection = Collection::withTrashed()->findOrFail($id);
        $collection->forceDelete();
        return redirect()->route('admincollection.trash')->with('success', 'Collection berhasil dihapus permanen.');
    }
}
