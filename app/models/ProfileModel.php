<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProfileModel extends Model {
    protected $table = 'profiles';
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

    public function get_profile_by_user_id($user_id) {
        return $this->filter(['user_id' => $user_id])->get();
    }

    public function getProfileWithUser($user_id) {
        return $this->db->table('profiles p')
                       ->join('users u', 'p.user_id = u.id')
                       ->where('p.user_id', $user_id)
                       ->select('p.*, u.username, u.role')
                       ->get();
    }

    public function get_profile_with_user($user_id) {
        return $this->db->table('profiles p')
                       ->join('users u', 'p.user_id = u.id')
                       ->where('p.user_id', $user_id)
                       ->select('p.*, u.username, u.role')
                       ->get();
    }

    public function save_picture($user_id, $picture_path) {
        return $this->filter(['user_id' => $user_id])->update(['picture' => $picture_path]);
    }
}
