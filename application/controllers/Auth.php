<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Auth extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding,Authorization");
        header("Content-Type: application/json");
        $this->load->model('Users_model');
    }

    protected function json_return($data, $status = 200)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data))
            ->set_status_header($status);
    }


    public function login()
    {

        if ($this->input->method() !== 'post') {
            return $this->json_return(['message' => 'Método não permitido'], 405);
        }

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if (empty($username) || empty($password)) {
            return $this->json_return(['message' => 'Usuário e senha são obrigatórios'], 400);
        }

        $user = $this->Users_model->get_user_by_username($username);

        if ($user && password_verify($password, $user->password)) {
            $token = bin2hex(random_bytes(32));
            $this->Users_model->set_user_token($token, $user->id);

            return $this->json_return(['id' => $user->id, 'token' => $token]);
        } else {
            return $this->json_return(['message' => 'Usuário ou senha inválidos'], 401);
        }
    }

    public function logout()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_return(['message' => 'Método não permitido'], 405);
        }

        $headers = $this->input->request_headers();
        if (isset($headers['token'])) {
            $token = $headers['token'];
            $user = $this->Users_model->get_user_by_token($token);
            if ($user) {
                $this->Users_model->set_logout($user->id);
                return $this->json_return(['message' => 'Logout realizado com sucesso']);
            } else {
                return $this->json_return(['message' => 'Token inválido'], 401);
            }
        } else {
            return $this->json_return(['message' => 'Token não fornecido'], 400);
        }
    }
}
