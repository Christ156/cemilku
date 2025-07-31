<?php
namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Models\ActivityLog;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user ke halaman admin.
     */
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            // $users = User::where('role', 'user')->get(); // Hanya user biasa
            $users = User::all();
            $user_logs = Activity::all();
            return view('admin.user.index', compact('users', 'user_logs'));
        }

        abort(403, 'Unauthorized'); // Untuk selain admin
    }

    /**
     * Menampilkan detail user (alamat dan lainnya).
     */
    public function show(string $id, string $slug)
    {
        if (Auth::user()->role == 'admin') {
            $user = User::findOrFail($id);
            $logs = Activity::where('causer_id', $id)->get();

            return \view('admin.user.show', \compact(['user', 'logs']));
        } else {
            $address = Address::where('user_id', $id)->get();
            return view('profile.index', compact('address'));
        }
    }

    /**
     * Mengupdate informasi user.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            // 2. Validasi: Definisikan aturan validasi untuk pembaruan profil.
            // Aturan ini dibuat agar sesuai dengan input yang Anda gunakan dalam if/else di bawah.
            $request->validate([
                'name' => 'sometimes|required|string|max:255', // Nama bisa opsional jika hanya update gender dll.
                'gender' => 'nullable', // Sesuaikan opsi gender Anda
                'dateofbirth' => 'nullable|date', // Sesuaikan dengan nama input 'dateofbirth'
                'email' => [
                    'sometimes',
                    'email', // Pastikan format email dasar (@ dan .)
                    'max:255',
                    'unique:users,email,' . $user->id, // Email harus unik, kecuali untuk pengguna ini sendiri
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', // Regex untuk validasi domain yang lebih ketat
                ],
                'telepon' => 'sometimes|numeric|digits_between:10,12', // Sesuaikan dengan nama input 'telepon'
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp', // Maksimal 2MB
            ]);

            // if ($request->name) {
            //     $user->name = $request->name;
            //     $user->gender = Auth::user()->gender;
            //     $user->date_of_birth = Auth::user()->date_of_birth;
            //     $user->email = Auth::user()->email;
            //     $user->phone_number = Auth::user()->phone_number;
            // } else {
            //     $user->name = Auth::user()->name;
            //     $user->gender = $request->gender;
            //     $user->date_of_birth = $request->dateofbirth;
            //     $user->email = $request->email;
            //     $user->phone_number = $request->telepon;
            // }

            if ($request->filled('name')) {
                $user->name = $request->name;
            }

            if ($request->filled('gender')) {
                $user->gender = $request->gender;
            }

            if ($request->filled('dateofbirth')) {
                $user->date_of_birth = $request->dateofbirth;
            }

            if ($request->filled('email')) {
                $user->email = $request->email;
            }

            if ($request->filled('telepon')) {
                $user->phone_number = $request->telepon;
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
                $profile_image_name = 'profile-' . Str::slug($user->name ?: 'user') . '-' . time() . '.' . $request->file('profile_image')->getClientOriginalExtension();

                // Pindahkan file yang diupload
                $request->file('profile_image')->move(public_path('assets/profile'), $profile_image_name);

                // Update path gambar di database
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
     * Mengekspor data user ke file Excel.
     */
    public function export()
    {
        return Excel::download(new UserExport, 'user.xlsx');
    }

    public function block($id)
    {
        $user             = User::findOrFail($id);
        $user->is_blocked = true;
        $user->save();

        return redirect()->back()->with('success', 'User berhasil diblokir.');
    }

    public function toggleBlock($id)
    {
        $user             = User::findOrFail($id);
        $user->is_blocked = ! $user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? 'diblokir' : 'diaktifkan kembali';
        return redirect()->back()->with('success', "User berhasil $status.");
    }
}

