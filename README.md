Website akan baru berfungsi ketika di dalam databse ditambahkan library dan didalamnya terdapat books dan borrows, berikut untuk query pada MySQL untuk menambahkannya

-- Membuat database 'library'
CREATE DATABASE IF NOT EXISTS library;

-- Menggunakan database 'library'
USE library;

-- Membuat tabel 'books'
CREATE TABLE books (
    ID INT AUTO_INCREMENT PRIMARY KEY, 
    Title VARCHAR(255) NOT NULL, 
    Author VARCHAR(255) NOT NULL, 
    Year YEAR NOT NULL, 
    Stock INT NOT NULL, 
    Image VARCHAR(255)
);

-- Membuat tabel 'borrows'
CREATE TABLE borrows (
    ID INT AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR(255) NOT NULL, 
    nim VARCHAR(20) NOT NULL, 
    departement VARCHAR(100) NOT NULL, 
    chort INT NOT NULL
);
