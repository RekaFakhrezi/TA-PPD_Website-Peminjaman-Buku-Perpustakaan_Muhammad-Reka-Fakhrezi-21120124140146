<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM books WHERE ID=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Buku berhasil dihapus!";
    } else {
        echo "Error: " . $conn->error;
    }
    header("Location: index.php");
    exit();
}
?>