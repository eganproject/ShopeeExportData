@extends('layouts.main')

{{-- Menambahkan style kustom dan library eksternal --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Font dan body */
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            /* Font modern yang mudah dibaca */
        }

        /* Kustomisasi Card */
        .custom-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            /* Membuat card memiliki tinggi yang sama dalam satu baris */
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        /* Tombol modern */
        .btn-modern {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            transition: all 0.2s ease;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
        }

        /* Kustomisasi Tabel dengan DataTables */
        .dataTables_wrapper {
            padding-top: 1rem;
        }

        .dataTables_filter input,
        .dataTables_length select {
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            border: 1px solid #dee2e6 !important;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .table-responsive {
            overflow-x: auto;
        }

        /* Loading Overlay */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            display: none;
            /* Awalnya disembunyikan */
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
@endpush


@section('content')
    {{-- Indikator Loading --}}
    <div id="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Header Halaman -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mt-2 mb-0 fw-bold">Manajemen Kategori Produk</h3>
                <small class="text-muted">Tambah, lihat, dan hapus kategori produk.</small>
            </div>
        </div>

        <!-- Konten Utama: Form dan Tabel -->
        <div class="row g-4">
            <!-- Kolom Form Tambah Kategori -->
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Tambah Kategori Baru</h5>
                        <p class="text-muted small">Buat kategori baru untuk mengelompokkan produk Anda.</p>
                        <form id="add-category-form">
                            <div class="mb-3">
                                <label for="parent_id" class="form-label fw-semibold">Parent</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="" disabled selected>Pilih Parent</option>
                                    <option value="0">Parent</option>
                                    @foreach ($parent as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="category_name" class="form-label fw-semibold">Nama Kategori</label>
                                <input type="text" class="form-control" id="category_name" name="category_name"
                                    placeholder="Contoh: Pakaian Pria" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-modern w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-plus-circle-fill me-1" viewBox="0 0 16 16">
                                    <path
                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z" />
                                </svg>
                                Tambah Kategori
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Kolom Tabel Daftar Kategori -->
            <div class="col-lg-8">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Daftar Kategori</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="categories-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Parent</th>
                                        <th>Nama Kategori</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data diisi via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Sertakan library eksternal --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#parent_id').select2({
                placeholder: "Pilih Parent",
                allowClear: true
            });
        });
    </script>
    
    <script>
        // --- VARIABEL GLOBAL & HELPER ---
        let categoriesTableInstance;
        const showLoading = () => $('#loading-overlay').css('display', 'flex');
        const hideLoading = () => $('#loading-overlay').hide();

        /**
         * Fungsi untuk memuat atau memuat ulang data kategori ke dalam DataTable.
         */
        function loadCategories() {
            showLoading();
            $.ajax({
                url: '/performa-produk/kategori/get',
                type: 'GET',
                success: function(response) {
                    // Gunakan API DataTables untuk memperbarui data secara efisien
                    categoriesTableInstance.clear();
                    categoriesTableInstance.rows.add(response.categories || []);
                    categoriesTableInstance.draw();
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', 'Tidak dapat memuat daftar kategori dari server.', 'error');
                },
                complete: function() {
                    hideLoading();
                }
            });
        }

        /**
         * Fungsi untuk menghapus kategori dengan konfirmasi SweetAlert.
         * @param {string} categoryId - ID kategori yang akan dihapus.
         */
        function deleteCategory(categoryId) {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Kategori ini dan semua data produk di dalamnya akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    $.ajax({
                        url: `/performa-produk/kategori/delete/${categoryId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                loadCategories(); // Muat ulang data setelah berhasil
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus kategori.', 'error');
                        },
                        complete: function() {
                            hideLoading();
                        }
                    });
                }
            });
        }

        // --- INISIALISASI & EVENT LISTENERS ---
        $(document).ready(function() {
            // Inisialisasi DataTable saat dokumen siap
            categoriesTableInstance = $('#categories-table').DataTable({
                processing: true,
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'parent',
                        render: (data, type, row) => `${data}`
                    },
                    {
                        data: 'nama_kategori',
                        render: (data, type, row) =>
                            `<a href="/performa-produk/kategori/detail/${row.id}" class="text-decoration-none fw-semibold">${data}</a>`
                    },
                    {
                        data: 'id',
                        searchable: false,
                        orderable: false,
                        render: (data) =>
                            `<button class="btn btn-danger btn-sm" onclick="deleteCategory('${data}')">Hapus</button>`
                    }
                ],
                language: {
                    emptyTable: "Belum ada kategori yang ditambahkan.",
                    zeroRecords: "Kategori tidak ditemukan.",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    infoFiltered: "(difilter dari _MAX_ total entri)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // Muat data untuk pertama kali
            loadCategories();

            // Event listener untuk form tambah kategori
            $('#add-category-form').on('submit', function(e) {
                e.preventDefault();
                showLoading();
                const categoryName = $('#category_name').val();
                const parentId = $('#parent_id').val();

                $.ajax({
                    url: '/performa-produk/kategori/add',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nama_kategori: categoryName,
                        parent_id: parentId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#category_name').val(''); // Kosongkan input
                            loadCategories(); // Muat ulang daftar kategori
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menambahkan kategori.',
                            'error');
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            });
        });
    </script>
@endpush
