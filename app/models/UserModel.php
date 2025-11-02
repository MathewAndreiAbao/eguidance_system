<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UserModel extends Model {
    protected $table = 'users';
    protected $primary_key = 'id';

    public function get_all() {
        return $this->all();
    }

    public function get_by_id($id) {
        return $this->find($id);
    }

    public function create_record($data) {
        return $this->insert($data);
    }

    public function update_record($id, $data) {
        return $this->update($id, $data);
    }

    public function delete_record($id) {
        return $this->delete($id);
    }

    public function get_user_by_username($username) {
        return $this->filter(['username' => $username])->get();
    }

    public function getAllCounselors() {
        return $this->filter(['role' => 'counselor'])->get_all();
    }

    public function get_all_counselors() {
        return $this->filter(['role' => 'counselor'])->get_all();
    }

    public function get_all_students() {
        return $this->filter(['role' => 'student'])->get_all();
    }

    public function username_exists($username) {
        $row = $this->filter(['username' => $username])->limit(1)->get();
        return !empty($row);
    }

    public function verify_password($username, $password) {
        $user = $this->get_user_by_username($username);
        if ($user) {
            return password_verify($password, $user['password']);
        }
        return false;
    }

    public function register($username, $password, $role = 'student') {
        $data = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->create_record($data);
    }
}
