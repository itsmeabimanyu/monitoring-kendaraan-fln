<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Kendaraan</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.bundle.min.css') }}" rel="stylesheet">

    <script src="{{ asset('assets/jquery/jquery-3.6.0.min.js') }}"></script>
</head>

<body class="d-flex" style="min-height: 100vh;">

    @include('components.sidebar')

    <!-- Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <div class="container mt-3">
            {{-- Logo di tengah --}}
            <div class="text-center mb-4">
                <img src="{{ asset('img/fln-logo.png') }}" width="120px" alt="Logo">
            </div>

            {{-- Baris utama: kiri judul, kanan info login --}}
            <div class="d-flex justify-content-between align-items-start mb-4">

                {{-- Kiri: Judul --}}
                <div>
                    <h2 class="mb-0">Monitoring Kendaraan</h2>
                </div>

                {{-- Kanan: Area Login/Logout, Tanggal --}}
                <div class="text-end">
                    @guest
                    {{-- Jika belum login: tampilkan tombol login --}}
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm mb-1">Login</a>

                    {{-- Tanggal tetap tampil --}}
                    <div>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    @endguest

                    @auth
                    {{-- Jika sudah login: tampilkan selamat datang --}}
                    <div class="mb-1">
                        <small>Selamat datang, <strong>{{ Auth::user()->username }}</strong></small>
                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-2">
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    {{-- Tombol logout --}}
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-danger btn-sm">Logout</button>
                    </form>
                    @endauth
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Mobil</th>
                        <th width="100">Gambar</th>
                        <th>No Polisi</th>
                        <th>Status</th>
                        <th>Pemakai</th>
                        <th>Driver</th>
                        <th>Tujuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="kendaraanTable">
                    @foreach($kendaraan as $k)
                    <tr>
                        <td>
                            {{ $loop->iteration }}
                        </td>
                        <td>{{ $k->nama_mobil }}</td>
                        <td>
                            <img src="{{ $k->image_path }}" alt="Gambar {{ $k->nopol }}" style="width:100px; height:100px; object-fit:cover;">
                        </td>
                        <td>{{ $k->nopol }}</td>
                        <td>
                            @php
                            $jam = \Illuminate\Support\Carbon::parse($k->updated_at)->timezone('Asia/Jakarta')->format('H:i');
                            @endphp

                            @if($k->status == 'Stand By')
                            <span class="badge bg-success">Stand By</span>
                            Jam {{ $jam }}
                            @elseif($k->status == 'Pergi')
                            <span class="badge bg-warning">Pergi</span>
                            Jam {{ $jam }}
                            @elseif($k->status == 'Perbaikan')
                            <span class="badge bg-danger">Perbaikan</span>
                            Jam {{ $jam }}
                            @else
                            <span class="badge bg-secondary">Status Tidak Dikenal</span>
                            @endif
                        </td>
                        <td>{{ $k->nama_pemakai }} <br> {{ $k->departemen }} </td>
                        <td>{{ $k->driver }}</td>
                        <td>{{ $k->tujuan }}</td>
                        <td>{{ $k->keterangan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function fetchData() {
            $.ajax({
                url: '/kendaraan/data'
                , type: 'GET'
                , dataType: 'json'
                , success: function(response) {
                    let tableBody = $('#kendaraanTable');
                    tableBody.empty();

                    response.forEach(function(k, index) {
                        let jam = new Date(k.updated_at).toLocaleTimeString('id-ID', {
                            hour: '2-digit'
                            , minute: '2-digit'
                            , timeZone: 'Asia/Jakarta'
                        });

                        let statusBadge = '';
                        if (k.status === 'Stand By') {
                            statusBadge = `<span class="badge bg-success">Stand By</span> Jam ${jam}`;
                        } else if (k.status === 'Pergi') {
                            statusBadge = `<span class="badge bg-warning">Pergi</span> Jam ${jam}`;
                        } else if (k.status === 'Perbaikan') {
                            statusBadge = `<span class="badge bg-danger">Perbaikan</span> Jam ${jam}`;
                        } else {
                            statusBadge = `<span class="badge bg-secondary">Status Tidak Dikenal</span>`;
                        }

                        let row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${k.nama_mobil}</td>
                                <td><img src="${k.image_path}" style="height: 100px; width: 100px; object-fit: cover;"></td>
                                <td>${k.nopol}</td>
                                <td>${statusBadge}</td>
                                <td>${(k.nama_pemakai && k.departemen) ? `${k.nama_pemakai}<br>${k.departemen}` : '-'}</td>
                                <td>${k.driver || '-'}</td>
                                <td>${k.tujuan || '-'}</td>
                                <td>${k.keterangan || '-'}</td>
                            </tr>
                            `;

                        tableBody.append(row);
                    });
                }
                , error: function() {
                    console.log('Gagal mengambil data.');
                }
            });
        }

        setInterval(fetchData, 3000);

    </script>

</body>
</html>
