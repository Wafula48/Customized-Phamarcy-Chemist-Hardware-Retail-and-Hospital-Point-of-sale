<?php
    require_once 'includes/header.php';
?>

<style type="text/css">
	.fileUpload {
	    position: relative;
	    overflow: hidden;
	    border-radius: 0px;
	    margin-left: -4px;
	    margin-top: -2px;
	}
	.fileUpload input.upload {
	    position: absolute;
	    top: 0;
	    right: 0;
	    margin: 0;
	    padding: 0;
	    font-size: 20px;
	    cursor: pointer;
	    opacity: 0;
	    filter: alpha(opacity=0);
	}
</style>

<script type="text/javascript">
	$(document).ready(function(){
		document.getElementById("uploadBtn").onchange = function () {
			document.getElementById("uploadFile").value = this.value;
		};
	});
</script>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo $lang_add_product; ?></h1>
		</div>
	</div><!--/.row-->
	
	<form action="<?= base_url() ?>products/insertProduct" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">

                    <?php
                    if (!empty($alert_msg)) {
                        $flash_status = $alert_msg[0];
                        $flash_desc = $alert_msg[2];

                        if ($flash_status == 'failure') {
                            echo '<div class="alert bg-warning" role="alert">' . $flash_desc . '</div>';
                        } elseif ($flash_status == 'success') {
                            echo '<div class="alert bg-success" role="alert">' . $flash_desc . '</div>';
                        }
                    }
                    ?>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product Code <span style="color: #F00">*</span></label>
                                <input type="text" name="code" class="form-control" maxlength="250" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product Name <span style="color: #F00">*</span></label>
                                <input type="text" name="name" class="form-control" maxlength="250" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product Category <span style="color: #F00">*</span></label>
                                <select name="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    $catData = $this->Constant_model->getDataOneColumn('category', 'status', '1');
                                    foreach ($catData as $cat) {
                                        echo '<option value="' . $cat->id . '">' . $cat->name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Purchase Price <span style="color: #F00">*</span></label>
                                <input type="text" name="purchase" class="form-control" maxlength="250" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Retail Price <span style="color: #F00">*</span></label>
                                <input type="text" name="retail" class="form-control" maxlength="250" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Expiry Date <span style="color: #F00">*</span></label>
                                <input type="date" name="expiry_date" class="form-control" required min="<?= date('Y-m-d') ?>" />
                            </div>
                        </div>
						 <div class="col-md-4">
                            <div class="form-group">
                                <label>Drug Prescription <span style="color: #F00">*</span></label>
                                <input type="text" name="pres" class="form-control" maxlength="250" required />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product Image</label>
                                <input type="file" name="uploadFile" class="form-control" accept="image/jpeg, image/png" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

	
	<br /><br /><br /><br /><br />
	
</div><!-- Right Colmn // END -->
	
	
	
<?php
    require_once 'includes/footer.php';
?>