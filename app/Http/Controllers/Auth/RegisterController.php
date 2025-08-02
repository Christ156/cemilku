<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => 'required',
            'phone_num' => ['required', 'numeric', 'digits_between:10,12'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'date_of_birth' => $data['birth_date'],
            'email' => $data['email'],
            'phone_number' => $data['phone_num'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function google_redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function google_callback(){
        $googleUser = Socialite::driver('google')->user();
        $user = User::whereEmail($googleUser->email)->first();

        if(!$user){
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'date_of_birth' => now(),
                'profile_image' => $googleUser->avatar,
                'password' => Hash::make($googleUser->id),
                'created_at' => now(),
            ]);
        }

        Auth::login($user);
        return redirect()->route('home');
    }
}
