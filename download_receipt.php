<?php
    require_once('tcpdf/tcpdf.php');

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $ticketData = isset($_POST['ticketData']) ? unserialize(base64_decode($_POST['ticketData'])) : null;

    if ($ticketData) {
        $stmt = $conn->prepare("INSERT INTO borrow_records (borrower_name, nim, department, cohort, book_id, borrow_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssiis",
            $ticketData['name'],
            $ticketData['nim'],
            $ticketData['department'],
            $ticketData['cohort'],
            $book_id,
            $ticketData['borrow_date']
        );
        $book_id = $ticketData['book_id'];
        $stmt->execute();
        $stmt->close();

        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Library System');
        $pdf->SetTitle('Borrowing Receipt');
        $pdf->SetHeaderData('', 0, 'Library Borrowing Receipt', 'Library System');
        $pdf->setHeaderFont(['helvetica', '', 12]);
        $pdf->setFooterFont(['helvetica', '', 10]);
        $pdf->SetDefaultMonospacedFont('helvetica');
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->AddPage();

        $html = <<<EOD
        <h2>Borrowing Receipt</h2>
        <p><strong>Name:</strong> {$ticketData['name']}</p>
        <p><strong>NIM:</strong> {$ticketData['nim']}</p>
        <p><strong>Department:</strong> {$ticketData['department']}</p>
        <p><strong>Cohort:</strong> {$ticketData['cohort']}</p>
        <hr>
        <p><strong>Book Title:</strong> {$ticketData['book_title']}</p>
        <p><strong>Author:</strong> {$ticketData['author']}</p>
        <p><strong>Year:</strong> {$ticketData['year']}</p>
        <hr>
        <p><strong>Borrow Date:</strong> {$ticketData['borrow_date']}</p>
        EOD;

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('borrowing_receipt.pdf', 'D');
    } else {
        echo "Error: Ticket data is missing.";
    }

    $conn->close();
?>
