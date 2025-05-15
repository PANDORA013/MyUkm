<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang di MyUkm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
        }
        a {
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #2779bd;
        }
    </style>
</head>
<body>
    <h1>Selamat Datang di <strong>MyUkm</strong> 👋</h1>
    <p>Gabung dan temukan UKM sesuai minatmu!</p>
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Daftar</a>
</body>
</html>
