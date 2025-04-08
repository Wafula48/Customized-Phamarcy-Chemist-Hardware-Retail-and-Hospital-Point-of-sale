								<?php
$mysqli = new mysqli("localhost", "root", "Kenya@50", "fahelior_phones");

if ($mysqli->connect_error) {
    die("Database Connection Failed: " . $mysqli->connect_error);
}

$productCode = $_GET['code'] ?? '';

if (!empty($productCode)) {
    $query = $mysqli->prepare("SELECT pres FROM products WHERE code = ?");
    $query->bind_param('s', $productCode);
    $query->execute();
    $query->bind_result($prescription);
    $query->fetch();
    $query->close();

    echo $prescription ? $prescription : 'No prescription available for this product.';
} else {
    echo 'Invalid product code.';
}

$mysqli->close();
?>