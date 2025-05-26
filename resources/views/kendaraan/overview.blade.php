<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Kendaraan</title>

    {{-- versi cdn --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    {{-- <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.1/echo.iife.js"></script> --}}

    {{-- offline --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/pusher.min.js') }}"></script>
    <script src="{{ asset('js/echo.iife.min.js') }}"></script>

</head>

<body>
    <div class="container mt-5">
        <div class="row align-items-center">
            {{-- Judul di kiri --}}
            <div class="col-12 col-md-4 text-start mt-3 mt-md-0">
                <h2 class="mt-0">List Kendaraan Operasional</h2>
            </div>

            {{-- Logo di tengah --}}
            <div class="col-12 col-md-4 text-center">
                <img src="img/fln-logo.png" width="120px" alt="" class="my-2">
            </div>

            {{-- Tombol logout di kanan --}}
            <div class="col-12 col-md-4 text-end">
                <div class="text-muted small mt-2">
                    Selamat datang, <strong>{{ auth()->user()->username }}</strong>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger mt-1">Logout</button>
                </form>
            </div>
        </div>

        <div id="alertBox"></div>
        <div class="row">
            @foreach($kendaraan as $k)
            <div class="col-md-4 mb-4 kendaraan-card" data-id="{{ $k->id }}">
                <div class="card">
                    <img src="{{ asset($k->image_path) }}" class="card-img-top w-100" style="height: 350px; object-fit: cover;">
                    <div class="card-body position-relative">
                        <h5 class="card-title">{{ $k->nama_mobil }}</h5>
                        <p class="card-text">{{ $k->nopol }}</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal{{ $k->id }}">Update Status</button>

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
                            {{-- time diffforhuman --}}
                            <small class="text-muted waktu-update mt-1 d-block" data-updated="{{ $k->updated_at }}">
                                {{ $k->updated_at ? $k->updated_at->diffForHumans() : 'Belum pernah diperbarui' }}
                            </small>

                        </div>
                    </div>
                </div>
            </div>

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

                                    <label class="form-label">Keterangan (opsional)</label>
                                    <textarea name="keterangan" class="form-control"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- cdn --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

    {{-- offline --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/vendor/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('js/vendor/dayjs/plugin/relativeTime.js') }}"></script>
    <script src="{{ asset('js/vendor/dayjs/locale/id.js') }}"></script>

    <script>
        // updated_at dinamis berubah ubah
        dayjs.locale('id-custom', {
            name: 'id-custom'
            , relativeTime: {
                future: 'dalam %s'
                , past: '%s yang lalu'
                , s: 'baru saja diubah'
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

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env("REVERB_APP_KEY") }}',
            wsHost: window.location.hostname,
            wsPort: 8080, // default port Reverb = 6001
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws'],
        });
    
        // Cek koneksi WebSocket
        Echo.connector.pusher.connection.bind('connected', () => {
            console.log('WebSocket Connected');
        });

        // Mendengarkan status kendaraan secara real-time
        window.kendaraanIds.forEach(id => {
            Echo.channel(`kendaraan.${id}`)
                .listen('.KendaraanUpdated', (event) => {
                    // console.log("Realtime Event:", event);
                    const card = document.querySelector(`[data-id='${event.id}']`);
                    if (!card) return;

                    const badge = card.querySelector(".status-badge");
                    badge.textContent = event.status;
                    badge.classList.remove("bg-success", "bg-warning", "bg-danger");

                    switch (event.status) {
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

                    const waktu = card.querySelector(".waktu-update");
                    waktu.textContent = dayjs(event.updated_at).fromNow();
                    waktu.setAttribute("data-updated", event.updated_at);
                });
        });

        // update_at berubah ubah dinamis
        setInterval(() => {
            document.querySelectorAll(".waktu-update").forEach(el => {
                const updatedAt = el.getAttribute("data-updated");
                if (updatedAt) {
                    const diffInSeconds = dayjs().diff(dayjs(updatedAt), 'second');
                    if (diffInSeconds < 60) {
                        el.textContent = "Baru saja diubah";
                    } else {
                        el.textContent = dayjs(updatedAt).fromNow();
                    }
                } else {
                    el.textContent = "Belum pernah diperbarui";
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
            let div = document.getElementById("additionalFields" + select.dataset.id);
            const inputs = div.querySelectorAll("input, textarea, select");

            if (select.value === "Pergi") {
                div.style.display = "block";
            } else {
                div.style.display = "none";
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
                inputLain.value = ""; // kosongkan kalau tidak dipakai
            }
        }

        // form submit
        document.querySelectorAll(".updateForm").forEach(form => {
            form.addEventListener("submit", function(e) {
                e.preventDefault();

                let status = form.querySelector("select[name='status']").value;

                if (status === "Pergi") {
                    let mobil = form.querySelector("input[name='nama_mobil']").value.trim();
                    let nopol = form.querySelector("input[name='nopol']").value.trim();
                    let nama = form.querySelector("input[name='nama_pemakai']").value.trim();
                    let departemen = form.querySelector("select[name='departemen']").value.trim();

                    let driverSelect = form.querySelector("select[name='driver']").value.trim();
                    let driver = driverSelect === "Lain-lain" ?
                        form.querySelector("input[name='driver_lain']").value.trim() :
                        driverSelect;

                    let tujuan = form.querySelector("input[name='tujuan']").value.trim();

                    if (!nama || !departemen || !driver || !tujuan) {
                        alert("Data masih ada yang kosong dan harus diisi!");
                        return;
                    }
                }

                // Proses fetch
                let formData = new FormData(form);
                let id = form.dataset.id;

                fetch("/kendaraan/update", {
                        method: "POST"
                        , body: formData
                        , headers: {
                            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // console.log(data);
                        if (data.success) {
                            document.getElementById("alertBox").innerHTML = `<div class='alert alert-success'>${data.message}</div>`;
                            setTimeout(() => {
                                document.getElementById("alertBox").innerHTML = "";
                            }, 3000); // hilang dalam 2 detik


                            // Update badge status (local update)
                            let card = document.querySelector(`[data-id='${id}']`);
                            const badge = card.querySelector(".status-badge");
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

                            // Jangan gunakan whisper pada public channel
                            // echo.channel(`kendaraan.${id}`).listen('.KendaraanUpdated', (event) => {
                                // console.log("Realtime Event:", event);
                                // Update status lainnya di sini
                            // });

                            let modal = bootstrap.Modal.getInstance(document.getElementById('modal' + id));
                            modal.hide();

                        } else {
                            document.getElementById("alertBox").innerHTML = `<div class='alert alert-danger'>Terjadi kesalahan, silahkan coba lagi.</div>`;
                            setTimeout(() => {
                                document.getElementById("alertBox").innerHTML = "";
                            }, 3000);
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });
        });
    });

    </script>

</body>

</html>
