<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - POS</title>
    
    <!-- Tambahkan CDN Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <h1 class="mb-4">Selamat Datang di Aplikasi POS</h1>

    <!-- Tombol menuju halaman Products dengan Bootstrap -->
    <a href="{{ url('/products') }}" class="btn btn-primary">
        Lihat Produk
    </a>
    <a href="{{ url('/user/1/name/Naditya') }}" class="btn btn-primary">
        User Profile
    </a>
    
    <a href="{{ url('/penjualan') }}" class="btn btn-primary">
        Penjualan
    </a>

    <!-- Tambahkan script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
