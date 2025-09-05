<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PT FLN | Kendaraan Operasional</title>

    {{-- offline --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <script src="{{ asset('assets/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/pusher-8.4.0.min.js') }}"></script>
    <script src="{{ asset('js/echo-1.11.1.js') }}"></script>

</head>

<body class="d-flex" style="min-height: 100vh;">

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <div class="container mt-3">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 text-start mt-3 mt-md-0">
                    <h2 class="mt-0">Kendaraan Operasional</h2>
                </div>

                <div class="col-12 col-md-4 text-center">
                    <img src="img/fln-logo.png" width="120px" alt="" class="my-2">
                </div>

                <div class="col-12 col-md-4 text-end">
                    @auth
                    <div class="text-muted small mt-2">
                        Selamat datang, <strong>{{ auth()->user()->username }}</strong>
                    </div>

                    {{-- Tampilkan hari dan tanggal di atas tombol --}}
                    <div class="text-muted small">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>

                    @if(in_array(auth()->user()->jabatan, ['Admin GA', 'Staff GA']))
                    <button type="button"" class=" btn btn-sm btn-info mt-1" style="color:white;" data-bs-toggle="modal" data-bs-target="#tambahKendaraanModal">+ Tambah Kendaraan</button>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger mt-1">Logout</button>
                    </form>
                    @endauth

                    @guest
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary mt-1">Login</a>

                    {{-- Tanggal juga bisa ditampilkan untuk guest jika perlu --}}
                    <div class="text-muted small mt-2">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>
                    @endguest
                </div>
            </div>

            {{-- alert sukses untuk crud kendaraan --}}
            @if (session('success'))
            <div id="alertBox" class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                {!! session('success') !!}
            </div>
            @endif

            {{-- Alert error dari validasi --}}
            @if ($errors->any())
            <div id="alertBox" class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- alert untuk input in/out dari js --}}
            <div id="alertBoxUpdateStatus" class="mt-3"></div>

            <div class="row mt-3" id="kendaraan-container">
                @foreach($kendaraan as $k)
                <div class="col-md-4 mb-4" data-id="{{ $k->id }}" data-status="{{ $k->status }}" data-updated="{{ $k->updated_at }}">
                    <div class="kendaraan-card">
                        <div class="card">
                            <img src="{{ asset($k->image_path) }}" class="card-img-top w-100" style="height: 350px; object-fit: cover;">
                            <div class="card-body position-relative">
                                <h5 class="card-title">{{ $k->nama_mobil }}</h5>
                                <p class="card-text">{{ $k->nopol }}</p>

                                @auth
                                @if(in_array(auth()->user()->jabatan, ['Admin GA', 'Staff GA', 'Security']))
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal{{ $k->id }}">
                                    Update Status
                                </button>
                                @endif
                                @endauth

                                {{-- status dalam card --}}
                                @php
                                $statusClass = match ($k->status) {
                                'Stand By' => 'success',
                                'Pergi' => 'warning',
                                'Perbaikan' => 'danger',
                                default => throw new \Exception('Status tidak dikenal: ' . $k->status),
                                };
                                @endphp

                                <div class="position-absolute bottom-0 end-0 text-end m-2">
                                    <span class="badge bg-{{ $statusClass }} mb-1 status-badge">
                                        {{ $k->status }}
                                    </span>
                                    <br>
                                    <small class="text-muted waktu-update mt-1 d-block" data-updated="{{ $k->updated_at }}">
                                        {{ $k->updated_at ? $k->updated_at->diffForHumans() : '' }}
                                    </small>

                                    @auth
                                    @if(in_array(auth()->user()->jabatan, ['Admin GA', 'Staff GA']) && !in_array($k->status, ['Pergi', 'Perbaikan']))

                                    <div class="mt-2 kendaraan-action-buttons position-relative" style="padding-bottom: 2.5rem;">
                                        {{-- Tombol Edit & Hapus --}}
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editKendaraanModal-{{ $k->id }}">
                                            Edit
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusKendaraanModal{{ $k->id }}">
                                            Hapus
                                        </button>

                                        {{-- Toggle Visibilitas --}}
                                        <form action="{{ route('sembunyikan.kendaraan', $k->id) }}" method="POST" class="position-absolute" style="right: 0.5rem; bottom: 0.5rem;">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" style="width: 3rem; height: 1.5rem;" onchange="this.form.submit()" {{ $k->isVisible == 0 ? 'checked' : '' }}>
                                            </div>
                                        </form>
                                    </div>

                                    @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- update status kendaraan --}}
                <div class="modal fade" id="modal{{ $k->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Status Kendaraan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger d-none error-message-box"></div>
                                <form class="updateForm" data-id="{{ $k->id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $k->id }}">

                                    <label class="form-label">Kendaraan</label>
                                    <input type="text" class="form-control" name="nama_mobil" value="{{ $k->nama_mobil }}" readonly style="background-color: #e9ecef; pointer-events: none;">
                                    <input type="text" class="form-control mt-2" name="nopol" value="{{ $k->nopol }}" readonly style="background-color: #e9ecef; pointer-events: none;">

                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select statusSelect" data-id="{{ $k->id }}">
                                        <option value="Stand By" {{ $k->status == 'Stand By' ? 'selected' : '' }}>Stand By
                                        </option>
                                        <option value="Pergi" {{ $k->status == 'Pergi' ? 'selected' : '' }}>Pergi</option>
                                        <option value="Perbaikan" {{ $k->status == 'Perbaikan' ? 'selected' : '' }}>Perbaikan
                                        </option>
                                    </select>

                                    {{-- jika status stand by, muncul inputan km akhir --}}
                                    <div class="standby-fields mt-3" id="standbyFields{{ $k->id }}" style="display: none;">
                                        <label class="form-label">KM Akhir *</label>
                                        <input type="number" class="form-control" name="km_akhir">
                                    </div>

                                    {{-- jika status perbaikan, muncul inputan  --}}
                                    <div class="perbaikan-fields mt-3" id="perbaikanFields{{ $k->id }}" style="display: none;">
                                        <label class="form-label">Catatan Perbaikan *</label>
                                        <textarea name="catatan_perbaikan" class="form-control"></textarea>
                                    </div>

                                    <div class="additional-fields mt-3" id="additionalFields{{ $k->id }}" style="display: none;">

                                        <label class="form-label">Nama Pemakai *</label>
                                        <input type="text" class="form-control" name="nama_pemakai">

                                        <label class="form-label">Departemen *</label>
                                        <select name="departemen" class="form-select">
                                            <option value="ENGINEERING">ENGINEERING</option>
                                            <option value="FA">FA</option>
                                            <option value="HR/GA">HR/GA</option>
                                            <option value="HSE">HSE</option>
                                            <option value="IT">IT</option>
                                            <option value="MR">MR</option>
                                            <option value="MAINTENANCE">MAINTENANCE</option>
                                            <option value="MARKETING">MARKETING</option>
                                            <option value="PPIC/RM">PPIC/RM</option>
                                            <option value="PRODUKSI">PRODUKSI</option>
                                            <option value="PURCHASING">PURCHASING</option>
                                            <option value="QUALITY">QUALITY</option>
                                        </select>

                                        <label class="form-label">Driver</label>
                                        <select name="driver" class="form-select driverSelect" data-id="{{ $k->id }}">
                                            <option value="Abas">Abas</option>
                                            <option value="Rahmat">Rahmat</option>
                                            <option value="Fiki">Fiki</option>
                                            <option value="Dwi">Dwi</option>
                                            <option value="Zaenudin">Zaenudin</option>
                                            <option value="Lain-lain">Lain-lain</option>
                                        </select>
                                        <input type="text" class="form-control mt-2 driverLainInput" name="driver_lain" placeholder="Masukkan nama driver lain" style="display:none;">

                                        <label class="form-label">Tujuan *</label>
                                        <input type="text" class="form-control" name="tujuan">

                                        <label class="form-label">Keterangan *</label>
                                        <textarea name="keterangan" class="form-control"></textarea>

                                        <label class="form-label">KM Awal *</label>
                                        <input type="number" class="form-control" name="km_awal">
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- modal tambah --}}
                <div class="modal fade" id="tambahKendaraanModal" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('tambah.kendaraan') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalTambahLabel">Tambah Kendaraan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Nama Mobil</label>
                                        <input type="text" class="form-control" name="nama_mobil" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>No Polisi</label>
                                        <input type="text" class="form-control" name="nopol" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gambar_mobil" class="form-label">Gambar Mobil</label>
                                        <input type="file" class="form-control" name="gambar_mobil" id="gambarMobilTambah" onchange="typeof previewGambar === 'function' && previewGambar(event)" accept="image/*" required />
                                        <img id="previewGambar" class="img-fluid mt-2" style="max-height: 200px; width: 200px; display: none;">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- modal hapus --}}
                <div class="modal fade" id="hapusKendaraanModal{{ $k->id }}" tabindex="-1" aria-labelledby="hapusModalLabel{{ $k->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('hapus.kendaraan', $k->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="hapusModalLabel{{ $k->id }}">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <p>Yakin ingin menghapus kendaraan berikut?</p>
                                            <ul>
                                                <li>Nama Mobil : <strong>{{ $k->nama_mobil }}</strong></li>
                                                <li>No Polisi : <strong>{{ $k->nopol }}</strong></li>
                                            </ul>
                                        </div>

                                        <div class="col-md-7 text-end">
                                            <img src="{{ asset('/storage/mobil/' . $k->gambar_mobil) }}" alt="Gambar Mobil" class="img-fluid" style="height:250px; width:500px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- modal edit --}}
                <div class="modal fade" id="editKendaraanModal-{{ $k->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('edit.kendaraan', $k->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Kendaraan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" id="editKendaraanId-{{ $k->id }}" value="{{ $k->id }}">
                                    <div class="mb-3">
                                        <label for="editNamaMobil-{{ $k->id }}">Nama Mobil</label>
                                        <input type="text" class="form-control" name="nama_mobil" id="editNamaMobil-{{ $k->id }}" value="{{ $k->nama_mobil }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editNopol-{{ $k->id }}">Nopol</label>
                                        <input type="text" class="form-control" name="nopol" id="editNopol-{{ $k->id }}" value="{{ $k->nopol }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="editGambarMobil-{{ $k->id }}">Gambar Mobil Saat Ini</label><br>
                                        <img src="{{ asset('storage/mobil/' . $k->gambar_mobil) }}" alt="Gambar Mobil" class="img-fluid mb-2" style="max-height: 200px; width: 200px;">
                                    </div>

                                    <div class="mb-3">
                                        <label for="editGambarMobil-{{ $k->id }}">Ganti Gambar</label>
                                        <input type="file" class="form-control" name="gambar_mobil" id="editGambarMobil-{{ $k->id }}" onchange="previewGambar(event, '{{ $k->id }}')">
                                        <img id="previewGambar-{{ $k->id }}" class="img-fluid mt-2" style="max-height: 200px; width: 200px;" />
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Edit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @endforeach
            </div>
        </div>
    </div>


    {{-- offline --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/vendor/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('js/vendor/dayjs/plugin/relativeTime.js') }}"></script>
    <script src="{{ asset('js/vendor/dayjs/locale/id.js') }}"></script>

    {{-- preview gambar --}}
    <script>
        // preview gambar
        function previewGambar(event, id = null) {
            const input = event.target;
            const preview = id ?
                document.getElementById('previewGambar-' + id) :
                document.getElementById('previewGambar');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#gambarMobilTambah').on('change', function(event) {
            previewGambar(event);
        });

        // alert
        setTimeout(() => {
            const alert = document.getElementById('alertBox');
            if (alert) {
                // Tambahkan class fade-out dan hapus setelah animasi
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500); // Tunggu animasi fade selesai
            }
        }, 3000);

    </script>

    {{-- input in/out --}}
    <script>
        // updated_at dinamis berubah ubah
        dayjs.locale('id-custom', {
            name: 'id-custom'
            , relativeTime: {
                future: 'dalam %s'
                , past: '%s yang lalu'
                , s: ''
                , m: '1 menit'
                , mm: '%d menit'
                , h: '1 jam'
                , hh: '%d jam'
                , d: '1 hari'
                , dd: '%d hari'
                , M: '1 bulan'
                , MM: '%d bulan'
                , y: '1 tahun'
                , yy: '%d tahun'
            }
        });

        dayjs.extend(dayjs_plugin_relativeTime);
        dayjs.locale('id-custom');

        window.kendaraanIds = @json($kendaraanIds);

        document.addEventListener("DOMContentLoaded", function() {

            // Inisialisasi Echo dan Pusher
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: '{{ env("REVERB_APP_KEY") }}',
                cluster: '{{ env("REVERB_APP_CLUSTER", "mt1") }}',
                wsHost: window.location.hostname,
                wsPort: 6001,
                wssPort: 443,
                forceTLS: true,
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
            });

            // Cek koneksi WebSocket
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('WebSocket Connected');
            });

            // Pastikan kendaraanIds sudah didefinisikan
            if (Array.isArray(window.kendaraanIds)) {
                // Realtime listener
                window.kendaraanIds.forEach(id => {
                    window.Echo.channel(`kendaraan.${id}`)
                        .listen('.KendaraanUpdated', (data) => {
                            const wrapper = document.querySelector(`.col-md-4[data-id="${id}"]`);
                            if (!wrapper) return;

                            wrapper.setAttribute("data-status", data.status);
                            wrapper.setAttribute("data-updated", data.updated_at);

                            const card = wrapper.querySelector(".kendaraan-card");
                            const badge = card.querySelector(".status-badge");
                            const waktu = card.querySelector(".waktu-update");

                            if (badge) {
                                badge.textContent = data.status;
                                badge.classList.remove("bg-success", "bg-warning", "bg-danger");

                                switch (data.status) {
                                    case "Stand By":
                                        badge.classList.add("bg-success");
                                        break;
                                    case "Pergi":
                                        badge.classList.add("bg-warning");
                                        break;
                                    case "Perbaikan":
                                        badge.classList.add("bg-danger");
                                        break;
                                }
                            }

                            if (waktu) {
                                waktu.textContent = dayjs(data.updated_at).fromNow();
                                waktu.setAttribute("data-updated", data.updated_at);
                            }

                            sortKendaraanCards();
                        });
                });
            } else {
                console.warn('kendaraanIds belum didefinisikan atau bukan array.');
            }

            function sortKendaraanCards() {
                const container = document.getElementById('kendaraan-container');
                if (!container) return;

                const statusOrder = {
                    'Stand By': 1
                    , 'Pergi': 2
                    , 'Perbaikan': 3
                };

                const cards = Array.from(container.querySelectorAll('.col-md-4'));

                const sorted = cards.sort((a, b) => {
                    const statusA = a.getAttribute('data-status');
                    const statusB = b.getAttribute('data-status');
                    const updatedA = new Date(a.getAttribute('data-updated'));
                    const updatedB = new Date(b.getAttribute('data-updated'));

                    if (statusOrder[statusA] !== statusOrder[statusB]) {
                        return statusOrder[statusA] - statusOrder[statusB];
                    }
                    return updatedB - updatedA; // lebih baru di atas
                });

                sorted.forEach(el => container.appendChild(el));
            }

            window.Echo.channel('kendaraan.global')
                .listen('.KendaraanCrud', (e) => {
                    const container = document.getElementById("kendaraan-container");
                    const userJabatan = document.getElementById('user-jabatan') ?.value || '';
                    const canUpdate = ['Admin GA', 'Staff GA', 'Security'].includes(userJabatan);
                    const canEditDelete = ['Admin GA', 'Staff GA'].includes(userJabatan);

                    const generateCard = (data) => {
                        const statusClass = {
                            'Stand By': 'success'
                            , 'Pergi': 'warning'
                            , 'Perbaikan': 'danger'
                        } [data.status] ?? 'secondary';

                        const waktuUpdate = dayjs(data.updated_at).isValid() ?
                            dayjs(data.updated_at).fromNow() :
                            "";

                        return `
                            <div class="col-md-4 mb-4" data-id="${data.id}" data-status="${data.status}" data-updated="${data.updated_at}">
                                <div class="kendaraan-card">
                                    <div class="card">
                                        <img src="${data.image_path}" class="card-img-top w-100" style="height: 350px; object-fit: cover;">
                                        <div class="card-body position-relative">
                                            <h5 class="card-title">${data.nama_mobil}</h5>
                                            <p class="card-text">${data.nopol}</p>

                                            ${canUpdate ? `
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal${data.id}">
                                                Update Status
                                            </button>` : ''}

                                            <div class="position-absolute bottom-0 end-0 text-end m-2">
                                                <span class="badge bg-${statusClass} mb-1 status-badge">${data.status}</span>
                                                <br>
                                                <small class="text-muted waktu-update mt-1 d-block" data-updated="${data.updated_at}">
                                                    ${waktuUpdate}
                                                </small>

                                                ${(canEditDelete && !['Pergi', 'Perbaikan'].includes(data.status)) ? `
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editKendaraanModal-${data.id}">Edit</button>
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusKendaraanModal${data.id}">Hapus</button>
                                                </div>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    };

                    // Handle Add
                    if (e.action === 'add') {
                        const existing = document.querySelector(`[data-id="${e.data.id}"]`);
                        if (existing) return; // Jangan tambah duplikat
                        container.insertAdjacentHTML('afterbegin', generateCard(e.data));
                        sortKendaraanCards();
                    }

                    // Handle Edit
                    if (e.action === 'edit') {
                        const existing = document.querySelector(`[data-id="${e.data.id}"]`);
                        if (existing) existing.remove();

                        container.insertAdjacentHTML('afterbegin', generateCard(e.data));
                        sortKendaraanCards();
                    }

                    // Handle Delete
                    if (e.action === 'delete') {
                        const existing = document.querySelector(`[data-id="${e.kendaraanId}"]`);
                        if (existing) existing.remove();
                    }
                });


            // update_at berubah ubah dinamis
            setInterval(() => {
                document.querySelectorAll(".waktu-update").forEach(el => {
                    const updatedAt = el.getAttribute("data-updated");
                    if (updatedAt && dayjs(updatedAt).isValid()) {
                        const diffInSeconds = dayjs().diff(dayjs(updatedAt), 'second');
                        if (diffInSeconds < 60) {
                            el.textContent = "";
                        } else {
                            el.textContent = dayjs(updatedAt).fromNow();
                        }
                    } else {
                        el.textContent = "";
                    }
                });
            }, 1000);

            //ketika pilih pergi maka muncul form
            document.querySelectorAll(".statusSelect").forEach(select => {
                toggleAdditionalFields(select);
                select.addEventListener("change", function() {
                    toggleAdditionalFields(this);
                });
            });

            function toggleAdditionalFields(select) {
                let pergiDiv = document.getElementById("additionalFields" + select.dataset.id);
                let standbyDiv = document.getElementById("standbyFields" + select.dataset.id);
                let perbaikanDiv = document.getElementById("perbaikanFields" + select.dataset.id);

                if (select.value === "Pergi") {
                    pergiDiv.style.display = "block";
                    standbyDiv.style.display = "none";
                    perbaikanDiv.style.display = "none";
                } else if (select.value === "Stand By") {
                    standbyDiv.style.display = "block";
                    pergiDiv.style.display = "none";
                    perbaikanDiv.style.display = "none";
                } else if (select.value === "Perbaikan") {
                    perbaikanDiv.style.display = "block";
                    standbyDiv.style.display = "none";
                    pergiDiv.style.display = "none";
                }
            }

            // Tampilkan input 'driver lain' jika pilihannya 'Lain-lain'
            document.querySelectorAll(".driverSelect").forEach(select => {
                toggleDriverInput(select);
                select.addEventListener("change", function() {
                    toggleDriverInput(this);
                });
            });

            function toggleDriverInput(select) {
                const id = select.dataset.id;
                const form = select.closest("form");
                const inputLain = form.querySelector("input[name='driver_lain']");

                if (select.value === "Lain-lain") {
                    inputLain.style.display = "block";
                } else {
                    inputLain.style.display = "none";
                    inputLain.value = "";
                }
            }

            // form submit
            document.querySelectorAll(".updateForm").forEach(form => {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();

                    const status = form.querySelector("select[name='status']").value;

                    if (status === "Pergi") {
                        const mobil = form.querySelector("input[name='nama_mobil']").value.trim();
                        const nopol = form.querySelector("input[name='nopol']").value.trim();
                        const nama = form.querySelector("input[name='nama_pemakai']") ?.value.trim() || "";
                        const departemen = form.querySelector("select[name='departemen']") ?.value.trim() || "";
                        const tujuan = form.querySelector("input[name='tujuan']") ?.value.trim() || "";
                        const keterangan = form.querySelector("textarea[name='keterangan']") ?.value.trim() || "";

                        const driverSelect = form.querySelector("select[name='driver']") ?.value.trim() || "";
                        const driver = driverSelect === "Lain-lain" ?
                            (form.querySelector("input[name='driver_lain']") ?.value.trim() || "") :
                            driverSelect;

                        const kmAwal = form.querySelector("input[name='km_awal']") ?.value.trim() || "";

                        if (!kmAwal) {
                            alert("KM Awal wajib diisi saat kendaraan Pergi!");
                            return;
                        }

                        if (!nama || !departemen || !driver || !tujuan || !keterangan) {
                            alert("Data masih ada yang kosong dan harus diisi!");
                            return;
                        }
                    }

                    if (status === "Stand By") {
                        const kmAkhir = form.querySelector("input[name='km_akhir']") ?.value.trim() || "";
                        if (!kmAkhir) {
                            alert("KM Akhir wajib diisi saat kendaraan kembali (Stand By)!");
                            return;
                        }
                    }

                    if (status === "Perbaikan") {
                        const catatan_perbaikan = form.querySelector("textarea[name='catatan_perbaikan']") ?.value.trim() || "";

                        if (!catatan_perbaikan) {
                            alert("Catatan perbaikan harus diisi ketika kendaraan sedang dalam masalah!");
                            return;
                        }
                    }


                    // Proses fetch
                    let formData = new FormData(form);
                    formData.append('_method', 'PUT');
                    let id = form.dataset.id;

                    fetch("/kendaraan/update", {
                            method: "POST"
                            , body: formData
                            , headers: {
                                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
                                , "Accept": "application/json"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // alert setelah update status in/out
                                document.getElementById("alertBoxUpdateStatus").innerHTML =
                                    `<div class='alert alert-success'>${data.message}</div>`;
                                setTimeout(() => {
                                    document.getElementById("alertBoxUpdateStatus").innerHTML = "";
                                }, 3000);

                                // Update card kendaraan
                                let wrapper = document.querySelector(`.col-md-4[data-id='${id}']`);
                                if (wrapper) {
                                    const card = wrapper.querySelector(".kendaraan-card");
                                    const badge = card.querySelector(".status-badge");

                                    // === Update badge ===
                                    if (badge) {
                                        badge.textContent = data.status;
                                        badge.classList.remove("bg-success", "bg-warning", "bg-danger");

                                        switch (data.status) {
                                            case "Stand By":
                                                badge.classList.add("bg-success");
                                                break;
                                            case "Pergi":
                                                badge.classList.add("bg-warning");
                                                break;
                                            case "Perbaikan":
                                                badge.classList.add("bg-danger");
                                                break;
                                        }
                                    }

                                    // === Update waktu ===
                                    const waktu = card.querySelector(".waktu-update");
                                    if (waktu) {
                                        waktu.textContent = dayjs(data.updated_at).fromNow();
                                        waktu.setAttribute("data-updated", data.updated_at);
                                    }

                                    // === Toggle switch sembunyikan kendaraan ===
                                    let toggleForm = card.querySelector(".form-toggle-visibility") ||
                                        card.querySelector("form[action*='sembunyikan']");

                                    if (!toggleForm && data.status === "Stand By") {
                                        const actionWrapper = card.querySelector('.kendaraan-action-buttons');
                                        if (actionWrapper) {
                                            const form = document.createElement('form');
                                            form.action = `/kendaraan/${id}/sembunyikan`;
                                            form.method = 'POST';
                                            form.className = 'form-toggle-visibility mt-2'; // posisi di bawah tombol
                                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
                                            form.innerHTML = `
                                                <input type="hidden" name="_token" value="${csrfToken}">
                                                <input type="hidden" name="_method" value="PUT">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" style="width: 3rem; height: 1.5rem;" onchange="this.form.submit()" ${data.isVisible==0 ? 'checked' : '' }>
                                                </div>
                                                `;
                                            actionWrapper.appendChild(form);
                                            toggleForm = form;
                                        }
                                    }

                                    if (toggleForm) {
                                        toggleForm.style.display = (data.status === "Stand By") ? "" : "none";
                                        const checkbox = toggleForm.querySelector("input[type='checkbox']");
                                        if (checkbox) {
                                            checkbox.checked = (data.isVisible == 0);
                                        }
                                    }

                                    // === Tombol edit & hapus ===
                                    const actionButtons = wrapper.querySelector(".kendaraan-action-buttons");
                                    if (['Pergi', 'Perbaikan'].includes(data.status)) {
                                        if (actionButtons) {
                                            // Hapus hanya tombolnya, biarkan toggle tetap ada
                                            const buttons = actionButtons.querySelectorAll("button");
                                            buttons.forEach(btn => btn.remove());
                                        }
                                    } else {
                                        if (actionButtons && canEditDelete) {
                                            // Jika tombol belum ada, tambahkan
                                            if (!actionButtons.querySelector(".btn-warning")) {
                                                const tombolEdit = document.createElement('button');
                                                tombolEdit.type = "button";
                                                tombolEdit.className = "btn btn-warning btn-sm";
                                                tombolEdit.setAttribute("data-bs-toggle", "modal");
                                                tombolEdit.setAttribute("data-bs-target", `#editKendaraanModal-${id}`);
                                                tombolEdit.innerText = "Edit";

                                                const tombolHapus = document.createElement('button');
                                                tombolHapus.type = "button";
                                                tombolHapus.className = "btn btn-danger btn-sm ms-1";
                                                tombolHapus.setAttribute("data-bs-toggle", "modal");
                                                tombolHapus.setAttribute("data-bs-target", `#hapusKendaraanModal${id}`);
                                                tombolHapus.innerText = "Hapus";

                                                // sisipkan sebelum toggle
                                                if (toggleForm) {
                                                    actionButtons.insertBefore(tombolEdit, toggleForm);
                                                    actionButtons.insertBefore(tombolHapus, toggleForm);
                                                } else {
                                                    actionButtons.appendChild(tombolEdit);
                                                    actionButtons.appendChild(tombolHapus);
                                                }
                                            }
                                        }
                                    }

                                    // update sorting
                                    card.setAttribute("data-status", data.status);
                                    card.setAttribute("data-updated", data.updated_at);
                                    sortKendaraanCards();
                                }

                                //  Tutup modal hanya jika berhasil ditemukan
                                const modalEl = document.getElementById('modal' + id);
                                if (modalEl) {
                                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                                    if (modalInstance) {
                                        modalInstance.hide();
                                    }
                                }

                            } else {
                                document.getElementById("alertBoxUpdateStatus").innerHTML = `<div class='alert alert-danger'>Terjadi kesalahan, silahkan coba lagi.</div>`;
                                setTimeout(() => {
                                    document.getElementById("alertBoxUpdateStatus").innerHTML = "";
                                }, 3000);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
            });
        });

    </script>

    <script>
        window.canEditDelete = @json(in_array(optional(auth()-> user())->jabatan, ['Admin GA', 'Staff GA']));

    </script>


</body>

</html>
