<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customize;
use App\Models\CustomizeDecoration;
use App\Models\CustomizeSnack;
use App\Models\Decoration;
use App\Models\LayerSnack;
use App\Models\Snack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomizeTowerBouquetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role == "admin") {
            return view('customize.index', [
                'customizes' => Customize::with(['snacks', 'decorations'])->get()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->role == "admin") {
            return view('customize.create', [
                'snacks' => Snack::all(),
                'decorations' => Decoration::all()
            ]);
        }
    }

    public function create_tower()
    {
        $snack = LayerSnack::all();
        $decoration = Decoration::all();

        return view('customize_tower.create', compact('snack', 'decoration'));
    }

    public function create_bouquet()
    {
        $snack = LayerSnack::all();
        $decoration = Decoration::all();

        return view('customize_bouquet/create', compact('snack', 'decoration'));
    }

    /**
     * Store a newly created resource in storage.
     */
    private function new_cart_item($cart_id, $customize_id, $quantity, $price, $total_price)
    {
        $cart_items = new CartItem();
        $cart_items->cart_id = $cart_id;
        $cart_items->customize_id = $customize_id;
        $cart_items->quantity = $quantity;
        $cart_items->price = $price;
        $cart_items->total_price = $total_price;
        $cart_items->created_at = now();
        $cart_items->save();
        return 0;
    }

    public function store(Request $request, string $type)
    {
        // Debugging (bisa dihapus setelah selesai):
        // dd($request->all());

        if (Auth::user()->role == "admin") {
            // Logika untuk Admin tetap sama
            $data = $request->validate([
                'name' => 'required',
                'type' => 'required',
                'price' => 'required|numeric',
                'image' => 'nullable|string',
                'layer' => 'required|in:2,3,4',
                'snack_id_1' => 'nullable|exists:snacks,id',
                'snack_id_2' => 'nullable|exists:snacks,id',
                'snack_id_3' => 'nullable|exists:snacks,id',
                'snack_id_4' => 'nullable|exists:snacks,id',
                'decoration_id_1' => 'nullable|exists:decorations,id',
                'decoration_id_2' => 'nullable|exists:decorations,id'
            ]);

            $customize = Customize::create($data);

            if ($request->snacks) {
                foreach ($request->snacks as $snackId => $qty) {
                    $customize->snacks()->attach($snackId, ['quantity' => $qty]);
                }
            }

            if ($request->decorations) {
                $customize->decorations()->attach($request->decorations);
            }

            return redirect()->route('customize.index')->with('success', 'Kustomisasi admin berhasil ditambahkan.');
        } else {
            // --- Alur Kustomisasi Multi-Tahap untuk Pengguna (Non-Admin) ---

            $customize = null;

            // Cari objek Customize yang sudah ada jika customize_id dikirimkan
            if ($request->has('customize_id')) {
                $customize = Customize::find($request->customize_id);
                if (!$customize) {
                    return redirect()->back()->withErrors('Kustomisasi tidak ditemukan atau tidak valid.');
                }
            }

            // --- Tahap 1: Pemilihan Gambar Base (Initial Creation) ---
            // Ini akan terjadi jika belum ada customize_id
            if (!$customize && $request->has('base_image_path')) {
                $request->validate([
                    'name' => 'required|string',
                    'base_image_path' => 'required|string',
                    'price' => 'required|numeric',
                ]);

                $customize = new Customize();
                $customize->name = $request->name;
                $customize->type = $type; // 'bouquet' atau 'tower'
                $customize->base_image_path = $request->input('base_image_path');
                $customize->price = $request->price;
                $customize->layer = 2; // <--- DEFAULTKAN KE 2 SEBAGAI PLACEHOLDER (bukan 0)
                $customize->created_at = now();
                $customize->updated_at = now();
                $customize->save();

                // Redirect ke halaman pemilihan layer
                return redirect()->route('customize-tower-bouquet.bouquet', [
                    'stage' => 'layer', // <--- REDIRECT KE TAHAP LAYER
                    'customize_id' => $customize->id
                ])->with('success', 'Gambar dasar dipilih, lanjutkan pilih layer.');
            }

            // --- Tahap Update Layer and Price (Adjusted for Test Flow) ---
            // This needs to happen if customize_id, layer, and price are present.
            // The previous condition `!$request->has('snack_1')` was too restrictive for the test.
            if ($customize && $request->has('layer') && $request->has('price')) {
                $request->validate([
                    'layer' => 'required|in:2,3,4',
                    'price' => 'required|numeric',
                ]);

                // Only delete snacks and decorations if the layer HAS ACTUALLY CHANGED.
                // This ensures existing snacks are cleared only when a layer change occurs.
                if ($customize->layer != $request->layer) {
                    CustomizeSnack::where('customize_id', $customize->id)->delete();
                    CustomizeDecoration::where('customize_id', $customize->id)->delete();
                }

                $customize->layer = $request->layer;
                $customize->price = $request->price;
                $customize->updated_at = now();
                $customize->save();

                // If the request also contains snack data, it means we're in the final stage.
                // Otherwise, it's just a layer update redirecting to snack selection.
                if (!$request->has('snack_1')) { // If no snack data, redirect to snack selection
                    return redirect()->route('customize-tower-bouquet.bouquet', [
                        'stage' => 'snack', // <--- REDIRECT KE TAHAP SNACK
                        'customize_id' => $customize->id
                    ])->with('success', 'Layer dipilih, lanjutkan pilih snack.');
                }
                // If snack_1 IS present, this block will simply update layer/price
                // and then fall through to the final snack/decoration processing below.
            }

            // --- Tahap 3: Pemilihan Snack/Dekorasi (Tahap Akhir) ---
            // This will now catch requests that have customize_id, potentially a layer/price update, AND snack_1.
            if ($customize && $request->has('snack_1')) {
                $request->validate([
                    'price' => 'required|numeric', // Harga final setelah snack/dekorasi
                ]);

                // Update price again (in case it was updated by previous stage or changed here)
                $customize->price = $request->price;
                $customize->updated_at = now();
                $customize->save();

                // Hapus snack dan dekorasi yang mungkin sudah ada sebelumnya untuk memastikan hanya yang baru yang terpasang
                // This will re-run the deletion, which is fine as we want a clean slate for final snack selection.
                CustomizeSnack::where('customize_id', $customize->id)->delete();
                CustomizeDecoration::where('customize_id', $customize->id)->delete();

                // Logika untuk melampirkan snack/dekorasi berdasarkan tipe
                if ($type == 'tower') {
                    for ($i = 1; $i <= $customize->layer; $i++) {
                        $snackId = $request->input('snack_' . $i);
                        if ($snackId !== null) {
                            CustomizeSnack::insert(['customize_id' => $customize->id, 'snack_id' => $snackId, 'quantity' => 10, 'created_at' => now()]);
                        }
                    }
                    if($request->decoration != NULL){
                        CustomizeDecoration::insert(['customize_id' => $customize->id, 'decoration_id' => $request->decoration, 'created_at' => now()]);
                    }
                } else if ($type == 'bouquet') {
                    for ($i = 1; $i <= $customize->layer; $i++) {
                        $snackId = $request->input('snack_' . $i);
                        if ($snackId !== null) {
                            CustomizeSnack::insert(['customize_id' => $customize->id, 'snack_id' => $snackId, 'quantity' => 5, 'created_at' => now()]);
                        }
                    }
                }

                // Logika Penambahan ke Keranjang
                $user_id = Auth::user()->id;
                $checkCartUser = Cart::where('user_id', '=', $user_id)->where('is_active', '=', 1);

                if ($checkCartUser->count() == 1) {
                    $cart_id = $checkCartUser->first()->id;
                    $this->new_cart_item($cart_id, $customize->id, 1, $customize->price, $customize->price);
                } else {
                    Cart::insert(['user_id' => $user_id, 'is_active' => 1, 'created_at' => now()]);
                    $cart_id = Cart::where('user_id', '=', $user_id)->where('is_active', '=', 1)->first()->id;
                    $this->new_cart_item($cart_id, $customize->id, 1, $customize->price, $customize->price);
                }

                // Redirect ke halaman keranjang setelah semua proses selesai
                return redirect()->route('cart.index', [
                    'id_user' => $user_id,
                    'slug' => Str::slug(Auth::user()->name)
                ])->with('success', 'Kustomisasi berhasil ditambahkan ke keranjang!');
            }

            // Fallback / Error Handling
            return redirect()->back()->withErrors('Aksi kustomisasi tidak valid. Silakan ulangi proses.');
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
