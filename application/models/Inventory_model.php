<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model
{
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }

    public function record_product_count()
    {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('products');
        $this->db->save_queries = false;

        return $query->num_rows();
    }
	// Add this method to Inventory_model.php
public function fetch_product_data_with_supplier($limit, $start) {
    $this->db->select('products.*, suppliers.name as supplier_name');
    $this->db->from('products');
    $this->db->join('suppliers', 'products.supplier_id = suppliers.id', 'left');
    $this->db->order_by('products.id', 'DESC');
    $this->db->limit($limit, $start);
    $query = $this->db->get();
    
    $result = $query->result();
    $this->db->save_queries = false;
    
    return $result;
}
	

    public function fetch_product_data($limit, $start)
    {
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $start);
        $query = $this->db->get('products');

        $result = $query->result();

        $this->db->save_queries = false;

        return $result;
    }
 
		public function get_low_stock_products() {
    $this->db->select('products.name, inventory.qty');
    $this->db->from('inventory');
    $this->db->join('products', 'inventory.product_code = products.code', 'left');
    $this->db->where('inventory.qty <', 10);
    $query = $this->db->get();

    return $query->result();
}
public function get_expiry_stock_products() {
    $one_year_from_now = date('Y-m-d', strtotime('+5 months'));

    $this->db->select('products.name, products.expiry_date');
    $this->db->from('products');
    $this->db->where('products.expiry_date <=', $one_year_from_now);
    $query = $this->db->get();

    return $query->result();
}
       /* $this->db->select('id, product_code, qty');
        $this->db->from('inventory');
        $this->db->where('qty <', 10);
        $query = $this->db->get();

        return $query->result();*/

    
}
