<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MysteryBox;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MysteryBoxController extends Controller
{
    /**
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $mode = session('mode', 'Budget');
        return view('mystery_box.create', compact('mode'));
    }

    /**
     *
     * @param Request $request
     * @return
     */
    public function setBudget(Request $request)
    {
        $request->validate(['budget' => 'required']);

        session(['selectedBudget' => $request->budget, 'mode' => 'Mood']);

        Log::info('Budget set in session: ' . $request->budget . ' for session ID: ' . session()->getId());

        return redirect()->route('mysterybox');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function setMood(Request $request)
    {
        $request->validate(['mood' => 'required']);

        session([
            'selectedMood' => $request->mood,
            'mode'         => 'Done',
        ]);

        try {
            $budget          = $request->session()->get('selectedBudget');
            $mood            = $request->session()->get('selectedMood');
            $isAuthenticated = Auth::check();

            Log::info('setMood called:', [
                'request_mood'     => $request->mood,
                'session_budget'   => $budget,
                'session_mood'     => $mood,
                'is_authenticated' => $isAuthenticated,
                'session_id'       => session()->getId(),
            ]);

            // Validasi data penting
            if (empty($budget) || empty($mood) || ! $isAuthenticated) {
                $request->session()->forget(['selectedBudget', 'selectedMood', 'mode']);
                Log::warning('Mystery Box purchase failed due to incomplete session data or not logged in.', [
                    'session_budget_empty' => empty($budget),
                    'session_mood_empty'   => empty($mood),
                    'not_authenticated'    => ! $isAuthenticated,
                ]);

                return response()->json([
                    'success'  => false,
                    'message'  => ! $isAuthenticated
                    ? 'User not logged in. Please log in to complete your purchase.'
                    : 'Budget or Mood not found in session. Please complete the steps again.',
                    'redirect' => ! $isAuthenticated ? route('login') : route('mysterybox'),
                ], ! $isAuthenticated ? 401 : 400);
            }

            // Bersihkan budget dari format "Rp 25.000"
            $cleanBudget   = str_replace(['Rp ', '.', ','], ['', '', '.'], $budget);
            $numericBudget = (float) $cleanBudget;

            // Cari mystery box berdasarkan budget dan mood
            $mysteryBox = MysteryBox::where('budget', $numericBudget)
                ->where('mood', $mood)
                ->first();

            if (! $mysteryBox) {
                Log::warning('Mystery Box not found for budget and mood.', [
                    'budget' => $numericBudget,
                    'mood'   => $mood,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Mystery Box not found for the selected budget and mood.',
                ], 404);
            }

            $userId   = Auth::id();
            $quantity = 1;
            $price    = $numericBudget;

            // Buat cart jika belum ada
            $cart = Cart::firstOrCreate(
                ['user_id' => $userId],
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            );

            // Tambahkan ke cart item
            CartItem::create([
                'cart_id'       => $cart->id,
                'collection_id' => null,
                'customize_id'  => null,
                'mysterybox_id' => $mysteryBox->id,
                'quantity'      => $quantity,
                'price'         => $price,
                'total_price'   => $quantity * $price,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
                'deleted_at'    => null,
            ]);

            // Bersihkan session
            $request->session()->forget(['selectedBudget', 'selectedMood']);
            Log::info('Mystery Box successfully added to cart for user ID: ' . $userId);

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
                'message' => 'Failed to add Mystery Box to cart: An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     *
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
