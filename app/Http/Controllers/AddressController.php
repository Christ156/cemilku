<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class AddressController extends Controller
{
    /**
     * Mengambil daftar alamat untuk user tertentu.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAddressesApi(User $user)
    {
        if (Auth::id() !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Anda tidak memiliki akses ke alamat pengguna ini.'
            ], 403);
        }

        try {
            $addresses = Address::where('user_id', $user->id)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'addresses' => $addresses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil alamat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan alamat baru untuk pengguna yang diidentifikasi oleh {user} di URL.
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, User $user)
    {
        // PENTING: Lapisan keamanan! Pastikan user yang diminta adalah user yang sedang login.
        // if (Auth::id() !== $user->id) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized: Anda tidak dapat menambahkan alamat untuk pengguna lain.'
        //     ], 403);
        // }

        // try {
        //     $validatedData = $request->validate([
        //         'label' => 'required|string|max:255',
        //         'provinsi' => 'required|string|max:255',
        //         'kota_kabupaten' => 'required|string|max:255',
        //         'kecamatan' => 'required|string|max:255',
        //         'kelurahan_desa' => 'required|string|max:255',
        //         'rt' => 'nullable|string|max:3',
        //         'rw' => 'nullable|string|max:3',
        //         'kode_pos' => 'required|string|max:10',
        //         'address' => 'required|string',
        //         'is_primary' => 'boolean',
        //         'receiver_name' => 'required|string|max:255',
        //         'phone_number' => 'required|string|max:20',
        //     ]);
        // } catch (ValidationException $e) {
        //     // Jika validasi gagal, selalu kembalikan JSON error karena ini adalah API
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Validasi gagal',
        //         'errors' => $e->errors()
        //     ], 422);
        // }

        // // Jika alamat baru akan dijadikan utama, set is_primary alamat lain menjadi false
        // if (isset($validatedData['is_primary']) && $validatedData['is_primary']) {
        //     Address::where('user_id', $user->id)
        //         ->update(['is_primary' => false]);
        // }

        // $address = Address::create(array_merge($validatedData, [
        //     'user_id' => $user->id,
        // ]));

        // // PERBAIKAN: Selalu kembalikan JSON response untuk API store
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Alamat berhasil ditambahkan!',
        //     'address' => $address
        // ], 201);

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
        ]);

        return redirect()->route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]);
    }

    /**
     * Memperbarui alamat yang sudah ada untuk pengguna yang diidentifikasi oleh {user} di URL.
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @param  \App\Models\Address $address
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user, Address $address)
    {
        if (Auth::id() !== $user->id || $address->user_id !== $user->id) {
            // Selalu kembalikan JSON error untuk API
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Alamat tidak milik user ini atau Anda tidak memiliki izin.'
            ], 403);
        }

        try {
            $validatedData = $request->validate([
                'label' => 'required|string|max:255',
                'provinsi' => 'required|string|max:255',
                'kota_kabupaten' => 'required|string|max:255',
                'kecamatan' => 'required|string|max:255',
                'kelurahan_desa' => 'required|string|max:255',
                'rt' => 'nullable|string|max:3',
                'rw' => 'nullable|string|max:3',
                'kode_pos' => 'required|string|max:10',
                'address' => 'required|string',
                'is_primary' => 'boolean',
                'receiver_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
            ]);
        } catch (ValidationException $e) {
            // Selalu kembalikan JSON error untuk API
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }

        if (isset($validatedData['is_primary']) && $validatedData['is_primary']) {
            Address::where('user_id', $user->id)
                ->where('id', '!=', $address->id)
                ->update(['is_primary' => false]);
        }

        $address->update($validatedData);

        // Selalu kembalikan JSON response untuk API update
        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diperbarui!',
            'address' => $address
        ]);
    }

    /**
     * Menetapkan alamat sebagai alamat utama (primary) untuk pengguna yang diidentifikasi oleh {user} di URL.
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @param  \App\Models\Address $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPrimary(Request $request, User $user, Address $address)
    {
        if (Auth::id() !== $user->id || $address->user_id !== $user->id) {
            // Selalu kembalikan JSON error untuk API
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Alamat tidak milik user ini atau Anda tidak memiliki izin.'
            ], 403);
        }

        try {
            Address::where('user_id', $user->id)->update(['is_primary' => false]);
            $address->is_primary = true;
            $address->save();

            // Selalu kembalikan JSON response untuk API setPrimary
            return response()->json([
                'success' => true,
                'message' => 'Alamat utama berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengatur alamat utama: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus alamat yang dipilih oleh pengguna yang diidentifikasi oleh {user} di URL.
     * @param  \App\Models\User $user
     * @param  \App\Models\Address $address
     * @return \Illuminate\Http\JsonResponse
     */
    // User $user, Address $address
    public function destroy(string $id)
    {
        // if (Auth::id() !== $user->id || $address->user_id !== $user->id) {
        //     // Selalu kembalikan JSON error untuk API
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Akses ditolak: Alamat tidak milik user ini atau Anda tidak memiliki izin.'
        //     ], 403);
        // }

        // if ($address->is_primary) {
        //     $otherAddress = Address::where('user_id', $user->id)
        //         ->where('id', '!=', $address->id)
        //         ->first();
        //     if ($otherAddress) {
        //         $otherAddress->update(['is_primary' => true]);
        //     }
        // }

        // $address->delete();

        // // Selalu kembalikan JSON response untuk API destroy
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Alamat berhasil dihapus!'
        // ]);

        $address = Address::findOrFail($id);

        $address->delete();

        return redirect()->route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]);
    }
}
