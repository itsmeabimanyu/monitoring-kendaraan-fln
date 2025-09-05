<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\KendaraanSort;
use Illuminate\Support\Carbon;
use App\Events\KendaraanUpdated;
use App\Models\HistoryKendaraan;
use Illuminate\Support\Facades\Storage;


class KendaraanController extends Controller
{

    // menampilkan gambar
    private function getImagePath($nopol)
    {
        $extensions = ['jpeg', 'jpg', 'png', 'webp'];
        $folder = 'mobil/';
        $storage = Storage::disk('public');

        $nopolClean = strtoupper(str_replace(' ', '', $nopol));

        foreach ($extensions as $ext) {
            $manualFile = "{$folder}{$nopolClean}.{$ext}";
            if ($storage->exists($manualFile)) {
                return asset("storage/{$manualFile}");
            }
        }

        // Scan all files in the folder and try partial match
        $files = $storage->files($folder);
        foreach ($files as $file) {
            if (stripos(str_replace(' ', '', $file), $nopolClean) !== false) {
                return asset("storage/{$file}");
            }
        }

        // Default image if not found
        return asset('images/no-image.png');
    }

    // sort by status
    private function sortByStatus($kendaraan)
    {
        return $kendaraan->sort(function ($a, $b) {
            $statusOrder = [
                'Stand By'  => 1,
                'Pergi'     => 2,
                'Perbaikan' => 3,
            ];

            $orderA = $statusOrder[$a->status] ?? 4;
            $orderB = $statusOrder[$b->status] ?? 4;

            if ($orderA === $orderB) {
                return strtotime($b->updated_at) <=> strtotime($a->updated_at); // lebih baru di atas
            }

            return $orderA <=> $orderB;
        })->values(); // reset index
    }

    // monitoring kendaraan di halaman user (halaman old / tidak dipakai)
    public function index()
    {
        // Ambil semua data kendaraan
        $kendaraan = Kendaraan::orderBy('updated_at', 'desc')->get()->map(function ($k) {
            // Ambil gambar kendaraan
            $k->image_path = $k->gambar_mobil ? asset('storage/mobil/' . $k->gambar_mobil) : null;

            // Ambil history terakhir untuk kendaraan ini
            $lastHistory = HistoryKendaraan::where('kendaraan_id', $k->id)
                ->latest()
                ->first();  // Dapatkan history terbaru

            // Tambahkan data dari history jika ada
            $k->nama_pemakai = $lastHistory ? $lastHistory->nama_pemakai : '-';
            $k->departemen = $lastHistory ? $lastHistory->departemen : '-';
            $k->driver = $lastHistory ? $lastHistory->driver : '-';
            $k->tujuan = $lastHistory ? $lastHistory->tujuan : '-';
            $k->keterangan = $lastHistory ? $lastHistory->keterangan : '-';

            return $k;
        });

        // Urutkan berdasarkan status
        $kendaraan = $this->sortByStatus($kendaraan);

        // Kirim data ke view
        return view('kendaraan.index', compact('kendaraan'));
    }

    // json ambil data kendaraan
    public function getData()
    {
        $kendaraan = Kendaraan::where('isActive', 1)
            ->orderByRaw("FIELD(status, 'Stand By', 'Pergi', 'Perbaikan')")
            ->orderBy('updated_at', 'desc')
            ->take(8)
            ->get();

        // Gabungkan dengan history terakhir untuk setiap kendaraan
        $kendaraan = $kendaraan->map(function ($k) {
            $image = $this->getImagePath($k->nopol);

            // Ambil history terakhir (status terbaru) untuk kendaraan ini
            $lastHistory = HistoryKendaraan::where('kendaraan_id', $k->id)
                ->latest()
                ->first();  // Dapatkan history terbaru

            return [
                'nama_mobil'    => $k->nama_mobil,
                'image_path'    => $image,
                'nopol'         => $k->nopol,
                'status'        => $k->status,
                'nama_pemakai'  => $lastHistory?->nama_pemakai ?? '-',
                'departemen'    => $lastHistory?->departemen ?? '-',
                'driver'        => $lastHistory?->driver ?? '-',
                'tujuan'        => $lastHistory?->tujuan ?? '-',
                'keterangan'    => $lastHistory?->keterangan ?? '-',
                'updated_at'    => Carbon::parse($k->updated_at)
                    ->timezone('Asia/Jakarta')
                    ->toIso8601String(),
            ];
        });

        return response()->json($kendaraan);
    }

    // List kendaraan untuk input keluar masuk kendaraan
    public function kendaraan()
    {
        $query = Kendaraan::where('isActive', 1);

        // Jika bukan Admin GA / Staff GA, filter hanya kendaraan yang visible
        if (!auth()->check() || !in_array(auth()->user()->jabatan, ['Admin GA', 'Staff GA'])) {
            $query->where('isVisible', 1);
        }

        $kendaraan = $query->get()->map(function ($k) {
            $k->image_path = $this->getImagePath($k->nopol);
            return $k;
        });

        $kendaraan = $this->sortByStatus($kendaraan);

        // Ambil ID kendaraan yang akan digunakan di frontend
        $kendaraanIds = $kendaraan->pluck('id');

        return view('kendaraan.overview', compact('kendaraan', 'kendaraanIds'));
    }

    public function update(Request $request)
    {
        $rules = [
            'status' => 'required|string',
        ];

        if ($request->status === 'Pergi') {
            $rules['nama_pemakai'] = 'nullable|string';
            $rules['departemen']  = 'nullable|string';
            $rules['driver']      = 'required|string';
            $rules['tujuan']      = 'nullable|string';
            $rules['keterangan']  = 'nullable|string';
            $rules['km_awal']     = 'required|numeric|min:0';

            if ($request->driver === 'Lain-lain') {
                $rules['driver_lain'] = 'required|string';
            }
        } elseif ($request->status === 'Stand By') {
            $rules['km_akhir'] = 'required|numeric|min:0';
        } else {
            $rules['catatan_perbaikan'] = 'nullable|string';
        }

        $request->validate($rules);

        $kendaraan = Kendaraan::findOrFail($request->id);

        $lastPergi = HistoryKendaraan::where('kendaraan_id', $kendaraan->id)
            ->where('status', 'Pergi')
            ->latest()
            ->first();

        if ($request->status === 'Pergi') {
            $namaPemakai = $request->nama_pemakai ?? $lastPergi?->nama_pemakai;
            $departemen  = $request->departemen ?? $lastPergi?->departemen;
            $driver      = $request->driver === 'Lain-lain'
                ? ($request->driver_lain ?? $lastPergi?->driver)
                : ($request->driver ?? $lastPergi?->driver);
            $tujuan      = $request->tujuan ?? $lastPergi?->tujuan;
            $keterangan  = $request->keterangan ?? $lastPergi?->keterangan;
            $km_awal     = $request->km_awal ?? $lastPergi?->km_awal;
            $km_akhir    = null;
        } elseif ($request->status === 'Stand By') {
            $namaPemakai = $lastPergi?->nama_pemakai;
            $departemen  = $lastPergi?->departemen;
            $driver      = $lastPergi?->driver;
            $tujuan      = $lastPergi?->tujuan;
            $keterangan  = $lastPergi?->keterangan;
            $km_awal     = $lastPergi?->km_awal;
            $km_akhir    = $request->km_akhir;
        } else {
            // Perbaikan
            $namaPemakai = $lastPergi?->nama_pemakai;
            $departemen  = $lastPergi?->departemen;
            $driver      = $lastPergi?->driver;
            $tujuan      = $lastPergi?->tujuan;
            $keterangan  = $lastPergi?->keterangan;
            $km_awal     = $lastPergi?->km_awal;
            $km_akhir    = $lastPergi?->km_akhir;
        }

        // pakai ternary untuk catatan_perbaikan
        $catatan_perbaikan = $request->status === 'Perbaikan'
            ? $request->catatan_perbaikan
            : null;

        $namaPemakai = $namaPemakai ? Str::title($namaPemakai) : null;
        $driver      = $driver ? Str::title($driver) : null;

        $kendaraan->status = $request->status;
        $kendaraan->updated_at = now();
        $kendaraan->save();

        HistoryKendaraan::create([
            'kendaraan_id' => $kendaraan->id,
            'status'       => $request->status,
            'nama_pemakai' => $namaPemakai,
            'departemen'   => $departemen,
            'driver'       => $driver,
            'tujuan'       => $tujuan,
            'keterangan'   => $keterangan,
            'km_awal'      => $km_awal ?? null,
            'km_akhir'     => $km_akhir ?? null,
            'catatan_perbaikan'   => $catatan_perbaikan,
            'pic_update'   => auth()->user()->username,
        ]);

        broadcast(new KendaraanUpdated($kendaraan));

        return response()->json([
            'success'    => true,
            'message'    => "Status kendaraan <strong>{$kendaraan->nama_mobil} {$kendaraan->nopol}</strong> berhasil diperbarui!",
            'status'     => $kendaraan->status,
            'updated_at' => $kendaraan->updated_at->toIso8601String(),
        ]);
    }
}
