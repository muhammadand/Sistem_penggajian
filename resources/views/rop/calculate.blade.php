@extends('layoutadmin.template')

@section('content')




<div class="container mx-auto my-5">
    <h1 class="text-2xl font-bold mb-4">Hitung Reorder Point (ROP)</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('rop.calculate') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="product_id" class="form-label">Pilih Produk:</label>
                    <select name="product_id" id="product_id" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produk as $item)
                            <option value="{{ $item->id_produk }}">{{ $item->nama_obat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="daily_usage" class="form-label">Permintaan Harian:</label>
                    <input type="number" name="daily_usage" id="daily_usage" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="lead_time" class="form-label">Waktu Pengiriman (hari):</label>
                    <input type="number" name="lead_time" id="lead_time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="safety_stock" class="form-label">Safety Stock:</label>
                    <input type="number" name="safety_stock" id="safety_stock" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Hitung ROP</button>
            </form>

            <div class="mt-4">
                <h2 class="text-xl font-semibold">Perhatian:</h2>
                <p class="text-danger">Hanya satu produk yang diperbolehkan untuk dihitung ROP-nya.</p>
            </div>
        </div>
    </div>
</div>
@endsection
