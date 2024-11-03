@extends('layout.template')

@section('content')
<div class="container mt-5 mb-5">
    <h2>Pembayaran</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Pilih Metode Pembayaran:</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="mbanking" value="mbanking">
                        <label class="form-check-label" for="mbanking">
                            M-Banking
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="ewallet" value="ewallet">
                        <label class="form-check-label" for="ewallet">
                            E-Wallet
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="qris" value="qris">
                        <label class="form-check-label" for="qris">
                            QRIS
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentDetails" class="mt-4">
        <!-- Detail pembayaran akan muncul di sini sesuai dengan metode pembayaran yang dipilih -->
    </div>
</div>

<script>
    document.querySelectorAll('input[name="paymentMethod"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var paymentMethod = this.value;
            var paymentDetails = document.getElementById('paymentDetails');

            // Kosongkan isi detail pembayaran
            paymentDetails.innerHTML = '';

            if (paymentMethod === 'mbanking') {
                paymentDetails.innerHTML = '<div class="card mt-3"><div class="card-body"><h5 class="card-title">Pembayaran Melalui M-Banking</h5><p class="card-text">Silakan melakukan pembayaran melalui transfer m-banking dengan nomor virtual account (VA) berikut:</p><p class="card-text">Nomor Virtual Account: <strong>1234567890</strong></p></div></div>';
            } else if (paymentMethod === 'ewallet') {
                paymentDetails.innerHTML = '<div class="card mt-3"><div class="card-body"><h5 class="card-title">Pembayaran Melalui E-Wallet</h5><p class="card-text">Silakan melakukan pembayaran melalui e-wallet dengan mentransfer ke nomor HP berikut:</p><p class="card-text">Nomor HP: <strong>081234567890</strong></p></div></div>';
            } else if (paymentMethod === 'qris') {
                paymentDetails.innerHTML = '<div class="card mt-3"><div class="card-body"><h5 class="card-title">Pembayaran Melalui QRIS</h5><p class="card-text">Silakan melakukan pembayaran dengan memindai kode QR berikut:</p><img src="{{ asset('/images/qr.jpg') }}" alt="QRIS Code" class="img-fluid"></div></div>';
            }
        });
    });
</script>
@endsection
