<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_name = $_POST['book_name'];
    $book_number = $_POST['book_number'];

    $stmt = $conn->prepare("INSERT INTO books (book_name, book_number) VALUES (?, ?)");
    $stmt->bind_param("ss", $book_name, $book_number);

    if ($stmt->execute()) {
        echo "<script>alert('Book added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding book!');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Add New Book</h1>
        <a href="librarian_dashboard.php" class="btn logout-btn">Back to Dashboard</a>

        <!-- Add Book Form -->
        <div class="form-container">
            <form method="POST" action="">
                <input type="text" name="book_name" placeholder="Book Name" required>
                <input type="text" name="book_number" placeholder="Book Number" required>
                <button type="submit" class="btn">Add Book</button>
            </form>
        </div>
    </div>
</body>
</html>