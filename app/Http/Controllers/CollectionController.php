<?php
namespace App\Http\Controllers;

use App\Exports\CollectionExport;
use App\Imports\CollectionImport;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

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
            $cart = Cart::where('user_id', $userId)->where('is_active', 1)->first();

            // Jika tidak ada, buat cart baru
            if (! $cart) {
                $cart = Cart::create([
                    'user_id'   => $userId,
                    'is_active' => 1,
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

    public function add_to_cart(Request $request, $id_collection, $quantity) // $quantity IS A PARAMETER
    {
        // Remove the dd() you added earlier, or it will stop the test prematurely
        // dd([
        //     'id_collection_received' => $id_collection,
        //     'quantity_received' => $quantity,
        //     'request_all_data' => $request->all(),
        //     'request_query_data' => $request->query(),
        //     'request_route_parameters' => $request->route()->parameters(),
        // ]);

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login untuk menambahkan produk ke keranjang.');
        }

        $user = auth()->user();
        $product = Collection::findOrFail($id_collection);

        // This is the line that uses the $quantity received as a parameter.
        // Make sure you are not trying to get it from $request->input() here.
        $quantityToAdd = (int) $quantity; // THIS IS LINE 273 or very close to it.

        // Pastikan kuantitas yang diminta tidak melebihi stok
        if ($quantityToAdd <= 0 || $quantityToAdd > $product->stock) {
            return back()->with('error', 'Kuantitas tidak valid atau melebihi stok yang tersedia.');
        }

        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'is_active' => true],
        );

        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('collection_id', $product->id)
                            ->first();

        // Logic for updating/creating cart item
        $newQuantity = ($cartItem ? $cartItem->quantity : 0) + $quantityToAdd;

        if ($newQuantity > $product->stock) {
            return back()->with('error', 'Penambahan kuantitas melebihi stok yang tersedia (' . $product->stock . ' pcs).');
        }

        if ($cartItem) {
            $cartItem->quantity = $newQuantity;
            $cartItem->price = $product->price;
            $cartItem->total_price = $product->price * $newQuantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'collection_id' => $product->id,
                'quantity' => $newQuantity,
                'price' => $product->price,
                'total_price' => $product->price * $newQuantity,
            ]);
        }

        return redirect()->route('cart.index', [
            'id_user' => $user->id,
            'slug' => Str::slug($user->name)
        ])->with('success', 'Produk berhasil ditambahkan ke keranjang!');
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
