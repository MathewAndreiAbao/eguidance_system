<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class FeedbackModel extends Model {
    protected $table = 'student_feedback';
    protected $primary_key = 'id';

    public function get_for_student($student_id) {
        return $this->db->table('student_feedback f')
                        ->join('users u', 'f.counselor_id = u.id', 'LEFT')
                        ->select('f.*, u.username as counselor_name')
                        ->where('f.student_id', $student_id)
                        ->order_by('f.created_at DESC')
                        ->get_all();
    }

    public function get_for_counselor($counselor_id) {
        return $this->db->table('student_feedback f')
                        ->join('users u', 'f.student_id = u.id', 'LEFT')
                        ->select('f.*, u.username as student_name')
                        ->where('f.counselor_id', $counselor_id)
                        ->order_by('f.created_at DESC')
                        ->get_all();
    }

    public function create_feedback($data) {
        return $this->insert($data);
    }

    public function update_status_for_counselor($id, $counselor_id, $status) {
        return $this->db->table($this->table)
                        ->where('id', $id)
                        ->where('counselor_id', $counselor_id)
                        ->update([
                            'status' => $status,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
    }

    public function get_recent_for_dashboard($counselor_id, $limit = 5) {
        return $this->db->table('student_feedback f')
                        ->join('users u', 'f.student_id = u.id', 'LEFT')
                        ->select('f.*, u.username as student_name')
                        ->where('f.counselor_id', $counselor_id)
                        ->order_by('f.created_at DESC')
                        ->limit($limit)
                        ->get_all();
    }

    public function count_new_feedback($counselor_id) {
        return $this->db->table($this->table)
                        ->where('counselor_id', $counselor_id)
                        ->where('status', 'new')
                        ->count();
    }
}

