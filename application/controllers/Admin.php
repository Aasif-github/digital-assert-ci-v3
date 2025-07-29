<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Project_model');
        $this->load->helper(['url', 'form', 'file']);
        $this->load->library(['form_validation', 'session']);
        $this->load->database();
         
         // Get current method
         $current_method = $this->router->fetch_method();
         $current_class  = $this->router->fetch_class();
 
         // Bypass login and auth methods
         $allowed_methods = ['login', 'authenticateUser', 'logout'];
 
         if (!in_array($current_method, $allowed_methods) && !$this->session->userdata('logged_in')) {
             redirect('login');
         }
    }

    public function index() {
        // Fetch projects
        $this->db->select('p.id, p.project_name, p.project_thumbnail, p.project_short_description, p.year_of_publish');
        $this->db->from('projects p');
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();
        $projects = $query->result_array();

        // Fetch file type counts
        $file_type_counts = $this->db->select('project_id, file_type, COUNT(*) as total')
            ->from('media_files')
            ->group_by(['project_id', 'file_type'])
            ->get()
            ->result_array();

        // Transform projects to include file type counts
        foreach ($projects as &$project) {
            $project['file_types'] = [];
            foreach ($file_type_counts as $count) {
                if ($count['project_id'] == $project['id']) {
                    $project['file_types'][$count['file_type']] = $count['total'];
                }
            }
        }

        $data['projects'] = $projects;
        $data['total_projects'] = $this->db->count_all('projects');
        $data['title'] = 'Admin Dashboard';
        $data['total_users'] = $this->db->count_all('users');

        if($this->session->userdata('role_id') == 1) {            
            $this->load->view('admin/header', $data);
            $this->load->view('admin/dashboard', $data);        
        }else{
            redirect('admin/login');
        }

        
    }


    public function login() {                
        $this->load->view('admin/login');
    }

    public function register() {
        $this->load->view('admin/register');
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

    public function authenticateUser() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        // print_r($username);
        // print_r('password'.$password);
        // die();

        // $isAuthenticated = $this->project_model->isAuthenticated($username, $password);

        $user = $this->db->get_where('users', ['username' => $username, 'password' => $password])->row();
        
        if ($user) {
            // $this->session->set_userdata('user_id', $user->id);
            $this->session->set_userdata([
                'user_id' => $user->id,
                'username' => $user->username,
                'role_id' => $user->role_id,
                'logged_in' => true
            ]);
            
            if($user->is_active == 0) {
                $this->session->set_flashdata('error', 'Your account is inactive. Please contact the admin.');
                redirect('admin/login');
            }

            if($user->role_id == 1) {
                redirect('admin');
            }else {
                redirect('client');
            }            
        }        

        $this->session->set_flashdata('error', 'Invalid username or password.');
        redirect('admin/login');
    }

    public function registerUser() {
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $this->db->insert('users', ['username' => $username, 'email' => $email, 'password' => $password]);

        $this->session->set_flashdata('success', 'User registered successfully.');  
                
        redirect('admin/login');
    }       

    public function addUser() {
        
        $data['roles'] = $this->db->get('roles')->result();
        
        if($this->session->userdata('role_id') == 1) {            
            $this->load->view('admin/header');
            $this->load->view('admin/add_user', $data);
        }else{
            redirect('admin/login');
        }
        
    }

    public function viewUser() {

        // $data['users'] = $this->db->get('users')->result_array();
        $this->db->select('users.id, users.name, users.username, users.email, users.is_active, roles.role_name as role');
        $this->db->from('users');
        $this->db->join('roles', 'users.role_id = roles.id', 'inner');
        $this->db->where('users.role_id !=', 1);
        $data['users'] = $this->db->get()->result_array();

        $this->load->view('admin/header');
        $this->load->view('admin/view_users', $data);
    }   

    public function createUser() {
        
        $userData = [
            'name' => $this->input->post('name'),
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'password' => $this->input->post('password'),
            'role_id' => $this->input->post('role_id')    
        ];
        
        if(empty($userData['name']) || empty($userData['username']) || empty($userData['email']) || empty($userData['password']) || empty($userData['role_id'])) {
            $this->session->set_flashdata('error', 'All fields are required.');
            redirect('admin/addUser');
        }

        // if($this->Project_model->isUserExists($userData['username'])) {
        //     $this->session->set_flashdata('error', 'User already exists.');
        //     redirect('admin/addUser');
        // }

        // if($this->Project_model->isEmailExists($userData['email'])) {
        //     $this->session->set_flashdata('error', 'Email already exists.');
        //     redirect('admin/addUser');
        // }

        if($userData['password'] != $this->input->post('confirm_password')) {
            $this->session->set_flashdata('error', 'Passwords do not match.');
            redirect('admin/addUser');
        }

        // var_dump($userData);
        // die();
        
        $this->Project_model->createUser($userData);
        $this->session->set_flashdata('success', 'User created successfully.');  
    
        redirect('admin/viewUser');
    }

    public function editUser($user_id) {
        $data['user'] = $this->db->where('id', $user_id)->get('users')->row();
        $data['roles'] = $this->db->get('roles')->result();
        $this->load->view('admin/header');
        $this->load->view('admin/edit_user', $data);        
    }
    
    public function updateUser($id) {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email_exists[' . $id . ']');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|callback_check_username_exists[' . $id . ']');
        $this->form_validation->set_rules('role_id', 'Role', 'required');
        
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'matches[password]');
        }

        if ($this->form_validation->run() == FALSE) {
            $data['user'] = $this->User_model->get_user_by_id($id);
            $data['roles'] = $this->Role_model->get_all_roles();
            $this->load->view('edit_user', $data);
        } else {
            $user_data = array(
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'username' => $this->input->post('username'),
                'role_id' => $this->input->post('role_id')
            );
            // var_dump($user_data);
            // die();

            if ($this->input->post('password')) {
                $user_data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            if ($this->Project_model->update_user($id, $user_data)) {
                $this->session->set_flashdata('message', 'User updated successfully');
                redirect('view-user');
            } else {
                $this->session->set_flashdata('message', 'Error updating user');
                redirect('users/edit/' . $id);
            }
        }
    }

    public function check_email_exists($email, $id) {
        $user = $this->Project_model->get_user_by_email($email);
        if ($user && $user->id != $id) {
            $this->form_validation->set_message('check_email_exists', 'This email is already in use.');
            return FALSE;
        }
        return TRUE;
    }

    public function check_username_exists($username, $id) {
        $user = $this->Project_model->get_user_by_username($username);
        if ($user && $user->id != $id) {
            $this->form_validation->set_message('check_username_exists', 'This username is already in use.');
            return FALSE;
        }
        return TRUE;
    }

    public function destroyUser($user_id) {
        $user = $this->Project_model->get_user_by_id($user_id);
        if (!$user) {
            $this->session->set_flashdata('message', 'User not found');
            redirect('view-user');
        }

        if ($user->role_id == 1) {
            $this->session->set_flashdata('message', 'Cannot delete admin user');
            redirect('view-user');
        }

        if ($this->Project_model->delete_user($user_id)) {
            $this->session->set_flashdata('message', 'User deleted successfully');
        } else {
            $this->session->set_flashdata('message', 'Error deleting user');
        }
        redirect('view-user');
    }
    
    public function show() {
        $data['title'] = 'Add Project';
        $this->load->view('admin/header', $data);
        $this->load->view('admin/add_project', $data);        
    }

    public function download($media_id) {
        $user_id = $this->session->userdata('user_id') ?? 1;
        if (!$user_id) {
            $this->session->set_flashdata('error', 'Authentication required to download files.');
            redirect('admin');
        }

        $media = $this->db->where('id', $media_id)->get('media_files')->row_array();
        if (!$media) {
            $this->session->set_flashdata('error', 'File not found.');
            redirect('admin');
        }

        $file_path = FCPATH . 'public/' . $media['file_url'];
        if (!file_exists($file_path)) {
            log_message('error', 'Download - File not found: ' . $file_path);
            $this->session->set_flashdata('error', 'File not available on server.');
            redirect('admin');
        }

        $file_name = basename($media['file_url']);
        $mime_type = $media['mime_type'] ?: get_mime_by_extension($file_name);
        if (!$mime_type) {
            $mime_type = 'application/octet-stream';
        }

        log_message('debug', 'Download - File: ' . $file_name . ', MIME: ' . $mime_type . ', Size: ' . filesize($file_path));

        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($file_path);
        exit;
    }

    public function store() {
        // Placeholder user ID
        $user_id = $this->session->userdata('user_id') ?? 1;
        if (!$user_id) {
            $this->session->set_flashdata('error', 'Authentication required to create project.');
            redirect('admin');
        }
    
        // Set validation rules
        $this->form_validation->set_rules('project_name', 'Project Name', 'required|max_length[100]');
        $this->form_validation->set_rules('project_short_description', 'Short Description', 'max_length[255]');
        $this->form_validation->set_rules('language', 'Language', 'max_length[50]');
        $this->form_validation->set_rules('year_of_publish', 'Year of Publish', 'callback_valid_date');
        $this->form_validation->set_rules('project_thumbnail', 'Project Thumbnail', 'callback_validate_thumbnail');
        $this->form_validation->set_rules('new_media_thumbnails[]', 'Media Thumbnails', 'callback_validate_thumbnail');
        
        $this->form_validation->set_rules('new_media_files[]', 'Media Files', 'callback_validate_media_files');
        // $this->form_validation->set_rules('new_media_titles[]', 'Media Titles', 'callback_validate_media_titles');    
        // $this->form_validation->set_rules('new_media_descriptions[]', 'Media descriptions', 'callback_validate_media_files'); 
    
        // log_message('debug', 'Store - Media Titles: ' . print_r($this->input->post('media_titles'), true));
        // log_message('debug', 'Store - Media Files: ' . print_r($_FILES['media_files'] ?? [], true));
    
        // Validate form
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            $this->show();
            return;
        }
    
        // Start transaction
        $this->db->trans_start();
    
        // Handle thumbnail upload
        $thumbnail_path = null;
        if (!empty($_FILES['project_thumbnail']['name'])) {
            $config['upload_path'] = FCPATH . 'public/storage/thumbnails/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB
            $config['file_ext_tolower'] = TRUE;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('project_thumbnail')) {
                $thumbnail_data = $this->upload->data();
                $thumbnail_path = 'storage/thumbnails/' . $thumbnail_data['file_name'];
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                $this->db->trans_rollback();
                $this->show();
                return;
            }
        }
    
        // Create project
        $project_data = [
            'project_name' => $this->input->post('project_name'),
            'project_thumbnail' => $thumbnail_path,
            'project_long_description' => $this->input->post('project_long_description'),
            'project_short_description' => $this->input->post('project_short_description'),
            'language' => $this->input->post('language'),
            'year_of_publish' => $this->input->post('year_of_publish'),
            'created_by' => $user_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('projects', $project_data);
        $project_id = $this->db->insert_id();

        // Handle media files
        if (!empty($_FILES['new_media_files']['name'][0])) {
            $media_thumbnails = $_FILES['new_media_thumbnails'];
            $files = $_FILES['new_media_files'];
            $media_titles = $this->input->post('new_media_titles');
            $media_descriptions = $this->input->post('new_media_descriptions');

            // Configuration for media file uploads
            $config['upload_path'] = FCPATH . 'public/storage/media/';
            $config['allowed_types'] = 'jpg|jpeg|png|mp4|mp3|3gp|pdf|doc|docx|txt|rtf|odt|xls|xlsx|csv|ppt|pptx|apk|zip';
            $config['mimes'] = [
                'apk' => [
                    'application/vnd.android.package-archive',
                    'application/octet-stream',
                    'application/zip',
                    'application/x-zip-compressed',
                    'application/x-apk'
                ]
            ];
            $config['max_size'] = 512000; // 500MB
            $config['file_ext_tolower'] = TRUE;

            // Configuration for thumbnail uploads
            $thumb_config['upload_path'] = FCPATH . 'public/storage/thumbnails/';
            $thumb_config['allowed_types'] = 'jpg|jpeg|png';
            $thumb_config['max_size'] = 2048; // 2MB
            $thumb_config['file_ext_tolower'] = TRUE;

            for ($i = 0; $i < count($files['name']); $i++) {
                if (!empty($files['name'][$i]) && !empty($media_titles[$i])) {
                    // Handle media file upload
                    $_FILES['file']['name'] = $files['name'][$i];
                    $_FILES['file']['type'] = $files['type'][$i];
                    $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                    $_FILES['file']['error'] = $files['error'][$i];
                    $_FILES['file']['size'] = $files['size'][$i];

                    $this->upload->initialize($config, TRUE);
                    if (!$this->upload->do_upload('file')) {
                        log_message('error', 'Media upload failed for ' . $files['name'][$i] . ': ' . $this->upload->display_errors());
                        $this->session->set_flashdata('error', 'Upload failed for ' . $files['name'][$i] . ': ' . $this->upload->display_errors());
                        $this->db->trans_rollback();
                        $this->show();
                        return;
                    }

                    $file_data = $this->upload->data();
                    $mime_type = $file_data['file_type'];
                    $file_type = $this->_get_file_type($mime_type);

                    // Handle media thumbnail upload
                    $thumbnail_path = null;
                    if (!empty($media_thumbnails['name'][$i])) {
                        $_FILES['thumb']['name'] = $media_thumbnails['name'][$i];
                        $_FILES['thumb']['type'] = $media_thumbnails['type'][$i];
                        $_FILES['thumb']['tmp_name'] = $media_thumbnails['tmp_name'][$i];
                        $_FILES['thumb']['error'] = $media_thumbnails['error'][$i];
                        $_FILES['thumb']['size'] = $media_thumbnails['size'][$i];

                        $this->upload->initialize($thumb_config, TRUE);
                        if ($this->upload->do_upload('thumb')) {
                            $thumb_data = $this->upload->data();
                            $thumbnail_path = 'storage/thumbnails/' . $thumb_data['file_name'];
                        } else {
                            log_message('error', 'Thumbnail upload failed for ' . $media_thumbnails['name'][$i] . ': ' . $this->upload->display_errors());
                            $this->session->set_flashdata('error', 'Thumbnail upload failed for ' . $media_thumbnails['name'][$i] . ': ' . $this->upload->display_errors());
                            $this->db->trans_rollback();
                            $this->show();
                            return;
                        }
                    }

                    // Insert media file data into database
                    $media_data = [
                        'project_id' => $project_id,
                        'media_thumbnail' => $thumbnail_path, // Store the path, not the array
                        'title' => $media_titles[$i],
                        'description' => $media_descriptions[$i] ?? null,
                        'file_type' => $file_type,
                        'mime_type' => $mime_type,
                        'file_extension' => ltrim($file_data['file_ext'], '.'),
                        'file_size' => $file_data['file_size'],
                        'file_url' => 'storage/media/' . $file_data['file_name'],
                        'uploaded_by' => $user_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('media_files', $media_data);
                }
            }
        }
    
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Failed to create project.');
            $this->show();
            return;
        }
    
        $this->session->set_flashdata('success', 'Project created successfully.');
        redirect('admin');
    }
    
    public function validate_media_files() {
        if (empty($_FILES['new_media_files']['name'][0])) {
            return TRUE;
        }

        $config['upload_path'] = FCPATH . 'public/storage/media/';
        $config['allowed_types'] = 'jpg|jpeg|png|mp4|mp3|3gp|pdf|doc|docx|txt|rtf|odt|xls|xlsx|csv|ppt|pptx|apk|zip';
        $config['file_ext_to_mimetypes'] = ['apk' => 'application/vnd.android.package-archive'];
        $config['max_size'] = 512000; // 500MB
        $config['file_ext_tolower'] = TRUE;
        // $config['check_mime'] = FALSE; // Add to upload config in update() and validate_media_files()
        
        $this->load->library('upload', $config);
        $files = $_FILES['new_media_files'];
        $media_titles = $this->input->post('new_media_titles');
    
        for ($i = 0; $i < count($files['name']); $i++) {
            if (!empty($files['name'][$i])) {
                if (empty($media_titles[$i])) {
                    $this->form_validation->set_message('validate_media_files', 'Media title is required for file ' . $files['name'][$i]);
                    return FALSE;
                }
                $_FILES['file']['name'] = $files['name'][$i];
                $_FILES['file']['type'] = $files['type'][$i];
                $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['file']['error'] = $files['error'][$i];
                $_FILES['file']['size'] = $files['size'][$i];
    
                // Debug MIME type
                log_message('debug', 'Validation - File: ' . $files['name'][$i] . ', MIME Type: ' . $files['type'][$i]);
    
                $this->upload->initialize($config, TRUE);
                if (!$this->upload->do_upload('file')) {
                    $this->form_validation->set_message('validate_media_files', 'Upload failed for ' . $files['name'][$i] . ': ' . $this->upload->display_errors());
                    return FALSE;
                }
            }
        }
        return TRUE;
    }


    public function edit($project_id) {
        $data['project'] = $this->Project_model->get_project($project_id);
        $data['project']['mediaFiles'] = $this->Project_model->get_project_media($project_id);
        if (empty($data['project'])) {
            show_404();
        }
        $data['title'] = 'Edit Project';
        $this->load->view('admin/header', $data);
        $this->load->view('admin/edit_project', $data);
        
    }

    public function __update($project_id) {
        $user_id = $this->session->userdata('user_id') ?? 1;
        if (!$user_id) {
            $this->session->set_flashdata('error', 'Authentication required to update project.');
            redirect('admin');
        }
    
        // Set validation rules
        $this->form_validation->set_rules('project_name', 'Project Name', 'required|max_length[100]');
        $this->form_validation->set_rules('project_short_description', 'Short Description', 'max_length[255]');
        $this->form_validation->set_rules('language', 'Language', 'max_length[50]');
        $this->form_validation->set_rules('year_of_publish', 'Year of Publish', 'callback_valid_date');
        $this->form_validation->set_rules('project_thumbnail', 'Project Thumbnail', 'callback_validate_thumbnail');        
        $this->form_validation->set_rules('media_thumbnails[]', 'Media Thumbnails', 'callback_validate_thumbnails','');        
        $this->form_validation->set_rules('new_media_files[]', 'Media Files', 'callback_validate_media_files');
    
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            $this->edit($project_id);
            return;
        }
    
        $this->db->trans_begin();
    
        // Update project
        $project_data = [
            'project_name' => $this->input->post('project_name', TRUE),
            'project_long_description' => $this->input->post('project_long_description', TRUE),
            'project_short_description' => $this->input->post('project_short_description', TRUE),
            'language' => $this->input->post('language', TRUE),
            'year_of_publish' => $this->input->post('year_of_publish', TRUE),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    
        // Handle thumbnail upload
        if (!empty($_FILES['project_thumbnail']['name'])) {
            $config['upload_path'] = FCPATH . 'public/storage/thumbnails/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB
            $config['file_ext_tolower'] = TRUE;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('project_thumbnail')) {
                $thumbnail_data = $this->upload->data();
                $project_data['project_thumbnail'] = 'storage/thumbnails/' . $thumbnail_data['file_name'];
                $project = $this->Project_model->get_project($project_id);
                if ($project['project_thumbnail'] && file_exists(FCPATH . 'public/' . $project['project_thumbnail'])) {
                    unlink(FCPATH . 'public/' . $project['project_thumbnail']);
                }
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                $this->db->trans_rollback();
                $this->edit($project_id);
                return;
            }
        }
    
        $this->db->where('id', $project_id)->update('projects', $project_data);
    
        // Handle deleted media files
        if ($this->input->post('deleted_media_ids')) {
            $deleted_media_ids = array_filter(explode(',', $this->input->post('deleted_media_ids', TRUE)), 'is_numeric');
            $media_files = $this->db->where_in('id', $deleted_media_ids)
                ->where('project_id', $project_id)
                ->get('media_files')
                ->result_array();
            foreach ($media_files as $media) {
                if ($media['file_url'] && file_exists(FCPATH . 'public/' . $media['file_url'])) {
                    unlink(FCPATH . 'public/' . $media['file_url']);
                }
                $this->db->where('id', $media['id'])->delete('media_files');
            }
        }
    
        // Handle existing media files
        if ($this->input->post('existing_media_ids')) {
            $media_ids = $this->input->post('existing_media_ids', TRUE);
            $media_thumbnail_ids = $this->input->post('existing_media_thumbnail', TRUE);
            $media_titles = $this->input->post('existing_media_titles', TRUE);
            $media_descriptions = $this->input->post('existing_media_descriptions', TRUE);
            $files = $_FILES['existing_media_files'] ?? [];
            
            // $config['detect_mime'] = FALSE;
            // $config['check_mime'] = FALSE; // Add to upload config in update() and validate_media_files()
            
            $config['upload_path'] = FCPATH . 'public/storage/media/';
            $config['allowed_types'] = 'jpg|jpeg|png|mp4|mp3|3gp|pdf|doc|docx|txt|rtf|odt|xls|xlsx|csv|ppt|pptx|apk|zip';
           
            $config['max_size'] = 512000; // 500MB
            $config['file_ext_tolower'] = TRUE;
            
            $config['mimes'] = [
                'apk' => [
                    'application/vnd.android.package-archive',
                    'application/octet-stream',
                    'application/zip',
                    'application/x-zip-compressed',
                    'application/x-apk'
                ]
            ];
            $this->load->library('upload');
    
            foreach ($media_ids as $index => $media_id) {
                $media_data = [
                    'title' => $media_titles[$index] ?? null,
                    'description' => $media_descriptions[$index] ?? null,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
    
                if (!empty($files['name'][$index])) {
                    $_FILES['file']['name'] = $files['name'][$index];
                    $_FILES['file']['type'] = $files['type'][$index];
                    $_FILES['file']['tmp_name'] = $files['tmp_name'][$index];
                    $_FILES['file']['error'] = $files['error'][$index];
                    $_FILES['file']['size'] = $files['size'][$index];
    
                    // Debug MIME type
                    log_message('debug', 'Existing File: ' . $files['name'][$index] . ', MIME Type: ' . $files['type'][$index] . ', Size: ' . $files['size'][$index]);
    
                    $this->upload->initialize($config, TRUE);
                    if ($this->upload->do_upload('file')) {
                        $file_data = $this->upload->data();
                        $mime_type = $file_data['file_type'];
                        $file_type = $this->_get_file_type($mime_type);
                        $media_data = array_merge($media_data, [
                            'file_type' => $file_type,
                            'mime_type' => $mime_type,
                            'file_extension' => ltrim($file_data['file_ext'], '.'),
                            'file_size' => $file_data['file_size'],
                            'file_url' => 'storage/media/' . $file_data['file_name']
                        ]);
    
                        $existing_media = $this->db->where('id', $media_id)
                            ->where('project_id', $project_id)
                            ->get('media_files')
                            ->row_array();
                        if ($existing_media['file_url'] && file_exists(FCPATH . 'public/' . $existing_media['file_url'])) {
                            unlink(FCPATH . 'public/' . $existing_media['file_url']);
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Media upload failed for ' . $files['name'][$index] . ': ' . $this->upload->display_errors());
                        log_message('error', 'Media upload failed for ' . $files['name'][$index] . ': ' . $this->upload->display_errors());
                        continue; // Skip to next file
                    }
                }
                $this->db->where('id', $media_id)->where('project_id', $project_id)->update('media_files', $media_data);
            }
        }
    
        // Handle new media files
        if (!empty($_FILES['new_media_files']['name'][0])) {
            $files = $_FILES['new_media_files'];
            $media_titles = $this->input->post('new_media_titles', TRUE);
            $media_descriptions = $this->input->post('new_media_descriptions', TRUE);
            // $config['detect_mime'] = FALSE;

            $config['upload_path'] = FCPATH . 'public/storage/media/';
            $config['allowed_types'] = 'jpg|jpeg|png|mp4|mp3|3gp|pdf|doc|docx|txt|rtf|odt|xls|xlsx|csv|ppt|pptx|apk|zip';
                   
            $config['mimes'] = [
                'apk' => [
                    'application/vnd.android.package-archive',
                    'application/octet-stream',
                    'application/zip',
                    'application/x-zip-compressed',
                    'application/x-apk'
                ]
            ];

            $config['max_size'] = 512000; // 500MB
            $config['file_ext_tolower'] = TRUE;
            $this->load->library('upload');
    
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['name'][$i] && !empty($media_titles[$i])) {
                    $_FILES['file']['name'] = $files['name'][$i];
                    $_FILES['file']['type'] = $files['type'][$i];
                    $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                    $_FILES['file']['error'] = $files['error'][$i];
                    $_FILES['file']['size'] = $files['size'][$i];
    
                    // Debug MIME type
                    log_message('debug', 'New File: ' . $files['name'][$i] . ', MIME Type: ' . $files['type'][$i] . ', Size: ' . $files['size'][$i]);
    
                    $this->upload->initialize($config, TRUE);
                    if ($this->upload->do_upload('file')) {
                        $file_data = $this->upload->data();
                        $mime_type = $file_data['file_type'];
                        $file_type = $this->_get_file_type($mime_type);
                        $media_data = [
                            'project_id' => $project_id,
                            'title' => $media_titles[$i],
                            'description' => $media_descriptions[$i] ?? null,
                            'file_type' => $file_type,
                            'mime_type' => $mime_type,
                            'file_extension' => ltrim($file_data['file_ext'], '.'),
                            'file_size' => $file_data['file_size'],
                            'file_url' => 'storage/media/' . $file_data['file_name'],
                            'uploaded_by' => $user_id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        $this->db->insert('media_files', $media_data);
                    } else {
                        $this->session->set_flashdata('error', 'Media upload failed for ' . $files['name'][$i] . ': ' . $this->upload->display_errors());
                        log_message('error', 'Media upload failed for ' . $files['name'][$i] . ': ' . $this->upload->display_errors());
                        continue; // Skip to next file
                    }
                }
            }
        }
    
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Failed to update project.');
            $this->edit($project_id);
            return;
        }
        $this->db->trans_commit();
    
        $this->session->set_flashdata('success', 'Project updated successfully.');
        redirect('admin');
    }

    public function update($project_id) {
        $user_id = $this->session->userdata('user_id') ?? 1;
        if (!$user_id) {
            $this->session->set_flashdata('error', 'Authentication required to update project.');
            redirect('admin');
        }
    
        // Set validation rules
        $this->form_validation->set_rules('project_name', 'Project Name', 'required|max_length[100]');
        $this->form_validation->set_rules('project_short_description', 'Short Description', 'max_length[255]');
        $this->form_validation->set_rules('language', 'Language', 'max_length[50]');
        $this->form_validation->set_rules('year_of_publish', 'Year of Publish', 'callback_valid_date');
        $this->form_validation->set_rules('project_thumbnail', 'Project Thumbnail', 'callback_validate_thumbnail');
        $this->form_validation->set_rules('new_media_files[]', 'Media Files', 'callback_validate_media_files');
    
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            $this->edit($project_id);
            return;
        }
    
        $this->db->trans_begin();
    
        // Update project
        $project_data = [
            'project_name' => $this->input->post('project_name', TRUE),
            'project_long_description' => $this->input->post('project_long_description', TRUE),
            'project_short_description' => $this->input->post('project_short_description', TRUE),
            'language' => $this->input->post('language', TRUE),
            'year_of_publish' => $this->input->post('year_of_publish', TRUE),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    
        // Handle thumbnail upload
        if (!empty($_FILES['project_thumbnail']['name'])) {
            $config['upload_path'] = FCPATH . 'public/storage/thumbnails/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048; // 2MB
            $config['file_ext_tolower'] = TRUE;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('project_thumbnail')) {
                $thumbnail_data = $this->upload->data();
                $project_data['project_thumbnail'] = 'storage/thumbnails/' . $thumbnail_data['file_name'];
                $project = $this->Project_model->get_project($project_id);
                if (!empty($project['project_thumbnail']) && file_exists(FCPATH . 'public/' . $project['project_thumbnail'])) {
                    unlink(FCPATH . 'public/' . $project['project_thumbnail']);
                }
            } else {
                $this->session->set_flashdata('error', 'Thumbnail upload failed: ' . $this->upload->display_errors());
                $this->db->trans_rollback();
                $this->edit($project_id);
                return;
            }
        }
    
        $this->db->where('id', $project_id)->update('projects', $project_data);
    
        // Handle deleted media files
        if ($this->input->post('deleted_media_ids')) {
            $deleted_media_ids = array_filter(explode(',', $this->input->post('deleted_media_ids', TRUE)), 'is_numeric');
            $media_files = $this->db->where_in('id', $deleted_media_ids)
                ->where('project_id', $project_id)
                ->get('media_files')
                ->result_array();
            foreach ($media_files as $media) {
                if (!empty($media['file_url']) && file_exists(FCPATH . 'public/' . $media['file_url'])) {
                    unlink(FCPATH . 'public/' . $media['file_url']);
                }
                if (!empty($media['media_thumbnail']) && file_exists(FCPATH . 'public/' . $media['media_thumbnail'])) {
                    unlink(FCPATH . 'public/' . $media['media_thumbnail']);
                }
                $this->db->where('id', $media['id'])->delete('media_files');
            }
        }
    
        // Handle existing media files
        if ($this->input->post('existing_media_ids')) {
            $media_ids = $this->input->post('existing_media_ids', TRUE);
            $media_titles = $this->input->post('existing_media_titles', TRUE);
            $media_descriptions = $this->input->post('existing_media_descriptions', TRUE);
            $files = $_FILES['existing_media_files'] ?? [];
            $thumbnails = $_FILES['existing_media_thumbnail'] ?? [];
            
            $config['upload_path'] = FCPATH . 'public/storage/media/';
            $config['allowed_types'] = 'jpg|jpeg|png|mp4|mp3|3gp|pdf|doc|docx|txt|rtf|odt|xls|xlsx|csv|ppt|pptx|apk|zip';
            $config['max_size'] = 512000; // 500MB
            $config['file_ext_tolower'] = TRUE;
            $config['mimes'] = [
                'apk' => [
                    'application/vnd.android.package-archive',
                    'application/octet-stream',
                    'application/zip',
                    'application/x-zip-compressed',
                    'application/x-apk'
                ]
            ];
            $this->load->library('upload');
    
            $thumbnail_config['upload_path'] = FCPATH . 'public/storage/thumbnails/';
            $thumbnail_config['allowed_types'] = 'jpg|jpeg|png|gif';
            $thumbnail_config['max_size'] = 2048; // 2MB
            $thumbnail_config['file_ext_tolower'] = TRUE;
    
            foreach ($media_ids as $index => $media_id) {
                $media_data = [
                    'title' => $media_titles[$index] ?? null,
                    'description' => $media_descriptions[$index] ?? null,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
    
                // Handle media file upload
                if (!empty($files['name'][$index])) {
                    $_FILES['file']['name'] = $files['name'][$index];
                    $_FILES['file']['type'] = $files['type'][$index];
                    $_FILES['file']['tmp_name'] = $files['tmp_name'][$index];
                    $_FILES['file']['error'] = $files['error'][$index];
                    $_FILES['file']['size'] = $files['size'][$index];
    
                    log_message('debug', 'Existing File: ' . $files['name'][$index] . ', MIME Type: ' . $files['type'][$index] . ', Size: ' . $files['size'][$index]);
    
                    $this->upload->initialize($config, TRUE);
                    if ($this->upload->do_upload('file')) {
                        $file_data = $this->upload->data();
                        $mime_type = $file_data['file_type'];
                        $file_type = $this->_get_file_type($mime_type);
                        $media_data = array_merge($media_data, [
                            'file_type' => $file_type,
                            'mime_type' => $mime_type,
                            'file_extension' => ltrim($file_data['file_ext'], '.'),
                            'file_size' => $file_data['file_size'],
                            'file_url' => 'storage/media/' . $file_data['file_name']
                        ]);
    
                        $existing_media = $this->db->where('id', $media_id)
                            ->where('project_id', $project_id)
                            ->get('media_files')
                            ->row_array();
                        if (!empty($existing_media['file_url']) && file_exists(FCPATH . 'public/' . $existing_media['file_url'])) {
                            unlink(FCPATH . 'public/' . $existing_media['file_url']);
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Media upload failed for ' . $files['name'][$index] . ': ' . $this->upload->display_errors());
                        log_message('error', 'Media upload failed for ' . $files['name'][$index] . ': ' . $this->upload->display_errors());
                        continue;
                    }
                }
    
                // Handle media thumbnail upload
                if (!empty($thumbnails['name'][$index])) {
                    $_FILES['thumbnail']['name'] = $thumbnails['name'][$index];
                    $_FILES['thumbnail']['type'] = $thumbnails['type'][$index];
                    $_FILES['thumbnail']['tmp_name'] = $thumbnails['tmp_name'][$index];
                    $_FILES['thumbnail']['error'] = $thumbnails['error'][$index];
                    $_FILES['thumbnail']['size'] = $thumbnails['size'][$index];
    
                    log_message('debug', 'Thumbnail File: ' . $thumbnails['name'][$index] . ', MIME Type: ' . $thumbnails['type'][$index] . ', Size: ' . $thumbnails['size'][$index]);
    
                    $this->upload->initialize($thumbnail_config, TRUE);
                    if ($this->upload->do_upload('thumbnail')) {
                        $thumbnail_data = $this->upload->data();
                        $media_data['media_thumbnail'] = 'storage/thumbnails/' . $thumbnail_data['file_name'];
    
                        $existing_media = $this->db->where('id', $media_id)
                            ->where('project_id', $project_id)
                            ->get('media_files')
                            ->row_array();
                        if (!empty($existing_media['media_thumbnail']) && file_exists(FCPATH . 'public/' . $existing_media['media_thumbnail'])) {
                            unlink(FCPATH . 'public/' . $existing_media['media_thumbnail']);
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Thumbnail upload failed for media ' . $media_id . ': ' . $this->upload->display_errors());
                        log_message('error', 'Thumbnail upload failed for media ' . $media_id . ': ' . $this->upload->display_errors());
                        continue;
                    }
                }
    
                $this->db->where('id', $media_id)->where('project_id', $project_id)->update('media_files', $media_data);
            }
        }
    
        // Handle new media files
        if (!empty($_FILES['new_media_files']['name'][0])) {
            $files = $_FILES['new_media_files'];
            $media_titles = $this->input->post('new_media_titles', TRUE);
            $media_descriptions = $this->input->post('new_media_descriptions', TRUE);
            $thumbnails = $_FILES['new_media_thumbnail'] ?? [];
            
            $config['upload_path'] = FCPATH . 'public/storage/media/';
            $config['allowed_types'] = 'jpg|jpeg|png|mp4|mp3|3gp|pdf|doc|docx|txt|rtf|odt|xls|xlsx|csv|ppt|pptx|apk|zip';
            $config['mimes'] = [
                'apk' => [
                    'application/vnd.android.package-archive',
                    'application/octet-stream',
                    'application/zip',
                    'application/x-zip-compressed',
                    'application/x-apk'
                ]
            ];
            $config['max_size'] = 512000; // 500MB
            $config['file_ext_tolower'] = TRUE;
    
            $thumbnail_config['upload_path'] = FCPATH . 'public/storage/thumbnails/';
            $thumbnail_config['allowed_types'] = 'jpg|jpeg|png|gif';
            $thumbnail_config['max_size'] = 2048; // 2MB
            $thumbnail_config['file_ext_tolower'] = TRUE;
    
            $this->load->library('upload');
    
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['name'][$i] && !empty($media_titles[$i])) {
                    $_FILES['file']['name'] = $files['name'][$i];
                    $_FILES['file']['type'] = $files['type'][$i];
                    $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                    $_FILES['file']['error'] = $files['error'][$i];
                    $_FILES['file']['size'] = $files['size'][$i];
    
                    log_message('debug', 'New File: ' . $files['name'][$i] . ', MIME Type: ' . $files['type'][$i] . ', Size: ' . $files['size'][$i]);
    
                    $this->upload->initialize($config, TRUE);
                    if ($this->upload->do_upload('file')) {
                        $file_data = $this->upload->data();
                        $mime_type = $file_data['file_type'];
                        $file_type = $this->_get_file_type($mime_type);
                        $media_data = [
                            'project_id' => $project_id,
                            'title' => $media_titles[$i],
                            'description' => $media_descriptions[$i] ?? null,
                            'file_type' => $file_type,
                            'mime_type' => $mime_type,
                            'file_extension' => ltrim($file_data['file_ext'], '.'),
                            'file_size' => $file_data['file_size'],
                            'file_url' => 'storage/media/' . $file_data['file_name'],
                            'uploaded_by' => $user_id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
    
                        // Handle media thumbnail upload for new media
                        if (!empty($thumbnails['name'][$i])) {
                            $_FILES['thumbnail']['name'] = $thumbnails['name'][$i];
                            $_FILES['thumbnail']['type'] = $thumbnails['type'][$i];
                            $_FILES['thumbnail']['tmp_name'] = $thumbnails['tmp_name'][$i];
                            $_FILES['thumbnail']['error'] = $thumbnails['error'][$i];
                            $_FILES['thumbnail']['size'] = $thumbnails['size'][$i];
    
                            log_message('debug', 'New Thumbnail File: ' . $thumbnails['name'][$i] . ', MIME Type: ' . $thumbnails['type'][$i] . ', Size: ' . $thumbnails['size'][$i]);
    
                            $this->upload->initialize($thumbnail_config, TRUE);
                            if ($this->upload->do_upload('thumbnail')) {
                                $thumbnail_data = $this->upload->data();
                                $media_data['media_thumbnail'] = 'storage/thumbnails/' . $thumbnail_data['file_name'];
                            } else {
                                $this->session->set_flashdata('error', 'Thumbnail upload for new media failed: ' . $this->upload->display_errors());
                                log_message('error', 'Thumbnail upload for new media failed: ' . $this->upload->display_errors());
                                continue;
                            }
                        }
    
                        $this->db->insert('media_files', $media_data);
                    } else {
                        $this->session->set_flashdata('error', 'Media upload failed for ' . $files['name'][$i] . ': ' . $this->upload->display_errors());
                        log_message('error', 'Media upload failed for ' . $files['name'][$i] . ': ' . $this->upload->display_errors());
                        continue;
                    }
                }
            }
        }
    
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Failed to update project.');
            $this->edit($project_id);
            return;
        }
        $this->db->trans_commit();
    
        $this->session->set_flashdata('success', 'Project updated successfully.');
        redirect('admin');
    }
    
    public function destroy($project_id) {
        $this->db->trans_start();

        $project = $this->Project_model->get_project($project_id);
        if (!$project) {
            $this->session->set_flashdata('error', 'Project not found.');
            redirect('admin');
        }

        // Delete thumbnail
        if ($project['project_thumbnail'] && file_exists('./public/' . $project['project_thumbnail'])) {
            unlink('./public/' . $project['project_thumbnail']);
        }

        // Delete media files
        $media_files = $this->Project_model->get_project_media($project_id);
        foreach ($media_files as $media) {
            if ($media['file_url'] && file_exists('./public/' . $media['file_url'])) {
                unlink('./public/' . $media['file_url']);
            }
            $this->db->where('id', $media['id'])->delete('media_files');
        }

        // Delete project
        $this->db->where('id', $project_id)->delete('projects');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Failed to delete project.');
        } else {
            $this->session->set_flashdata('success', 'Project deleted successfully.');
        }
        redirect('admin');
    }

    public function project($project_id) {
        $data['project'] = $this->Project_model->get_project($project_id);
        $data['project']['mediaFiles'] = $this->Project_model->get_project_media($project_id);
        if (empty($data['project'])) {
            show_404();
        }
        $data['title'] = $data['project']['project_name'];
        $this->load->view('admin/header', $data);
        $this->load->view('admin/project', $data);
        
    }

    public function valid_date($date) {
        if (empty($date)) {
            return TRUE;
        }
        $date = strtotime($date);
        if ($date === FALSE) {
            $this->form_validation->set_message('valid_date', 'The {field} must be a valid date.');
            return FALSE;
        }
        return TRUE;
    }

    public function validate_thumbnail($str) {
        if (empty($_FILES['project_thumbnail']['name']) && empty($_FILES['new_media_thumbnails']['name'][0])) {
            return TRUE; // Allow empty thumbnails if optional
        }
        $config['upload_path'] = FCPATH . 'public/storage/thumbnails/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 2048;
        $this->load->library('upload', $config);
        return $this->upload->do_upload('project_thumbnail') || $this->upload->do_upload('new_media_thumbnails');
    }

    public function __validate_thumbnail() {
        if (empty($_FILES['project_thumbnail']['name'])) {
            return TRUE;
        }
        $config['upload_path'] = './public/storage/thumbnails/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 2048;
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('project_thumbnail')) {
            return TRUE;
        }
        $this->form_validation->set_message('validate_thumbnail', $this->upload->display_errors());
        return FALSE;
    }

    private function _get_file_type($mime_type) {
        if (empty($mime_type)) {
            log_message('error', 'Empty MIME type provided to _get_file_type');
            return 'unknown';
        }

        // var_dump($mime_type);
        // die();

        $mime_type = strtolower($mime_type);

        $mime_type_map = [
            'image/' => 'image',
            'video/' => 'video',
            'audio/' => 'audio',
            'application/pdf' => 'pdf',
            'application/msword' => 'document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml' => 'document',
            'application/vnd.oasis.opendocument.text' => 'document',
            'application/vnd.ms-excel' => 'spreadsheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml' => 'spreadsheet',
            'text/csv' => 'spreadsheet',
            'application/vnd.ms-powerpoint' => 'presentation',
            'application/vnd.openxmlformats-officedocument.presentationml' => 'presentation',
            'application/zip' => 'apk',            
            'text/' => 'text',
            'application/java-archive' => 'apk',            
            // 'apk' => ['application/vnd.android.package-archive', 'application/octet-stream', 'application/x-apk', 'application/zip', 'application/java-archive', 'application/x-zip-compressed'],
        ];
        
        // An .apk (Android Package) is just a ZIP archive with a specific structure. That’s why many systems, including PHP and some Linux tools, detect it as application/zip.


        if (isset($mime_type_map[$mime_type])) {
            return $mime_type_map[$mime_type];
        }
       
        foreach ($mime_type_map as $key => $type) {
            if (strpos($mime_type, $key) !== FALSE) {
                return $type;
            }
        }

        log_message('error', "Unrecognized MIME type: {$mime_type}");
        return 'unknown';
    }

    public function update_status() {
        if ($this->input->is_ajax_request()) {
            $user_id = $this->input->post('user_id');
            $current_status = $this->input->post('current_status');
            $new_status = ($current_status == 1) ? 1 : 0;
         
            if ($this->Project_model->update_status($user_id, $new_status)) {
                $response = array(
                    'success' => true,
                    'new_status' => $new_status,
                    'message' => 'User status updated successfully.'
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to update user status.'
                );
            }

            echo json_encode($response);
            exit;
        }
        show_error('Invalid request', 400);
    }
}