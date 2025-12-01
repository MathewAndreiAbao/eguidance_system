<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class CareerPathwayModel extends Model {
    protected $table = 'career_pathways';
    protected $primary_key = 'id';

    public function get_all() {
        return $this->db->table('career_pathways cp')
                       ->join('users u', 'cp.created_by = u.id', 'LEFT')
                       ->select('cp.*, u.username as counselor_name')
                       ->order_by('cp.created_at DESC')
                       ->get_all();
    }

    public function get_by_id($id) {
        return $this->find($id);
    }

    public function get_by_field($field) {
        return $this->db->table('career_pathways cp')
                       ->join('users u', 'cp.created_by = u.id', 'LEFT')
                       ->select('cp.*, u.username as counselor_name')
                       ->where('cp.field', $field)
                       ->order_by('cp.created_at DESC')
                       ->get_all();
    }

    public function search($keyword) {
        return $this->db->table('career_pathways cp')
                       ->join('users u', 'cp.created_by = u.id', 'LEFT')
                       ->select('cp.*, u.username as counselor_name')
                       ->like('cp.title', $keyword)
                       ->or_like('cp.description', $keyword)
                       ->or_like('cp.field', $keyword)
                       ->order_by('cp.created_at DESC')
                       ->get_all();
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
    
    public function count_all() {
        return $this->db->table('career_pathways')->count();
    }
}