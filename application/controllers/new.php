 public function insertSale()
    {
        if (isset($_POST['hold_bill_submit'])) {
            $customer = $this->input->post('customer');
            $hold_ref = $this->input->post('hold_ref');

            $row_count = $this->input->post('row_count');
            $subTotal = $this->input->post('subTotal');
            $dis_amt = $this->input->post('dis_amt');
            $grandTotal = $this->input->post('final_total_payable');
            $total_item_qty = $this->input->post('final_total_qty');
            $taxTotal = $this->input->post('tax_amt');

            $user_id = $this->session->userdata('user_id');
            $user_outlet = $this->input->post('outlet');
            $tm = date('Y-m-d H:i:s', time());

            if (empty($dis_amt)) {
                $dis_amt = 0;
            } elseif (strpos($dis_amt, '%') > 0) {
                $temp_dis_Array = explode('%', $dis_amt);
                $temp_dis = $temp_dis_Array[0];

                $temp_item_price = 0;

                for ($i = 1; $i <= $row_count; ++$i) {
                    $pcode = $this->input->post("pcode_$i");
                    $price = $this->input->post("price_$i");
                    $qty = $this->input->post("qty_$i");

                    if (!empty($pcode)) {
                        $temp_item_price += ($price * $qty);
                    }
                }

                $dis_amt = number_format(($temp_item_price * ($temp_dis / 100)), 2, '.', '');
            }

            // Get Customer Detail;
            $custDtaData = $this->Constant_model->getDataOneColumn('customers', 'id', $customer);
            $custDta_fn = $custDtaData[0]->fullname;
            $custDta_email = $custDtaData[0]->email;
            $custDta_mb = $custDtaData[0]->mobile;

            $ins_sus_data = array(
                    'customer_id' => $customer,
                    'fullname' => $custDta_fn,
                    'email' => $custDta_email,
                    'mobile' => $custDta_mb,
                    'ref_number' => $hold_ref,
                    'outlet_id' => $user_outlet,
                    'subtotal' => $subTotal,
                    'discount_total' => $dis_amt,
                    'tax' => $taxTotal,
                    'grandtotal' => $grandTotal,
                    'total_items' => $total_item_qty,
                    'created_user_id' => $user_id,
                    'created_datetime' => $tm,
                    'status' => '0',
            );
            $sus_id = $this->Constant_model->insertDataReturnLastId('suspend', $ins_sus_data);

            // Order Item -- START;
            for ($i = 1; $i <= $row_count; ++$i) {
                $pcode = $this->input->post("pcode_$i");
                $price = $this->input->post("price_$i");
                $qty = $this->input->post("qty_$i");

                if (!empty($pcode)) {
                    $pcodeDtaData = $this->Constant_model->getDataOneColumn('products', 'code', $pcode);
                    $pcode_name = $pcodeDtaData[0]->name;
                    $pcode_categeory_id = $pcodeDtaData[0]->category;
                    $pcode_cost = $pcodeDtaData[0]->purchase_price;

                    $ins_sus_item_data = array(
                            'suspend_id' => $sus_id,
                            'product_code' => $pcode,
                            'product_name' => $pcode_name,
                            'product_category' => $pcode_categeory_id,
                            'product_cost' => $pcode_cost,
                            'qty' => $qty,
                            'product_price' => $price,
                    );
                    $this->Constant_model->insertData('suspend_items', $ins_sus_item_data);
                }
            }

            $this->session->set_flashdata('alert_msg', array('success', 'Add Opened Bill', 'Successfully Added to Opened Bill.'));
            redirect(base_url().'pos');
        } elseif (isset($_POST['add_prod_submit'])) {
            $pop_pcode = $this->input->post('pop_pcode');
            $pop_pname = $this->input->post('pop_pname');
            $pop_pcate = $this->input->post('pop_pcate');
            $pop_price = $this->input->post('pop_price');

            $user_id = $this->session->userdata('user_id');
            $tm = date('Y-m-d H:i:s', time());

            $ckProdCodeResult = $this->db->query("SELECT * FROM products WHERE code = '$pop_pcode' ");
            $ckProdCodeRows = $ckProdCodeResult->num_rows();

            if ($ckProdCodeRows > 0) {
                ?>
				<script type="text/javascript">
					alert("Product Code : <?php echo $pop_pcode; ?>is already existing in the system! Please try another one");
					window.location.href = "<?=base_url()?>pos";
				</script>
		<?php

            } else {
                $ins_prod_data = array(
                        'code' => $pop_pcode,
                        'name' => $pop_pname,
                        'category' => $pop_pcate,
                        'retail_price' => $pop_price,
                        'thumbnail' => 'no_image.jpg',
                        'created_user_id' => $user_id,
                        'created_datetime' => $tm,
                        'status' => '1',
                );
                $this->Constant_model->insertData('products', $ins_prod_data);

                $this->session->set_flashdata('alert_msg', array('success', 'Add New Product', 'Successfully Added New Product.'));
                redirect(base_url().'pos');
            }
        } else {    // Sales;

            $addi_card_numb = $this->input->post('addi_card_numb');

            $suspend_id = $this->input->post('suspend_id');
            $row_count = $this->input->post('row_count');
            $card_numb = $this->input->post('card_numb');

            $subTotal = $this->input->post('subTotal');
            $dis_amt = $this->input->post('dis_amt');
            $grandTotal = $this->input->post('final_total_payable');
            $total_item_qty = $this->input->post('final_total_qty');
            $taxTotal = $this->input->post('tax_amt');

            $customer_id = $this->input->post('customer');
            if (empty($customer_id)) {
                $customer_id = 0; // Default to Walk-in Customer
                $custDtaData = [];
            } else {
                $custDtaData = $this->Constant_model->getDataOneColumn('customers', 'id', $customer_id);
            }
            $paid_by = $this->input->post('paid_by');
            $cheque = $this->input->post('cheque');
            $paid_amt = $this->input->post('paid');
            $return_change = $this->input->post('returned_change');

            $user_id = $this->session->userdata('user_id');
            $user_outlet = $this->input->post('outlet');
            $tm = date('Y-m-d H:i:s', time());

            $custDtaData = $this->Constant_model->getDataOneColumn('customers', 'id', $customer_id);
            $cust_full_name = isset($custDtaData[0]->fullname) ? $custDtaData[0]->fullname : 'Walk-in Customer';
            $cust_email = isset($custDtaData[0]->email) ? $custDtaData[0]->email : '';
            $cust_mobile = isset($custDtaData[0]->mobile) ? $custDtaData[0]->mobile : '';

            $pay_name = '';
            $payNameData = $this->Constant_model->getDataOneColumn('payment_method', 'id', $paid_by);
            if (count($payNameData) == 1) {
                $pay_name = $payNameData[0]->name;
            }

            $vt_status = '';
            if ($paid_by == '6') {            // Debit;
                $vt_status = '0';
            } else {                        // Full Payment;
                   $vt_status = '1';
            }

            $outlet_name = '';
            $outlet_address = '';
            $outlet_contact = '';
            $outlet_footer = '';

            $outletDtaData = $this->Constant_model->getDataOneColumn('outlets', 'id', $user_outlet);
            $outlet_name = $outletDtaData[0]->name;
            $outlet_address = $outletDtaData[0]->address;
            $outlet_contact = $outletDtaData[0]->contact_number;
            $outlet_footer = $outletDtaData[0]->receipt_footer;

            $discount_percentage = '';

            if (empty($dis_amt)) {
                $dis_amt = 0;
            } elseif (strpos($dis_amt, '%') > 0) {
                $discount_percentage = $dis_amt;

                $temp_dis_Array = explode('%', $dis_amt);
                $temp_dis = $temp_dis_Array[0];

                $temp_item_price = 0;

                for ($i = 1; $i <= $row_count; ++$i) {
                    $pcode = $this->input->post("pcode_$i");
                    $price = $this->input->post("price_$i");
                    $qty = $this->input->post("qty_$i");

                    if (!empty($pcode)) {
                        $temp_item_price += ($price * $qty);
                    }
                }

                $dis_amt = number_format(($temp_item_price * ($temp_dis / 100)), 2, '.', '');
            }

            // Insert Into Order;
            $ins_order_data = array(
                    'customer_id' => $customer_id,
                    'customer_name' => $cust_full_name,
                    'customer_email' => $cust_email,
                    'customer_mobile' => $cust_mobile,
                    'ordered_datetime' => $tm,
                    'outlet_id' => $user_outlet,
                    'outlet_name' => $outlet_name,
                    'outlet_address' => $outlet_address,
                    'outlet_contact' => $outlet_contact,
                    'outlet_receipt_footer' => $outlet_footer,
                    'gift_card' => $card_numb,
                    'subtotal' => $subTotal,
                    'discount_total' => $dis_amt,
                    'discount_percentage' => $discount_percentage,
                    'tax' => $taxTotal,
                    'grandtotal' => $grandTotal,
                    'total_items' => $total_item_qty,
                    'payment_method' => $paid_by,
                    'payment_method_name' => $pay_name,
                    'cheque_number' => $cheque,
                    'paid_amt' => $paid_amt,
                    'return_change' => $return_change,
                    'created_user_id' => $user_id,
                    'created_datetime' => $tm,
                    'vt_status' => $vt_status,
                    'status' => '1',
                    'card_number' => $addi_card_numb,
            );
            $order_id = $this->Constant_model->insertDataReturnLastId('orders', $ins_order_data);

            // Order Item -- START;
            for ($i = 1; $i <= $row_count; ++$i) {
                $pcode = $this->input->post("pcode_$i");
                $price = $this->input->post("price_$i");
                $qty = $this->input->post("qty_$i");

                if (!empty($pcode)) {
                    $pcode_name = '';
                    $pcode_category = '0';
                    $cost = 0;

                    $pcodeDtaData = $this->Constant_model->getDataOneColumn('products', 'code', $pcode);
                    if (count($pcodeDtaData) == 1) {
                        $pcode_name = $pcodeDtaData[0]->name;
                        $pcode_category = $pcodeDtaData[0]->category;
                        $cost = $pcodeDtaData[0]->purchase_price;
                    } else {
                        if ($suspend_id > 0) {
                            $ckSusItemResult = $this->db->query("SELECT * FROM suspend_items WHERE suspend_id = '$suspend_id' AND product_code = '$pcode' ");
                            $ckSusItemRows = $ckSusItemResult->num_rows();
                            if ($ckSusItemRows == 1) {
                                $ckSusItemData = $ckSusItemResult->result();

                                $pcode_name = $ckSusItemData[0]->product_name;
                                $pcode_category = $ckSusItemData[0]->product_category;
                                $cost = $ckSusItemData[0]->product_cost;

                                unset($ckSusItemData);
                            }
                            unset($ckSusItemResult);
                            unset($ckSusItemRows);
                        }
                    }

                    $ins_order_item_data = array(
                            'order_id' => $order_id,
                            'product_code' => $pcode,
                            'product_name' => $pcode_name,
                            'product_category' => $pcode_category,
                            'cost' => $cost,
                            'price' => $price,
                            'qty' => $qty,
                    );
                    $this->Constant_model->insertData('order_items', $ins_order_item_data);

                    // Deduction Inventory -- START;
                    $ex_qty = 0;
                    $ckInvData = $this->Constant_model->getDataTwoColumn('inventory', 'product_code', $pcode, 'outlet_id', $user_outlet);

                    if (count($ckInvData) == 1) {
                        $ex_inv_id = $ckInvData[0]->id;
                        $ex_qty = $ckInvData[0]->qty;

                        $deduct_qty = 0;
                        $deduct_qty = $ex_qty - $qty;

                        $upd_inv_data = array(
                                'qty' => $deduct_qty,
                        );
                        $this->Constant_model->updateData('inventory', $upd_inv_data, $ex_inv_id);
                    }
                    // Deduction Inventory -- END;
                }
            }
            // Order Item -- END;

            if ($suspend_id > 0) {
                $ckSusData = $this->Constant_model->getDataOneColumn('suspend', 'id', $suspend_id);

                if (count($ckSusData) == 1) {
                    $upd_data = array(
                            'updated_user_id' => $user_id,
                            'updated_datetime' => $tm,
                            'status' => '1',
                    );
                    $this->Constant_model->updateData('suspend', $upd_data, $suspend_id);
                }
            }

            // Gift Card;
            if (!empty($card_numb)) {
                $ckGiftResult = $this->db->query("SELECT * FROM gift_card WHERE card_number = '$card_numb' ");
                $ckGiftRows = $ckGiftResult->num_rows();
                if ($ckGiftRows == 1) {
                    $ckGiftData = $ckGiftResult->result();

                    $ckGift_id = $ckGiftData[0]->id;

                    $upd_gift_data = array(
                              'status' => '1',
                              'updated_user_id' => $user_id,
                              'updated_datetime' => $tm,
                    );
                    $this->Constant_model->updateData('gift_card', $upd_gift_data, $ckGift_id);

                    unset($ckGiftData);
                }
                unset($ckGiftResult);
                unset($ckGiftRows);
            }

            redirect(base_url().'pos/view_invoice?id='.$order_id, 'refresh');
        }
    }