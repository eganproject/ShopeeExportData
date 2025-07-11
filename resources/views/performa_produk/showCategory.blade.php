@extends('layouts.main')


@section('content')
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="mb-4">
            <h4>{{ $kategori->nama_kategori }}</h4>
        </div>

        <form id="productForm">
            @csrf
            <div class="form-group">
            <label for="product_code">Kode Produk (SKU)</label>
            <input type="hidden" name="kategori_id" id="kategori_id" value="{{ $kategori->id }}">
            <input type="text" class="form-control" id="product_code" name="product_code" placeholder="Masukan kode produk." required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Submit</button>
        </form>
    </div>

    <div class="mt-4"></div>
        <table id="productCodesTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>
    </div>


</div>
@endsection


@push('scripts')
<script>
            $(document).ready(function() {
            $('#productForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                url: "{{ route('performa_produk.createProductCode') }}",
                method: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function(response) {
                    alert('Product saved successfully!');
                    $('#productForm')[0].reset();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
                });
            });
            });



            function loadProductCodes() {
                $.ajax({
                    url: `/performa-produk/kategori/get-product-codes/${$('#kategori_id').val()}`,
                    method: "GET",
                    success: function(response) {
                        let tbody = $('#productCodesTable tbody');
                        tbody.empty();
                        response.data.forEach(function(item, index) {
                            tbody.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td class="${item.status == 'tidak_ada' ? 'text-danger fw-bold' : ''}">${item.kode_produk}</td>
                                    <td>${item.nama_produk}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" onclick="deleteProductCode('${item.id}')">Delete</button>
                                    </td>
                                </tr>
                            `);
                        });
                    },
                    error: function(xhr) {
                        alert('Failed to load product codes');
                    }
                });
            }

            function deleteProductCode(id) {
                if (confirm('Are you sure you want to delete this product code?')) {
                    $.ajax({
                        url: `/performa-produk/kategori/delete-product-code/${id}`,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        method: "DELETE",
                        success: function() {
                            alert('Product code deleted successfully!');
                            loadProductCodes();
                        },
                        error: function(xhr) {
                            alert('Failed to delete product code');
                        }
                    });
                }
            }

            // Call loadProductCodes on page load and after form submit
            $(document).ready(function() {
                loadProductCodes();
                $('#productForm').on('submit', function() {
                    setTimeout(loadProductCodes, 500);
                });
            });
</script>

@endpush