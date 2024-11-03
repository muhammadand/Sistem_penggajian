@extends('layout.template')

@section('content')
<div class="content mt-2 container">
    <div class="row g-4">
        <div class="card" style="width: 10rem;">
            <div class="card-header" style="font-size: 10px;">Nama produk yang tersedia</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <a href="#" class="1">
                        <i class="fa fa-plus"></i>
                    </a>
                </li>
                @foreach($products as $product)
                <li class="list-group-item" style="font-size: 10px;">{{ $product->name }}</li>
                @endforeach
            </ul>
        </div>

        <!-- Loop untuk menampilkan produk -->
        @foreach($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card">
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
                    <p class="mb-0" style="font-size: 14px;">Rp. {{ $product->harga }}</p>
                    <button class="btn btn-outline-primary btn-sm" style="font-size:24px;">
                        <a href="{{ route('orders.create', $product) }}">
                            <i class="fa-solid fa-cart-plus"></i>
                        </a>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item disabled">
                    <a class="page-link">Previous</a>
                </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<script>
   
@endsection
