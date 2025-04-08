<?php
require_once 'includes/header.php';

// Initialize variables
$total_cost_amt = 0;
$total_stock_qty = 0;
$total_sales_value = 0;
$expected_profit = 0;
$low_stock_threshold = 10; // Adjust as needed
$low_stock = [];
$expiry_stock = [];
$bestseling = [];


// Fetch all inventory data with product cost & selling price
$getAllInvResult = $this->db->query("
    SELECT i.product_code, i.qty, p.purchase_price, p.retail_price 
    FROM inventory i
    JOIN products p ON i.product_code = p.code
");

// Calculate total stock quantity, value, sales value, and profit
foreach ($getAllInvResult->result() as $invData) {
    $each_row_qty = $invData->qty;
    $each_cost = isset($invData->purchase_price) ? $invData->purchase_price : 0;
	$each_selling_price = isset($invData->retail_price) ? $invData->retail_price : 0;
  

    $total_stock_qty += $each_row_qty;
    $total_cost_amt += ($each_row_qty * $each_cost);
    $total_sales_value += ($each_row_qty * $each_selling_price);
    $expected_profit += ($each_row_qty * ($each_selling_price - $each_cost));
}

// Fetch low stock products
$getLowStockResult = $this->db->query("SELECT product_code, qty FROM inventory WHERE qty <= $low_stock_threshold");
if ($getLowStockResult->num_rows() > 0) {
    $low_stock = $getLowStockResult->result();
}

//Expiry date
/*$one_year_from_now = date('Y-m-d', strtotime('+1 year'));
$getExpiringProducts = $this->db->query("SELECT name, expiry_date FROM products WHERE expiry_date <= '$one_year_from_now'");
if ($getExpiringProducts->num_rows() > 0) {
    $expiry_stock = $getExpiringProducts->result();
}*/
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h1 class="page-header"><?php echo $lang_inventory; ?></h1>

            <!-- Beautiful Stock Summary -->
            <div class="stock-summary-box text-white p-3" style="
                background: linear-gradient(135deg, #5cb85c, #4cae4c); 
                border-radius: 8px; 
                display: flex; 
                align-items: center;
                padding: 10px 20px;
                color: white;
                font-weight: bold;
            ">
                <div style="margin-right: 20px;">
                    <h5 style="margin: 0;">Total Stock Qty:</h5>
                    <h3 style="margin: 0;"><?php echo number_format($total_stock_qty); ?></h3>
                </div>
                <div style="border-left: 2px solid white; height: 40px; margin-right: 20px;"></div>
                <div>
                    <h5 style="margin: 0;">Total Cost (<?php echo $site_currency; ?>):</h5>
                    <h3 style="margin: 0;"><?php echo number_format($total_cost_amt, 2, '.', ','); ?></h3>
                </div>
                <div style="border-left: 2px solid white; height: 40px; margin-right: 20px;"></div>
                <div>
                    <h5 style="margin: 0;">Total Sales Value (<?php echo $site_currency; ?>):</h5>
                    <h3 style="margin: 0;"><?php echo number_format($total_sales_value, 2, '.', ','); ?></h3>
                </div>
                <div style="border-left: 2px solid white; height: 40px; margin-right: 20px;"></div>
                <div>
                    <h5 style="margin: 0;">Expected Profit (<?php echo $site_currency; ?>):</h5>
                    <h3 style="margin: 0;"><?php echo number_format($expected_profit, 2, '.', ','); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <!-- Low Stock Notification -->
        <?php if (!empty($low_stock)): ?>
            <div class="alert alert-warning">
                <strong>Low Stock Alert!</strong> Some products are running low on stock.
                <a href="<?= base_url('inventory/low_stock') ?>" class="btn btn-link">View Details</a>
            </div>
        <?php endif; ?>
		<?php if (!empty($low_stock)): ?>
            <div class="alert alert-warning">
                <strong>Expiry Stock Alert!</strong> Some drugs will expire in less than 5 months.
                <a href="<?= base_url('inventory/expiry_stock') ?>" class="btn btn-link">View Details</a>
            </div>
        <?php endif; ?>
		<?php if (!empty($low_stock)): ?>
           <div class="alert" style="
    background-color: #d9f1ff; 
    color: #004085; 
    padding: 15px; 
    border-radius: 8px; 
    border-left: 5px solid #007BFF; 
    font-family: Arial, sans-serif; 
    display: inline-block;
    width: auto;">
    
    <strong style="font-size: 16px;">Best-Selling Products Alert!</strong> 
    <a href="<?= base_url('inventory/bestseling') ?>" style="
        text-decoration: none; 
        background-color: #007BFF; 
        color: white; 
        padding: 6px 12px; 
        border-radius: 5px;
        font-size: 14px; 
        font-weight: bold;
        margin-left: 10px;
        display: inline-block;">
        View Details
    </a>
</div>
        <?php endif; ?>

        <!-- Inventory Table -->
    </div>
	<?php if (!empty($expiring_soon)): ?>
    <div class="alert alert-danger">
        <strong>Expiry Alert!</strong> Some drugs are expiring within a month.
        <a href="<?= base_url('inventory/expiring_soon') ?>" class="btn btn-link">View Details</a>
    </div>
<?php endif; ?>
</div><!--/.row-->

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row" style="border-bottom: 1px solid #e0dede; padding-bottom: 8px;">
                    <div class="col-md-6"></div>
                    <div class="col-md-6 text-right">
                        <a href="<?= base_url() ?>inventory/exportInventory">
                            <button type="button" class="btn btn-success">
                                <?php echo $lang_export_inventory; ?>
                            </button>
                        </a>
                    </div>
                </div>

                <form action="<?= base_url() ?>inventory/searchInventory" method="get">
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php echo $lang_product_code; ?></label>
                                <input type="text" name="code" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php echo $lang_product_name; ?></label>
                                <input type="text" name="name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br />
                                <button class="btn btn-primary btn-block"><?php echo $lang_search; ?></button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?php echo $lang_code; ?></th>
                                <th><?php echo $lang_name; ?></th>
								<th>Supplier</th> 
                                <th><?php echo $lang_total_quantity; ?></th>
                                <th><?php echo $lang_action; ?></th>
                            </tr>
                        </thead>
                      <tbody>
    <?php
    if (!empty($results)) {
        foreach ($results as $data) {
            $id = $data->id;
            $code = $data->code;
            $name = $data->name;
            $supplier_name = isset($data->supplier_name) ? $data->supplier_name : 'N/A';

            // Get inventory quantity
            $ckInvResult = $this->db->query("SELECT SUM(qty) as total_qty FROM inventory WHERE product_code = ?", [$code]);
            $inv_qty = $ckInvResult->row()->total_qty ?? 0;

            echo "<tr>
                <td>$code</td>
                <td>$name</td>
                <td>$supplier_name</td>
                <td>$inv_qty</td>
                <td>
                    <a href='".base_url()."inventory/view_detail?pcode=$code' class='btn btn-primary'>".$lang_view."</a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr class='no-records-found'>
            <td colspan='5'>$lang_no_match_found</td>
        </tr>";
    }
    ?>
</tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php echo $displayshowingentries; ?>
                    </div>
                    <div class="col-md-6 text-right">
                        <?php echo $links; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br /><br /><br />

</div><!-- Right Column // END -->

<?php require_once 'includes/footer.php'; ?>