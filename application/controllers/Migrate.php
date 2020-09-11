<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function __construct()
    {
        if (!is_cli())
            show_404();
        else {
            parent::__construct();
            $this->load->library('migration');
        }
    }

    public function index()
    {
        if (!$this->migration->current() === FALSE)
            echo $this->migration->error_string();

        echo "\n\nCompleted.\n\n";
    }
}
