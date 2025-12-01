<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Career Pathway Model
 * Handles career pathway data and information
 */
class CareerPathwayModel extends Model {

    public function __construct() {
        parent::__construct();
        $this->call->database();
    }

    /**
     * Get all career pathways
     */
    public function get_all_pathways() {
        return $this->db->table('career_pathways')
            ->order_by('name', 'ASC')
            ->get_all();
    }

    /**
     * Get pathway by ID
     */
    public function get_pathway($id) {
        return $this->db->table('career_pathways')
            ->where('id', $id)
            ->get();
    }

    /**
     * Create a new pathway
     */
    public function create_pathway($data) {
        return $this->db->table('career_pathways')->insert($data);
    }

    /**
     * Update a pathway
     */
    public function update_pathway($id, $data) {
        return $this->db->table('career_pathways')
            ->where('id', $id)
            ->update($data);
    }

    /**
     * Delete a pathway
     */
    public function delete_pathway($id) {
        return $this->db->table('career_pathways')
            ->where('id', $id)
            ->delete();
    }

    /**
     * Search pathways by keyword
     */
    public function search_pathways($keyword) {
        return $this->db->table('career_pathways')
            ->like('name', $keyword)
            ->or_like('description', $keyword)
            ->order_by('name', 'ASC')
            ->get_all();
    }
}
?>