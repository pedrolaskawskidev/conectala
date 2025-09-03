<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	public function index()
	{
		$data['mensagem'] = "Olá, CodeIgniter 3!";
		$this->load->view('home_view', $data);
	}
}
