<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Abel&family=Akshar:wght@404&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/costume.css') }}">
    <title>Toko Kue |</title>
    <style>
        .footer {
            background-color: #A67B5B;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .footer a {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <main>
        <nav class="navbar navbar-dark navbar-expand-lg" style="background-color: #A67B5B">
            <div class="container">
              <a class="navbar-brand" href="#">Toko Kue</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse justify-content-end gap-4" id="navbarSupportedContent">
                <ul class="navbar-nav gap-4">
                  <li class="nav-item">
                    <a class="nav-link {{Request::path() =='/' ? 'active' : '';}}" aria-current="page" href="{{('/')}}">Home</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link {{Request::path() =='shop' ? 'active' : '';}} " href="{{('shop')}}">shop</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link {{Request::path() =='contact' ? 'active' : '';}} " href="{{('contact')}}">Contact Us</a>
                  </li>
                </ul>
                <form class="d-flex gap-4 align-items-center">
                  <button class="btn btn-success" type="button"><a href="{{route('login')}}"><Label></Label>Login  | Register</a></button>
                  <div class="notif">
                    <a href="{{route('orders.index')}}">  <i class="fa-solid icon-nav fa-bag-shopping"></i></a>
                
                  </div>
                  <div class="circle">{{$totalOrders}}</div>
                </form>
              </div>
            </div>
          </nav>

        <!-- Page content -->
        @yield('content')

        <footer class="footer">
            <div class="container mt-5">
                <div class="d-flex flex-wrap justify-content-center gap-5 pt-3">
                    <div class="title-left w-30 text-center">
                        <div class="header-title fs-6 mb-1 font-weight-bold">
                            Toko Kue Patma
                        </div>
                        <p class="small">Lorem, ipsum dolor sit amet consecquia.</p>
                    </div>
            
                    <div class="title-middle w-20 text-center">
                        <div class="header-title fs-6 mb-1 font-weight-bold">
                            Tentang kami
                        </div>
                        <p class="small">Nim quam magni quis.</p>
                    </div>
            
                    <div class="title-sosmed w-10 text-center">
                        <div class="header-title fs-6 mb-1 font-weight-bold">
                            Sosial Media
                        </div>
                        <div class="sosmed">
                            <i class="fa-brands fa-instagram me-2"></i>
                            <i class="fa-brands fa-facebook me-2"></i>
                            <i class="fa-brands fa-whatsapp me-2"></i>
                            <i class="fa-brands fa-linkedin"></i>
                        </div>
                    </div>
                </div>
                <div class="text-center py-2 mt-2">
                    <i class="small">Copy Right @imasimaniar 2024</i>
                </div>
            </div>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script src="{{asset('js/jquery-1.11.0.min.js')}}"></script>
    <script src="{{asset('js/plugins.js')}}"></script>
    <script src="{{asset('js/script.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
      crossorigin="anonymous"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</body>

</html>
