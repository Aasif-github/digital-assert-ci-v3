<h1 class="my-4"><?php echo $project['project_name']; ?></h1>
<div class="row">
    <div class="col-md-8">
        <?php if ($project['project_thumbnail']): ?>
            <img src="<?php echo base_url('public/' .$project['project_thumbnail']); ?>" class="img-fluid mb-3" alt="<?php echo $project['project_name']; ?>">
        <?php endif; ?>
        <p><strong>Short Description:</strong> <?php echo $project['project_short_description'] ?: 'N/A'; ?></p>
        <p><strong>Long Description:</strong> <?php echo $project['project_long_description'] ?: 'N/A'; ?></p>
        <p><strong>Language:</strong> <?php echo $project['language'] ?: 'N/A'; ?></p>
        <p><strong>Year of Publish:</strong> <?php echo $project['year_of_publish'] ? date('Y', strtotime($project['year_of_publish'])) : 'N/A'; ?></p>
        <p><strong>Created by:</strong> <?php echo $project['username'] ?? 'Unknown'; ?></p>
    </div>
</div>
<h3 class="my-4">Media Files</h3>
<div class="row">
    <?php if (!empty($project['mediaFiles'])): ?>
        <?php foreach ($project['mediaFiles'] as $media): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php if (in_array($media['file_extension'], ['jpg', 'jpeg', 'png', 'gif'])): ?>
                        <img src="<?php echo base_url($media['file_url']); ?>" class="card-img-top" alt="<?php echo $media['title']; ?>">
                    <?php else: ?>
                        <div class="card-img-top text-center p-3 bg-light">File: <?php echo $media['title']; ?></div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $media['title'] ?: 'Untitled'; ?></h5>
                        <p class="card-text"><?php echo $media['description'] ?: 'No description'; ?></p>
                        <p><small>Type: <?php echo $media['file_type']; ?> | Size: <?php echo round($media['file_size'] / 1024, 2); ?> KB</small></p>
                        <p><small>Uploaded by: <?php echo $media['username'] ?? 'Unknown'; ?></small></p>
                        <a href="<?php echo base_url($media['file_url']); ?>" class="btn btn-primary" download>Download</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No media files found for this project.</p>
    <?php endif; ?>
</div>
<a href="<?php echo site_url('admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>