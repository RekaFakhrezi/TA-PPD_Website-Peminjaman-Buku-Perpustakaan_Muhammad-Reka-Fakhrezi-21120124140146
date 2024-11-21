<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etalase Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- NAVIGATION BAR -->
    <nav>
        <div class="container-navbar">
            <ul class="ul-navbar">
                <li class="logo">
                    <img src="logo.png" alt="Logo">
                </li>
                <li class="li-navbar">
                    <a href="index.html" class="a-navbar">HOME PAGE</a>
                </li>
                <li class="li-navbar">
                    <a href="etalasebuku.php" class="a-navbar">ETALASE BUKU</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- NAVIGATION BAR -->

    <div class="content">
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "library";

        $conn = new mysqli($servername, $username, $password, $dbname);


        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $category_query = "SELECT DISTINCT category FROM books ORDER BY category ASC";
        $category_result = $conn->query($category_query);

        if ($category_result->num_rows > 0) {
            while ($category_row = $category_result->fetch_assoc()) {
                $category = $category_row['category'];
                echo '<div class="category-container">';
                echo '<h2 class="category-title">' . $category . '</h2>';
                echo '<div class="book-container">';

                $sql = "SELECT * FROM books WHERE category='$category'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($book = $result->fetch_assoc()) {
                        echo '<div class="book-item">';
                        echo '<img src="uploads/' . $book['Image'] . '" alt="Cover of ' . $book['Title'] . '">';
                        echo '<div class="book-info">';
                        echo '<div class="book-title">' . $book['Title'] . '</div>';
                        echo '<div class="book-author">' . $book['Author'] . '</div>';
                        echo '<div class="book-year">' . $book['Year'] . '</div>';
                        echo '<div class="book-stock">Stok: ' . $book['Stock'] . '</div>';
                        
                    
                        if ($book['Stock'] > 0) {
                            echo '<a href="borrowpage.php?id=' . $book['ID'] . '" class="btn-pinjam">Pinjam</a>';
                        } else {
                            echo '<button class="btn-pinjam disabled" disabled>Out of Stock</button>';
                        }
                        
                        echo '</div>'; 
                        echo '</div>'; 
                    }
                } else {
                    echo "No books available in this category.";
                }

                echo '</div>'; 
                echo '</div>';
            }
        } else {
            echo "No categories available.";
        }

        $conn->close();
        ?>
        
    </div>
    
</body>
</html>
