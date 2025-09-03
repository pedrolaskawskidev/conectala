<?php

class Api extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding,Authorization");
    }

    protected function json_return($data, $status = 200)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data))
            ->set_status_header($status);
    }

    public function index()
    {
        try {

            $users = $this->Users_model->get_all_users();

            if ($users === false) {
                return $this->json_return(['message' => 'Erro no retorno de usuários, informe ao desenvolvimento'], 500);
            }

            if (empty($users)) {
                return $this->json_return(['message' => 'Nenhum usuário encontrado'], 404);
            }

            foreach ($users as &$user) {
                unset($user->password, $user->api_token, $user->updated_at);
                $user->created_at = date('d/m/Y', strtotime($user->created_at));
            }

            return $this->json_return($users);
        } catch (Throwable $e) {
            return $this->json_return(['message' => 'Erro inesperado, informe ao desenvolvimento'], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->Users_model->get_user_by_id($id);

            if (!$user) {
                return $this->json_return(['message' => 'Usuário não encontrado'], 404);
            }

            unset($user->password, $user->api_token, $user->updated_at);
            $user->created_at = date('d/m/Y', strtotime($user->created_at));

            return $this->json_return($user);
        } catch (Throwable $e) {
            return $this->json_return(['message' => 'Erro inesperado, informe ao desenvolvimento'], 500);
        }
    }

    public function create()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $verify = $this->verifyUser($input);

            if ($verify !== true) {
                return $verify;
            }

            $user_data = [
                'username' => $input['username'],
                'email' => $input['email'],
                'password' => password_hash($input['password'], PASSWORD_BCRYPT)
            ];

            $new_user_id = $this->Users_model->create_user($user_data);
            if (!$new_user_id) {
                return $this->json_return(['message' => 'Erro ao criar usuário, informe ao desenvolvimento'], 500);
            }

            unset($user_data['password']);
            unset($user_data['api_token']);
            unset($user_data['id']);

            return $this->json_return(['message' => 'Usuário criado com sucesso', 'user' => $user_data], 201);
        } catch (Throwable $e) {
            return $this->json_return(['message' => 'Erro inesperado, informe ao desenvolvimento'], 500);
        }
    }

    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !is_array($input)) {
            return $this->json_return(['message' => 'Nenhum dado enviado'], 400);
        }

        $user = $this->verifyUpdateUser($input);

        if ($user !== true) {
            return $this->json_return(['message' => $user['message']], $user['status']);
        }
        $user_data = [];

        if (isset($input['username'])) {
            $user_data['username'] = $input['username'];
        }

        if (isset($input['email'])) {
            $user_data['email'] = $input['email'];
        }

        if (isset($input['password'])) {
            $user_data['password'] = password_hash($input['password'], PASSWORD_BCRYPT);
        }

        $updated = $this->Users_model->update_user($id, $user_data);
        if (!$updated) {
            return $this->json_return(['message' => 'Erro ao atualizar usuário, informe ao desenvolvimento'], 500);
        }

        unset($user_data['password']);
        unset($user_data['api_token']);
        unset($user_data['id']);

        return $this->json_return(['message' => 'Usuário atualizado com sucesso', 'user' => $user_data], 200);
    }

    public function delete($id)
    {

        try {
            $token = $this->input->get_request_header('token');

            $loggedUser = $this->Users_model->get_user_by_token($token);

            if ($loggedUser->id == $id) {
                return $this->json_return(['message' => 'Usuário não pode excluir a si mesmo'], 403);
            }

            $user = $this->Users_model->get_user_by_id($id);
            if (!$user) {
                return $this->json_return(['message' => 'Usuário não encontrado'], 404);
            }

            $deleted = $this->Users_model->delete_user($id);
            if (!$deleted) {
                return $this->json_return(['message' => 'Erro ao deletar usuário, informe ao desenvolvimento'], 500);
            }

            return $this->json_return(['message' => 'Usuário deletado com sucesso'], 200);
        } catch (Throwable $e) {
            return $this->json_return(['message' => 'Erro inesperado, informe ao desenvolvimento'], 500);
        }
    }

    private function verifyUser($data)
    {
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return $this->json_return(['message' => 'Nome de usuário, email e senha são obrigatórios'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json_return(['message' => 'Email inválido'], 400);
        }

        if (strlen($data['password']) < 5) {
            return $this->json_return(['message' => 'Senha deve ter no mínimo 5 caracteres'], 400);
        }

        $existing_username = $this->Users_model->get_user_by_username($data['username']);
        $existing_email = $this->Users_model->get_user_by_email($data['email']);

        if ($existing_username || $existing_email) {
            return $this->json_return(['message' => 'Nome de usuário ou email já cadastrado'], 409);
        }

        return true;
    }

    private function verifyUpdateUser($data)
    {
        $existing_username = null;
        $existing_email = null;

        if (isset($data['username'])) {
            if (empty($data['username'])) {
                return ['message' => 'Nome de usuário não pode ser vazio', 'status' => 400];
            }

            $existing_username = $this->Users_model->get_user_by_username($data['username']);
            if ($existing_username) {
                return ['message' => 'Nome de usuário já cadastrado', 'status' => 409];
            }
        }

        if (isset($data['email'])) {
            if (empty($data['email'])) {
                return ['message' => 'Email não pode ser vazio', 'status' => 400];
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['message' => 'Email inválido', 'status' => 400];
            }

            $existing_email = $this->Users_model->get_user_by_email($data['email']);
            if ($existing_email) {
                return ['message' => 'Email já cadastrado', 'status' => 409];
            }
        }

        if (isset($data['password']) && strlen($data['password']) < 5) {
            return ['message' => 'Senha deve ter no mínimo 5 caracteres', 'status' => 400];
        }

        return true;
    }
}
