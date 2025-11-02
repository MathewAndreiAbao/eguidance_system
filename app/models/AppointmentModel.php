<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AppointmentModel extends Model {
    protected $table = 'appointments';
    protected $primary_key = 'id';

    public function get_all() {
        return $this->db->table('appointments a')
                       ->join('users u1', 'a.student_id = u1.id', 'LEFT')
                       ->join('users u2', 'a.counselor_id = u2.id', 'LEFT')
                       ->select('a.*, u1.username as student_name, u2.username as counselor_name')
                       ->get_all();
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

    public function getAllAppointments() {
        return $this->db->table('appointments a')
                       ->join('users u1', 'a.student_id = u1.id', 'LEFT')
                       ->join('users u2', 'a.counselor_id = u2.id', 'LEFT')
                       ->select('a.*, u1.username as student_name, u2.username as counselor_name')
                       ->get_all();
    }

    public function getStudentAppointments($student_id) {
        return $this->filter(['student_id' => $student_id])->get_all();
    }

    public function get_student_appointments($student_id) {
        return $this->filter(['student_id' => $student_id])->get_all();
    }

    public function get_counselor_appointments($counselor_id) {
        return $this->filter(['counselor_id' => $counselor_id])->get_all();
    }

    public function get_upcoming_appointments($user_id, $role = 'student') {
        $field = ($role == 'student') ? 'student_id' : 'counselor_id';
        return $this->filter([$field => $user_id, 'status' => 'pending'])
                   ->filter(['date', '>=', date('Y-m-d')])
                   ->order_by('date ASC, time ASC')
                   ->get_all();
    }

    public function is_time_slot_available($date, $time, $counselor_id = null) {
        $conditions = ['date' => $date, 'time' => $time];
        if ($counselor_id) {
            $conditions['counselor_id'] = $counselor_id;
        }
        $row = $this->filter($conditions)->limit(1)->get();
        return empty($row);
    }
}
