<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expiry Stock Products</title>

    
    <style>
        /* Light blue background */
        body {
            background: #fff; /* Soft light blue */
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        /* Centering container */
        .container {
            max-width: 900px;
        }

        /* Card styling */
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            background: white;
            padding: 20px;
            margin-top: 30px;
            text-align: center;
        }

        /* Header styling */
        h1 {
            font-weight: bold;
            font-size: 26px;
            color: #2ebc60;
            text-transform: uppercase;
        }

        /* Table styling */
        .table {
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #2ebc60;
            color: white;
            text-align: center;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Low stock styling */
        .low-stock {
            color: #dc3545; /* Bootstrap danger color */
            font-weight: bold;
        }

        /* Smooth table sorting effect */
        tbody tr {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body>
<div class="row" id="bkpos_wrp" style="padding: 10px 0; background-color: #e3f2fd;">
    <div class="col-md-12 text-center">
        <a href="<?=base_url()?>dashboard" class="btn btn-primary shadow-sm"
           style="padding: 10px 20px; border-radius: 8px; font-size: 16px; font-weight: bold;
                  background:#2ebc60; border: none;
                  text-decoration: none; transition: 0.3s ease-in-out; display: inline-block;"
           onmouseover="this.style.background=#2ebc60; this.style.transform='scale(1.05)';"
           onmouseout="this.style.background=#2ebc60; this.style.transform='scale(1)';">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
</div>
    <div class="container">
        <div class="card">
            <h1>Expiry Stock Products</h1>
		<a href="<?= base_url('inventory/export_expiry_stock_products"') ?>" class="btn btn-success mb-3">
    Export to Excel
</a>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Expiry date</th>
                        </tr>
                    </thead>
                    <tbody id="productTable">
                        <?php if (!empty($expiry_stock_details)): ?>
                            <?php foreach ($expiry_stock_details as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product->name) ?></td>
                                    <td class="low-stock"><?= htmlspecialchars($product->expiry_date) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted">No products with is about to expire.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Auto-sort table from highest to lowest quantity
        document.addEventListener("DOMContentLoaded", function() {
            let table = document.getElementById("productTable");
            let rows = Array.from(table.rows);

            rows.sort((a, b) => {
                let qtyA = parseInt(a.cells[1].textContent);
                let qtyB = parseInt(b.cells[1].textContent);
                return qtyB - qtyA; // Sorting in descending order (top to bottom)
            });

            rows.forEach(row => table.appendChild(row)); // Re-append sorted rows
        });
    </script>

</body>
</html>