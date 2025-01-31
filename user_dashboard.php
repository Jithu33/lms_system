<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Fetch assigned books for the logged-in user
$assigned_books = $conn->query("
    SELECT b.book_name, b.book_number, ba.taken_at, ba.returned_at 
    FROM book_assignments ba 
    JOIN books b ON ba.book_id = b.id 
    WHERE ba.user_id = " . $_SESSION['user_id']
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/styled.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>User Dashboard</h1>
        <a href="logout.php" class="btn logout-btn">Logout</a>

        <!-- Assigned Books -->
        <div class="table-container">
            <h2>Your Assigned Books</h2>
            <?php if ($assigned_books->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Book Name</th>
                            <th>Book Number</th>
                            <th>Taken At</th>
                            <th>Returned At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($assigned = $assigned_books->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $assigned['book_name']; ?></td>
                                <td><?php echo $assigned['book_number']; ?></td>
                                <td><?php echo $assigned['taken_at']; ?></td>
                                <td><?php echo $assigned['returned_at'] ? $assigned['returned_at'] : 'Not Returned'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no books assigned.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
