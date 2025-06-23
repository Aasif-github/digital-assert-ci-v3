<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Get all projects
    public function get_projects() {
        $this->db->select('p.*, u.username');
        $this->db->from('projects p');
        $this->db->join('users u', 'p.created_by = u.id', 'left');
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // Get a single project by ID
    public function get_project($project_id) {
        $this->db->select('p.*, u.username');
        $this->db->from('projects p');
        $this->db->join('users u', 'p.created_by = u.id', 'left');
        $this->db->where('p.id', $project_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Get media files for a project
    public function get_project_media($project_id) {
        $this->db->select('m.*, u.username');
        $this->db->from('media_files m');
        $this->db->join('users u', 'm.uploaded_by = u.id', 'left');
        $this->db->where('m.project_id', $project_id);
        $this->db->order_by('m.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    
}