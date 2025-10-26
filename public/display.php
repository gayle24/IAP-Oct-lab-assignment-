<?php
include_once '../config/database.php';
include_once '../classes/user.php';
include_once '../classes/product.php';

$database = getDbConnection();

$user = new User($db);
$product = new Product($db);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Data Display</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h3>Users</h3>
  <table class="table table-bordered">
    <tr><th>Name</th><th>Email</th></tr>
    <?php
      $users = $user->readAll();
      while ($row = $users->fetch(PDO::FETCH_ASSOC)) {
          echo "<tr><td>{$row['name']}</td><td>{$row['email']}</td></tr>";
      }
    ?>
  </table>

  <h3>Products</h3>
  <table class="table table-bordered">
    <tr><th>Product Name</th><th>Price</th></tr>
    <?php
      $products = $product->readAll();
      while ($row = $products->fetch(PDO::FETCH_ASSOC)) {
          echo "<tr><td>{$row['product_name']}</td><td>{$row['price']}</td></tr>";
      }
    ?>
  </table>
</div>
</body>
</html>
