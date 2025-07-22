<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MysteryBoxController extends Controller
{
    public function index(Request $request)
    {
        $mode = session('mode', 'Budget');
        return view('mystery_box.create', compact('mode'));
    }

    public function setBudget(Request $request)
    {
        $request->validate(['budget' => 'required']);
        session(['budget' => $request->budget, 'mode' => 'Mood']);
        return redirect()->route('mysterybox');
    }

    public function setMood(Request $request)
    {
        $request->validate(['mood' => 'required']);
        session(['mood' => $request->mood, 'mode' => 'Done']);
        return redirect()->route('mysterybox');
    }

    public function reset(Request $request)
    {
        $request->session()->forget(['budget', 'mood', 'mode']);
        return response()->json(['message' => 'Session reset.']);
    }
}
