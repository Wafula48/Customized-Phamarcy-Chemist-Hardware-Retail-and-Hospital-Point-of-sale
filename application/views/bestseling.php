<?php
// Database connection
    /// $mysqli = new mysqli("localhost", "bukembel_che", "Kenya@50", "bukembel_fahelior_phones");

function getBestSellingProducts($limit =10) {
    try {
         $pdo = new PDO('mysql:host=localhost;dbname=bukembel_fahelior_phones', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
        /*/ SQL Query: Get top-selling products
        $query = "SELECT p.name, SUM(s.qty) AS total_sold 
              FROM order_items s 
              JOIN products p ON s.product_code = p.id 
              GROUP BY p.id 
              ORDER BY total_sold DESC 
              LIMIT " . intval($limit);*/
			    $query = "SELECT product_name, SUM(qty) AS total_sold 
              FROM order_items 
              
              GROUP BY product_name 
              ORDER BY total_sold DESC 
              LIMIT " . intval($limit);
        
       
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Fetch best-selling products
$best_sellers = getBestSellingProducts();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Best-Selling Products</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h2 {
            color: #2ebc60;
        }

        table {
            width: 60%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2ebc60;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        p {
            font-size: 18px;
            color: #666;
        }
		
}
    </style>
</head>
<body>
<div class="row" id="bkpos_wrp" style="padding: 10px 0; background-color: #e3f2fd;">
    <div class="col-md-12 text-center">
        <a href="<?=base_url()?>dashboard" class="btn btn-primary shadow-sm"
           style="padding: 10px 20px; border-radius: 8px; font-size: 16px; font-weight: bold;
                  background: #2ebc60; border: none;
                  text-decoration: none; transition: 0.3s ease-in-out; display: inline-block;"
           onmouseover="this.style.background='linear-gradient(135deg, #0056b3, #00408d)'; this.style.transform='scale(1.05)';"
           onmouseout="this.style.background='linear-gradient(135deg, #007bff, #0056b3)'; this.style.transform='scale(1)';">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
</div>
<h2>Best-Selling Products</h2>

<?php if (!empty($best_sellers)): ?>
<table>
    <tr>
        <th>Product Name</th>
        <th>Total Sold</th>
    </tr>
    <?php foreach ($best_sellers as $product): ?>
    <tr>
        <td><?= htmlspecialchars($product['product_name']) ?></td>
        <td><?= htmlspecialchars($product['total_sold']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
    <p>No sales data available.</p>
<?php endif; ?>

</body>
</html>