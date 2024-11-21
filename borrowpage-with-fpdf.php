<?php
// Import library FPDF
require('fpdf.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch book details based on ID
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $sql = "SELECT * FROM books WHERE id = $book_id";
    $result = $conn->query($sql);
    $book = $result->fetch_assoc();
}

// Initialize variables for displaying the ticket
$displayTicket = false;
$ticketData = [];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $nim = $_POST['nim'];
    $department = $_POST['department'];
    $cohort = $_POST['cohort'];

    // Decrease stock by 1 if stock is greater than 0
    $update_sql = "UPDATE books SET stock = stock - 1 WHERE id = $book_id AND stock > 0";
    if ($conn->query($update_sql) === TRUE) {
        // Display the ticket
        $displayTicket = true;
        $ticketData = [
            'name' => $name,
            'nim' => $nim,
            'department' => $department,
            'cohort' => $cohort,
            'book_title' => $book['title'],
            'author' => $book['author'],
            'year' => $book['year'],
            'borrow_date' => date("Y-m-d")
        ];
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Generate PDF if requested
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    // Create a new PDF instance
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Borrowing Receipt', 0, 1, 'C');
            $this->Ln(10);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    // Create PDF
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Add content
    $pdf->Cell(0, 10, 'Name: ' . $_GET['name'], 0, 1);
    $pdf->Cell(0, 10, 'NIM: ' . $_GET['nim'], 0, 1);
    $pdf->Cell(0, 10, 'Department: ' . $_GET['department'], 0, 1);
    $pdf->Cell(0, 10, 'Cohort: ' . $_GET['cohort'], 0, 1);
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Book Title: ' . $_GET['book_title'], 0, 1);
    $pdf->Cell(0, 10, 'Author: ' . $_GET['author'], 0, 1);
    $pdf->Cell(0, 10, 'Year: ' . $_GET['year'], 0, 1);
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Borrow Date: ' . $_GET['borrow_date'], 0, 1);

    // Output PDF
    $pdf->Output('D', 'Borrowing_Receipt.pdf');
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-submit {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
        .ticket {
            background: #f1f1f1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Pinjam Buku</h2>
        
        <?php if ($book): ?>
            <p><strong>Judul Buku:</strong> <?php echo $book['title']; ?></p>
            <p><strong>Penulis:</strong> <?php echo $book['author']; ?></p>
            <p><strong>Tahun Terbit:</strong> <?php echo $book['year']; ?></p>
            <p><strong>Stok:</strong> <?php echo $book['stock']; ?></p>
        <?php else: ?>
            <p>Book not found.</p>
        <?php endif; ?>

        <?php if ($book && $book['stock'] > 0 && !$displayTicket): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="nim">NIM</label>
                    <input type="text" name="nim" id="nim" required>
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" required>
                </div>
                <div class="form-group">
                    <label for="cohort">Angkatan</label>
                    <input type="text" name="cohort" id="cohort" required>
                </div>
                <button type="submit" class="btn-submit">Ajukan Pinjaman</button>
            </form>
        <?php elseif ($book && $displayTicket): ?>
            <div class="ticket">
                <h3>Bukti Peminjaman</h3>
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
                <a href="?id=<?php echo $book_id; ?>&download=true&name=<?php echo $ticketData['name']; ?>&nim=<?php echo $ticketData['nim']; ?>&department=<?php echo $ticketData['department']; ?>&cohort=<?php echo $ticketData['cohort']; ?>&book_title=<?php echo $ticketData['book_title']; ?>&author=<?php echo $ticketData['author']; ?>&year=<?php echo $ticketData['year']; ?>&borrow_date=<?php echo $ticketData['borrow_date']; ?>" class="btn-submit">Download PDF</a>
            </div>
        <?php elseif ($book): ?>
            <p>This book is currently out of stock.</p>
        <?php endif; ?>
    </div>
</body>
</html>
