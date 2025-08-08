@extends('layouts.main')

@push('styles')
    {{-- Font Awesome for modern and consistent icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Keyframe animation for cards appearing on page load */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-title {
            color: #2c3e50;
            font-weight: 700;
        }

        .card-link {
            text-decoration: none;
        }

        .modern-card {
            border: none;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            color: #fff;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
            transform-style: preserve-3d; /* Enable 3D transforms */
        }

        .modern-card:hover {
            transform: perspective(1200px) translateY(-12px) rotateX(10deg); /* Powerful 3D tilt effect */
            box-shadow: 0 45px 65px rgba(0, 0, 0, 0.25); /* Softer, deeper shadow */
        }

        /* Subtle geometric background pattern */
        .modern-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 0;
            opacity: 0.8;
            transition: opacity 0.5s ease;
        }

        /* Sweeping light effect on hover */
        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 70%;
            height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.25) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg);
            transition: left 0.9s cubic-bezier(0.23, 1, 0.32, 1);
            z-index: 1;
        }

        .modern-card:hover::before {
            left: 150%;
        }

        .modern-card .card-body {
            position: relative;
            z-index: 2; /* Ensure content is above effects */
            transform: translateZ(25px); /* Bring content forward in 3D space */
        }

        .modern-card .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            transform: translateZ(40px); /* Push icon even further forward */
        }

        .modern-card:hover .icon-circle {
            transform: translateZ(55px) rotate(10deg); /* Rotate and bring forward on hover */
            background-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.3);
        }

        .modern-card .card-title {
            font-weight: 600;
            font-size: 1.3rem;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .modern-card .card-text {
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        /* Refined Card Specific Colors */
        .card-primary-shadow {
            background: linear-gradient(135deg, #0d6efd, #0747a6);
            box-shadow: 0 15px 30px rgba(13, 110, 253, 0.2);
        }

        .card-success-shadow {
            background: linear-gradient(135deg, #198754, #0b5331);
            box-shadow: 0 15px 30px rgba(25, 135, 84, 0.2);
        }

        .card-warning-shadow {
            background: linear-gradient(135deg, #ffc107, #b38600);
            box-shadow: 0 15px 30px rgba(255, 193, 7, 0.2);
        }
        
        .card-danger-shadow {
            background: linear-gradient(135deg, #dc3545, #8f131f);
            box-shadow: 0 15px 30px rgba(220, 53, 69, 0.2);
        }

        .card-info-shadow {
            background: linear-gradient(135deg, #0dcaf0, #067c94);
            box-shadow: 0 15px 30px rgba(13, 202, 240, 0.2);
        }

        .card-purple-shadow {
            background: linear-gradient(135deg, #6f42c1, #482a7e);
            box-shadow: 0 15px 30px rgba(111, 66, 193, 0.2);
        }

    </style>
@endpush

@section('content')
    <div class="container-fluid py-5">
        <h1 class="text-center mb-5 dashboard-title">Dashboard Performa Produk</h1>
        <div class="row justify-content-center">

            {{-- Card 1 --}}
            <div class="col-lg-4 col-md-6 mb-4" style="animation-delay: 0.1s;">
                <a href="/performa-produk" class="card-link">
                    <div class="card modern-card card-primary-shadow">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle mb-3">
                                <i class="fa-solid fa-chart-line fa-2x"></i>
                            </div>
                            <h5 class="card-title">Performa</h5>
                            <p class="card-text">Analisis performa produk dalam 1 periode.</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Card 2 --}}
            <div class="col-lg-4 col-md-6 mb-4" style="animation-delay: 0.2s;">
                <a href="/performa-produk/compare" class="card-link">
                    <div class="card modern-card card-success-shadow">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle mb-3">
                                <i class="fa-solid fa-layer-group fa-2x"></i>
                            </div>
                            <h5 class="card-title">Comparative Performa</h5>
                            <p class="card-text">Perbandingan performa produk 2 periode.</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Card 3 --}}
            <div class="col-lg-4 col-md-6 mb-4" style="animation-delay: 0.3s;">
                <a href="/performa-produk/kategori" class="card-link">
                    <div class="card modern-card card-warning-shadow">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle mb-3">
                                <i class="fa-solid fa-tags fa-2x"></i>
                            </div>
                            <h5 class="card-title">Kategori</h5>
                            <p class="card-text">Lihat kategori produk.</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Card 4 --}}
            <div class="col-lg-4 col-md-6 mb-4" style="animation-delay: 0.4s;">
                <a href="/performa-produk/compare-sales" class="card-link">
                    <div class="card modern-card card-danger-shadow">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle mb-3">
                                <i class="fa-solid fa-calendar-days fa-2x"></i>
                            </div>
                            <h5 class="card-title">Comparative Sales</h5>
                            <p class="card-text">Bandingkan sales dalam 4 periode.</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Card 5 --}}
            <div class="col-lg-4 col-md-6 mb-4" style="animation-delay: 0.5s;">
                <a href="/performa-produk/compare-sales/twoperiod" class="card-link">
                    <div class="card modern-card card-info-shadow">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle mb-3">
                                <i class="fa-solid fa-calendar-week fa-2x"></i>
                            </div>
                            <h5 class="card-title">Sales 2 Periode</h5>
                            <p class="card-text">Bandingkan sales dalam 2 periode.</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Card 6 --}}
            {{-- <div class="col-lg-4 col-md-6 mb-4" style="animation-delay: 0.6s;">
                <a href="/performa-produk/pelanggan" class="card-link">
                    <div class="card modern-card card-purple-shadow">
                        <div class="card-body text-center p-4">
                            <div class="icon-circle mb-3">
                                <i class="fa-solid fa-users fa-2x"></i>
                            </div>
                            <h5 class="card-title">Analisis Pelanggan</h5>
                            <p class="card-text">Pahami segmentasi pelanggan Anda.</p>
                        </div>
                    </div>
                </a>
            </div> --}}

        </div>
    </div>
@endsection
