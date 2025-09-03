<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {


    protected $user = null;

    public function __construct() {
        parent::__construct();
        $this->load->model('Users_model');

        $headers = $this->input->request_headers();
        $tokenHeader = isset($headers['token']) ? $headers['token'] : null;

        if ($tokenHeader) {
            $this->user = $this->Users_model->get_user_by_token($tokenHeader);
            
        }
        if (!$this->user || strtotime($this->user->updated_at) < time()) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Não autorizado ou token expirado.']))
                ->set_status_header(401)
                ->_display();
            exit;
        }
    }
}