@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .card-link {
            text-decoration: none;
        }

        .modern-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            overflow: hidden;
            /* Ensures the pseudo-element doesn't overflow */
            position: relative;
        }

        .modern-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .modern-card .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .modern-card:hover .icon-circle {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .modern-card .card-body {
            position: relative;
            z-index: 2;
        }

        .card-title,
        .card-text {
            color: #fff;
        }

        .card-text {
            opacity: 0.8;
        }

        /* Card Specific Colors */
        .card-primary-shadow {
            background: linear-gradient(45deg, #0d6efd, #0558d3);
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.3);
        }

        .card-success-shadow {
            background: linear-gradient(45deg, #198754, #10633e);
            box-shadow: 0 10px 25px rgba(25, 135, 84, 0.3);
        }

        .card-warning-shadow {
            background: linear-gradient(45deg, #ffc107, #d39e00);
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.3);
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <a href="/performa-produk" class="card-link">
                <div class="card modern-card card-primary-shadow">
                    <div class="card-body text-center">
                        <div class="icon-circle mb-3">
                            <i class="bi bi-graph-up fa-2x"></i>
                        </div>
                        <h5 class="card-title">Performa</h5>
                        <p class="card-text">Performa produk 1 periode.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <a href="/performa-produk/compare" class="card-link">
                <div class="card modern-card card-success-shadow">
                    <div class="card-body text-center">
                        <div class="icon-circle mb-3">
                            <i class="bi bi-bar-chart-steps  fa-2x"></i>
                        </div>
                        <h5 class="card-title">Comparative Performa Produk</h5>
                        <p class="card-text">Perbandingan 2 periode.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <a href="/performa-produk/kategori" class="card-link">
                <div class="card modern-card card-warning-shadow">
                    <div class="card-body text-center">
                        <div class="icon-circle mb-3">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                        <h5 class="card-title">Kategori</h5>
                        <p class="card-text">Lihat per Kategori.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <a href="/performa-produk/compare-sales" class="card-link">
                <div class="card modern-card card-success-shadow">
                    <div class="card-body text-center">
                        <div class="icon-circle mb-3">
                          <i class="fa-regular fa-calendar-days fa-2x"></i>
                        </div>
                        <h5 class="card-title">Comparative Sales</h5>
                        <p class="card-text">Comparative 4 Periode dari Sales.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <a href="/performa-produk/compare-sales/twoperiod" class="card-link">
                <div class="card modern-card card-warning-shadow">
                    <div class="card-body text-center">
                        <div class="icon-circle mb-3">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                        <h5 class="card-title">Comparative Sales 2 Periode</h5>
                        <p class="card-text">Comparative 2 Periode dari Sales.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection
