<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

class Sales Reports_model extends CI_Model
{
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }
}
