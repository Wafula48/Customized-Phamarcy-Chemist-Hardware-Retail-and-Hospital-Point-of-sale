<?php
    require_once 'includes/header.php';

    // Calculate Total Stock Value & Quantity
    $total_cost_amt = 0;
    $total_stock_qty = 0;

    $getAllInvResult = $this->db->query('SELECT product_code, qty FROM inventory');
    foreach ($getAllInvResult->result() as $invData) {
        $each_row_code = $invData->product_code;
        $each_row_qty = $invData->qty;
        $total_stock_qty += $each_row_qty;

        $getCostResult = $this->db->query("SELECT purchase_price FROM products WHERE code = '$each_row_code'");
        $each_cost = $getCostResult->row()->purchase_price ?? 0;
        $total_cost_amt += ($each_row_qty * $each_cost);
    }
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h1 class="page-header"><?php echo $lang_inventory; ?></h1>

            <!-- Beautiful Total Stock Display -->
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
                    <h5 style="margin: 0;">Total Value (<?php echo $site_currency; ?>):</h5>
                    <h3 style="margin: 0;"><?php echo number_format($total_cost_amt, 2, '.', ','); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <h1>Inventory</h1>

        <!-- Low Stock Notification -->
        <?php if (!empty($low_stock)): ?>
            <div class="alert alert-warning">
                <strong>Low Stock Alert!</strong>
                <a href="<?= base_url('inventory/low_stock') ?>" class="btn btn-link">View Details</a>
            </div>
        <?php endif; ?>

        <!-- Search Form -->
        <form action="<?= base_url('inventory/search') ?>" method="get" class="mb-3">
            <div class="form-row">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Product Name">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <!-- Inventory Table -->
       
    </div>
</div><!--/.row-->
	
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row" style="border-bottom: 1px solid #e0dede; padding-bottom: 8px; margin-top: -5px;">
                    <div class="col-md-6"></div>
                    <div class="col-md-6 text-right">
                        <a href="<?=base_url()?>inventory/exportInventory">
                            <button type="button" class="btn btn-success">
                                <?php echo $lang_export_inventory; ?>
                            </button>
                        </a>
                    </div>
                </div>

                <form action="<?=base_url()?>inventory/searchInventory" method="get">
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
                                <th><?php echo $lang_total_quantity; ?></th>
                                <th><?php echo $lang_action; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if (count($results) > 0) {
                                    foreach ($results as $data) {
                                        $id = $data->id;
                                        $code = $data->code;
                                        $name = $data->name;
                                        $inv_qty = 0;

                                        $ckInvResult = $this->db->query("SELECT qty FROM inventory WHERE product_code = '$code'");
                                        foreach ($ckInvResult->result() as $inv) {
                                            $inv_qty += $inv->qty;
                                        }

                                        echo "<tr>
                                            <td>$code</td>
                                            <td>$name</td>
                                            <td>$inv_qty</td>
                                            <td>
                                                <a href='".base_url()."inventory/view_detail?pcode=$code' class='btn btn-primary'>".$lang_view."</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr class='no-records-found'>
                                        <td colspan='3'>$lang_no_match_found</td>
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

            </div><!-- Panel Body // END -->
        </div><!-- Panel Default // END -->
    </div><!-- Col md 12 // END -->
</div><!-- Row // END -->

<br /><br /><br />

</div><!-- Right Column // END -->

<?php
    require_once 'includes/footer.php';
?>