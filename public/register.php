<?php
include_once '../config/Database.php';
include_once '../classes/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $user->name = $_POST['name'];
    $user->email = $_POST['email'];
    $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user->code = rand(100000, 999999); // Mock 2FA code

    if($user->register()) {
        echo "<div class='alert alert-success'>User registered. 2FA Code: {$user->code}</div>";
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
    <form method="post">
      <input class="form-control mb-2" name="name" placeholder="Name" required>
      <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
      <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
      <button class="btn btn-primary" type="submit">Register</button>
    </form>
  </div>
</body>
</html>
