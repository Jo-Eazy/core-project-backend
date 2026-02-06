<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// index.php - login page (no authentication, just redirect)
// Login details: Username - "admin" | Password - "1234"
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ModernTech HR Login</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-container">
  <h2>ModernTech HR System</h2>

  <input type="text" id="username" placeholder="Username">
  <input type="password" id="password" placeholder="Password">
  <button id="loginBtn">Login</button>

  <p id="loginMsg"></p>
</div>

<script src="js/app.js"></script>
<script>
// No authentication: clicking login will redirect to dashboard (demo)
document.getElementById('loginBtn').addEventListener('click', function() {
    // you can customize fake validation on client side, but no server auth is performed
    window.location.href = 'dashboard.php';
});
</script>
</body>
</html>

