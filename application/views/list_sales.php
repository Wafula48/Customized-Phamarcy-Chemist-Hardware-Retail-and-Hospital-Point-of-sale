<?php
    require_once 'includes/header.php';
?>
<script type="text/javascript" src="<?=base_url()?>assets/js/datatables/jquery-1.12.3.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/datatables/jquery.dataTables.min.js"></script>
<link href="<?=base_url()?>assets/js/datatables/jquery.dataTables.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script type="text/javascript">
	$(document).ready(function() {
	    $('#example').DataTable();
        
        // Initialize date range pickers
        var startDate = "<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); ?>";
        var endDate = "<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); ?>";
        
        $('#start_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked"
        }).datepicker('update', startDate);
        
        $('#end_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked"
        }).datepicker('update', endDate);
        
        // Handle date range change
        $('#filter_btn').click(function() {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            
            if(startDate && endDate) {
                if(new Date(startDate) > new Date(endDate)) {
                    alert('End date should be after start date');
                    return false;
                }
                window.location.href = '<?=base_url()?>sales/list_sales?start_date=' + startDate + '&end_date=' + endDate;
            }
        });
	} );
</script>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Sales</h1>
		</div>
	</div><!--/.row-->

<script type="text/javascript">
	function openReceipt(ele){
		var myWindow = window.open(ele, "", "width=380, height=550");
	}	
</script>
	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					
					<?php
                        if (!empty($alert_msg)) {
                            $flash_status = $alert_msg[0];
                            $flash_header = $alert_msg[1];
                            $flash_desc = $alert_msg[2];

                            if ($flash_status == 'failure') {
                                ?>
							<div class="row" id="notificationWrp">
								<div class="col-md-12">
									<div class="alert bg-warning" role="alert">
										<i class="icono-exclamationCircle" style="color: #FFF;"></i> 
										<?php echo $flash_desc; ?> <i class="icono-cross" id="closeAlert" style="cursor: pointer; color: #FFF; float: right;"></i>
									</div>
								</div>
							</div>
					<?php	
                            }
                            if ($flash_status == 'success') {
                                ?>
							<div class="row" id="notificationWrp">
								<div class="col-md-12">
									<div class="alert bg-success" role="alert">
										<i class="icono-check" style="color: #FFF;"></i> 
										<?php echo $flash_desc; ?> <i class="icono-cross" id="closeAlert" style="cursor: pointer; color: #FFF; float: right;"></i>
									</div>
								</div>
							</div>
					<?php
                            }
                        }
                    ?>
                    
                    <!-- Date Range Picker Row -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label>Date Range:&nbsp;</label>
                                    <div class="input-group date" style="width: 150px;">
                                        <input type="text" class="form-control" id="start_date" placeholder="Start Date">
                                        
                                    </div>
                                </div>
                                <div class="form-group" style="margin-left: 10px; margin-right: 10px;">
                                    <label>to</label>
                                </div>
                                <div class="form-group">
                                    <div class="input-group date" style="width: 150px;">
                                        <input type="text" class="form-control" id="end_date" placeholder="End Date">
                                        
                                    </div>
                                </div>
                                <button type="button" id="filter_btn" class="btn btn-primary" style="margin-left: 10px;">
                                    Get Sales
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4" style="text-align: right; padding-top: 6px;">
                            <a href="<?=base_url()?>sales/exportSales?start_date=<?php echo isset($_GET['start_date']) ? urlencode($_GET['start_date']) : date('Y-m-01'); ?>&end_date=<?php echo isset($_GET['end_date']) ? urlencode($_GET['end_date']) : date('Y-m-d'); ?>" 
                               style="text-decoration: none">
                                <button type="button" class="btn btn-success" style="background-color: #5cb85c; border-color: #4cae4c;">
                                    <?php echo $lang_export_to_excel; ?>
                                </button>
                            </a>
                        </div>
                    </div>
					
					<div class="row" style="margin-top: 10px;">
						<div class="col-md-12">
							
							<div class="table-responsive">
								<table id="example" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
									    	<th width="14%"><?php echo $lang_date; ?></th>
									    	<th width="7%"><?php echo $lang_sale_id; ?></th>
									    	<th width="6%"><?php echo $lang_type; ?></th>
									    	<th width="12%"><?php echo $lang_outlets; ?></th>
										    <th width="13%"><?php echo "Served By"; ?></th>
										    <th width="7%"><?php echo $lang_items; ?></th>
										    <th width="9%"><?php echo $lang_sub_total; ?></th>
										    <th width="9%"><?php echo $lang_tax; ?></th>
										    <th width="9%"><?php echo $lang_grand_total; ?></th>
										    <th width="10%"><?php echo $lang_action; ?></th>
										</tr>
									</thead>
									<tbody>
<?php
    // Get the selected date range from URL parameters or use current month as default
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
    
    // Validate date formats
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-d');
    }
    
    $date_start = date('Y-m-d 00:00:00', strtotime($startDate));
    $date_end = date('Y-m-d 23:59:59', strtotime($endDate));

    if ($user_role == 1) {
        $orderResult = $this->db->query("SELECT * FROM orders WHERE ordered_datetime >= '$date_start' AND ordered_datetime <= '$date_end' ORDER BY id DESC ");
    } else {
        $orderResult = $this->db->query("SELECT * FROM orders WHERE ordered_datetime >= '$date_start' AND ordered_datetime <= '$date_end' AND outlet_id= '$user_outlet' ORDER BY id DESC ");
    }
    $orderRows = $orderResult->num_rows();

    if ($orderRows > 0) {
        $orderData = $orderResult->result();

        foreach ($orderData as $data) {
            $order_id = $data->id;
            $cust_fn = $data->customer_name;
            $ordered_dtm = date("$setting_dateformat H:i A", strtotime($data->ordered_datetime));
            $outlet_id = $data->outlet_id;
            $subTotal = $data->subtotal;
            $discountTotal = $data->discount_total;
            $taxTotal = $data->tax;
            $grandTotal = $data->grandtotal;
            $total_items = $data->total_items;
            $payment_method = $data->payment_method;
            $status = $data->status;
            $outlet_name = $data->outlet_name;
            $order_type = $data->status; ?>
			<tr>
				<td><?php echo $ordered_dtm; ?></td>
				<td><?php echo $order_id; ?></td>
				<td style="font-weight: bold;">
				<?php
                    if ($order_type == '1') {
                        echo 'Sale';
                    } elseif ($order_type == '2') {
                        echo 'Return';
                    } ?>
				</td>
				<td><?php echo $outlet_name; ?></td>
				<td><?php echo $cust_fn; ?></td>
				<td><?php echo $total_items; ?></td>
				<td><?php echo $subTotal; ?></td>
				<td><?php echo $taxTotal; ?></td>
				<td><?php echo $grandTotal; ?></td>
				<td>
<?php
    if ($order_type == '1') {
        ?>
<a onclick="openReceipt('<?=base_url()?>pos/view_invoice?id=<?php echo $order_id; ?>')" style="text-decoration: none; cursor: pointer;" title="Print Receipt">
	<i class="icono-list" style="color: #2ebc60;"></i>
</a>
<?php
    }
            if ($order_type == '2') {
                ?>
<a onclick="openReceipt('<?=base_url()?>returnorder/printReturn?return_id=<?php echo $order_id; ?>')" style="text-decoration: none; cursor: pointer;" title="Print Receipt">
	<i class="icono-list" style="color: #2ebc60;"></i>
</a>
<?php
            } ?>
<a href="<?=base_url()?>sales/deleteSale?id=<?php echo $order_id; ?>" style="text-decoration: none; margin-left: 5px;" title="Delete" onclick="return confirm('Are you confirm to delete this Sale?')">
<i class="icono-crossCircle" style="color: #F00"></i>
</a>
				</td>
			</tr>
<?php
        }
        unset($orderData);
    }
?>
									</tbody>
								</table>
							</div>
							
						</div>
					</div>
					
				</div><!-- Panel Body // END -->
			</div><!-- Panel Default // END -->
		</div><!-- Col md 12 // END -->
	</div><!-- Row // END -->
	
	<br /><br /><br />
	
</div><!-- Right Colmn // END -->
	
<?php
    require_once 'includes/footer.php';
?>