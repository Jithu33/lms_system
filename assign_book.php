<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];

    $stmt = $conn->prepare("UPDATE books SET available = FALSE WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO book_assignments (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();

    $stmt->close();
}

// Fetch all users
$users = $conn->query("SELECT id, username FROM users WHERE role = 'user'");

// Fetch available books
$books = $conn->query("SELECT * FROM books WHERE available = TRUE");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Book to User</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Assign Book to User</h1>
        <a href="librarian_dashboard.php" class="btn logout-btn">Back to Dashboard</a>

        <!-- Assign Book Form -->
        <div class="form-container">
            <form method="POST" action="">
                <select name="user_id" required>
                    <option value="">Select User</option>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
                    <?php endwhile; ?>
                </select>
                <select name="book_id" required>
                    <option value="">Select Book</option>
                    <?php while ($book = $books->fetch_assoc()): ?>
                        <option value="<?php echo $book['id']; ?>"><?php echo $book['book_name']; ?> (<?php echo $book['book_number']; ?>)</option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn">Assign Book</button>
            </form>
        </div>
    </div>
</body>
</html>