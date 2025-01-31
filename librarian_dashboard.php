<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Handle Mark Returned Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_book'])) {
    $assignment_id = $_POST['assignment_id'];

    // Update the returned_at timestamp for the assignment
    $stmt = $conn->prepare("UPDATE book_assignments SET returned_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();

    // Update the book's availability status
    $stmt = $conn->prepare("UPDATE books SET available = TRUE WHERE id = (SELECT book_id FROM book_assignments WHERE id = ?)");
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();

    // Refresh the page to reflect the changes
    header("Location: librarian_dashboard.php");
    exit();
}

// Fetch assigned books
$assigned_books = $conn->query("
    SELECT ba.id, u.username, b.book_name, b.book_number, ba.taken_at, ba.returned_at 
    FROM book_assignments ba 
    JOIN users u ON ba.user_id = u.id 
    JOIN books b ON ba.book_id = b.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Librarian Dashboard</h1>
        <a href="logout.php" class="btn logout-btn">Logout</a>

        <!-- Navigation Links -->
        <div class="nav-links">
            <a href="add_book.php" class="btn add-book-btn">Add New Book</a>
            <a href="assign_book.php" class="btn assign-book-btn">Assign Book to User</a>
        </div>

        <!-- View Assigned Books -->
        <div class="table-container">
            <h2>Assigned Books</h2>
            <?php if ($assigned_books->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Book Name</th>
                            <th>Book Number</th>
                            <th>Taken At</th>
                            <th>Returned At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($assigned = $assigned_books->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $assigned['username']; ?></td>
                                <td><?php echo $assigned['book_name']; ?></td>
                                <td><?php echo $assigned['book_number']; ?></td>
                                <td><?php echo $assigned['taken_at']; ?></td>
                                <td><?php echo $assigned['returned_at'] ? $assigned['returned_at'] : 'Not Returned'; ?></td>
                                <td>
                                    <?php if (!$assigned['returned_at']): ?>
                                        <form method="POST" action="" style="display:inline;">
                                            <input type="hidden" name="assignment_id" value="<?php echo $assigned['id']; ?>">
                                            <button type="submit" name="return_book" class="btn return-btn">Mark Returned</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No books have been assigned yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>