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
                       ->order_by('a.date ASC, a.time ASC')
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
        return $this->get_student_appointments($student_id);
    }

    public function get_student_appointments($student_id) {
        return $this->db->table('appointments a')
                       ->join('users u2', 'a.counselor_id = u2.id', 'LEFT')
                       ->select('a.*, u2.username as counselor_name')
                       ->where('a.student_id', $student_id)
                       ->order_by('a.date ASC, a.time ASC')
                       ->get_all();
    }

    public function get_counselor_appointments($counselor_id) {
        return $this->db->table('appointments a')
                       ->join('users u1', 'a.student_id = u1.id', 'LEFT')
                       ->join('users u2', 'a.counselor_id = u2.id', 'LEFT')
                       ->select('a.*, u1.username as student_name, u2.username as counselor_name')
                       ->where('a.counselor_id', $counselor_id)
                       ->order_by('a.date ASC, a.time ASC')
                       ->get_all();
    }

    public function get_upcoming_appointments($user_id, $role = 'student') {
        $field = ($role == 'student') ? 'student_id' : 'counselor_id';
        return $this->db->table('appointments')
                       ->where($field, $user_id)
                       ->where('status', 'pending')
                       ->where('date', '>=', date('Y-m-d'))
                       ->order_by('date ASC, time ASC')
                       ->get_all();
    }

    /**
     * Check if a time slot is available for a given counselor and date/time.
     * Returns true when no other appointment exists for the same counselor+date+time.
     * If $exclude_id is provided, that appointment id will be ignored (useful when editing).
     */
    public function is_time_slot_available($date, $time, $counselor_id = null, $exclude_id = null) {
        $query = $this->db->table('appointments')
                         ->where('date', $date)
                         ->where('time', $time);
        
        if ($counselor_id) {
            $query->where('counselor_id', $counselor_id);
        }
        
        if (!empty($exclude_id)) {
            $query->not_where('id', $exclude_id);
        }
        
        $row = $query->limit(1)->get();
        
        // Slot is available only when no matching appointment exists
        return empty($row);
    }
    
    public function count_all() {
        return $this->db->table('appointments')->count();
    }
    
    public function count_student_appointments($student_id) {
        return $this->db->table('appointments')->where('student_id', $student_id)->count();
    }
    
    /**
     * Update appointment status for counselor
     * @param int $id Appointment ID
     * @param int $counselor_id Counselor ID
     * @param string $status New status
     * @return bool|int Number of affected rows or false on failure
     */
    public function update_status_for_counselor($id, $counselor_id, $status) {
        $result = $this->db->table($this->table)
                          ->where('id', $id)
                          ->where('counselor_id', $counselor_id)
                          ->update([
                              'status' => $status,
                              'updated_at' => date('Y-m-d H:i:s')
                          ]);
        
        // Return true if at least one row was affected, false otherwise
        return $result !== false && $result >= 0 ? $result : false;
    }
}