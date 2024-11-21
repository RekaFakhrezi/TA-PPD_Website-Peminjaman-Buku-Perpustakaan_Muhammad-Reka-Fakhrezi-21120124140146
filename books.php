<?php
include 'db.php'; // Include koneksi database

session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect ke halaman login jika belum login
    exit;
}

// Tambah Buku Baru
if (isset($_POST['add'])) {
    // Menerima data dari form
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $year = (int)$_POST['year'];
    $category = $conn->real_escape_string($_POST['category']);
    $stock = (int)$_POST['stock'];

    // Memeriksa apakah gambar di-upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "uploads/"; // Direktori penyimpanan gambar
        $imageName = time() . "_" . basename($_FILES['image']['name']); // Nama unik untuk file
        $targetFile = $targetDir . $imageName; // Path lengkap file
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)); // Ekstensi file gambar

        // Validasi file gambar
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

        // Pindahkan gambar ke folder uploads
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Simpan nama file saja (tanpa 'uploads/') ke database
            $relativePath = $imageName;
        
            // Query untuk memasukkan data buku ke database
            $stmt = $conn->prepare("INSERT INTO books (Title, Author, Year, Category, Stock, Image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisis", $title, $author, $year, $category, $stock, $relativePath);
        
            // Eksekusi query
            if ($stmt->execute()) {
                echo "Buku berhasil ditambahkan!";
            } else {
                echo "Gagal menambahkan buku: " . $stmt->error;
            }
            $stmt->close();
        }else {
            echo "Gagal mengunggah gambar.";
        }
    } else {
        echo "Pilih file gambar yang valid.";
    }
}


// Edit Buku
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $year = intval($_POST['year']);
    $category = $conn->real_escape_string($_POST['category']);
    $stock = intval($_POST['stock']);
    $image = $_POST['existing_image']; // Gambar lama

    // Proses unggah gambar baru jika ada
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Hapus gambar lama jika ada
                if (file_exists($image)) {
                    unlink($image);
                }
                $image = $targetFile; // Path baru untuk database
            } else {
                echo "Terjadi kesalahan saat mengunggah file.";
            }
        }
    }

    // Update data buku di database
    $sql = "UPDATE books 
            SET Title = '$title', Author = '$author', Year = $year, 
                Category = '$category', Stock = $stock, Image = '$image'
            WHERE ID = $id";
    if ($conn->query($sql)) {
        echo "Buku berhasil diperbarui!";
    } else {
        echo "Kesalahan: " . $conn->error;
    }
}

// Hapus Buku
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Hapus gambar dari server
    $imageQuery = $conn->query("SELECT Image FROM books WHERE ID = $id");
    if ($imageQuery) {
        $imageData = $imageQuery->fetch_assoc();
        if (file_exists($imageData['Image'])) {
            unlink($imageData['Image']);
        }
    }
    $sql = "DELETE FROM books WHERE ID = $id";
    if ($conn->query($sql)) {
        echo "Buku berhasil dihapus!";
    } else {
        echo "Kesalahan: " . $conn->error;
    }
}

// Query untuk menampilkan daftar buku
$sql = "SELECT * FROM books";
$result = $conn->query($sql);
if (!$result) {
    die("Query gagal: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        table img {
            max-width: 80px;
            max-height: 80px;
            border-radius: 5px;
        }
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
            background-color: #333;
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

        .btn-delete {
            color: white;
            background-color: red;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 12px;
            border-radius: 5px;
            text-decoration: none;
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
            background-color: #333;
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #575757;
        }

        a {
            text-decoration: none;
            color: #333;
        }

        a:hover {
            text-decoration: none;
            color: #ffffff;
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
    <div class="navbar">
        <a href="index.php">Dashboard</a>
        <a href="books.php">Daftar Buku</a>
        <a href="borrowings.php">Daftar Peminjam</a>
        <a href="logout.php">Logout</a>
    </div>
    <h1>Daftar Buku</h1>
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
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['ID']; ?></td>
            <td><?php echo $row['Title']; ?></td>
            <td><?php echo $row['Author']; ?></td>
            <td><?php echo $row['Year']; ?></td>
            <td><?php echo $row['Category']; ?></td>
            <td><?php echo $row['Stock']; ?></td>
            <td>
                <!-- Menambahkan path "uploads/" agar gambar diambil dari folder uploads -->
                <img src="uploads/<?php echo $row['Image']; ?>" alt="<?php echo $row['Title']; ?>" width="100">
            </td>
            <td>
                <a class="btn-delete"href="?delete=<?php echo $row['ID']; ?>" onclick="return confirm('Yakin ingin menghapus buku ini?')">Hapus</a> |
                <button style="font-size:14px" onclick="location.href='?edit=<?php echo $row['ID']; ?>'">Edit <i class="fa fa-edit"></i></button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>


    <?php if (isset($_GET['edit'])): 
        $id = intval($_GET['edit']);
        $edit_query = $conn->query("SELECT * FROM books WHERE ID = $id");
        $edit_data = $edit_query->fetch_assoc();
    ?>
    <h2>Edit Buku</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $edit_data['ID']; ?>">
        <input type="hidden" name="existing_image" value="<?php echo $edit_data['Image']; ?>">
        <input type="text" name="title" value="<?php echo $edit_data['Title']; ?>" required>
        <input type="text" name="author" value="<?php echo $edit_data['Author']; ?>" required>
        <input type="number" name="year" value="<?php echo $edit_data['Year']; ?>" required>
        <input type="text" name="category" value="<?php echo $edit_data['Category']; ?>" required>
        <input type="number" name="stock" value="<?php echo $edit_data['Stock']; ?>" required>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif">
        <button type="submit" name="edit">Simpan Perubahan</button>
    </form>
    <?php endif; ?>

    <h2>Tambah Buku Baru</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Judul" required>
        <input type="text" name="author" placeholder="Penulis" required>
        <input type="number" name="year" placeholder="Tahun" required>
        <input type="text" name="category" placeholder="Kategori" required>
        <input type="number" name="stock" placeholder="Stok" required>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif" required>
        <button type="submit" name="add">Tambah Buku</button>
    </form>
</body>
</html>