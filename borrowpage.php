<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $sql = "SELECT * FROM books WHERE ID = $book_id";
    $result = $conn->query($sql);
    $book = $result->fetch_assoc();
}

$displayTicket = false;
$ticketData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $nim = $_POST['nim'];
    $department = $_POST['department'];
    $cohort = $_POST['cohort'];

    // Update stok buku
    $update_sql = "UPDATE books SET stock = stock - 1 WHERE ID = $book_id AND stock > 0";
    if ($conn->query($update_sql) === TRUE) {
        // Simpan data peminjaman ke tabel borrowings
        $insert_borrowing_sql = "INSERT INTO borrowings (name, nim, department, cohort, book_id, borrow_date) VALUES ('$name', '$nim', '$department', '$cohort', $book_id, CURDATE())";
        if ($conn->query($insert_borrowing_sql) === TRUE) {
            $displayTicket = true;
            $ticketData = [
                'name' => $name,
                'nim' => $nim,
                'department' => $department,
                'cohort' => $cohort,
                'book_title' => $book['Title'],
                'author' => $book['Author'],
                'year' => $book['Year'],
                'borrow_date' => date("Y-m-d")
            ];
        } else {
            echo "Error saving borrowing record: " . $conn->error;
        }
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-submit {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            font-size: 1em;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        . btn-submit:hover {
            background-color: #45a049;
        }

        .ticket {
            width: 100%;
            max-width: 400px;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: left;
            color: #333;
        }

        .ticket h3 {
            text-align: center;
            margin: 0 0 10px;
        }

        .ticket p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Pinjam Buku</h2>
        
        <?php if ($book): ?>
            <p><strong>Judul Buku:</strong> <?php echo $book['Title']; ?></p>
            <p><strong>Penulis:</strong> <?php echo $book['Author']; ?></p>
            <p><strong>Tahun Terbit:</strong> <?php echo $book['Year']; ?></p>
            <p><strong>Stok Tersisa:</strong> <?php echo $book['Stock']; ?></p>
        <?php else: ?>
            <p>Buku tidak ditemukan.</p>
        <?php endif; ?>

        <?php if ($book && $book['Stock'] > 0 && !$displayTicket): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="nim">NIM</label>
                    <input type="text" name="nim" id="nim" required>
                </div>
                <div class="form-group">
                    <label for="department">Departemen</label>
                    <input type="text" name="department" id="department" required>
                </div>
                <div class="form-group">
                    <label for="cohort">Angkatan</label>
                    <input type="text" name="cohort" id="cohort" required>
                </div>
                <button type="submit" class="btn-submit">Ajukan Peminjaman</button>
            </form>
        <?php elseif ($book && $displayTicket): ?>
            <div class="ticket">
                <h3>Bukti Pengajuan Peminjaman</h3>
                <p><strong>Nama:</strong> <?php echo $ticketData['name']; ?></p>
                <p><strong>NIM:</strong> <?php echo $ticketData['nim']; ?></p>
                <p><strong>Department:</strong> <?php echo $ticketData['department']; ?></p>
                <p><strong>Angkatan:</strong> <?php echo $ticketData['cohort']; ?></p>
                <hr>
                <p><strong>Judul Buku:</strong> <?php echo $ticketData['book_title']; ?></p>
                <p><strong>Penulis:</strong> <?php echo $ticketData['author']; ?></p>
                <p><strong>Tahun Terbit:</strong> <?php echo $ticketData['year']; ?></p>
                <hr>
                <p><strong>Tanggal Peminjaman:</strong> <?php echo $ticketData['borrow_date']; ?></p>
            </div>
        <?php elseif ($book): ?>
            <p>Stok untuk buku ini sedang kosong.</p>
        <?php endif; ?>
    </div>

</body>
</html>