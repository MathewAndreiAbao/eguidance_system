<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Career Assessment Model
 * Handles career assessment data and results
 */
class CareerAssessmentModel extends Model {

    public function __construct() {
        parent::__construct();
        $this->call->database();
    }

    /**
     * Get all career assessments
     */
    public function get_all_assessments() {
        return $this->db->table('career_assessments')
            ->order_by('created_at', 'DESC')
            ->get_all();
    }

    /**
     * Get assessment by ID
     */
    public function get_assessment($id) {
        return $this->db->table('career_assessments')
            ->where('id', $id)
            ->get();
    }

    /**
     * Create a new assessment
     */
    public function create_assessment($data) {
        return $this->db->table('career_assessments')->insert($data);
    }

    /**
     * Update an assessment
     */
    public function update_assessment($id, $data) {
        return $this->db->table('career_assessments')
            ->where('id', $id)
            ->update($data);
    }

    /**
     * Delete an assessment
     */
    public function delete_assessment($id) {
        return $this->db->table('career_assessments')
            ->where('id', $id)
            ->delete();
    }

    /**
     * Get assessment responses
     */
    public function get_responses($assessment_id) {
        return $this->db->table('career_assessment_responses')
            ->where('assessment_id', $assessment_id)
            ->get_all();
    }

    /**
     * Save assessment response
     */
    public function save_response($data) {
        return $this->db->table('career_assessment_responses')->insert($data);
    }
}
?>