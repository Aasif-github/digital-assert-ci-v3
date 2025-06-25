<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Project_model');
        $this->load->helper(['url', 'form', 'file']);
        $this->load->library(['form_validation', 'session']);
        $this->load->database();
        // Placeholder for authentication check
        // if (!$this->session->userdata('user_id')) {
        //     redirect('auth/login');
        // }    
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
        $this->load->view('admin/header', $data);
        $this->load->view('admin/dashboard', $data);        
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
            $files = $_FILES['new_media_files'];
            $media_titles = $this->input->post('new_media_titles');
            $media_descriptions = $this->input->post('new_media_descriptions');
    
            $config['upload_path'] = FCPATH . 'public/storage/media/';
            $config['allowed_types'] = 'jpg|jpeg|png|mp4|mp3|3gp|pdf|doc|docx|txt|rtf|odt|xls|xlsx|csv|ppt|pptx|apk|zip';
            
            $config['mimes'] = ['apk' => ['application/vnd.android.package-archive', 'application/octet-stream', 'application/zip']];
            // $config['check_mime'] = FALSE; // Add to upload config in update() and validate_media_files()
        
            $config['file_ext_to_mimetypes'] = ['apk' => 'application/vnd.android.package-archive'];

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
    
            for ($i = 0; $i < count($files['name']); $i++) {
                if (!empty($files['name'][$i]) && !empty($media_titles[$i])) {
                    $_FILES['file']['name'] = $files['name'][$i];
                    $_FILES['file']['type'] = $files['type'][$i];
                    $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                    $_FILES['file']['error'] = $files['error'][$i];
                    $_FILES['file']['size'] = $files['size'][$i];
    
                    // Debug MIME type
                    log_message('debug', 'File: ' . $files['name'][$i] . ', MIME Type: ' . $files['type'][$i] . ', Size: ' . $files['size'][$i]);
    
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
                        log_message('error', 'Media upload failed for ' . $files['name'][$i] . ': ' . $this->upload->display_errors());
                        $this->session->set_flashdata('error', 'Upload failed for ' . $files['name'][$i] . ': ' . $this->upload->display_errors());
                        $this->db->trans_rollback();
                        $this->show();
                        return;
                    }
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

    public function validate_thumbnail() {
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
}