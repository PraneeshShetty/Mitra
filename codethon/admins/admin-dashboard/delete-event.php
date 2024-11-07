<?php
// Database connection details
$host = 'localhost'; // Your database host
$dbname = 'admin_portal'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

try {
    // Establish database connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection failure
    die("Connection failed: " . $e->getMessage());
}

// Check if an ID is passed in the URL (via GET method)
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Validate the ID to prevent SQL injection (ensure it's an integer)
    if (filter_var($id, FILTER_VALIDATE_INT)) {
        // Prepare the SQL statement to delete the event
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);

        // Redirect back to the admin dashboard after deletion
        header('Location: admin-dashboard.php');
        exit;
    } else {
        // In case of invalid ID, show an error
        echo "Invalid event ID.";
    }
} else {
    // If no ID is passed, show an error
    echo "No event ID specified.";
}
?>
