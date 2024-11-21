<?php
include 'db.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect jika belum login
    exit;
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM borrowings WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href = 'borrowings.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.');</script>";
    }
    $stmt->close();
}

// Ambil data peminjam
$sql = "SELECT * FROM borrowings";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peminjam</title>
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

        .btn-delete:hover {
            background-color: darkred;
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
        <h2>Daftar Peminjam</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Jurusan</th>
                    <th>Angkatan</th>
                    <th>ID Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['nim']; ?></td>
                        <td><?php echo $row['department']; ?></td>
                        <td><?php echo $row['cohort']; ?></td>
                        <td><?php echo $row['book_id']; ?></td>
                        <td><?php echo $row['borrow_date']; ?></td>
                        <td>
                            <a href="borrowings.php?delete_id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
