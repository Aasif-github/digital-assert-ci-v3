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

    public function get_project_media_by_type($file_type){
        $this->db->select('
            p.project_name,
            p.language,
            p.year_of_publish,
            m.title,
            m.description,
            m.file_type,
            m.file_extension,
            m.file_size,
            m.file_url,
            m.uploaded_by
        ');
        $this->db->from('projects p');
        $this->db->join('media_files m', 'p.id = m.project_id', 'inner');
        $this->db->where('m.file_type', $file_type); 
        $this->db->order_by('m.updated_at', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array(); // Returns objects instead of arrays
    }

    //for client
    public function search_projects_and_media($query, $project_id) {
        $this->db->select('
            p.id AS project_id,
            p.project_name,
            p.project_thumbnail,
            p.project_short_description,
            p.project_long_description,
            p.language,
            p.year_of_publish,
            m.id AS media_id,
            m.title AS media_title,
            m.description AS media_description,
            m.file_type,
            m.mime_type,
            m.file_extension,
            m.file_size,
            m.file_url
        ');
        $this->db->from('projects p');
        $this->db->join('media_files m', 'm.project_id = p.id', 'left');

        // Filter by specific project ID
        $this->db->where('p.id', $project_id);

        $this->db->group_start();
            // $this->db->like('p.project_name', $query);
            // $this->db->or_like('p.project_short_description', $query);
            // $this->db->or_like('p.project_long_description', $query);
            $this->db->or_like('m.title', $query);
            $this->db->or_like('m.description', $query);
            $this->db->or_like('m.file_type', $query);
            $this->db->or_like('m.mime_type', $query);
            $this->db->or_like('m.file_extension', $query);
        $this->db->group_end();        
        $this->db->order_by('p.id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    //for admin
    public function isAuthenticated($username, $password) {
        $query = $this->db->get_where('users', ['username' => $username, 'password' => $password]);
        return $query->num_rows() > 0;
    }   

    public function createUser($data) {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function get_user_by_id($id) {
        return $this->db->get_where('users', array('id' => $id))->row();
    }
    public function get_user_by_email($email) {
        return $this->db->get_where('users', array('email' => $email))->row();
    }

    public function get_user_by_username($username) {
        return $this->db->get_where('users', array('username' => $username))->row();
    }

    public function get_all_roles() {
        return $this->db->get('roles')->result();
    }

    public function update_user($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function delete_user($id) {
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }
}