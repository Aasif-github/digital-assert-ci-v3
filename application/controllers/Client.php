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
        $data['projects'] = $this->Project_model->get_projects();
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
}