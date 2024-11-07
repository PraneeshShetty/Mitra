<?php
session_start();
$host = 'localhost';
$dbname ='admin_portal';
$username = 'root';
$password = '';

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize response message
$message = '';
$messageType = '';

// Handle Sign Up
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Email already registered!";
        $messageType = "error";
    } else {
        // Insert new user with hashed password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Account created successfully!";
            $messageType = "success";
        } else {
            $message = "Error creating account. Please try again.";
            $messageType = "error";
        }
    }
    $stmt->close();
}

// Handle Sign In
if (isset($_POST['signin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Set session and redirect to dashboard
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: ../");
            exit;
        } else {
            $message = "Incorrect password.";
            $messageType = "error";
        }
    } else {
        $message = "No account found with that email.";
        $messageType = "error";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="singup.css">
  <title>Login Page</title>
  <style>
    .message {
      text-align: center;
      margin: 10px 0;
      padding: 10px;
      color: white;
      font-weight: bold;
      border-radius: 5px;
    }
    .error { background-color: #e74c3c; }
    .success { background-color: #2ecc71; }
  </style>
</head>
<body>
  <div class="container" id="container">
    <div class="sign-up">
      <form method="post">
        <h1>Create Account</h1>
        <?php if ($message && isset($_POST['signup'])): ?>
          <p class="message <?= $messageType ?>"><?= $message ?></p>
        <?php endif; ?>
        <input type="text" name="name" placeholder="Name" required />
        <input type="text" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" name="signup">Sign Up</button>
      </form>
    </div>
    <div class="sign-in">
      <form method="post" >
        <h1>Sign In</h1>
        <?php if ($message && isset($_POST['signin'])): ?>
          <p class="message <?= $messageType ?>"><?= $message ?></p>
        <?php endif; ?>
        <input type="text" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" name="signin">Sign In</button>
      </form>
    </div>
    <div class="toogle-container">
        <div class="toogle">
          <div class="toogle-panel toogle-left">
            <h1>Welcome User!</h1>
            <p>If you already have an account</p>
            <button class="hidden" id="login">Sign In</button>
          </div>
          <div class="toogle-panel toogle-right">
            <h1>Hello, User!</h1>
            <p>If you don't have an account</p>
            <button class="hidden" id="register">Sign Up</button>
          </div>
        </div>
      </div>
  </div>
  
  <script>
    const container = document.getElementById("container");
const registerBtn = document.getElementById("register");
const loginBtn = document.getElementById("login");

// Only allow toggle if there are no error or success messages in the URL
registerBtn.addEventListener("click", () => {
  const urlParams = new URLSearchParams(window.location.search);
  if (!urlParams.has('error') && !urlParams.has('success')) {
    container.classList.add("active");
  }
});

loginBtn.addEventListener("click", () => {
  const urlParams = new URLSearchParams(window.location.search);
  if (!urlParams.has('error') && !urlParams.has('success')) {
    container.classList.remove("active");
  }
});

// Prevent section switch on error or success
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('error') === 'signup') {
  container.classList.add("active"); // Stay on sign-up form
} else if (urlParams.get('error') === 'signin') {
  container.classList.remove("active"); // Stay on sign-in form
} else if (urlParams.get('success') === 'signup') {
  container.classList.add("active"); // Optionally keep on sign-up on success, or change as needed
} else if (urlParams.get('success') === 'signin') {
  container.classList.remove("active"); // Optionally keep on sign-in on success
}

  </script>
</body>
</html>
