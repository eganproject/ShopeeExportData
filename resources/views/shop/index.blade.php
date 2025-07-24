@extends('layouts.main')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0" id="form-title">Tambah Toko</h5>
                </div>
                <div class="card-body">
                    <form id="shop-form">
                        @csrf
                        <input type="hidden" id="shop-id" name="id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Toko</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary" id="save-btn">Simpan</button>
                            <button type="button" class="btn btn-secondary" id="reset-btn">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Toko</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover" id="shops-table">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
    // Setup CSRF for AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    let editMode = false;

    // Load shops
    function loadShops(){
        $.getJSON("{{ route('shops.index') }}", function(data){
            const tbody = $('#shops-table tbody').empty();
            data.forEach((shop, i) => {
                tbody.append(`
                    <tr>
                        <td>${i+1}</td>
                        <td>${shop.name}</td>
                        <td>${shop.description || '-'}</td>
                        <td>${shop.status}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-btn" data-id="${shop.id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${shop.id}">Hapus</button>
                        </td>
                    </tr>
                `);
            });
        });
    }

    loadShops();

    // Reset form
    $('#reset-btn').click(function(){
        editMode = false;
        $('#form-title').text('Tambah Toko');
        $('#shop-form')[0].reset();
        $('#shop-id').val('');
    });

    // Submit form (create or update)
    $('#shop-form').submit(function(e){
        e.preventDefault();
        const id = $('#shop-id').val();
        const url = editMode
            ? `{{ url('performa-produk/shops') }}/${id}`
            : `{{ route('shops.store') }}`;
        const method = editMode ? 'PUT' : 'POST';
        $.ajax({ url, method, data: $(this).serialize() })
         .done(res => {
            loadShops();
            $('#reset-btn').click();
        });
    });

    // Edit button
    $(document).on('click', '.edit-btn', function(){
        const id = $(this).data('id');
        $.getJSON(`{{ url('performa-produk/shops') }}/${id}`, function(shop){
            editMode = true;
            $('#form-title').text('Edit Toko');
            $('#shop-id').val(shop.id);
            $('#name').val(shop.name);
            $('#description').val(shop.description);
            $('#status').val(shop.status);
        });
    });

    // Delete button
    $(document).on('click', '.delete-btn', function(){
        if(!confirm('Yakin ingin menghapus data ini?')) return;
        const id = $(this).data('id');
        $.ajax({ url: `{{ url('performa-produk/shops') }}/${id}`, method: 'DELETE' })
         .done(() => loadShops());
    });
});
</script>
@endpush
