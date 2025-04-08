<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Products</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Light blue background */
        body {
            background: #fff; /* Softer light blue */
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        /* Centering container */
        .container {
            max-width: 900px;
            padding: 20px;
        }

        /* Card styling */
        .card {
            border-radius: 16px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            background: white;
            padding: 25px;
            margin-top: 20px;
            border: none;
            overflow: hidden;
        }

        /* Header styling */
        h1 {
            font-weight: 700;
            font-size: 28px;
            color: #2ebc60;
            text-transform: uppercase;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #007bff, #00bfff);
            border-radius: 3px;
        }

        /* Table styling */
        .table {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background-color: #2ebc60;
            color: white;
            text-align: center;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
            transition: all 0.3s ease;
        }

        /* Low stock styling */
        .low-stock {
            color: #dc3545;
            font-weight: bold;
            position: relative;
        }

        .low-stock::after {
            content: '!';
            position: absolute;
            right: -15px;
            color: #dc3545;
            font-weight: bold;
        }

        /* Status indicator */
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-low {
            background-color: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.5);
        }

        /* Button styling */
        .btn-dashboard {
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            background:#2ebc60;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 86, 179, 0.2);
        }

        .btn-dashboard:hover {
            background: #2ebc60;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 86, 179, 0.3);
        }

        /* Empty state */
        .empty-state {
            padding: 40px 0;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 50px;
            margin-bottom: 20px;
            color: #dee2e6;
        }
    </style>
</head>
<body>
<div class="row" id="bkpos_wrp" style="padding: 15px 0; background-color: #fff;">
    <div class="col-md-12 text-center">
        <a href="<?=base_url()?>dashboard" class="btn-dashboard">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
</div>

    <div class="container">
        <div class="card">
            <h1><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Products</h1>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 70%">Product Name</th>
                            <th style="width: 30%">Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="productTable">
                        <?php 
                        // Process data to remove duplicates
                        $uniqueProducts = [];
                        if (!empty($low_stock_details)) {
                            foreach ($low_stock_details as $product) {
                                $key = $product->name . $product->qty; // Create unique key
                                if (!isset($uniqueProducts[$key])) {
                                    $uniqueProducts[$key] = $product;
                                }
                            }
                        }
                        ?>
                        
                        <?php if (!empty($uniqueProducts)): ?>
                            <?php foreach ($uniqueProducts as $product): ?>
                                <tr>
                                    <td>
                                        <span class="status-indicator status-low"></span>
                                        <?= htmlspecialchars($product->name) ?>
                                    </td>
                                    <td class="low-stock"><?= htmlspecialchars($product->qty) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">
                                    <div class="empty-state">
                                        <i class="fas fa-check-circle"></i>
                                        <h3>All Products Well Stocked</h3>
                                        <p>No products are currently low in inventory</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-sort table from highest to lowest quantity
        document.addEventListener("DOMContentLoaded", function() {
            let table = document.getElementById("productTable");
            let rows = Array.from(table.querySelectorAll("tr")).filter(row => row.cells.length === 2);

            rows.sort((a, b) => {
                let qtyA = parseInt(a.cells[1].textContent) || 0;
                let qtyB = parseInt(b.cells[1].textContent) || 0;
                return qtyB - qtyA; // Sorting in descending order
            });

            rows.forEach(row => table.appendChild(row));
        });
    </script>
</body>
</html>