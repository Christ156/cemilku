<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user ke halaman admin.
     */
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $users = User::where('role', 'user')->get(); // Hanya user biasa
            return view('admin.user.index', compact('users'));
        }

        abort(403, 'Unauthorized'); // Untuk selain admin
    }

    /**
     * Menampilkan detail user (alamat dan lainnya).
     */
    public function show(string $id, string $slug)
    {
        $address = Address::where('user_id', $id)->get();
        return view('profile.index', compact('address'));
    }

    /**
     * Mengupdate informasi user.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($request->name) {
            $user->name = $request->name;
            $user->gender = Auth::user()->gender;
            $user->date_of_birth = Auth::user()->date_of_birth;
            $user->email = Auth::user()->email;
            $user->phone_number = Auth::user()->phone_number;
        }
        else{
            $user->name = Auth::user()->name;
            $user->gender = $request->gender;
            $user->date_of_birth = $request->dateofbirth;
            $user->email         = $request->email;
            $user->phone_number  = $request->telepon;
        }

        // if($request->profile_image != NULL){
        //     if(file_exists(public_path('assets\profile', $user->profile_image))){
        //         File::delete(public_path('assets/profile'. $user->profile_image))
        //     }

        //     $profile_image_name = 'profile'.Str::slug(Auth::user()->name()).Str::slug(now()).'.'.$request->file('profile_image')->getClientOriginalExtension();
        //     $request->profile_image->move(public_path('assets\profile'), $profile_image_name);
        //     $user->profile_image = $profile_image_name;
        // }

        if ($request->hasFile('profile_image')) { // Gunakan hasFile untuk memeriksa apakah ada file yang diupload
            $oldProfileImage = $user->profile_image; // Simpan nama file lama

            // Hapus gambar profil lama jika ada dan file-nya eksis
            if ($oldProfileImage && File::exists(public_path('assets/profile/' . $oldProfileImage))) {
                File::delete(public_path('assets/profile/' . $oldProfileImage));
            }

            // Buat nama unik untuk gambar profil baru
            // Gabungkan nama pengguna (jika unik) dan timestamp untuk keunikan
            $profile_image_name = 'profile-' . Str::slug(Auth::user()->name) . '-' . time() . '.' . $request->file('profile_image')->getClientOriginalExtension();

            // Pindahkan file yang diupload
            $request->file('profile_image')->move(public_path('assets/profile'), $profile_image_name);

            // Update path gambar di database
            $user->profile_image = $profile_image_name;
        }





        $user->save();

        return redirect()->route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]);
    }

    /**
     * Mengekspor data user ke file Excel.
     */
    public function export()
    {
        return Excel::download(new UserExport, 'user.xlsx');
    }

    // Metode lainnya (create, store, edit, destroy) bisa ditambahkan nanti jika dibutuhkan
}
