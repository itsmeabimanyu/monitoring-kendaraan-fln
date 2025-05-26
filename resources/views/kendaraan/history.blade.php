<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Kendaraan</title>

    {{-- datatables cdn --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    {{-- datatables non cdn / offline --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/jquery/jquery-3.6.0.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/datatables/css/jquery.dataTables.min.css') }}">
</head>

<body>
    <div class="container mt-5">
        <div class="text-center" style="margin-top: -20px;">
            <img src="img/fln-logo.png" width="120px" alt="">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">History Kendaraan</h2>
            <span style="white-space: nowrap;">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>

        <div id="loading" style="text-align: center; margin: 30px 0; display: none;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        </div>

        <div class="table-responsive">
            <table id="history-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Id History</th>
                        <th>Tanggal Update</th>
                        <th>Jam</th>
                        <th>Mobil</th>
                        <th>Status</th>
                        <th>Pemakai</th>
                        <th>Driver</th>
                        <th>Tujuan</th>
                        <th>Keterangan</th>
                        <th>PIC Update</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- DataTables CDN JS -->
    {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}

    <!-- DataTables non cdn offline JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const table = $('#history-table').DataTable({
                columns: [{
                        title: "No"
                    }
                    , {
                        title: "Id History"
                    }
                    , {
                        title: "Tanggal Update"
                    }
                    , {
                        title: "Jam Update"
                    }
                    , {
                        title: "Mobil"
                    }
                    , {
                        title: "Status"
                    }
                    , {
                        title: "Pemakai"
                    }
                    , {
                        title: "Driver"
                    }
                    , {
                        title: "Tujuan"
                    }
                    , {
                        title: "Keterangan"
                    }
                    , {
                        title: "PIC Update"
                    }
                ]
                , pageLength: 10
            });

            let isFirstLoad = true; // Mengatur apakah ini pemuatan pertama kali

            function fetchData() {
                if (isFirstLoad) {
                    $('#loading').show(); // Menampilkan spinner hanya saat pemuatan pertama
                }

                $.ajax({
                    url: "{{ route('history.kendaraan.data') }}"
                    , type: 'GET'
                    , dataType: 'json'
                    , success: function(data) {
                        let tableData = [];

                        $.each(data, function(i, item) {
                            const date = new Date(item.updated_at);
                            const tanggalUpdate = new Intl.DateTimeFormat('id-ID', {
                                weekday: 'long'
                                , day: '2-digit'
                                , month: 'long'
                                , year: 'numeric'
                            }).format(date);

                            const jamUpdate = date.toLocaleTimeString('id-ID', {
                                hour: '2-digit'
                                , minute: '2-digit'
                                , hour12: false
                            });

                            let statusBadge = '';
                            switch (item.status.toLowerCase()) {
                                case 'stand by':
                                    statusBadge = '<span class="badge bg-success">Stand By</span>';
                                    break;
                                case 'pergi':
                                    statusBadge = '<span class="badge bg-warning text-dark">Pergi</span>';
                                    break;
                                case 'perbaikan':
                                    statusBadge = '<span class="badge bg-danger">Perbaikan</span>';
                                    break;
                                default:
                                    statusBadge = `<span class="badge bg-secondary">${item.status}</span>`;
                                    break;
                            }

                            tableData.push([
                                i + 1
                                , item.id
                                , tanggalUpdate
                                , jamUpdate
                                , item.mobil
                                , statusBadge
                                , item.pemakai
                                , item.driver
                                , item.tujuan
                                , item.keterangan
                                , item.pic_update
                            ]);
                        });

                        //table.clear().rows.add(tableData).draw();
                        const currentPage = table.page(); // simpan halaman saat ini
                        table.clear().rows.add(tableData).draw(false); // false supaya tetap di halaman sekarang
                        table.page(currentPage).draw(false); // kembali ke halaman sebelumnya

                        $('#loading').hide(); // Sembunyikan spinner setelah data selesai dimuat

                        if (isFirstLoad) {
                            isFirstLoad = false; // Mengatur flag agar spinner tidak muncul di pemuatan berikutnya
                        }
                    }
                    , error: function() {
                        $('#loading').hide(); // Sembunyikan spinner jika terjadi error
                    }
                });
            }

            fetchData(); // Ambil data pertama kali saat halaman dimuat
            setInterval(fetchData, 5000); // Ambil data setiap 5 detik tanpa spinner
        });

    </script>
</body>
</html>
