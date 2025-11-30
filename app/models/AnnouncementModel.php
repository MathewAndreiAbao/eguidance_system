<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AnnouncementModel extends Model {
    protected $table = 'announcements';
    protected $primary_key = 'id';

    public function get_all() {
        return $this->db->table('announcements a')
                       ->join('users u', 'a.counselor_id = u.id', 'LEFT')
                       ->select('a.*, u.username as counselor_name')
                       ->order_by('a.created_at DESC')
                       ->get_all();
    }

    public function get_by_id($id) {
        return $this->db->table('announcements a')
                       ->join('users u', 'a.counselor_id = u.id', 'LEFT')
                       ->select('a.*, u.username as counselor_name')
                       ->where('a.id', $id)
                       ->get();
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

    public function get_by_counselor($counselor_id) {
        return $this->db->table('announcements')
                       ->where('counselor_id', $counselor_id)
                       ->order_by('created_at', 'DESC')
                       ->get_all();
    }

    public function get_recent($limit = 10) {
        return $this->db->table('announcements')
                       ->order_by('created_at', 'DESC')
                       ->limit($limit)
                       ->get_all();
    }
    
    public function count_all() {
        return $this->db->table('announcements')->count();
    }
    
    public function count_by_counselor($counselor_id) {
        return $this->db->table('announcements')->where('counselor_id', $counselor_id)->count();
    }
}