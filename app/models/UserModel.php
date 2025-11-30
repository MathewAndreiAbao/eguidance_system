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

    /**
     * Get user by username
     * @param string $username
     * @return array|null
     */
    public function get_user_by_username($username) {
        $result = $this->db->table($this->table)
            ->where('username', $username)
            ->get();
        return is_array($result) ? $result : null;
    }

    /**
     * Get all counselors with their profile information
     * @return array
     */
    public function get_all_counselors() {
        $counselors = $this->db->table('users u')
            ->join('profiles p', 'u.id = p.user_id', 'left')
            ->where('u.role', 'counselor')
            ->select('u.id, u.username, u.role, p.name, p.email, p.phone')
            ->get_all();
        
        return is_array($counselors) ? $counselors : [];
    }

    /**
     * Get all counselors (deprecated - use get_all_counselors instead)
     * @return array
     * @deprecated Use get_all_counselors() instead
     */
    public function getAllCounselors() {
        return $this->get_all_counselors();
    }

    /**
     * Get all students
     * @return array
     */
    public function get_all_students() {
        $result = $this->db->table($this->table)
            ->where('role', 'student')
            ->get_all();
        return is_array($result) ? $result : [];
    }

    /**
     * Check if username exists
     * @param string $username
     * @return bool
     */
    public function username_exists($username) {
        $result = $this->db->table($this->table)
            ->where('username', $username)
            ->limit(1)
            ->get();
        return !empty($result);
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
