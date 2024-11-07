<?php
$host = 'localhost'; // Your database host
$dbname = 'admin_portal'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

// Handle event submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['event-title'];
  $date = $_POST['event-date'];
  $time = $_POST['event-time'];
  $description = $_POST['event-description'];
  $type = $_POST['event-type'];

  if ($title && $date && $time && $description && $type) {
    $stmt = $pdo->prepare("INSERT INTO events (title, event_date, event_time, description, event_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $date, $time, $description, $type]);

    // Redirect back to the same page to refresh the event list
    header('Location: admin-dashboard.php');
    exit;
  } else {
    echo "Please fill in all fields.";
  }
}

// Fetch events from the database
$stmt = $pdo->prepare("SELECT * FROM events ORDER BY event_date ASC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Volunteer Events</title>
    <link rel="stylesheet" href="admin-dashboard.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <section>
      <!-- Navbar -->
      <nav class="navbar">
        <div class="logo">Admin Dashboard</div>
        <ul class="menu">
          <li><a href="./">Home</a></li>
          <li><a href="#">Manage Events</a></li>
          <li><a href="#">Settings</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="mainarea">
        <!-- Left Panel: Form to Add New Events -->
        <div class="left">
          <h1>Welcome, Admin</h1>
          <h3>Add New Event</h3>
          <form method="POST" class="event-form">
            <input type="text" name="event-title" placeholder="Event Title" required />
            <input type="date" name="event-date" required />
            <input type="time" name="event-time" required />
            <textarea name="event-description" placeholder="Event Description" required></textarea>
            <select name="event-type">
              <option value="workshop">Workshop</option>
              <option value="seminar">Seminar</option>
              <option value="networking">Networking</option>
            </select>
            <button type="submit">Add Event</button>
          </form>
        </div>

        <!-- Right Panel: List of Upcoming Events -->
        <div class="right">
          <h3>Upcoming Events</h3>
          <div class="event-list" id="event-list">
            <!-- Event Cards will be dynamically inserted here -->
            <?php if ($events): ?>
              <?php foreach ($events as $event): ?>
                <div class="event-card">
                  <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                  <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                  <p><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></p>
                  <p><strong>Type:</strong> <?php echo htmlspecialchars($event['event_type']); ?></p>
                  <p><?php echo htmlspecialchars($event['description']); ?></p>
                  <a href="delete-event.php?id=<?php echo $event['id']; ?>">Remove Event</a>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p>No events found.</p>
            <?php endif; ?>
          </div>
        </div>
      </main>
    </section>
  </body>
</html>
