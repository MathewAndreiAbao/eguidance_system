<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth {
    protected $_lava;

    public function __construct() {
        $this->_lava = lava_instance();
        $this->_lava->call->model('UserModel');
        $this->_lava->call->library('session');
    }

    public function register($username, $password, $role = 'student') {
        return $this->_lava->UserModel->register($username, $password, $role);
    }

    public function login($username, $password) {
        if ($this->_lava->UserModel->verify_password($username, $password)) {
            $user = $this->_lava->UserModel->get_user_by_username($username);
            if ($user) {
                $this->_lava->session->set_userdata([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'logged_in' => true
                ]);
                return true;
            }
        }
        return false;
    }

    public function is_logged_in() {
        return (bool)$this->_lava->session->userdata('logged_in');
    }

    public function logout() {
        $this->_lava->session->unset_userdata(['user_id','username','role','logged_in']);
    }
}
