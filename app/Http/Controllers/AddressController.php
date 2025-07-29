<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


// class AddressController extends Controller
// {
//     public function store(Request $request, User $user)
//     {
//         $request->validate([
//             'label' => 'required|string|max:255',
//             'provinsi' => 'required|string|max:255',
//             'kota_kabupaten' => 'required|string|max:255',
//             'kecamatan' => 'required|string|max:255',
//             'kelurahan_desa' => 'required|string|max:255',
//             'rt' => 'nullable|string|max:10', // Assuming RT/RW can be optional or formatted differently
//             'rw' => 'nullable|string|max:10',
//             'kode_pos' => 'nullable|string|max:10',
//             'address' => 'required|string|max:500',
//             'nomor_telepon' => 'number|max:20',
//         ]);

//         Address::create([
//             'user_id' => Auth::user()->id,
//             'label' => $request->input('label'),
//             'provinsi' => $request->input('provinsi'),
//             'kota_kabupaten' => $request->input('kota_kabupaten'),
//             'kecamatan' => $request->input('kecamatan'),
//             'kelurahan_desa' => $request->input('kelurahan_desa'),
//             'rt' => $request->input('rt'),
//             'rw' => $request->input('rw'),
//             'kode_pos' => $request->input('kode_pos'),
//             'address' => $request->input('address'),
//             'phone_number' => $request->input('nomor_telepon')
//         ]);

//         return redirect()->route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]);
//     }

//     public function update(Request $request, User $user, Address $address) {}

//     public function destroy(string $id)
//     {
//         $address = Address::findOrFail($id);

//         $address->delete();

//         return redirect()->route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]);
//     }
// }


namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AddressController extends Controller
{
    public function store(Request $request, User $user)
    {
        try {
            $request->validate([
                'label' => 'required|string|max:255',
                'provinsi' => 'required|string|max:255',
                'kota_kabupaten' => 'required|string|max:255',
                'kecamatan' => 'required|string|max:255',
                'kelurahan_desa' => 'required|string|max:255',
                'rt' => 'required|numeric|digits:3', // Changed to 'required'
                'rw' => 'required|numeric|digits:3', // Changed to 'required'
                'kode_pos' => 'required|numeric|digits:5', // Changed to 'required'
                'address' => 'required|string|max:255',
                'nomor_telepon' => 'required|numeric|digits_between:10,12', // Changed to 'nullable'
                'receiver_name' => 'required|string|max:255', // Added this field
            ]);

            $userId = Auth::user()->id;

            // Check if this is the first address for the user
            $isFirstAddress = Address::where('user_id', $userId)->doesntExist();

            Address::create([
                'user_id' => Auth::user()->id,
                'label' => $request->input('label'),
                'provinsi' => $request->input('provinsi'),
                'kota_kabupaten' => $request->input('kota_kabupaten'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan_desa' => $request->input('kelurahan_desa'),
                'rt' => $request->input('rt'),
                'rw' => $request->input('rw'),
                'kode_pos' => $request->input('kode_pos'),
                'address' => $request->input('address'),
                'phone_number' => $request->input('nomor_telepon'),
                'receiver_name' => $request->input('receiver_name'),
                'is_primary' => $isFirstAddress
            ]);

            return redirect()->route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)])
                ->with('success', 'Alamat berhasil ditambahkan!');
        } catch (ValidationException $e) {
            // Laravel akan otomatis redirect kembali dengan errors yang sudah di-flash.
            // Anda bisa menambahkan pesan error umum di sini jika mau,
            // tapi biasanya errors spesifik per field lebih informatif.
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Gagal menambahkan alamat. Mohon periksa kembali input Anda.');
        } catch (\Exception $e) {
            // Tangani error lain yang tidak terkait validasi
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan alamat: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user, Address $address)
    {
        // 1. Authorization: Ensure the authenticated user owns this address
        if (Auth::id() !== $address->user_id) {
            abort(403, 'Unauthorized action.'); // Or redirect with an error message
        }

        // 2. Validation: Define and apply validation rules based on your migration schema
        $request->validate([
            'label' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kota_kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan_desa' => 'required|string|max:255',
            'rt' => 'required|numeric|digits:3', // Changed to 'required'
            'rw' => 'required|numeric|digits:3', // Changed to 'required'
            'kode_pos' => 'required|numeric|digits:5', // Changed to 'required'
            'address' => 'required|string|max:255',
            'nomor_telepon' => 'required|numeric|digits_between:10,12', // Changed to 'nullable'
            'receiver_name' => 'required|string|max:255', // Added this field
        ]);

        // 3. Update the address attributes
        $address->update([
            'label' => $request->input('label'),
            'provinsi' => $request->input('provinsi'),
            'kota_kabupaten' => $request->input('kota_kabupaten'),
            'kecamatan' => $request->input('kecamatan'),
            'kelurahan_desa' => $request->input('kelurahan_desa'),
            'rt' => $request->input('rt'),
            'rw' => $request->input('rw'),
            'kode_pos' => $request->input('kode_pos'),
            'address' => $request->input('address'),
            'phone_number' => $request->input('nomor_telepon'), // Ensure this matches the input name from your form
            'receiver_name' => $request->input('receiver_name'), // Added this field
        ]);

        // 4. Redirect back to the profile page with a success message
        return redirect()->route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)])
            ->with('success', 'Address updated successfully!');
    }

    public function destroy(string $id)
    {
        $address = Address::findOrFail($id);

        // Authorization: Ensure the authenticated user owns this address before deleting
        if (Auth::id() !== $address->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $address->delete();

        return redirect()->route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)])
            ->with('success', 'Address deleted successfully!');
    }

    public function togglePrimary(Request $request, Address $address)
    {
        // 1. Otorisasi: Pastikan pengguna yang diautentikasi adalah pemilik alamat ini
        if (Auth::id() !== $address->user_id) {
            Log::warning('Unauthorized attempt to toggle primary address.', ['user_id' => Auth::id(), 'address_id' => $address->id]);
            return response()->json(['success' => false, 'message' => 'Anda tidak diizinkan mengubah alamat ini.'], 403);
        }

        try {
            // 2. Validasi: Validasi input status dari request AJAX
            $request->validate([
                'status' => 'required|in:primary,not-primary', // Hanya menerima 'primary' atau 'not-primary'
            ]);

            // Konversi status string dari request ke boolean (true jika 'primary')
            $newPrimaryStatus = ($request->status === 'primary');

            // Logika penting: Jika alamat ini akan dijadikan utama,
            // nonaktifkan semua alamat utama lainnya milik user ini.
            if ($newPrimaryStatus) {
                Address::where('user_id', Auth::id())
                    ->where('is_primary', true)
                    ->where('id', '!=', $address->id) // Kecualikan alamat yang sedang diupdate
                    ->update(['is_primary' => false]);
            } else {
                // Opsional: Jika Anda ingin mencegah semua alamat menjadi non-utama
                // Misalnya, jika mencoba menonaktifkan alamat utama dan itu satu-satunya alamat.
                // Atau, Anda bisa membiarkan user menghapus alamat utama saja.
                // Untuk kesederhanaan, kita hanya berfokus pada menjadikan satu utama.
                // Jika is_primary diset false dari request, itu berarti toggle dari js ke non-utama,
                // tapi design kita di js selalu kirim "primary"
            }

            // Perbarui status is_primary alamat saat ini
            $address->is_primary = $newPrimaryStatus;
            $address->save();

            // Kembalikan respons JSON sukses ke JavaScript
            return response()->json([
                'success'    => true,
                'is_primary' => $address->is_primary, // Kirim status baru kembali
                'message'    => 'Status alamat berhasil diperbarui!'
            ]);
        } catch (ValidationException $e) {
            // Jika validasi gagal (misalnya status tidak valid)
            Log::error('Validation error toggling address primary status: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $e->getMessage(),
                'errors'  => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            // Tangani error lainnya yang tidak terduga
            Log::error('Error toggling address primary status: ' . $e->getMessage(), ['address_id' => $address->id]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status alamat.'
            ], 500); // Internal Server Error
        }
    }
}
