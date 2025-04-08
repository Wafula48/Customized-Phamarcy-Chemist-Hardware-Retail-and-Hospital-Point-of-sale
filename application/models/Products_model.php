<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

class Products_model extends CI_Model
{
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }

    public function record_category_count()
    {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('category');
        $this->db->save_queries = false;

        return $query->num_rows();
    }

    public function fetch_category_data($limit, $start)
    {
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $start);
        $query = $this->db->get('category');

        $result = $query->result();

        $this->db->save_queries = false;

        return $result;
    }
public function get_total_stock_value()
{
    $this->db->select('SUM(purchase_price * status) AS total_stock_value');
    $this->db->from('products'); // Ensure 'products' is your correct table name
    $query = $this->db->get();
    
    $result = $query->row();
    return $result->total_stock_value ? $result->total_stock_value : 0;
}
public function get_total_salesprofit()
{
    $this->db->select('SUM(retail_price * status) AS total_salesprofit');
    $this->db->from('products'); // Ensure 'products' is your correct table name
    $query = $this->db->get();
    
    $result = $query->row();
    return $result->total_salesprofit ? $result->total_salesprofit : 0;
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

    public function record_label_count()
    {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('products');
        $this->db->save_queries = false;

        return $query->num_rows();
    }

    public function fetch_label_data($limit, $start)
    {
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $start);
        $query = $this->db->get('products');

        $result = $query->result();

        $this->db->save_queries = false;

        return $result;
    }
}
