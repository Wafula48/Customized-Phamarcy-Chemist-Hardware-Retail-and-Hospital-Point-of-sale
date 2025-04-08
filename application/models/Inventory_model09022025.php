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
       /* $this->db->select('id, product_code, qty');
        $this->db->from('inventory');
        $this->db->where('qty <', 10);
        $query = $this->db->get();

        return $query->result();*/
    
}
