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
?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo $lang_dashboard; ?></h1>
		</div>
	</div><!--/.row-->
	
	<div class="row">
		<div class="col-xs-6 col-md-2">
			<div class="panel panel-default">
				<a href="<?=base_url()?>pos" style="text-decoration: none">
					<div class="panel-body easypiechart-panel" style="padding-bottom: 30px;">
						<h4><?php echo $lang_point_of_sales; ?></h4>
						<i class="icono-tiles" style="color: #2ebc60;"></i>
					</div>
				</a>
			</div>
		</div>
		<div class="col-xs-6 col-md-2">
			<div class="panel panel-default">
				<a href="<?=base_url()?>sales/list_sales" style="text-decoration: none">
					<div class="panel-body easypiechart-panel" style="padding-bottom: 30px;">
						<h4><?php echo $lang_sales; ?></h4>
						<i class="icono-cart" style="color: #2ebc60;"></i>
					</div>
				</a>
			</div>
		</div>
		<?php
            if ($user_role < 3) {
                ?>
		<div class="col-xs-6 col-md-2">
			<div class="panel panel-default">
				<a href="<?=base_url()?>reports/sales_report" style="text-decoration: none">
					<div class="panel-body easypiechart-panel" style="padding-bottom: 30px;">
						<h4><?php echo $lang_reports; ?></h4>
						<i class="icono-barChart" style="color: #2ebc60;"></i>
					</div>
				</a>
			</div>
		</div>
		<?php

            }
        ?>
		<div class="col-xs-6 col-md-2">
			<div class="panel panel-default">
				<a href="<?=base_url()?>setting/outlets" style="text-decoration: none">
					<div class="panel-body easypiechart-panel" style="padding-bottom: 30px;">
						<h4><?php echo $lang_outlets; ?></h4>
						<i class="icono-market" style="color: #2ebc60;"></i>
					</div>
				</a>
			</div>
		</div>
		<div class="col-xs-6 col-md-2">
			<div class="panel panel-default">
				<a href="<?=base_url()?>setting/users" style="text-decoration: none;">
					<div class="panel-body easypiechart-panel" style="padding-bottom: 30px;">
						<h4><?php echo $lang_users; ?></h4>
						<i class="icono-user" style="color: #2ebc60;"></i>
					</div>
				</a>
			</div>
		</div>
		<?php
            if ($user_role == '1') {
                ?>
		<div class="col-xs-6 col-md-2">
			<div class="panel panel-default">
				<a href="<?=base_url()?>setting/system_setting" style="text-decoration: none;">
					<div class="panel-body easypiechart-panel" style="padding-bottom: 30px;">
						<h4><?php echo $lang_system_setting; ?></h4>
						<i class="icono-gear" style="color: #2ebc60;"></i>
					</div>
				</a>
			</div>
			
		</div>
		
		<?php

            }
        ?>
	</div><!--/.row-->
	
	<?php
        $current_year = date('Y');
        $months = array();
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $current_year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        $month_name_array = array();
        $year_name_array = array();
        for ($m = 0; $m < count($months); ++$m) {
            $year = date('Y', strtotime($months[$m]));
            $mon = date('m', strtotime($months[$m]));
            $month_name = date('M', strtotime($months[$m]));

            array_push($month_name_array, $month_name);
            array_push($year_name_array, $year);
        }
    ?>
	
	
<script src="<?=base_url()?>assets/js/highcharts.js"></script>
<script src="<?=base_url()?>assets/js/exporting.js"></script>	
<script type="text/javascript">
	$(document).on('ready', function() {
		$(function () {
		    $('#sales_chart').highcharts({
		        chart: {
		            type: 'column'
		        },
		        title: {
		            text: '<?php echo $lang_monthly_sales_outlet; ?>'
		        },
		        subtitle: {
		            text: ''
		        },
		        xAxis: {
		            categories: [
			        <?php
                          for ($mn = 0; $mn < count($month_name_array); ++$mn) {
                              echo "'".$month_name_array[$mn].' '.$year_name_array[$mn]."',";
                          }
                    ?>
		            ],
		            crosshair: true
		        },
		        yAxis: {
		            min: 0,
		            title: {
		                text: '<?php echo $lang_amount; ?> (<?php echo $currency; ?>)'
		            }
		        },
		        tooltip: {
		            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
		            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
		                '<td style="padding:0"><b> {point.y:.2f}</b></td></tr>',
		            footerFormat: '</table>',
		            shared: true,
		            useHTML: true
		        },
		        plotOptions: {
		            column: {
		                pointPadding: 0.2,
		                borderWidth: 0
		            }
		        },
		        series: [
			        <?php
                        if ($user_role == '1') {
                            $outletData = $this->Constant_model->getDataOneColumnSortColumn('outlets', 'status', '1', 'id', 'DESC');
                        } else {
                            $outletData = $this->Constant_model->getDataTwoColumnSortColumn('outlets', 'id', "$user_outlet", 'status', '1', 'id', 'DESC');
                        }

                          for ($o = 0; $o < count($outletData); ++$o) {
                              $outlet_id = $outletData[$o]->id;
                              $outlet_name = $outletData[$o]->name; ?>
					{
			            name: '<?php echo $outlet_name; ?>',
			            data: [
			            	<?php
                                for ($m = 0; $m < count($months); ++$m) {
                                    $year = date('Y', strtotime($months[$m]));
                                    $mon = date('m', strtotime($months[$m]));

                                    $total_monthly_amt = 0;
                                    $number_of_day = cal_days_in_month(CAL_GREGORIAN, $mon, $year);

                                    for ($d = 1; $d <= $number_of_day; ++$d) {
                                        if (strlen($d) == 1) {
                                            $d = '0'.$d;
                                        }

                                        $full_date_start = $year.'-'.$mon.'-'.$d.' 00:00:00';
                                        $full_date_end = $year.'-'.$mon.'-'.$d.' 23:59:59';

                                        $orderResult = $this->db->query("SELECT grandtotal FROM orders WHERE ordered_datetime >= '$full_date_start' AND ordered_datetime <= '$full_date_end' AND outlet_id = '$outlet_id' ");
                                        $orderData = $orderResult->result();
                                        for ($od = 0; $od < count($orderData); ++$od) {
                                            $total_monthly_amt += number_format($orderData[$od]->grandtotal, 2, '.', '');
                                        }
                                        unset($orderResult);
                                        unset($orderData);
                                    }    // End of Number of Day Loop;
                                    echo $total_monthly_amt.',';
                                } ?>
			            ]
			
			        }, 
					<?php

                          }
                    ?>
		        ]
		    });
		});		
	});
</script>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<!-- <div class="panel-heading">Sales Chart</div> -->
				<div class="panel-body">
										
					<div id="sales_chart" style="min-width: 310px; height: 400px;"></div>
					
				</div>
				
			</div>
		</div>
		
	</div><!--/.row-->
	     <div class="alert" style="
    background-color: #d9f1ff; 
    color: #2ebc60; 
    padding: 15px; 
    border-radius: 8px; 
    border-left: 5px solid #2ebc60; 
    font-family: Arial, sans-serif; 
    display: inline-block;
    width: auto;">
    
    <strong style="font-size: 16px;">Best-Selling Products Alert!</strong> 
    <a href="<?= base_url('inventory/bestseling') ?>" style="
        text-decoration: none; 
        background-color: #2ebc60; 
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
   <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($low_stock)): ?>
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
      
        <?php endif; ?>

        <!-- Inventory Table -->
    </div>
            <?php endif; ?>
            
            <?php if (!empty($expiry_stock)): ?>
                <div class="alert alert-warning">
                    <strong>Expiry Stock Alert!</strong> Some drugs will expire in less than 5 months.
                    <a href="<?= base_url('inventory/expiry_stock') ?>" class="btn btn-link">View Details</a>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($bestseling)): ?>
                <div class="alert" style="
                    background-color: #d9f1ff; 
                    color: #004085; 
                    padding: 15px; 
                    border-radius: 8px; 
                    border-left: 5px solid #007BFF; 
                    font-family: Arial, sans-serif; 
                    display: inline-block;
                    width: 100%;
                    margin-bottom: 20px;">
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
            
            <?php if (!empty($expiring_soon)): ?>
                <div class="alert alert-danger">
                    <strong>Expiry Alert!</strong> Some drugs are expiring within a month.
                    <a href="<?= base_url('inventory/expiring_soon') ?>" class="btn btn-link">View Details</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
	<br /><br /><br />
	
</div><!-- Right Colmn // END -->
	
	
	
<?php
    require_once 'includes/footer.php';
?>