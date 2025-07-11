@extends('layouts.main')

@section('content')
    <div class="card shadow border-0 mx-auto">
        <div class="card-body p-4">
            <h1 class="h3 fw-bold text-center mb-4">
                Manajemen Kategori Produk
            </h1>
            <div class="row">
                <div class="row mb-4">
                    <div class="col-lg-8 mx-auto">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Tambah Kategori Baru</h6>
                            </div>
                            <div class="card-body">
                                <form id="add-category-form">
                                    <div class="mb-3">
                                        <label for="category_name" class="form-label fw-semibold">Nama Kategori:</label>
                                        <input type="text" class="form-control" id="category_name" name="category_name"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Tambah Kategori</button>
                                </form>
                                <div id="message-area" class="alert text-center fw-medium d-none mt-3" role="alert">
                                    <!-- Pesan akan ditampilkan di sini -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Daftar Kategori</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="categories-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Kategori</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data kategori akan dimuat di sini melalui JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            // Fungsi untuk menampilkan pesan
            function showMessage(msg, isSuccess) {
                const messageArea = document.getElementById('message-area');
                messageArea.textContent = msg;
                messageArea.classList.remove('d-none');
                if (isSuccess) {
                    messageArea.classList.remove('alert-danger');
                    messageArea.classList.add('alert-success');
                } else {
                    messageArea.classList.remove('alert-success');
                    messageArea.classList.add('alert-danger');
                }
            }

            // Fungsi untuk memuat data kategori ke tabel
            function loadCategories() {
                $.ajax({
                    url: '/performa-produk/kategori/get',
                    type: 'GET',
                    success: function(response) {
                        // Inisialisasi DataTable jika belum ada
                        if (!$.fn.DataTable.isDataTable('#categories-table')) {
                            $('#categories-table').DataTable({
                                data: response.categories,
                                destroy: true,
                                columns: [{
                                        data: null,
                                        render: function(data, type, row, meta) {
                                            return meta.row + 1;
                                        }
                                    },
                                    {
                                        data: 'nama_kategori',
                                        render: function(data, type, row) {
                                            return `<a href="/performa-produk/kategori/detail/${row.id}" class="text-primary">${data}</a>`;
                                        }
                                    },
                                    {
                                        data: 'id',
                                        render: function(data, type, row) {
                                            return `<button class="btn btn-danger btn-sm delete-category" data-id="${data}">Hapus</button>`;
                                        },
                                        orderable: false,
                                        searchable: false
                                    }
                                ],
                                language: {
                                    emptyTable: "Tidak ada data kategori."
                                }
                            });
                        } else {
                            // Jika sudah ada, update datanya saja
                            const table = $('#categories-table').DataTable();
                            table.clear();
                            table.rows.add(response.categories);
                            table.draw();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading categories:', error);
                        showMessage('Gagal memuat kategori.', false);
                    }
                });
            }

            // Event listener untuk form tambah kategori
            $('#add-category-form').on('submit', function(e) {
                e.preventDefault();
                const categoryName = $('#category_name').val();

                $.ajax({
                    url: '/performa-produk/kategori/add',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nama_kategori: categoryName
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, true);
                            $('#category_name').val(''); // Kosongkan input
                            loadCategories(); // Muat ulang daftar kategori
                        } else {
                            showMessage(response.message, false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error adding category:', error);
                        showMessage('Terjadi kesalahan saat menambahkan kategori.', false);
                    }
                });
            });

            // Event listener untuk tombol hapus kategori (delegasi event)
            $(document).on('click', '.delete-category', function() {
                const categoryId = $(this).data('id');
                if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
                    $.ajax({
                        url: '/performa-produk/kategori/delete/' + categoryId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                showMessage(response.message, true);
                                loadCategories(); // Muat ulang daftar kategori
                            } else {
                                showMessage(response.message, false);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting category:', error);
                            showMessage('Terjadi kesalahan saat menghapus kategori.', false);
                        }
                    });
                }
            });

            // Muat kategori saat halaman pertama kali dimuat
            $(document).ready(function() {
                loadCategories();
            });
        </script>
    @endpush
