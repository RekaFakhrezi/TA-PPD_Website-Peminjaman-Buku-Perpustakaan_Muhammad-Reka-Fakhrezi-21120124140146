<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .navbar {
            background-color: #3498db;
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
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h1>Selamat Datang di Dashboard Admin Perpustakaan</h1>
    <p>Halo, <?php echo $_SESSION['username']; ?>! Anda telah berhasil login.</p>
    <a href="dashboard.php?logout=true">Logout</a>
</body>
</html>
