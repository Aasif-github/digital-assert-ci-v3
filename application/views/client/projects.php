<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?> - Digital Asset Management</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
    <?php $this->load->view('client/header'); ?>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Projects</h1>
        <div class="row">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php if ($project['project_thumbnail']): ?>
                                <img src="<?php echo base_url('public/' . $project['project_thumbnail']); ?>" class="card-img-top" alt="<?php echo $project['project_name']; ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $project['project_name']; ?></h5>
                                <p class="card-text"><?php echo $project['project_short_description']; ?></p>
                                <p><small>Created by: <?php echo $project['username'] ?? 'Unknown'; ?> | Year: <?php echo $project['year_of_publish'] ? date('Y', strtotime($project['year_of_publish'])) : 'N/A'; ?></small></p>
                                <a href="<?php echo site_url('client/project/' . $project['id']); ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No projects found.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
</body>
</html>