<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role == "admin") {
            return view('admin.user.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $slug)
    {
        $address = Address::where('user_id', $id)->get();
        return view('profile.index', compact('address'));
    }

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
        try {
            $user = User::findOrFail($id);

            // 2. Validasi: Definisikan aturan validasi untuk pembaruan profil.
            // Aturan ini dibuat agar sesuai dengan input yang Anda gunakan dalam if/else di bawah.
            $request->validate([
                'name' => 'nullable|string|min:1|max:255', // Nama bisa opsional jika hanya update gender dll.
                'gender' => 'nullable|in:Laki-laki,Perempuan', // Sesuaikan opsi gender Anda
                'dateofbirth' => 'nullable|date', // Sesuaikan dengan nama input 'dateofbirth'
                'email' => 'nullable|email|max:255|unique:users,email,' . $user->id, // Email harus unik, kecuali untuk pengguna ini sendiri
                'telepon' => 'nullable|string|min:1|max:20', // Sesuaikan dengan nama input 'telepon'
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg', // Maksimal 2MB
            ]);

            if ($request->name) {
                $user->name = $request->name;
                $user->gender = Auth::user()->gender;
                $user->date_of_birth = Auth::user()->date_of_birth;
                $user->email = Auth::user()->email;
                $user->phone_number = Auth::user()->phone_number;
            } else {
                $user->name = Auth::user()->name;
                $user->gender = $request->gender;
                $user->date_of_birth = $request->dateofbirth;
                $user->email = $request->email;
                $user->phone_number = $request->telepon;
            }

            // Tangani upload gambar profil
            if ($request->hasFile('profile_image')) {
                $oldProfileImage = $user->profile_image;

                // Hapus gambar profil lama jika ada dan file-nya eksis
                if ($oldProfileImage && File::exists(public_path('assets/profile/' . $oldProfileImage))) {
                    File::delete(public_path('assets/profile/' . $oldProfileImage));
                }

                // Buat nama unik untuk gambar profil baru
                $profile_image_name = 'profile-' . Str::slug($user->name ?: 'user') . '-' . time() . '.' . $request->file('profile_image')->getClientOriginalExtension();

                // Pindahkan file yang diupload
                $request->file('profile_image')->move(public_path('assets/profile'), $profile_image_name);

                // Perbarui path gambar di database
                $user->profile_image = $profile_image_name;
            }

            $user->save();

            // Redirect ke halaman profil dengan pesan sukses
            return redirect()->route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)])
                             ->with('success', 'Profil berhasil diperbarui!');

        } catch (ValidationException $e) {
            // Jika validasi gagal, redirect kembali dengan error dan input lama.
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput()
                             ->with('error', 'Gagal memperbarui profil. Mohon periksa kembali input Anda.');
        } catch (\Exception $e) {
            // Tangani error lain yang tidak terkait validasi
            return redirect()->back()
                             ->with('error', 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
