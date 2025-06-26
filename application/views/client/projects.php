<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?> - Digital Asset Management</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
    <?php $this->load->view('client/header'); ?>
 
    <style>
        .card.flex-row {
            min-height: 200px;
        }

        @media (max-width: 768px) {
            .card.flex-row {
                flex-direction: column !important;
                min-height: auto;
            }
        }
    </style>

</head>
<body>
            <div class="container">
                <h2 class="my-4">Projects</h2>
               
                <?php foreach ($total_media_by_type as $item): ?>
                    <a href="<?php echo site_url('client/media_files_by_type/' . urlencode($item['file_type'])); ?>" class="btn btn-outline-secondary btn-sm me-1 mb-1">
                        <?php echo $item['file_type']; ?>: <?php echo $item['total']; ?>
                    </a>
                <?php endforeach; ?>
                <br/>
                <br/>                
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $project): ?>
                                    
                                        <div class="card flex-row shadow-sm mb-3">
                                            <div class="d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; overflow: hidden;">
                                                <?php if (!empty($project['project_thumbnail']) && file_exists(FCPATH . 'public/' . $project['project_thumbnail'])): ?>
                                                    <img src="<?php echo base_url('public/' . $project['project_thumbnail']); ?>" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;" alt="<?php echo $project['project_name']; ?>">
                                                <?php else: ?>
                                                    <img src="<?php echo base_url('assets/images/default-thumbnail.png'); ?>" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;" alt="Default Thumbnail">
                                                <?php endif; ?>
                                                
                                            </div>
                                            
                                            <div class="card-body d-flex flex-column justify-content-between">
                                                <div>
                                                    <h5 class="card-title"><?php echo $project['project_name']; ?></h5>
                                                    <p class="card-text"><?php echo $project['project_short_description']; ?></p>
                                                    <p class="card-text">
                                                        <small class="text-muted">Created by: <?php echo $project['username'] ?? 'Unknown'; ?> |
                                                            Year: <?php echo $project['year_of_publish'] ? date('Y', strtotime($project['year_of_publish'])) : 'N/A'; ?>
                                                        </small>
                                                    </p>
                                                </div>
                                                <!-- Display file types -->
                                                <?php if (!empty($project['file_types'])): ?>
                                                    <div class="mt-1">
                                                        <p class="mb-1"><strong>Resources:</strong></p>
                                                        <ul class="list-inline">
                                                            <?php foreach ($project['file_types'] as $type => $count): ?>
                                                                <li class="list-inline-item badge bg-secondary me-1">
                                                                    <?= ucfirst($type) ?>: <?= $count ?>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="mt-1">
                                                    <a href="<?php echo site_url('client/project/' . $project['id']); ?>" class="btn btn-primary btn-sm">View Details</a>
                                                    <?php if (!empty($project['download_url'])): ?>
                                                        <a href="<?php echo base_url($project['download_url']); ?>" class="btn btn-success btn-sm" download>Download</a>
                                                    <?php endif; ?>
                                                    <!-- <button class="btn btn-secondary btn-sm" onclick="shareProject('</?php echo site_url('client/project/' . $project['id']); ?>')">Share</button> -->
                                                </div>
                                            </div>
                                        </div>
                                                    
                        <?php endforeach; ?>
                        <?php else: ?>
                            <p>No projects found.</p>
                        <?php endif; ?>         
                    </div>    
                    <div class="col-md-4 mb-4">
                        Total Projects: <?php echo $total_projects??'0'; ?>
                    </div>
                </div>                                 
    </div>
    
    <script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
 
        <script>
        function shareProject(link) {
            if (navigator.share) {
                navigator.share({
                    title: 'Check out this project',
                    url: link
                }).catch((error) => console.log('Share failed:', error));
            } else {
                prompt("Copy this link:", link);
            }
        }        
    </script>
</body>
</html>