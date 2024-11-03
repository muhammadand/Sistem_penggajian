@extends('layout.template')

@section('content')
  <!-- Tambahkan link ke Google Fonts di bagian <head> -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Hero Start -->
<div class="container-fluid hero-section py-6 my-6 mt-1">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7 col-md-12">
                <small class="d-inline-block fw-bold text-uppercase hero-subtitle mb-4 animated bounceInDown">Welcome to</small>
                <h1 class="display-1 mb-4 animated bounceInDown hero-title">TOKO <span class="text-success">KUE</span> Menerima Pesanan</h1>
                <a href="{{('shop')}}" class="btn btn-success border-0 rounded-pill py-3 px-4 px-md-5 me-4 animated bounceInLeft hero-btn">Beli sekarang</a>
            </div>
            <div class="col-lg-5 col-md-12">
                <img src="images/bg_kue2.jpg" class="img-fluid rounded animated zoomIn" alt="Kue">
            </div>
        </div>
    </div>
</div>
<!-- Hero End -->

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
    }

    .hero-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.9)), url('https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
        color: #343a40;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        padding-top: 100px;
        padding-bottom: 100px;
    }

    .hero-subtitle {
        background-color: rgba(255, 255, 255, 0.7);
        border: 2px solid #00ff2f;
        color: #00ff08;
        border-radius: 50px;
        padding: 0.5rem 1rem;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .hero-title .text-primary {
        color: #007bff;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
    }

    .hero-btn {
        background-color: #00ff08;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .hero-btn:hover {
        background-color: #00b345;
        transform: translateY(-2px);
    }

    .animated {
        transition: transform 0.5s ease, opacity 0.5s ease;
    }

    .bounceInDown {
        animation: bounceInDown 1s;
    }

    .bounceInLeft {
        animation: bounceInLeft 1s;
    }

    .zoomIn {
        animation: zoomIn 1s;
    }

    @keyframes bounceInDown {
        from, 60%, 75%, 90%, to {
            animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
        }
        from {
            opacity: 0;
            transform: translate3d(0, -3000px, 0);
        }
        60% {
            opacity: 1;
            transform: translate3d(0, 25px, 0);
        }
        75% {
            transform: translate3d(0, -10px, 0);
        }
        90% {
            transform: translate3d(0, 5px, 0);
        }
        to {
            transform: none;
        }
    }

    @keyframes bounceInLeft {
        from, 60%, 75%, 90%, to {
            animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
        }
        from {
            opacity: 0;
            transform: translate3d(-3000px, 0, 0);
        }
        60% {
            opacity: 1;
            transform: translate3d(25px, 0, 0);
        }
        75% {
            transform: translate3d(-10px, 0, 0);
        }
        90% {
            transform: translate3d(5px, 0, 0);
        }
        to {
            transform: none;
        }
    }

    @keyframes zoomIn {
        from {
            opacity: 0;
            transform: scale3d(0.3, 0.3, 0.3);
        }
        50% {
            opacity: 1;
        }
    }
</style>


<div class="m-5">
    <h4>Best Seller Product</h4>
    <div class="content mt-2 container mb-5">
        <div class="row g-4">
            @forelse($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card" style="width: 100%;">
                    <div class="card-header p-0">
                        <img src="{{ asset('/images/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid" style="width: 100%; height: auto;">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title mb-1">{{ $product->name }}</h5>
                        <p class="card-text mb-2">
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star-half-alt text-warning"></i>
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <p class="mb-0" style="font-size: 14px;">Rp. {{($product->harga) }}</p>
                        
                        
                        <button class="btn btn-outline-primary btn-sm" style="font-size:24px;">
                            <a href="{{ route('orders.create', $product) }}">
                                <i class="fa-solid fa-cart-plus"></i>
                            </a>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <p>No products available.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
