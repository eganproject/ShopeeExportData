@extends('layouts.main')

{{-- Menambahkan style kustom dan library eksternal --}}
@section('content')
    <div class="card">
        <div class="card-header bg-white">
            <div class="card-title fw-bold fst-italic fs-5">
                Comparative analyze 2 periode in Kategori
            </div>
        </div>
        <div class="card-body">
            <form action="" method="" id="formInputNih">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="periode1" class="form-label">Periode A</label>
                        <select name="periode1" id="periode1" class="form-select">
                            <option value="cur_1">Periode 1 (saat ini)</option>
                            <option value="cur_2">Periode 2 (saat ini)</option>
                            <option value="cur_3">Periode 3 (saat ini)</option>
                            <option value="cur_4">Periode 4 (saat ini)</option>
                            <option value="prev_1">Periode 1 (-prev)</option>
                            <option value="prev_2">Periode 2 (-prev)</option>
                            <option value="prev_3">Periode 3 (-prev)</option>
                            <option value="prev_4">Periode 4 (-prev)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="periode2" class="form-label">Periode B</label>
                        <select name="periode2" id="periode2" class="form-select">
                            <option value="cur_1">Periode 1 (saat ini)</option>
                            <option value="cur_2">Periode 2 (saat ini)</option>
                            <option value="cur_3">Periode 3 (saat ini)</option>
                            <option value="cur_4">Periode 4 (saat ini)</option>
                            <option value="prev_1">Periode 1 (-prev)</option>
                            <option value="prev_2">Periode 2 (-prev)</option>
                            <option value="prev_3">Periode 3 (-prev)</option>
                            <option value="prev_4">Periode 4 (-prev)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="shop_id" class="form-label">Toko</label>
                        <select name="shop_id" id="shop_id" class="form-select">
                            <option value="semua">Semua Toko</option>
                            @foreach ($shop as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">

                        <button type="button" class="btn btn-primary" onclick="getDataTwoPeriod()">Filter</button>
                    </div>

                </div>
            </form>

        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3 text-center">Detail Pendapatan per SKU</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="table-periode-1" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">No</th>
                                    <th rowspan="2" class="text-center align-middle">SKU</th>
                                    <th style="min-width: 150px;" rowspan="2" class="text-center align-middle">Produk
                                    </th>
                                    <th colspan="3" class="text-center align-middle">Period A</th>
                                    <th rowspan="2" class="text-center align-middle">Selisih</th>
                                    <th colspan="3" class="text-center align-middle">Period B</th>
                                </tr>
                                <tr>
                                    <th>Shopee</th>
                                    <th>Tiktok</th>
                                    <th>Total</th>
                                    <th>Shopee</th>
                                    <th>Tiktok</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <script>
        const periode1Select = document.getElementById('periode1');
        const periode2Select = document.getElementById('periode2');

        periode1Select.addEventListener('change', function() {
            if (periode1Select.value === periode2Select.value) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Periode A dan periode B tidak boleh sama',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                periode1Select.value = "";
            }
        });

        periode2Select.addEventListener('change', function() {
            if (periode2Select.value === periode1Select.value) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Periode A dan periode B tidak boleh sama',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                periode2Select.value = "";
            }
        });

        function getDataTwoPeriod() {
            if (periode1Select.value === periode2Select.value) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Periode A dan periode B tidak boleh sama',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            $.ajax({
                url: '/performa-produk/compare-sales/twoperiod',
                type: 'POST',
                data: $('#formInputNih').serialize(),
                success: function(response) {
                    $('#table-data tbody').html(response);
                }
            });
        }
    </script>
@endpush
