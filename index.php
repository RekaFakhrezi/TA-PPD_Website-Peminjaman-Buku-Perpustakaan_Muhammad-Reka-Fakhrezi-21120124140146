<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Perpustakaan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            text-align: center;
        }

        .navbar a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            display: inline-block;
            font-size: 18px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #575757;
            border-radius: 4px;
        }

        div.content {
            padding: 14px 20px;
        }

        div.content h1 {
            text-align: center;
        }

        div.content h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Dashboard</a>
        <a href="books.php">Daftar Buku</a>
        <a href="borrowings.php">Daftar Peminjam</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Dashboard Perpustakaan</h1>
        <h2>Selamat Datang, <?php echo $_SESSION['username']; ?>!</h2>
    </div>
</body>
</html>
