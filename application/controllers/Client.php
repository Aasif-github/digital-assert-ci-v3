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
            
        $this->db->from('projects');
        $total_projects = $this->db->count_all_results();

        $data['total_media_by_type'] = $total_media_by_type;
        $data['projects'] = $projects;
        $data['title'] = 'Projects';
        $data['total_projects'] = $total_projects;

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

    // Example search method
    public function _1search($project_id) {
        $query = $this->input->post('query');
        log_message('debug', 'Received query: ' . $query);
    
        if (empty($query)) {
            echo '<p class="text-muted mt-4">No search input provided.</p>';
            return;
        }
    
        $results = $this->Project_model->search_projects_and_media($query, $project_id);
    
        if (empty($results)) {
            echo '<p class="text-muted mt-4">No results found for <strong>' . htmlspecialchars($query) . '</strong>.</p>';
            return;
        }
    
        // Map file extensions to thumbnail icons
        $thumbnail_map = [
            'jpg' => 'image.jpg',
            'jpeg' => 'image.jpg',
            'png' => 'image.jpg',
            'gif' => 'image.jpg',
            'mp4' => 'video.png',
            'mov' => 'video.png',
            'mp3' => 'audio.png',
            'wav' => 'audio.png',
            'pdf' => 'pdf.png',
            'doc' => 'doc.png',
            'docx' => 'doc.png',
            'ppt' => 'ppt.png',
            'pptx' => 'ppt.png',
            'csv' => 'csv.png',
            'apk' => 'apk.png'
        ];
    
        // Default fallback thumbnail
        $default_thumbnail = 'default.png';
    
        echo '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mt-3">';
    
        foreach ($results as $row) {
            // Determine thumbnail
            $thumbnail = base_url('assets/thumbnails/' . $default_thumbnail);
            if (!empty($row->project_thumbnail)) {
                $thumbnail = base_url('public/' . $row->project_thumbnail);
            } elseif (!empty($row->file_extension)) {
                $ext = strtolower($row->file_extension);
                if (isset($thumbnail_map[$ext])) {
                    $thumbnail = base_url('assets/thumbnails/' . $thumbnail_map[$ext]);
                }
            }
    
            echo '<div class="col">';
            echo '<div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s; overflow: hidden;">';
            
            // Thumbnail
            echo '<div class="card-img-top position-relative" style="height: 200px; overflow: hidden;">';
            echo '<img src="' . $thumbnail . '" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Thumbnail" onerror="this.src=\'' . base_url('assets/thumbnails/' . $default_thumbnail) . '\';">';
            echo '</div>';
    
            // Card body
            echo '<div class="card-body d-flex flex-column">';
            echo '<h5 class="card-title mb-2" style="font-size: 1.25rem;">' . htmlspecialchars($row->project_name) . '</h5>';
            echo '<p class="card-text text-muted mb-3 flex-grow-1" style="font-size: 0.9rem;">' . htmlspecialchars($row->project_short_description ?? 'No description available') . '</p>';
    
            // Media info
            if (!empty($row->media_title)) {
                echo '<div class="mt-auto">';
                echo '<h6 class="mb-1 text-primary" style="font-size: 0.95rem;">Media</h6>';
                echo '<p class="mb-1 fw-medium" style="font-size: 0.9rem;">' . htmlspecialchars($row->media_title) . '</p>';
                echo '<p class="text-muted small mb-2">' . htmlspecialchars($row->file_type ?? 'Unknown') . ' | ' . htmlspecialchars($row->file_extension ?? 'N/A') . '</p>';
                echo '<a href="' . base_url($row->file_url) . '" target="_blank" class="btn btn-sm btn-outline-primary w-100">View File</a>';
                echo '</div>';
            }
    
            echo '</div>'; // card-body
            echo '</div>'; // card
            echo '</div>'; // col
        }
    
        echo '</div>'; // row
    }

    public function _2search($project_id) {
        $query = $this->input->post('query');
        log_message('debug', 'Received query: ' . $query);
    
        if (empty($query)) {
            echo '<p class="text-muted mt-4">No search input provided.</p>';
            return;
        }
    
        $results = $this->Project_model->search_projects_and_media($query, $project_id);
    
        if (empty($results)) {
            echo '<p class="text-muted mt-4">No results found for <strong>' . htmlspecialchars($query) . '</strong>.</p>';
            return;
        }
    
        // Supported image extensions for direct file_url usage
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    
        // Default fallback thumbnail
        $default_thumbnail = 'default.png';
    
        echo '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mt-3">';
    
        foreach ($results as $row) {
            // var_dump($row);
            
            // Determine thumbnail
            $thumbnail = base_url('assets/thumbnails/' . $default_thumbnail);

            if (!empty($row->project_thumbnail)) {
                $thumbnail = base_url('public/' . $row->file_url);
            } elseif (!empty($row->file_url) && !empty($row->file_extension)) {
                $ext = strtolower($row->file_extension);
                if (in_array($ext, $image_extensions)) {
                    $thumbnail = base_url($row->file_url); // Use file_url for images
                }
            }
    
            echo '<div class="col">';
            echo '<div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s; overflow: hidden;">';
            
            // Thumbnail
            echo '<div class="card-img-top position-relative" style="height: 200px; overflow: hidden;">';
            // echo '<img src="' . $thumbnail . '" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Thumbnail" onerror="this.src=\'' . base_url('assets/thumbnails/' . $default_thumbnail) . '\';">';
            echo '</div>';
    
            // Card body
            echo '<div class="card-body d-flex flex-column">';
            echo '<h5 class="card-title mb-2" style="font-size: 1.25rem;">' . htmlspecialchars($row->media_title) . '</h5>';
            echo '<p class="card-text text-muted mb-3 flex-grow-1" style="font-size: 0.9rem;">' . htmlspecialchars($row->media_description ?? 'No description available') . '</p>';
    
            // Media info
            if (!empty($row->media_title)) {
                echo '<div class="mt-auto">';
                echo '<h6 class="mb-1 text-primary" style="font-size: 0.95rem;">Media</h6>';
                echo '<p class="mb-1 fw-medium" style="font-size: 0.9rem;">' . htmlspecialchars($row->media_title) . '</p>';
                echo '<p class="text-muted small mb-2">' . htmlspecialchars($row->file_type ?? 'Unknown') . ' | ' . htmlspecialchars($row->file_extension ?? 'N/A') . '</p>';
                echo '<a href="' . base_url($row->file_url) . '" target="_blank" class="btn btn-sm btn-outline-primary w-100">View File</a>';
                echo '</div>';
            }
    
            echo '</div>'; // card-body
            echo '</div>'; // card
            echo '</div>'; // col
        }
    
        echo '</div>'; // row
    }

    public function _3search($project_id) {
        $query = $this->input->post('query');
        log_message('debug', 'Received query: ' . $query);
    
        if (empty($query)) {
            echo '<div class="col-12"><div class="alert alert-warning text-center" role="alert"><i class="fas fa-exclamation-circle me-2"></i>No search input provided.</div></div>';
            return;
        }
    
        $results = $this->Project_model->search_projects_and_media($query, $project_id);
    
        if (empty($results)) {
            echo '<div class="col-12"><div class="alert alert-warning text-center" role="alert"><i class="fas fa-exclamation-circle me-2"></i>No results found for <strong>' . htmlspecialchars($query) . '</strong>.</div></div>';
            return;
        }
    
        // Supported extensions for preview and view
        $previewable = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'mp3', 'wav', 'pdf'];
        $viewable = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'mp3', 'wav', 'pdf', 'docx', 'ppt', 'pptx'];
        $logos = [
            'apk' => 'fa-android',
            'docx' => 'fa-file-word',
            'csv' => 'fa-solid fa-file-csv',
            'ppt' => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint'
        ];
        $default_thumbnail = 'default.png';
    
        echo '<div class="row" id="mediaFiles">';
    
        foreach ($results as $row) {
            $extension = !empty($row->file_extension) ? strtolower($row->file_extension) : '';
            
            // Determine thumbnail
            $thumbnail = base_url('assets/thumbnails/' . $default_thumbnail);
            if (!empty($row->project_thumbnail)) {
                $thumbnail = base_url('public/' . $row->project_thumbnail);
            } elseif (!empty($row->file_url) && in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $thumbnail = base_url($row->file_url);
            }
    
            echo '<div class="col-md-6 col-lg-4 mb-4 file-card" 
                       data-title="' . htmlspecialchars($row->media_title ?? '') . '" 
                       data-project="' . htmlspecialchars($row->project_name ?? '') . '" 
                       data-language="' . htmlspecialchars($row->language ?? '') . '">';
            echo '<div class="card h-100 shadow-sm">';
    
            // Card Header (Project Name)
            echo '<div class="card-header text-white bg-primary">';
            echo '<h6 class="mb-0"><i class="fas fa-folder me-2"></i>' . htmlspecialchars($row->project_name ?? 'Unknown Project') . '</h6>';
            echo '</div>';
    
            // Card Header (Thumbnail and Metadata)
            echo '<div class="card-header text-white bg-secondary">';
            echo '<div class="row">';
            echo '<div class="col-4">';
            echo '<div class="thumbnail-container" style="height: 100px; overflow: hidden;">';
    
            // Thumbnail handling
            if (in_array($extension, $previewable)) {
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo '<img src="' . $thumbnail . '" alt="Thumbnail" class="img-fluid w-100 h-100" style="object-fit: cover;" onerror="this.src=\'' . base_url('assets/thumbnails/' . $default_thumbnail) . '\';">';
                } elseif (in_array($extension, ['mp4', 'webm'])) {
                    echo '<video muted class="w-100 h-100" style="object-fit: cover;"><source src="' . base_url($row->file_url) . '" type="video/' . $extension . '"></video>';
                } elseif (in_array($extension, ['mp3', 'wav'])) {
                    echo '<i class="fas fa-file-audio fa-3x"></i>';
                } elseif ($extension === 'pdf') {
                    echo '<i class="fas fa-file-pdf fa-3x"></i>';
                }
            } elseif (array_key_exists($extension, $logos)) {
                echo '<i class="fas ' . $logos[$extension] . ' fa-3x"></i>';
            } else {
                echo '<i class="fas fa-file fa-3x"></i>';
            }
    
            echo '</div></div>';
    
            // Metadata (Language, Year, Uploaded By)
            echo '<div class="col-8">';
            echo '<p><i class="fas fa-language me-2"></i><strong>Language:</strong> ' . htmlspecialchars($row->language ?? 'N/A') . '</p>';
            echo '<p><i class="fas fa-calendar-alt me-2"></i><strong>Year:</strong> ' . htmlspecialchars($row->year_of_publish ?? 'N/A') . '</p>';
            echo '<p><i class="fas fa-user me-2"></i><strong>Uploaded By:</strong> ' . htmlspecialchars($row->uploaded_by ?? 'Admin') . '</p>';
            echo '</div>';
            echo '</div>'; // row
            echo '</div>'; // card-header
    
            // Card Body
            echo '<div class="card-body">';
            echo '<h6 class="text-secondary mb-3">Media Info</h6>';
            echo '<ul class="list-group list-group-flush mb-3">';
            echo '<li class="list-group-item"><strong>Title:</strong> <span data-bs-toggle="tooltip" title="' . htmlspecialchars($row->media_title ?? '') . '">' . htmlspecialchars($row->media_title ?? 'N/A') . '</span></li>';
            echo '<li class="list-group-item"><strong>Description:</strong> <span data-bs-toggle="tooltip" title="' . htmlspecialchars($row->media_description ?? '') . '">' . htmlspecialchars($row->media_description ?? 'No description available') . '</span></li>';
            echo '<li class="list-group-item"><strong>File Type:</strong> ' . htmlspecialchars($row->file_type ?? 'Unknown') . '</li>';
            echo '<li class="list-group-item"><strong>File Extension:</strong> ' . htmlspecialchars($row->file_extension ?? 'N/A') . '</li>';
            echo '<li class="list-group-item"><strong>File Size:</strong> ' . htmlspecialchars($row->file_size ?? 'N/A') . ' KB</li>';
            echo '</ul>';
    
            // View Button
            if (in_array($extension, $viewable) && $extension !== 'apk') {
                if (in_array($extension, ['docx', 'ppt', 'pptx'])) {
                    // Note: You might want to use a viewer like Google Docs Viewer for these types
                    echo '<a href="' . base_url($row->file_url) . '" class="btn btn-sm btn-outline-secondary mb-3 w-100" target="_blank"><i class="fas fa-eye me-2"></i>View</a>';
                } else {
                    echo '<button class="btn btn-sm btn-outline-secondary mb-3 w-100 btn-view" 
                                data-bs-toggle="modal" 
                                data-bs-target="#previewModal" 
                                data-file-url="' . base_url($row->file_url) . '" 
                                data-file-type="' . $extension . '">
                                <i class="fas fa-eye me-2"></i>View</button>';
                }
            }
    
            // Download Button
 
            echo '<a href="' . base_url($row->file_url) . '" class="btn btn-sm btn-outline-primary w-100" download><i class="fas fa-download me-2"></i>Download File</a>';
    
            echo '</div>'; // card-body
            echo '</div>'; // card
            echo '</div>'; // col
        }
    
        echo '</div>'; // row
    }

    public function search($project_id) {
        // Get the search query
        $query = $this->input->post('query');
        log_message('debug', 'Received query: ' . $query);

        // Initialize response array
        $response = [
            'status' => 'error',
            'message' => '',
            'data' => []
        ];

        // Handle empty query
        if (empty($query)) {
            $response['message'] = 'No search input provided.';
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($response));
            return;
        }

        // Validate project_id
        if (!is_numeric($project_id) || $project_id <= 0) {
            $response['message'] = 'Invalid project ID.';
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($response));
            return;
        }

        try {
            // Fetch results from model
            $results = $this->Project_model->search_projects_and_media($query, $project_id);

            // Handle no results
            if (empty($results)) {
                $response['message'] = 'No results found for "' . htmlspecialchars($query) . '".';
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode($response));
                return;
            }

            // Map file extensions to thumbnail icons
            $thumbnail_map = [
                'jpg' => 'image.jpg',
                'jpeg' => 'image.jpg',
                'png' => 'image.jpg',
                'gif' => 'image.jpg',
                'mp4' => 'video.png',
                'mov' => 'video.png',
                'mp3' => 'audio.png',
                'wav' => 'audio.png',
                'pdf' => 'pdf.png',
                'doc' => 'doc.png',
                'docx' => 'doc.png',
                'ppt' => 'ppt.png',
                'pptx' => 'ppt.png',
                'csv' => 'csv.png',
                'apk' => 'apk.png'
            ];

            $default_thumbnail = 'default.png';
            $formatted_results = [];

            // Format results for frontend
            foreach ($results as $row) {
                // Determine thumbnail
                $thumbnail = base_url('assets/thumbnails/' . $default_thumbnail);
                if (!empty($row->project_thumbnail)) {
                    $thumbnail = base_url('public/' . $row->project_thumbnail);
                } elseif (!empty($row->file_extension)) {
                    $ext = strtolower($row->file_extension);
                    if (isset($thumbnail_map[$ext])) {
                        $thumbnail = base_url('assets/thumbnails/' . $thumbnail_map[$ext]);
                    }
                }

                // Build result item
                $item = [
                    'project_id' => $row->project_id ?? null,
                    'project_name' => htmlspecialchars($row->project_name ?? ''),
                    'project_short_description' => htmlspecialchars($row->project_short_description ?? 'No description available'),
                    'thumbnail' => $thumbnail,
                    'default_thumbnail' => base_url('assets/thumbnails/' . $default_thumbnail)
                ];

                // Add media info if available
                if (!empty($row->media_title)) {
                    $item['media'] = [
                        'title' => htmlspecialchars($row->media_title),
                        'media_description' => htmlspecialchars($row->media_description),
                        'file_type' => htmlspecialchars($row->file_type ?? 'Unknown'),
                        'file_size' => htmlspecialchars($row->file_size ?? '0'),
                        'file_extension' => htmlspecialchars($row->file_extension ?? 'N/A'),
                        'file_url' => base_url($row->file_url ?? '')
                    ];
                }

                $formatted_results[] = $item;
            }

            // Set success response
            $response['status'] = 'success';
            $response['message'] = 'Results retrieved successfully.';
            $response['data'] = $formatted_results;

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            // Handle unexpected errors
            log_message('error', 'Search error: ' . $e->getMessage());
            $response['message'] = 'An error occurred while processing your request.';
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode($response));
        }
    }

    public function all_projects(){
        try {
        // Fetch projects
        $this->db->select('p.id, p.project_name, p.project_thumbnail, p.project_short_description, p.year_of_publish, p.created_at, p.updated_at');
        $this->db->from('projects p');
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();
        $projects = $query->result_array();

        /*
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
       */
      // Fetch file_type counts with latest updated_at for each project_id + file_type
        $file_type_data = $this->db
            ->select('project_id, file_type, COUNT(*) as total, MAX(updated_at) as last_updated')
            ->from('media_files')
            ->group_by(['project_id', 'file_type'])
            ->get()
            ->result_array();

        // Combine file_type data with projects
        foreach ($projects as &$project) {
            $project['file_types'] = [];
            foreach ($file_type_data as $file_info) {
                if ($file_info['project_id'] == $project['id']) {
                    $project['file_types'][$file_info['file_type']] = [
                        'count' => $file_info['total'],
                        'updated_at' => $file_info['last_updated'],
                    ];
                }
            }
        }

        $data['projects'] = $projects;
        $data['total_projects'] = $this->db->count_all('projects');
        $data['title'] = 'Total Projects';
        
        $this->load->view('client/header', $data);
        $this->load->view('client/all_projects', $data);      
            
        }catch (Exception $e) {
            log_message('error', 'Search error: ' . $e->getMessage());
        } 
    }
}