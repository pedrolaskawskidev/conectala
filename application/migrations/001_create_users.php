<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Users extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ),
            'username' => array(
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ),
            'email' => array(
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ),
            'password' => array(
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ),
            'api_token' => array(
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => TRUE,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
        ));

        $this->dbforge->add_key('id', TRUE);

        if (!$this->dbforge->create_table('users', TRUE)) {
            echo $this->db->last_query();
            die('Erro ao criar tabela users');
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('users', TRUE);
    }
}
