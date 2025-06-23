<h1 class="my-4">Admin Dashboard</h1>
<p>Total Projects: <?php echo $total_projects; ?></p>
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
                        <p class="card-text"><?php echo $project['project_short_description'] ?: 'No description'; ?></p>
                        <p><small>Year: <?php echo $project['year_of_publish'] ? date('Y', strtotime($project['year_of_publish'])) : 'N/A'; ?></small></p>
                        <p><small>File Types: <?php echo implode(', ', array_map(function($type, $count) { return "$type: $count"; }, array_keys($project['file_types']), $project['file_types'])); ?></small></p>
                        <a href="<?php echo site_url('admin/project/' . $project['id']); ?>" class="btn btn-primary">View</a>
                        <a href="<?php echo site_url('admin/edit/' . $project['id']); ?>" class="btn btn-warning">Edit</a>
                        <a href="<?php echo site_url('admin/destroy/' . $project['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this project?');">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No projects found.</p>
    <?php endif; ?>
</div>