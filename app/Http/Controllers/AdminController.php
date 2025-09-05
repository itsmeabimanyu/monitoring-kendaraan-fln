<?php

namespace App\Http\Controllers;

use Image;
use App\Models\User;
use App\Models\Kendaraan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\KendaraanCrud;
use Illuminate\Support\Carbon;
use App\Models\HistoryKendaraan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function historyKendaraan()
    {
        return view('admin.history');
    }

    public function getDatahistoryKendaraan()
    {
        $data = HistoryKendaraan::with('kendaraan')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                $namaMobil = $item->kendaraan->nama_mobil ?? '-';
                $nopol = $item->kendaraan->nopol ?? '-';
                $status = $item->status ?? '-';
                $catatan_perbaikan = $item->catatan_perbaikan ?? '';

                $item->mobil = $namaMobil . '<br>(' . $nopol . ')';
                $item->pemakai = ($item->nama_pemakai ?? '') . ' <br> ' . ($item->departemen ?? '');
                $item->status = $status;
                $item->catatan_perbaikan = $catatan_perbaikan;

                // Default 0 kalau null
                $km_awal  = $item->km_awal ?? 0;
                $km_akhir = $item->km_akhir ?? 0;
                $selisih  = $km_akhir - $km_awal;

                if (strtolower($status) === 'pergi') {
                    $item->total_km = "<div>
                    <small><em>Km Awal</em></small><br>
                    {$km_awal}
                </div>";
                } elseif (strtolower($status) === 'stand by') {
                    $item->total_km = "<div>
                    <small><em>Km Awal - Km Akhir</em></small><br>
                    {$km_awal} - {$km_akhir}<br>
                    <strong>= {$selisih} km</strong>
                </div>";
                } else {
                    $item->total_km = "<div>
                    <small><em>Km Awal - Km Akhir</em></small><br>
                    {$km_awal} - {$km_akhir}<br>
                    <strong>= {$selisih} km</strong>
                </div>";
                }

                return $item;
            });

        return response()->json($data);
    }

    public function listKendaraan()
    {
        $kendaraans = Kendaraan::all();
        return view('admin.kendaraan', compact('kendaraans'));
    }

    public function getDataKendaraan()
    {
        $data = Kendaraan::select('id', 'nama_mobil', 'nopol', 'gambar_mobil')
            ->orderBy('created_at', 'desc')
            ->where('isActive', 1)
            ->get();

        $data->transform(function ($item) {
            $item->gambar_url = $item->gambar_mobil
                ? asset('storage/mobil/' . $item->gambar_mobil)
                : asset('storage/mobil/default.jpg');

            return $item;
        });

        return response()->json($data);
    }

    public function tambahKendaraan(Request $request)
    {
        $request->validate([
            'nama_mobil' => 'required|string|max:255',
            'nopol' => 'required|string|max:255',
            'gambar_mobil' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'gambar_mobil.max' => 'Error : Ukuran gambar maksimal 2MB.',
            'gambar_mobil.mimes' => 'Error : Format gambar harus jpeg, png, atau jpg.',
            'gambar_mobil.image' => 'Error : File yang diunggah harus berupa gambar.',
        ]);

        $nama_mobil = ucwords(strtolower($request->nama_mobil));
        $nopol = strtoupper($request->nopol);

        // Cek apakah sudah ada kendaraan dengan nopol aktif
        $aktif = Kendaraan::where('nopol', $nopol)->where('isActive', 1)->first();
        if ($aktif) {
            return back()->withErrors(['nopol' => 'Error : Nopol kendaraan <strong>' . $request->nopol . '</strong> telah tersedia!'])->withInput();
        }

        // Cek apakah ada kendaraan nonaktif (soft delete)
        $nonaktif = Kendaraan::where('nopol', $nopol)->where('isActive', 0)->first();

        // Proses gambar
        $file = $request->file('gambar_mobil');
        $extension = $file->getClientOriginalExtension();
        $filename = "{$nama_mobil}_{$nopol}.{$extension}";

        $image = Image::make($file->getPathname());
        $image->resize(1024, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $tempPath = sys_get_temp_dir() . '/' . $filename;
        $quality = 90;
        $image->save($tempPath, $quality);

        while (filesize($tempPath) > 1024 * 1024 && $quality >= 30) {
            $quality -= 5;
            $image->save($tempPath, $quality);
        }

        Storage::disk('public')->put("mobil/{$filename}", file_get_contents($tempPath));
        unlink($tempPath);

        // Simpan ke DB
        if ($nonaktif) {
            // Update data lama (revive)
            $nonaktif->update([
                'nama_mobil' => $nama_mobil,
                'gambar_mobil' => $filename,
                'status' => 'Stand By',
                'isActive' => 1,
            ]);
            $kendaraan = $nonaktif;
        } else {
            // Buat data baru
            $kendaraan = Kendaraan::create([
                'nama_mobil' => $nama_mobil,
                'nopol' => $nopol,
                'gambar_mobil' => $filename,
                'status' => 'Stand By',
                'isActive' => 1,
            ]);
        }

        event(new KendaraanCrud('add', $kendaraan->id, [
            'id' => $kendaraan->id,
            'nama_mobil' => $kendaraan->nama_mobil,
            'nopol' => $kendaraan->nopol,
            'status' => $kendaraan->status,
            'updated_at' => $kendaraan->updated_at,
            'image_path' => asset('storage/mobil/' . $kendaraan->gambar_mobil),
        ]));


        return redirect('/kendaraan')->with('success', "Kendaraan <strong>{$kendaraan->nama_mobil} {$kendaraan->nopol}</strong> berhasil ditambahkan!");
    }


    public function editKendaraan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:kendaraans,id',
            'nama_mobil' => 'required|string|max:255',
            'nopol' => 'required|string|max:255|unique:kendaraans,nopol,' . $request->id,
            'gambar_mobil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nopol.unique' => 'Error : Nopol kendaraan <strong>' . $request->nopol . '</strong> telah terdaftar sebelumnya.',
            'gambar_mobil.max' => 'Error : Ukuran gambar maksimal 2MB.',
            'gambar_mobil.mimes' => 'Error : Format gambar harus jpeg, png, atau jpg.',
            'gambar_mobil.image' => 'Error : File yang diunggah harus berupa gambar.',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->id);

        $oldNama = $kendaraan->nama_mobil;
        $oldNopol = $kendaraan->nopol;

        $nama_mobil = ucwords(strtolower($request->nama_mobil));
        $nopol = strtoupper($request->nopol);

        $gambarPath = $kendaraan->gambar_mobil;

        // Upload gambar baru
        if ($request->hasFile('gambar_mobil')) {
            $file = $request->file('gambar_mobil');
            $extension = $file->getClientOriginalExtension();
            $filename = "{$nama_mobil}_{$nopol}." . $extension;

            $image = \Image::make($file->getPathname());
            $image->resize(1024, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $tempPath = sys_get_temp_dir() . '/' . $filename;
            $quality = 90;
            $image->save($tempPath, $quality);

            while (filesize($tempPath) > 1024 * 1024 && $quality >= 30) {
                $quality -= 5;
                $image->save($tempPath, $quality);
            }

            // Hapus gambar lama hanya jika nama & nopol tidak berubah
            if (
                $oldNama === $nama_mobil &&
                $oldNopol === $nopol &&
                $kendaraan->gambar_mobil &&
                Storage::disk('public')->exists("mobil/{$kendaraan->gambar_mobil}")
            ) {
                Storage::disk('public')->delete("mobil/{$kendaraan->gambar_mobil}");
            }

            Storage::disk('public')->put("mobil/{$filename}", file_get_contents($tempPath));
            unlink($tempPath);

            $gambarPath = $filename;
        } else {
            // Tidak upload gambar, cek apakah perlu rename
            if ($gambarPath) {
                $oldPath = "mobil/{$gambarPath}";
                $extension = pathinfo($gambarPath, PATHINFO_EXTENSION);
                $newFilename = "{$nama_mobil}_{$nopol}.{$extension}";
                $newPath = "mobil/{$newFilename}";

                if ($gambarPath !== $newFilename && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->move($oldPath, $newPath);
                    $gambarPath = $newFilename;
                }
            }
        }

        $kendaraan->update([
            'nama_mobil' => $nama_mobil,
            'nopol' => $nopol,
            'gambar_mobil' => $gambarPath,
        ]);

        event(new KendaraanCrud('edit', $kendaraan->id, [
            'id' => $kendaraan->id,
            'nama_mobil' => $kendaraan->nama_mobil,
            'nopol' => $kendaraan->nopol,
            'status' => $kendaraan->status,
            'updated_at' => $kendaraan->updated_at,
            'image_path' => asset('storage/mobil/' . $kendaraan->gambar_mobil) . '?' . now()->timestamp,
        ]));

        return redirect('/kendaraan')->with('success', "Kendaraan berhasil diubah!");
    }

    public function hapusKendaraan($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        // Hapus file gambar jika ada
        if ($kendaraan->gambar_mobil && Storage::disk('public')->exists('mobil/' . $kendaraan->gambar_mobil)) {
            Storage::disk('public')->delete('mobil/' . $kendaraan->gambar_mobil);
        }

        $kendaraan->isActive = 0;
        $kendaraan->save();

        event(new KendaraanCrud('delete', $kendaraan->id));

        return redirect('/kendaraan')->with('success', "Kendaraan <strong>{$kendaraan->nama_mobil} {$kendaraan->nopol}</strong> berhasil dihapus!");
    }

    public function sembunyikanKendaraan(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        // Toggle nilai
        $kendaraan->isVisible = !$kendaraan->isVisible;
        $kendaraan->save();

        // Tentukan pesan berdasarkan hasil toggle
        $statusTextToggle = $kendaraan->isVisible
            ? "ditampilkan"
            : "disembunyikan";

        return redirect('/kendaraan')->with(
            'success',
            "Kendaraan <strong>{$kendaraan->nama_mobil} {$kendaraan->nopol}</strong> berhasil {$statusTextToggle}!"
        );
    }




    // =============================== USER =========================================================
    public function listUsers()
    {
        $users = User::all();
        $jabatanList = ['Admin GA', 'Staff GA', 'Security'];
        return view('admin.users', compact('users', 'jabatanList'));
    }

    public function getDataUsers()
    {
        $data = User::where('isActive', 1)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($data);
    }

    public function tambahUsers(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'username' => 'required|unique:users,username|min:3',
            'jabatan' => 'required',
            'password' => 'required|min:6'
        ], [
            'username.unique' => 'Username sudah ada.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.'
        ]);

        User::create([
            'nama_lengkap' => ucwords(strtolower($request->nama_lengkap)),
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'User berhasil ditambahkan!']);
    }

    public function editUsers(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'username' => 'required|min:3|unique:users,username,' . $id,
            'jabatan' => 'required'
        ], [
            'username.unique' => 'Username sudah ada.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
        ]);

        $user = User::findOrFail($id);
        $user->nama_lengkap = ucwords(strtolower($request->nama_lengkap));
        $user->username = $request->username;
        $user->jabatan = $request->jabatan;
        $user->save();

        return response()->json(['message' => 'User berhasil diperbarui!']);
    }


    public function hapusUsers($id)
    {
        $user = User::findOrFail($id);
        $user->isActive = 0;
        $user->save();

        return response()->json(['message' => 'User berhasil dihapus!']);
    }

    public function gantiPassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:3|confirmed', // Validasi untuk new_password dan konfirmasinya
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 3 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Menemukan user berdasarkan ID
        $user = User::find($request->user_id);

        // Memeriksa apakah password saat ini cocok
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.'])->withInput();
        }

        // Mengubah password pengguna dengan password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Mengirimkan respons sukses
        return response()->json(['message' => 'Password berhasil diperbarui!']);
    }
}
