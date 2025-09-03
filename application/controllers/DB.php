<?php
defined('BASEPATH') or exit('No direct script access allowed');



class DB extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('migration');
        $this->load->dbforge();
        $this->load->model('Users_model');
    }

    public function migrate()
    {
        try {
            if ($this->migration->version(1) === FALSE) {
                // retorna erro
                echo $this->migration->error_string();
            }

            if ($this->migration->latest() === FALSE) {

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode([
                        'message' => 'Erro ao executar migration',
                        'error'   => $this->migration->error_string()
                    ]));
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'message' => 'Migração executada com sucesso!'
                ]));
        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'message' => 'Erro inesperado',
                    'error'   => $e->getMessage()
                ]));
        }
    }

    public function seed()
    {

        $data = [
            [
                'username' => 'teste',
                'email'    => 'teste@example.com',
                'password' => password_hash('teste01', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($data as $user) {
            $this->Users_model->create_user($user);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'message' => 'Dados de usuário inseridos com sucesso!',
                'user' => 'teste',
                'password' => 'teste01'
            ]));
    }
}
