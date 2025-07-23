<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Cart;
use Carbon\Carbon;
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

        session(['selectedMood' => $request->mood, 'mode' => 'Done']);

        try {
            $budget = $request->session()->get('selectedBudget');
            $mood = $request->session()->get('selectedMood');
            $isAuthenticated = Auth::check();

            Log::info('setMood called:', [
                'request_mood' => $request->mood,
                'session_budget' => $budget,
                'session_mood' => $mood,
                'is_authenticated' => $isAuthenticated,
                'session_id' => session()->getId()
            ]);

            if (empty($budget) || empty($mood) || !$isAuthenticated) {
                $request->session()->forget(['selectedBudget', 'selectedMood', 'mode']);
                Log::warning('Mystery Box purchase failed due to incomplete session data or not logged in.', [
                    'session_budget_empty' => empty($budget),
                    'session_mood_empty' => empty($mood),
                    'not_authenticated' => !$isAuthenticated
                ]);

                if (!$isAuthenticated) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not logged in. Please log in to complete your purchase.',
                        'redirect' => route('login')
                    ], 401); // 401 Unauthorized
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Budget or Mood not found in session, or user not logged in. Please complete the steps or log in.',
                        'redirect' => route('mysterybox')
                    ], 400); // 400 Bad Request
                }
            }

            $cleanBudget = str_replace(['Rp ', '.'], '', $budget);
            $cleanBudget = str_replace(',', '.', $cleanBudget);
            $numericBudget = (float) $cleanBudget;

            $mysteryBoxProductId = $request->input('mystery_box_id') ?? 1;
            $quantity = 1;
            $price = $numericBudget;
            $userId = Auth::id();

            $cart = Cart::firstOrCreate(
                ['user_id' => $userId],
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            );

            $userCartId = $cart->id;

            CartItem::create([
                'cart_id' => $userCartId,
                'collection_id' => null,
                'customize_id' => null,
                'mysterybox_id' => $mysteryBoxProductId,
                'quantity' => $quantity,
                'price' => $price,
                'total_price' => $quantity * $price,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ]);

            $request->session()->forget(['selectedBudget', 'selectedMood']);
            Log::info('Mystery Box successfully added to cart for user ID: ' . $userId);

            return response()->json(['success' => true, 'message' => 'Mystery Box successfully added to your cart!']);

        } catch (\Exception $e) {
            Log::error('Failed to add Mystery Box to cart:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to add Mystery Box to cart: An unexpected error occurred. Please try again later.'
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
