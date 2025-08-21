<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ella_contractors_model extends App_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Basic model for ella contractors module
     */
    public function test() {
        return "Hello from Ella Contractors Model";
    }
}
