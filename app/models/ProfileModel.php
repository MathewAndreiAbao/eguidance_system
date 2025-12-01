<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProfileModel extends Model {
    protected $table = 'profiles';
    protected $primary_key = 'id';
    protected $fillable = ['user_id', 'name', 'email', 'phone', 'bio'];

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
     * Get profile by user ID
     * @param int $user_id
     * @return array|null
     */
    public function get_profile_by_user_id($user_id) {
        // Direct query approach to avoid linter issues
        $result = $this->db->table($this->table)
            ->where('user_id', $user_id)
            ->get();
        return is_array($result) ? $result : null;
    }

    public function getProfileWithUser($user_id) {
        try {
            $result = $this->db->table('profiles p')
                           ->join('users u', 'p.user_id = u.id')
                           ->where('p.user_id', $user_id)
                           ->select('p.*, u.username, u.role')
                           ->get();
            return $result ? $result : null;
        } catch (Exception $e) {
            error_log('Database error in getProfileWithUser: ' . $e->getMessage());
            return null;
        }
    }

    public function get_profile_with_user($user_id) {
        $result = $this->db->table('profiles p')
                       ->join('users u', 'p.user_id = u.id')
                       ->where('p.user_id', $user_id)
                       ->select('p.*, u.username, u.role')
                       ->get();
        return $result ? $result : null;
    }
}