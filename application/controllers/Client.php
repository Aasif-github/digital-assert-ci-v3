<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Project_model');
        $this->load->helper('url');
    }

    // Display all projects
    public function index() {
        // Fetch projects
        $this->db->select('p.id, p.project_name, p.project_thumbnail, p.project_short_description, p.year_of_publish, u.username');
        $this->db->from('projects p');
        $this->db->join('users u', 'u.id = p.created_by', 'left'); // optional if you want username
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();
        $projects = $query->result_array();
    
        // Fetch resource counts grouped by project and file_type
        $file_type_counts = $this->db->select('project_id, file_type, COUNT(*) as total')
            ->from('media_files')
            ->group_by(['project_id', 'file_type'])
            ->get()
            ->result_array();
    
        // Merge file type counts into each project
        foreach ($projects as &$project) {
            $project['file_types'] = [];
            foreach ($file_type_counts as $count) {
                if ($count['project_id'] == $project['id']) {
                    $project['file_types'][$count['file_type']] = $count['total'];
                }
            }
        }

        //Count total media files grouped by file_type (for all projects)
        $total_media_by_type = $this->db
        ->select('file_type, COUNT(*) as total')
        ->from('media_files')
        ->group_by('file_type')
        ->get()
        ->result_array();
            

        $data['total_media_by_type'] = $total_media_by_type;
        $data['projects'] = $projects;
        $data['title'] = 'Projects';
        
        $this->load->view('client/projects', $data);
    }
    

    // Display a single project with its media files
    public function project($project_id) {
        $data['project'] = $this->Project_model->get_project($project_id);
        if (empty($data['project'])) {
            show_404();
        }
        $data['media_files'] = $this->Project_model->get_project_media($project_id);
        $data['title'] = $data['project']['project_name'];
        $this->load->view('client/project_detail', $data);
    }

    public function media_files_by_type($resource_type) {
        
        if (empty($resource_type)) {
            show_error('Invalid resource type', 400);
            return;
        }

        $file_type = urldecode($resource_type);
        $data['media_files'] = $this->Project_model->get_project_media_by_type($file_type);
        $data['title'] = htmlspecialchars($file_type); // Sanitize title
        // var_dump($resource_type);
        $this->load->view('client/resource_type', $data);
    }
}