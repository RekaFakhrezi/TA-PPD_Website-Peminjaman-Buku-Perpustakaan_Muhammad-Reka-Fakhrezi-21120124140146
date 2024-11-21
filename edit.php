<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    $sql = "UPDATE books SET Title='$title', Author='$author', Year=$year, Category='$category', Stock=$stock, Image='$image' WHERE ID=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Buku berhasil diperbarui!";
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$id = $_GET['id'];
$sql = "SELECT * FROM books WHERE ID=$id";
$result = $conn->query($sql);
$book = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
</head>
<body>
    <h1>Edit Buku</h1>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $book['ID']; ?>">
        <input type="text" name="title" value="<?php echo $book['Title']; ?>" required>
        <input type="text" name="author" value="<?php echo $book['Author']; ?>" required>
        <input type="number" name="year" value="<?php echo $book['Year']; ?>" required>
        <input type="text" name="category" value="<?php echo $book['Category']; ?>" required>
        <input type="number" name="stock" value="<?php echo $book['Stock']; ?>" required>
        <input type="text" name="image" value="<?php echo $book['Image']; ?>" required>
        <button type="submit" name="update">Perbarui Buku</button>
    </form>
</body>
</html>