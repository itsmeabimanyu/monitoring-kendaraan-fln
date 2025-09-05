<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Users</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- datatables non cdn / offline --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.bundle.min.css') }}" rel="stylesheet">

    <script src="{{ asset('assets/jquery/jquery-3.6.0.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/datatables/css/jquery.dataTables.min.css') }}">
</head>

<body class="d-flex" style="min-height: 100vh;">

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <div class="container mt-3">
            <div class="text-center" style="margin-top: -20px;">
                <img src="/img/fln-logo.png" width="120px" alt="">
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">List Users</h2>

                <div class="d-flex flex-column align-items-end">
                    <span style="white-space: nowrap;">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>

                    <div class="d-flex mt-2">
                        <button class="btn btn-primary btn-sm me-3" data-bs-toggle="modal" data-bs-target="#tambahUserModal">
                            Tambah Users
                        </button>

                        <a href="/admin" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div id="loading" style="text-align: center; margin: 30px 0; display: none;">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
            </div>

            <div class="table-responsive">
                <table id="users-table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>username</th>
                            <th>Jabatan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- tambah data --}}
    <div class="modal fade" id="tambahUserModal" tabindex="-1" aria-labelledby="tambahDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-tambah-user">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahUserModalLabel">Tambah Data Users</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label>Jabatan</label>
                            <select name="jabatan" class="form-select">
                                <option value="Admin GA">Admin GA</option>
                                <option value="Staff GA">Staff GA</option>
                                <option value="Security">Security</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Buat Password Baru</label>
                            <input type="text" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- edit data --}}
    @foreach($users as $user)
    <!-- Looping untuk setiap user -->
    <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-edit-user-{{ $user->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editUserId-{{ $user->id }}" value="{{ $user->id }}">
                        <div class="mb-3">
                            <label for="editNamalengkap-{{ $user->id }}">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" id="editNamalengkap-{{ $user->id }}" value="{{ $user->nama_lengkap }}">
                        </div>
                        <div class="mb-3">
                            <label for="editUsername-{{ $user->id }}">Username</label>
                            <input type="text" class="form-control" name="username" id="editUsername-{{ $user->id }}" value="{{ $user->username }}">
                        </div>
                        <div class="mb-3">
                            <label for="editJabatan-{{ $user->id }}">Jabatan</label>
                            <select class="form-select" name="jabatan" id="editJabatan-{{ $user->id }}">
                                @foreach($jabatanList as $jabatan)
                                <option value="{{ $jabatan }}" {{ $jabatan == $user->jabatan ? 'selected' : '' }}>
                                    {{ $jabatan }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    {{-- hapus data --}}
    <div class="modal fade" id="hapusUserModal" tabindex="-1" aria-labelledby="hapusUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hapusUserModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin menghapus user <strong id="hapusUsername"></strong>?</p>
                    <input type="hidden" id="hapusUserId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="konfirmasiHapusBtnUser">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ganti Password -->
    <div class="modal fade" id="gantiPasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-ganti-password">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="gantiPasswordModalLabel">Ganti Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="modalUserId">
                        <input type="text" class="form-control mb-2" id="modalUsername" readonly style="background-color: #e9ecef; pointer-events: none;" autocomplete="modalUsername">

                        <div class="mb-3">
                            <label>Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                            @error('current_password') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Password Baru</label>
                            <input type="password" name="new_password" class="form-control" required autocomplete="new-password">
                            @error('new_password') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required autocomplete="new-password-confirmation">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Ganti Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- DataTables non cdn offline JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const table = $('#users-table').DataTable({
                columns: [{
                        title: "No"
                        , width: "40px"
                    }
                    , {
                        title: "Nama"
                    }
                    , {
                        title: "username"
                    }
                    , {
                        title: "Jabatan"
                    }
                    , {
                        title: "Action"
                    }
                ]
                , columnDefs: [{
                    targets: 0
                    , width: "40px"
                }]
                , pageLength: 10
            });

            let isFirstLoad = true; // Mengatur apakah ini pemuatan pertama kali

            function fetchData() {
                if (isFirstLoad) {
                    $('#loading').show(); // Menampilkan spinner hanya saat pemuatan pertama
                }

                $.ajax({
                    url: "{{ route('list.users.data') }}"
                    , type: 'GET'
                    , dataType: 'json'
                    , success: function(data) {
                        let tableData = [];

                        $.each(data, function(i, item) {
                            tableData.push([
                                i + 1
                                , item.nama_lengkap
                                , item.username
                                , item.jabatan
                                , `<button class="btn btn-sm btn-info btn-password" data-bs-toggle="modal" data-bs-target="#gantiPasswordModal" data-id="${item.id}" data-username="${item.username}">Ganti Password</button>
                                   <button class="btn btn-sm btn-warning btn-edit-user" data-id="${item.id}" data-username="${item.username}" data-jabatan="${item.jabatan}" data-bs-toggle="modal" data-bs-target="#editUserModal-${item.id}">
                                       Edit
                                   </button>
                                   <button class="btn btn-sm btn-danger btn-hapus-user" data-id="${item.id}" data-username="${item.username}" data-bs-toggle="modal" data-bs-target="#hapusUserModal">Hapus</button>`
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
                    , error: function(xhr) {
                        $('#loading').hide(); // Sembunyikan spinner
                    }
                });
            }

            fetchData(); // Ambil data pertama kali saat halaman dimuat
            setInterval(fetchData, 5000); // Ambil data setiap 5 detik tanpa spinner
        });

    </script>

    {{-- ganti password --}}
    <script>
        $(document).on('click', '.btn-password', function() {
            const username = $(this).data('username');
            const userId = $(this).data('id');

            $('#modalUsername').val(username); // tampilkan username di input
            $('#modalUserId').val(userId); // simpan ID user jika dibutuhkan
        });

        $('#form-ganti-password').on('submit', function(e) {
            e.preventDefault(); // Cegah form submit biasa

            let form = $(this);
            let url = form.attr('action');
            let data = form.serialize();

            $.ajax({
                url: '{{ route("users.gantiPassword") }}'
                , method: 'POST'
                , data: data
                , success: function(response) {
                    alert(response.message);
                    $('#gantiPasswordModal').modal('hide');
                    form[0].reset();
                }
                , error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let message = '';
                        for (let key in errors) {
                            message += errors[key][0] + '\n';
                        }
                        alert(message);
                    } else {
                        alert('Terjadi kesalahan server. Silakan coba lagi.');
                    }
                }
            });
        });

    </script>

    <script>
        // tambah
        $('#form-tambah-user').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route("tambah.users") }}'
                , method: 'POST'
                , data: $(this).serialize()
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(res) {
                    alert(res.message);
                    $('#tambahUserModal').modal('hide');
                    form[0].reset();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                }
                , error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let message = '';
                        for (let key in errors) {
                            message += errors[key][0] + '\n';
                        }
                        alert(message);
                    } else {
                        alert('Terjadi kesalahan server.');
                        console.log(xhr.responseText); // Boleh aktifkan untuk lihat error detail
                    }
                }
            });
        });

        // edit user
        $(document).on('submit', '[id^="form-edit-user-"]', function(e) {
            e.preventDefault();

            const form = $(this);
            const id = form.find('input[name="id"]').val();
            const nama_lengkap = form.find('input[name="nama_lengkap"]').val();
            const username = form.find('input[name="username"]').val();
            const jabatan = form.find('select[name="jabatan"]').val();

            $.ajax({
                url: `/users/${id}/edit`
                , method: 'PUT'
                , data: form.serialize()
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(res) {
                    alert(res.message);
                    $('#editUserModal-' + id).modal('hide');
                }
                , error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let message = '';
                        for (let key in errors) {
                            message += errors[key][0] + '\n';
                        }
                        alert(message);
                    } else {
                        alert('Terjadi kesalahan server.');
                        console.log(xhr.responseText);
                    }
                }
            });
        });


        // hapus
        let userIdToDelete = null;
        $(document).on('click', '.btn-hapus-user', function() {
            userIdToDelete = $(this).data('id');
            const username = $(this).data('username');

            $('#hapusUsername').text(username);
            $('#hapusUserId').val(userIdToDelete);
        });

        $('#konfirmasiHapusBtnUser').click(function() {
            if (!userIdToDelete) return;

            $.ajax({
                url: '/users/' + userIdToDelete + '/hapus'
                , type: 'POST'
                , data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                    , _method: 'DELETE'
                }
                , success: function(res) {
                    alert(res.message);
                    $('#hapusUserModal').modal('hide');
                }
                , error: function(xhr) {
                    alert('Gagal menghapus data. Silakan coba lagi.');
                    console.log(xhr.responseText);
                }
            });
        });

    </script>

</body>
</html>
