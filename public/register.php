<?php
include_once '../config/Database.php';
include_once '../classes/User.php';

$db = getDbConnection();

$user = new User($db);

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $user->username = $_POST['username'] ?? '';
    $user->email = $_POST['email'] ?? '';
    $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if($user->register()) {
        echo "<div class='alert alert-success'>User registered.</div>";
    } else {
        echo "<div class='alert alert-danger'>Registration failed.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h2>User Registration</h2>
    <form method="POST">
      <input class="form-control mb-2" name="username" placeholder="username" required>
      <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
      <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
      <button class="btn btn-primary" type="submit">Register</button>
    </form>
    <div class="signup">
        <span class="signup">Already have an account?
         <label  id="log-in"><a href="login.php">Login</a></label>
        </span>
      </div>
  </div>
</body>
</html>
