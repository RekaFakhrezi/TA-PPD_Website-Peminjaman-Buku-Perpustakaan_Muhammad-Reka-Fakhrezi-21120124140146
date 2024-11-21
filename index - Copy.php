<?php
include 'db.php'; 


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

if (isset($_POST['add'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $year = (int)$_POST['year'];
    $category = $conn->real_escape_string($_POST['category']);
    $stock = (int)$_POST['stock'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            echo "File bukan gambar.";
            exit;
        }
        if (!in_array($imageFileType, $allowedTypes)) {
            echo "Hanya file JPG, JPEG, PNG, & GIF yang diperbolehkan.";
            exit;
        }
        if ($_FILES['image']['size'] > 2000000) {
            echo "Ukuran file terlalu besar (maksimal 2MB).";
            exit;
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO books (Title, Author, Year, Category, Stock, Image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisis", $title, $author, $year, $category, $stock, $imageName);

            if ($stmt->execute()) {
                echo "Buku berhasil ditambahkan!";
            } else {
                echo "Gagal menambahkan buku: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Gagal mengunggah gambar.";
        }
    } else {
        echo "Pilih file gambar yang valid.";
    }
}

$sql = "SELECT * FROM books";
$result = $conn->query($sql);
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

        .table-container {
            max-width: 90%;
            margin: 20px auto;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 14px;
        }

        table th, table td {
            padding: 8px; 
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #3498db;
            color: #ffffff;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table img {
            border-radius: 5px;
            max-width: 60px;
            max-height: 60px;
        }

        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        form input, form button {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border-radius: 5px;
        }

        form input {
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        form button {
            background-color: #3498db;
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #2980b9;
        }

        a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
            color: #2980b9;
        }

        .header {
            text-align: center;
            margin: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header h2 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 16px;
            color: #555;
        }
        
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="index.php">Dashboard</a>
        <a href="add_book.php">Tambah Buku</a>
        <a href="index.php?logout=true" class="logout-btn">Logout</a>
    </div>

    <!-- Konten -->
    <div class="header">
        <h1>Dashboard Perpustakaan</h1>
        <h2>Selamat Datang, <?php echo $_SESSION['username']; ?>!</h2>
        <p>Gunakan menu di bawah ini untuk mengelola data buku.</p>
        <h2>Daftar Buku</h2>
        <div class="table-container">
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tahun</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['ID']; ?></td>
                            <td><?php echo $row['Title']; ?></td>
                            <td><?php echo $row['Author']; ?></td>
                            <td><?php echo $row['Year']; ?></td>
                            <td><?php echo $row['Category']; ?></td>
                            <td><?php echo $row['Stock']; ?></td>
                            <td><img src="<?php echo $row['Image']; ?>" alt="<?php echo $row['Title']; ?>"></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['ID']; ?>">Edit</a>
                                <a href="delete.php?id=<?php echo $row['ID']; ?>">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Tidak ada buku yang tersedia.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <h2>Tambah Buku Baru</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Judul" required>
            <input type="text" name="author" placeholder="Penulis" required>
            <input type="number" name="year" placeholder="Tahun" required>
            <input type="text" name="category" placeholder="Kategori" required>
            <input type="number" name="stock" placeholder="Stok" required>
            <input type="file" name="image" required> <!-- Form input file untuk gambar -->
            <button type="submit" name="add">Tambah Buku</button>
        </form>
    </div>
</body>
</html>
