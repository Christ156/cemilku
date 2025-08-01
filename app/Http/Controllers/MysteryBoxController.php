<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MysteryBox;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Tambahkan ini untuk Str::title()

class MysteryBoxController extends Controller
{
    /**
     * Menampilkan halaman pembuatan Mystery Box berdasarkan mode (Budget/Mood).
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $mode = session('mode', 'Budget');
        return view('mystery_box.create', compact('mode'));
    }

    /**
     * Memproses pemilihan budget untuk Mystery Box.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setBudget(Request $request)
    {
        $request->validate(['budget' => 'required']);

        session(['selectedBudget' => $request->budget, 'mode' => 'Mood']);

        Log::info('Budget set in session: ' . $request->budget . ' for session ID: ' . session()->getId());

        return redirect()->route('mysterybox'); // Redirect ke halaman yang sama untuk update UI ke mode Mood
    }

    /**
     * Memproses pemilihan mood dan menambahkan Mystery Box ke keranjang.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setMood(Request $request)
    {
        $request->validate([
            'mood' => 'required|string',
        ]);

        try {
            $budget          = $request->session()->get('selectedBudget');
            $rawMood         = $request->input('mood'); // Ambil mood dari request
            $isAuthenticated = Auth::check();

            Log::info('setMood called:', [
                'request_mood_raw' => $rawMood,
                'session_budget'   => $budget,
                'is_authenticated' => $isAuthenticated,
                'session_id'       => session()->getId(),
            ]);

            // Validasi data penting
            if (empty($budget) || empty($rawMood) || !$isAuthenticated) {
                $request->session()->forget(['selectedBudget', 'selectedMood', 'mode']); // Bersihkan session juga
                Log::warning('Mystery Box purchase failed due to incomplete session data or not logged in.', [
                    'session_budget_empty' => empty($budget),
                    'session_mood_empty'   => empty($rawMood),
                    'not_authenticated'    => !$isAuthenticated,
                ]);

                return response()->json([
                    'success'  => false,
                    'message'  => !$isAuthenticated
                                        ? 'User not logged in. Please log in to complete your purchase.'
                                        : 'Budget or Mood not found. Please complete the steps again.',
                    'redirect' => !$isAuthenticated ? route('login') : route('mysterybox'),
                ], !$isAuthenticated ? 401 : 400);
            }

            // PERBAIKAN PENTING: Pembersihan dan konversi budget
            // Berdasarkan `create_mystery_boxes_table.php`, kolom 'budget' adalah DECIMAL(10,2).
            // Input dari frontend adalah "Rp X.XXX,XX"
            $cleanBudget = str_replace(['Rp ', '.'], '', $budget); // Hapus 'Rp ' dan titik ribuan
            $cleanBudget = str_replace(',', '.', $cleanBudget);   // Ganti koma desimal dengan titik desimal
            $numericBudget = (float) $cleanBudget;                // Konversi ke float untuk mencocokkan DECIMAL

            // PERBAIKAN: Pastikan kapitalisasi mood cocok dengan ENUM di database
            // Contoh: Mengubah "romantic" menjadi "Romantic"
            $mood = Str::title($rawMood);

            // CARI MYSTERY BOX BERDASARKAN BUDGET DAN MOOD YANG TERPILIH
            $mysteryBox = MysteryBox::where('budget', $numericBudget)
                                           ->where('mood', $mood)
                                           ->first();

            if (!$mysteryBox) {
                Log::warning('Mystery Box not found for the selected budget and mood.', [
                    'budget_searched' => $numericBudget,
                    'mood_searched'   => $mood,
                    'db_budget_type'  => 'DECIMAL(10,2)', // Tambahkan informasi tipe DB untuk debugging
                    'db_mood_enum'    => '["Romantic", "Mysterious", "Funny", "Brave", "Calm", "Happy"]', // Tambahkan info enum
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Mystery Box not found for the selected budget and mood. Please contact support if the issue persists.',
                ], 404);
            }

            // ======================================================================
            // Logika Penambahan ke Cart
            // ======================================================================
            $userId   = Auth::id();
            $quantity = 1;
            $price    = $numericBudget; // Harga diambil dari budget yang dipilih

            // *** START PERUBAHAN PENTING DI SINI ***
            // Dapatkan keranjang aktif atau buat jika belum ada, dengan is_active = 1
            $cart = Cart::firstOrCreate(
                ['user_id' => $userId, 'is_active' => 1], // Tambahkan is_active = 1
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            );

            // Periksa apakah Mystery Box ini sudah ada di keranjang user
            $cartItem = CartItem::where('cart_id', $cart->id)
                                 ->where('mysterybox_id', $mysteryBox->id)
                                 ->first();

            if ($cartItem) {
                // Jika item sudah ada, update kuantitas (jika Anda ingin memungkinkan multiple identical MB)
                $cartItem->quantity += 1;
                $cartItem->total_price = $cartItem->quantity * $price;
                $cartItem->save();
                Log::info('Updated existing Mystery Box item in cart:', [
                    'cart_item_id' => $cartItem->id,
                    'cart_id' => $cart->id,
                    'mysterybox_id' => $mysteryBox->id,
                    'new_quantity' => $cartItem->quantity,
                    'new_total_price' => $cartItem->total_price,
                ]);
            } else {
                // Jika belum ada, tambahkan sebagai item baru di keranjang
                $newCartItem = CartItem::create([
                    'cart_id'       => $cart->id,
                    'collection_id' => null, // Pastikan ini null untuk Mystery Box
                    'customize_id'  => null, // Pastikan ini null untuk Mystery Box
                    'mysterybox_id' => $mysteryBox->id,
                    'quantity'      => $quantity,
                    'price'         => $price,
                    'total_price'   => $quantity * $price,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]);
                Log::info('Added new Mystery Box item to cart:', [
                    'cart_item_id' => $newCartItem->id,
                    'cart_id' => $cart->id,
                    'mysterybox_id' => $mysteryBox->id,
                    'quantity' => $newCartItem->quantity,
                    'total_price' => $newCartItem->total_price,
                ]);
            }
            // *** END PERUBAHAN PENTING DI SINI ***

            // Bersihkan session setelah berhasil ditambahkan ke keranjang
            $request->session()->forget(['selectedBudget', 'selectedMood', 'mode']);
            // Log ini sekarang akan lebih akurat karena sudah di dalam block if/else di atas
            // Log::info('Mystery Box successfully added to cart for user ID: ' . $userId . ' MysteryBox ID: ' . $mysteryBox->id);

            return response()->json([
                'success' => true,
                'message' => 'Mystery Box successfully added to your cart!',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to add Mystery Box to cart:', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add Mystery Box to cart: An unexpected error occurred. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mereset session Mystery Box.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->session()->forget(['selectedBudget', 'selectedMood', 'mode']);
        Log::info('Mystery Box session reset.');
        return response()->json(['message' => 'Session reset.']);
    }
}
