<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Scholarship Model
 * Handles scholarship data and information
 */
class ScholarshipModel extends Model {

    public function __construct() {
        parent::__construct();
        $this->call->database();
    }

    /**
     * Get all scholarships
     */
    public function get_all_scholarships() {
        return $this->db->table('scholarships')
            ->order_by('name', 'ASC')
            ->get_all();
    }

    /**
     * Get scholarship by ID
     */
    public function get_scholarship($id) {
        return $this->db->table('scholarships')
            ->where('id', $id)
            ->get();
    }

    /**
     * Create a new scholarship
     */
    public function create_scholarship($data) {
        return $this->db->table('scholarships')->insert($data);
    }

    /**
     * Update a scholarship
     */
    public function update_scholarship($id, $data) {
        return $this->db->table('scholarships')
            ->where('id', $id)
            ->update($data);
    }

    /**
     * Delete a scholarship
     */
    public function delete_scholarship($id) {
        return $this->db->table('scholarships')
            ->where('id', $id)
            ->delete();
    }

    /**
     * Search scholarships by keyword
     */
    public function search_scholarships($keyword) {
        return $this->db->table('scholarships')
            ->like('name', $keyword)
            ->or_like('description', $keyword)
            ->or_like('provider', $keyword)
            ->order_by('name', 'ASC')
            ->get_all();
    }

    /**
     * Get scholarships by type
     */
    public function get_by_type($type) {
        return $this->db->table('scholarships')
            ->where('type', $type)
            ->order_by('name', 'ASC')
            ->get_all();
    }
}
?>