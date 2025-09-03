<?php
defined('BASEPATH') or exit('No direct script access allowed');



class Users_Model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_users()
    {
        $query = $this->db->get('users');
        return $query->result();
    }

    public function get_user_by_username($username)
    {
        $query = $this->db->get_where('users', array('username' => $username));
        return $query->row();
    }

    public function get_user_by_id($id)
    {
        $query = $this->db->get_where('users', array('id' => $id));
        return $query->row();
    }

    public function get_user_by_email($email)
    {
        $query = $this->db->get_where('users', array('email' => $email));
        return $query->row();
    }

    public function create_user($data)
    {
        return $this->db->insert('users', $data);
    }

    public function update_user($id, $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function delete_user($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }

    public function get_user_login($data)
    {
        $query = $this->db->get_where('users', array('username' => $data['username'], 'password' => $data['password']));

        if (!$query) {
            return false;
        }

        return $query->row();
    }

    public function set_user_token($token, $user_id)
    {
        $this->db->where('id', $user_id);

        return $this->db->update('users', array('api_token' => $token, 'updated_at' => date('Y-m-d H:i:s', strtotime("+2 hours"))));
    }

    public function get_user_by_token($token)
    {
        $query = $this->db->get_where('users', ['api_token' => $token]);

        if (!$query) {
            return false;
        }

        return $query->row();
    }

    public function set_logout($user_id)
    {
        $this->db->where('id', $user_id);

        $this->db->update('users', array('api_token' => null, 'updated_at' => date('Y-m-d H:i:s')));
        
        return true;
    }
}
