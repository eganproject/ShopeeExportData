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
                            <option value="current sales">Periode 1 (saat ini)</option>
                            <option value="current sales_twos">Periode 2 (saat ini)</option>
                            <option value="current sales_threes">Periode 3 (saat ini)</option>
                            <option value="current sales_fours">Periode 4 (saat ini)</option>
                            <option value="previous sales">Periode 1 (-prev)</option>
                            <option value="previous sales_twos">Periode 2 (-prev)</option>
                            <option value="previous sales_threes">Periode 3 (-prev)</option>
                            <option value="previous sales_fours">Periode 4 (-prev)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="periode2" class="form-label">Periode B</label>
                        <select name="periode2" id="periode2" class="form-select">
                            <option value="current sales">Periode 1 (saat ini)</option>
                            <option value="current sales_twos">Periode 2 (saat ini)</option>
                            <option value="current sales_threes">Periode 3 (saat ini)</option>
                            <option value="current sales_fours">Periode 4 (saat ini)</option>
                            <option value="previous sales">Periode 1 (-prev)</option>
                            <option value="previous sales_twos">Periode 2 (-prev)</option>
                            <option value="previous sales_threes">Periode 3 (-prev)</option>
                            <option value="previous sales_fours">Periode 4 (-prev)</option>
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
                        <table class="table table-hover align-middle" id="tableComparative" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">No</th>
                                    <th style="min-width: 150px;" rowspan="2" class="text-center align-middle">Kategori
                                    </th>
                                    <th colspan="3" class="text-center align-middle">Period A</th>
                                    <th colspan="3" class="text-center align-middle">Period B</th>
                                    <th rowspan="2" class="text-center align-middle">Selisih</th>
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
                    var html = '';
                    let totalShopee1 = 0,
                        totalTiktok1 = 0,
                        total1 = 0,
                        totalShopee2 = 0,
                        totalTiktok2 = 0,
                        total2 = 0,
                        totalSelisih = 0;

                    var selisih = 0;
                    //    looping untuk response.data
                    //    lalu diisi ke dalam table
                    for (let i = 0; i < response.data.length; i++) {
                        selisih = 0;
                        // carikan selisih dari pendapatan_per_1 dan pendapatan_per_2
                        selisih = response.data[i].pendapatan_per_2 - response.data[i].pendapatan_per_1;


                        html += '<tr>';
                        html += '<td>' + (i + 1) + '</td>';
                        html += '<td><a href="/performa-produk/compare-sales/kategori/' + response.data[i].id + '">' +
                            response.data[i].nama_kategori + '</a></td>';
                        html += '<td>' + parseInt(response.data[i].pendapatan_shopee_per_1).toLocaleString(
                            'id-ID').replace(/,/g, '.') + '</td>';
                        html += '<td>' + parseInt(response.data[i].pendapatan_tiktok_per_1).toLocaleString(
                            'id-ID').replace(/,/g, '.') + '</td>';
                        html += '<td>' + parseInt(response.data[i].pendapatan_per_1).toLocaleString('id-ID')
                            .replace(/,/g, '.') + '</td>';
                        html += '<td>' + parseInt(response.data[i].pendapatan_shopee_per_2).toLocaleString(
                            'id-ID').replace(/,/g, '.') + '</td>';
                        html += '<td>' + parseInt(response.data[i].pendapatan_tiktok_per_2).toLocaleString(
                            'id-ID').replace(/,/g, '.') + '</td>';
                        html += '<td>' + parseInt(response.data[i].pendapatan_per_2).toLocaleString('id-ID')
                            .replace(/,/g, '.') + '</td>';
                        html += '<td class="' + (selisih < 0 ? 'text-danger' : 'text-success') + '">' +
                            parseInt(selisih).toLocaleString('id-ID').replace(/,/g, '.') + '</td>';
                        html += '</tr>';


                        // Calculate the sum for each column


                        totalShopee1 += parseInt(response.data[i].pendapatan_shopee_per_1);
                        totalTiktok1 += parseInt(response.data[i].pendapatan_tiktok_per_1);
                        total1 += parseInt(response.data[i].pendapatan_per_1);
                        totalShopee2 += parseInt(response.data[i].pendapatan_shopee_per_2);
                        totalTiktok2 += parseInt(response.data[i].pendapatan_tiktok_per_2);
                        total2 += parseInt(response.data[i].pendapatan_per_2);
                        totalSelisih += response.data[i].pendapatan_per_2 - response.data[i]
                            .pendapatan_per_1;


                        // Append the totals to the footer



                    }


                    $('#tableComparative tbody').html(html);
                    $('#tableComparative tfoot').html(`
                    <tr>
                        <th colspan="2" class="text-center">Total</th>
                        <th>${totalShopee1.toLocaleString('id-ID').replace(/,/g, '.')}</th>
                        <th>${totalTiktok1.toLocaleString('id-ID').replace(/,/g, '.')}</th>
                        <th>${total1.toLocaleString('id-ID').replace(/,/g, '.')}</th>
                        <th>${totalShopee2.toLocaleString('id-ID').replace(/,/g, '.')}</th>
                        <th>${totalTiktok2.toLocaleString('id-ID').replace(/,/g, '.')}</th>
                        <th>${total2.toLocaleString('id-ID').replace(/,/g, '.')}</th>
                        <th class="${totalSelisih < 0 ? 'text-danger' : 'text-success'}">${totalSelisih.toLocaleString('id-ID').replace(/,/g, '.')}</th>
                    </tr>
                `);

                }
            });
        }
    </script>
@endpush
