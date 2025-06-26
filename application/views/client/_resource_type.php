 <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <?php $this->load->view('client/header'); ?>
  <title><?php echo ucfirst(htmlspecialchars($title)); ?> Files</title>  
</head>
<body class="bg-light">

<div class="container my-5">
  <h2 class="mb-4">Media Files of Type: <span class="text-primary"><?php echo htmlspecialchars($title); ?></span></h2>

  <div class="row">
    <?php if (!empty($media_files)): ?>
      <?php foreach ($media_files as $file): ?>
        <div class="col-md-6 col-lg-3 mb-4">
          <div class="card h-100 shadow-sm rounded-4">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">Project: <?php echo htmlspecialchars($file['project_name']); ?></h6>
            </div>
            <div class="card-body">
              <p><strong>Language:</strong> <?php echo htmlspecialchars($file['language']); ?></p>
              <p><strong>Year of Publish:</strong> <?php echo htmlspecialchars($file['year_of_publish']); ?></p>
              <p><strong>Uploaded By:</strong> <?php echo htmlspecialchars($file['uploaded_by']); ?></p>
              <hr>
              <h6 class="text-secondary">Media Info</h6>
              <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Title:</strong> <?php echo htmlspecialchars($file['title']); ?></li>
                <li class="list-group-item"><strong>Description:</strong> <?php echo htmlspecialchars($file['description']); ?></li>
                <li class="list-group-item"><strong>File Type:</strong> <?php echo htmlspecialchars($file['file_type']); ?></li>
                <li class="list-group-item"><strong>File Extension:</strong> <?php echo htmlspecialchars($file['file_extension']); ?></li>
                <li class="list-group-item"><strong>File Size:</strong> <?php echo htmlspecialchars($file['file_size']); ?> KB</li>
              </ul>
              <a href="<?php echo base_url($file['file_url']); ?>" class="btn btn-success w-100" download>
                📥 Download File
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning text-center">No media files found for this type.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>