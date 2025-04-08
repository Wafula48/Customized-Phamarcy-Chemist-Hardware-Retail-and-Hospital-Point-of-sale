<?php
$mysqli = new mysqli("localhost", "root", "Kenya@50", "fahelior_phones");

if ($mysqli->connect_error) {
    die("Database Connection Failed: " . $mysqli->connect_error);
}

if (isset($_POST['product_code'])) {
    $productCode = $_POST['product_code'];
    $query = "SELECT prescription FROM products WHERE code='$productCode'";
    $result = $mysqli->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        echo "<p>" . nl2br(htmlspecialchars($row['prescription'])) . "</p>";
    } else {
        echo "<p>No prescription available for this product.</p>";
    }
}

$mysqli->close();
?>