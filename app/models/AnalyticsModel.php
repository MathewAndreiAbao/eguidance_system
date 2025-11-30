<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AnalyticsModel extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    // Get total counts for counselor dashboard
    public function get_overview_stats($counselor_id) {
        $total_students = $this->db->raw("
            SELECT COUNT(DISTINCT student_id) as count 
            FROM appointments 
            WHERE counselor_id = ?
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        $total_appointments = $this->db->raw("
            SELECT COUNT(*) as count 
            FROM appointments 
            WHERE counselor_id = ?
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        $total_feedback = $this->db->raw("
            SELECT COUNT(*) as count 
            FROM student_feedback 
            WHERE counselor_id = ?
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        $total_wellness_forms = $this->db->raw("
            SELECT COUNT(*) as count 
            FROM wellness_forms 
            WHERE counselor_id = ?
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total_students' => $total_students[0]['count'] ?? 0,
            'total_appointments' => $total_appointments[0]['count'] ?? 0,
            'total_feedback' => $total_feedback[0]['count'] ?? 0,
            'total_wellness_forms' => $total_wellness_forms[0]['count'] ?? 0
        ];
    }

    // Get appointments by status
    public function get_appointments_by_status($counselor_id) {
        $results = $this->db->raw("
            SELECT status, COUNT(*) as count 
            FROM appointments 
            WHERE counselor_id = ? 
            GROUP BY status
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'pending' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];

        foreach ($results as $row) {
            $data[$row['status']] = (int)$row['count'];
        }

        return $data;
    }

    // Get appointments trend (last 6 months)
    public function get_appointments_trend($counselor_id, $months = 6) {
        $results = $this->db->raw("
            SELECT 
                DATE_FORMAT(date, '%Y-%m') as month,
                COUNT(*) as count
            FROM appointments
            WHERE counselor_id = ?
            AND date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(date, '%Y-%m')
            ORDER BY month ASC
        ", [$counselor_id, $months])->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    // Get feedback statistics
    public function get_feedback_stats($counselor_id) {
        // Average rating
        $avg_rating = $this->db->raw("
            SELECT AVG(rating) as avg_rating 
            FROM student_feedback 
            WHERE counselor_id = ? AND rating IS NOT NULL
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        // Feedback by status
        $by_status = $this->db->raw("
            SELECT status, COUNT(*) as count 
            FROM student_feedback 
            WHERE counselor_id = ? 
            GROUP BY status
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        // Rating distribution
        $rating_dist = $this->db->raw("
            SELECT rating, COUNT(*) as count 
            FROM student_feedback 
            WHERE counselor_id = ? AND rating IS NOT NULL 
            GROUP BY rating 
            ORDER BY rating ASC
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        $status_data = [
            'new' => 0,
            'in_review' => 0,
            'resolved' => 0
        ];

        foreach ($by_status as $row) {
            $status_data[$row['status']] = (int)$row['count'];
        }

        $rating_data = [];
        for ($i = 1; $i <= 5; $i++) {
            $rating_data[$i] = 0;
        }

        foreach ($rating_dist as $row) {
            if ($row['rating'] !== null) {
                $rating_data[(int)$row['rating']] = (int)$row['count'];
            }
        }

        return [
            'average_rating' => round($avg_rating[0]['avg_rating'] ?? 0, 2),
            'by_status' => $status_data,
            'rating_distribution' => $rating_data
        ];
    }

    // Get wellness form response statistics
    public function get_wellness_stats($counselor_id) {
        $forms = $this->db->raw("
            SELECT 
                wf.id,
                wf.title,
                COUNT(wfr.id) as response_count
            FROM wellness_forms wf
            LEFT JOIN wellness_form_responses wfr ON wf.id = wfr.wellness_form_id
            WHERE wf.counselor_id = ?
            GROUP BY wf.id, wf.title
            ORDER BY response_count DESC
            LIMIT 10
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        return $forms;
    }

    // Get most active students (by appointments)
    public function get_top_active_students($counselor_id, $limit = 5) {
        $results = $this->db->raw("
            SELECT 
                u.id,
                u.username,
                COUNT(a.id) as appointment_count
            FROM users u
            INNER JOIN appointments a ON u.id = a.student_id
            WHERE a.counselor_id = ?
            GROUP BY u.id, u.username
            ORDER BY appointment_count DESC
            LIMIT ?
        ", [$counselor_id, $limit])->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    // Get appointment time distribution (by hour)
    public function get_appointment_time_distribution($counselor_id) {
        $results = $this->db->raw("
            SELECT 
                HOUR(time) as hour,
                COUNT(*) as count
            FROM appointments
            WHERE counselor_id = ?
            GROUP BY HOUR(time)
            ORDER BY hour ASC
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    // Export data for external analytics (API-ready format)
    public function get_export_data($counselor_id, $start_date = null, $end_date = null) {
        $date_filter = '';
        $params = [$counselor_id];

        if ($start_date && $end_date) {
            $date_filter = " AND date BETWEEN ? AND ?";
            $params[] = $start_date;
            $params[] = $end_date;
        }

        $appointments = $this->db->raw("
            SELECT 
                a.*,
                u.username as student_username
            FROM appointments a
            LEFT JOIN users u ON a.student_id = u.id
            WHERE a.counselor_id = ?" . $date_filter . "
            ORDER BY a.date DESC, a.time DESC
        ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $feedback = $this->db->raw("
            SELECT 
                f.*,
                u.username as student_username
            FROM student_feedback f
            LEFT JOIN users u ON f.student_id = u.id
            WHERE f.counselor_id = ?
            ORDER BY f.created_at DESC
        ", [$counselor_id])->fetchAll(PDO::FETCH_ASSOC);

        return [
            'appointments' => $appointments,
            'feedback' => $feedback,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
}
