<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ResourceModel extends Model {
    protected $table = 'resources';
    protected $primary_key = 'id';

    public function get_all() {
        return $this->db->table('resources r')
                       ->join('users u', 'r.counselor_id = u.id', 'LEFT')
                       ->select('r.*, u.username as counselor_name')
                       ->order_by('r.created_at DESC')
                       ->get_all();
    }

    public function get_by_id($id) {
        return $this->find($id);
    }

    public function get_by_type($type) {
        return $this->db->table('resources r')
                       ->join('users u', 'r.counselor_id = u.id', 'LEFT')
                       ->select('r.*, u.username as counselor_name')
                       ->where('r.type', $type)
                       ->order_by('r.created_at DESC')
                       ->get_all();
    }

    public function search($keyword) {
        return $this->db->table('resources r')
                       ->join('users u', 'r.counselor_id = u.id', 'LEFT')
                       ->select('r.*, u.username as counselor_name')
                       ->like('r.title', $keyword)
                       ->or_like('r.description', $keyword)
                       ->order_by('r.created_at DESC')
                       ->get_all();
    }

    public function increment_views($id) {
        $stmt = $this->db->raw("UPDATE resources SET views = views + 1 WHERE id = ?", [$id]);
        return $stmt->rowCount();
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
        return $this->db->table('resources r')
                       ->where('r.counselor_id', $counselor_id)
                       ->get_all();
    }

    public function get_recent($limit = 5) {
        return $this->db->table('resources r')
                       ->join('users u', 'r.counselor_id = u.id', 'LEFT')
                       ->select('r.*, u.username as counselor_name')
                       ->order_by('r.created_at DESC')
                       ->limit($limit)
                       ->get_all();
    }
    
    public function count_all() {
        return $this->db->table('resources')->count();
    }
    
    public function get_paginated($limit_clause) {
        $stmt = $this->db->raw("SELECT r.*, u.username as counselor_name FROM resources r LEFT JOIN users u ON r.counselor_id = u.id ORDER BY r.created_at DESC {$limit_clause}");
        return $stmt->fetchAll();
    }
    
    public function count_by_counselor($counselor_id) {
        return $this->db->table('resources')->where('counselor_id', $counselor_id)->count();
    }
    
    public function get_paginated_by_counselor($counselor_id, $limit_clause) {
        $stmt = $this->db->raw("SELECT * FROM resources WHERE counselor_id = ? ORDER BY created_at DESC {$limit_clause}", [$counselor_id]);
        return $stmt->fetchAll();
    }
}