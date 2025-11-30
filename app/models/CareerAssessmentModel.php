<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class CareerAssessmentModel extends Model {
    protected $table = 'career_assessments';
    protected $primary_key = 'id';

    public function get_all() {
        return $this->db->table('career_assessments ca')
                       ->join('users u', 'ca.student_id = u.id', 'LEFT')
                       ->select('ca.*, u.username as student_name')
                       ->order_by('ca.created_at DESC')
                       ->get_all();
    }

    public function get_by_id($id) {
        return $this->find($id);
    }

    public function get_by_student($student_id) {
        return $this->db->table('career_assessments ca')
                       ->join('users u', 'ca.student_id = u.id', 'LEFT')
                       ->select('ca.*, u.username as student_name')
                       ->where('ca.student_id', $student_id)
                       ->order_by('ca.created_at DESC')
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
        return $this->db->table('career_assessments')->count();
    }
}