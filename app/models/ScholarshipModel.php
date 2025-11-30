<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ScholarshipModel extends Model {
    protected $table = 'scholarships';
    protected $primary_key = 'id';

    public function get_all() {
        return $this->db->table('scholarships s')
                       ->join('users u', 's.created_by = u.id', 'LEFT')
                       ->select('s.*, u.username as counselor_name')
                       ->order_by('s.created_at DESC')
                       ->get_all();
    }

    public function get_by_id($id) {
        return $this->find($id);
    }

    public function get_active() {
        $today = date('Y-m-d');
        return $this->db->table('scholarships s')
                       ->join('users u', 's.created_by = u.id', 'LEFT')
                       ->select('s.*, u.username as counselor_name')
                       ->where('s.application_deadline', '>=', $today)
                       ->order_by('s.application_deadline ASC')
                       ->get_all();
    }
    
    public function get_active_paginated($offset, $limit) {
        $today = date('Y-m-d');
        return $this->db->table('scholarships s')
                       ->join('users u', 's.created_by = u.id', 'LEFT')
                       ->select('s.*, u.username as counselor_name')
                       ->where('s.application_deadline', '>=', $today)
                       ->order_by('s.application_deadline ASC')
                       ->limit($limit, $offset)
                       ->get_all();
    }

    public function search($keyword) {
        return $this->db->table('scholarships s')
                       ->join('users u', 's.created_by = u.id', 'LEFT')
                       ->select('s.*, u.username as counselor_name')
                       ->group_start()
                           ->like('s.title', $keyword)
                           ->or_like('s.description', $keyword)
                           ->or_like('s.provider', $keyword)
                       ->group_end()
                       ->order_by('s.created_at DESC')
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
        return $this->db->table('scholarships')->count();
    }
    
    public function count_active() {
        $today = date('Y-m-d');
        return $this->db->table('scholarships')
                       ->where('application_deadline', '>=', $today)
                       ->count();
    }
}