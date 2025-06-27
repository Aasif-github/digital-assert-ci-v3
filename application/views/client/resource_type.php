<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo ucfirst(htmlspecialchars($title)); ?> Files</title>
  <?php $this->load->view('client/header'); ?>
  
  <style>
    body {
      background-color: #f4f7fc;
      font-family: 'Inter', sans-serif;
    }
    .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: none;
      border-radius: 12px;
      overflow: hidden;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }
    .card-header {
      background: linear-gradient(135deg, #007bff, #00d4ff);
      padding: 1rem;
    }
    .card-body {
      padding: 1.5rem;
    }
    .list-group-item {
      border: none;
      padding: 0.5rem 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .btn-download {
      background-color: #28a745;
      border: none;
      border-radius: 8px;
      padding: 0.75rem;
      font-weight: 500;
      transition: background-color 0.3s ease;
    }
    .btn-download:hover {
      background-color: #218838;
    }
    .search-bar {
      max-width: 500px;
      margin: 0 auto 2rem;
    }
    .no-files {
      animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    .spinner {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
    .thumbnail-container {
      width: 80px;
      height: 80px;
      overflow: hidden;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #fff;
    }
    .thumbnail-container img, .thumbnail-container video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .thumbnail-container audio {
      width: 100%;
    }
    .thumbnail-container i {
      font-size: 2.5rem;
      color: #6c757d;
    }
    .card-header-content {
      flex: 1;
    }
    .modal-content {
      border-radius: 12px;
    }
    .modal-body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 400px; /* Ensure enough space for content */
      padding: 1rem;
    }
    #previewContent {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      height: 100%;
    }
    #previewContent img, #previewContent video {
      max-width: 100%;
      max-height: 500px; /* Limit height to prevent overflow */
      object-fit: contain; /* Preserve aspect ratio */
    }
    #previewContent audio {
      width: 100%;
      max-width: 500px; /* Limit audio player width */
    }
    #previewContent iframe {
      width: 100%;
      max-width: 100%;
      height: 500px;
      border: none;
    }
  </style>
</head>
<body>
<div class="container my-5">
  <div class="text-center mb-4">
    <h2 class="display-6 fw-bold">Media Files: <span class="text-primary"><?php echo htmlspecialchars($title); ?></span></h2>
    <p class="text-muted">Browse and download available <?php echo htmlspecialchars($title); ?> files</p>
  </div>

  <div class="search-bar">
    <div class="input-group">
      <span class="input-group-text"><i class="fas fa-search"></i></span>
      <input type="text" class="form-control" id="searchInput" placeholder="Search by title, project, or language...">
    </div>
  </div>

  <div class="spinner" id="loadingSpinner">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <div class="row" id="mediaFiles">
    <?php if (!empty($media_files)): ?>
      <?php foreach ($media_files as $file): ?>
        <div class="col-md-6 col-lg-4 mb-4 file-card" 
             data-title="<?php echo htmlspecialchars($file['title']); ?>" 
             data-project="<?php echo htmlspecialchars($file['project_name']); ?>" 
             data-language="<?php echo htmlspecialchars($file['language']); ?>">
          <div class="card h-100 shadow-sm">
            <div class="card-header text-white">
              <h6 class="mb-0"><i class="fas fa-folder me-2"></i> <?php echo htmlspecialchars($file['project_name']); ?></h6>
            </div>
            
            <div class="card-header text-white">
            <div class="row">
                    <div class="col"> <div class="thumbnail-container">                
                <?php
                  
                 $previewable = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'mp3', 'wav', 'pdf'];
                 $viewable = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'mp3', 'wav', 'pdf', 'docx', 'ppt', 'pptx'];
                  $extension = strtolower($file['file_extension']);
                  $previewable = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'mp3', 'wav', 'pdf'];
                  $logos = [
                    'apk' => 'fa-android',
                    'docx' => 'fa-file-word',
                    'csv' => 'fa-solid fa-file-csv',
                    'ppt' => 'fa-file-powerpoint',
                    'pptx' => 'fa-file-powerpoint'
                  ];
                ?>
                <?php if (in_array($extension, $previewable)): ?>
                  <?php if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="<?php echo base_url('public/' . $file['file_url']); ?>" alt="Thumbnail">
                  <?php elseif (in_array($extension, ['mp4', 'webm'])): ?>
                    <video muted>
                      <source src="<?php echo base_url('public/' . $file['file_url']); ?>" type="video/<?php echo $extension; ?>">
                    </video>
                  <?php elseif (in_array($extension, ['mp3', 'wav'])): ?>
                    <i class="fas fa-file-audio"></i>
                  <?php elseif ($extension === 'pdf'): ?>
                    <i class="fas fa-file-pdf"></i>                     
                  <?php endif; ?>                   
                <?php elseif (array_key_exists($extension, $logos)): ?>
                  <i class="<?php echo $logos[$extension]; ?>"></i>
                <?php else: ?>
                  <i class="fas fa-file"></i>
                <?php endif; ?>                 
              </div></div>
                    <div class="col-8">
                    <p><i class="fas fa-language me-2"></i><strong>Language:</strong> <?php echo htmlspecialchars($file['language']); ?></p>
              <p><i class="fas fa-calendar-alt me-2"></i><strong>Year:</strong> <?php echo htmlspecialchars($file['year_of_publish']); ?></p>
              <p><i class="fas fa-user me-2"></i><strong>Uploaded By:</strong> 
              <!-- </?php echo htmlspecialchars($file['uploaded_by']); ?> -->
               Admin
            </p>
                    </div>
                </div>
             
            </div>
            <div class="card-body">
              <!-- <p><i class="fas fa-language me-2"></i><strong>Language:</strong> </?php echo htmlspecialchars($file['language']); ?></p>
              <p><i class="fas fa-calendar-alt me-2"></i><strong>Year:</strong> </?php echo htmlspecialchars($file['year_of_publish']); ?></p>
              <p><i class="fas fa-user me-2"></i><strong>Uploaded By:</strong> </?php echo htmlspecialchars($file['uploaded_by']); ?></p>
              <hr> -->
              <h6 class="text-secondary mb-3">Media Info</h6>
              <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Title:</strong> <span data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($file['title']); ?>"><?php echo htmlspecialchars($file['title']); ?></span></li>
                <li class="list-group-item"><strong>Description:</strong> <span data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($file['description']); ?>"><?php echo htmlspecialchars($file['description']); ?></span></li>
                <li class="list-group-item"><strong>File Type:</strong> <?php echo htmlspecialchars($file['file_type']); ?></li>
                <li class="list-group-item"><strong>File Extension:</strong> <?php echo htmlspecialchars($file['file_extension']); ?></li>
                <li class="list-group-item"><strong>File Size:</strong> <?php echo htmlspecialchars($file['file_size']); ?> KB</li>
              </ul>

                <!-- view button -->
                <?php if (in_array($extension, $viewable) && $extension !== 'apk'): ?>
                  <?php if (in_array($extension, ['docx', 'ppt', 'pptx'])): ?>
                    <!-- <a href="</?php echo urlencode(base_url('public/' . $file['file_url'])); ?>&embedded=true" 
                       class="btn btn-view w-50" target="_blank">
                      <i class="fas fa-eye me-2"></i> View
                    </a> -->
                  <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary mb-3 btn-view w-100" 
                            data-bs-toggle="modal" 
                            data-bs-target="#previewModal" 
                            data-file-url="<?php echo base_url($file['file_url']); ?>" 
                            data-file-type="<?php echo $extension; ?>">
                      <i class="fas fa-eye me-2"></i> View
                    </button>
                  <?php endif; ?>
                <?php endif; ?>

              <a href="<?php echo base_url('public/' . $file['file_url']); ?>" class="btn btn-download w-100" download>
                <i class="fas fa-download me-2"></i> Download File
              </a>
            
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning text-center no-files" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i>No media files found for this type.
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">File Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="previewContent"></div>
      </div>
    </div>
  </div>
</div>


<script>
  // Enable tooltips
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

  // Search functionality
  document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const fileCards = document.querySelectorAll('.file-card');
    
    fileCards.forEach(card => {
      const title = card.dataset.title.toLowerCase();
      const project = card.dataset.project.toLowerCase();
      const language = card.dataset.language.toLowerCase();
      
      if (title.includes(searchTerm) || project.includes(searchTerm) || language.includes(searchTerm)) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  });

  // Simulate loading
  window.addEventListener('load', () => {
    const spinner = document.getElementById('loadingSpinner');
    spinner.style.display = 'block';
    setTimeout(() => {
      spinner.style.display = 'none';
    }, 500);
  });

  // Preview modal content
  document.querySelectorAll('.btn-view[data-bs-toggle="modal"]').forEach(button => {
    button.addEventListener('click', function() {
      let fileUrl = this.dataset.fileUrl;
      const fileType = this.dataset.fileType.toLowerCase();
      const previewContent = document.getElementById('previewContent');
      
      previewContent.innerHTML = '';
      
      if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
        // Display image
        
        fileUrl = fileUrl.replace('/storage/', '/public/storage/');
        
        previewContent.innerHTML = `
            <div class="d-flex justify-content-center align-items-center" style="width: 100%; min-height: 500px;">
                <img 
                src="${fileUrl}" 
                alt="Preview" 
                style="max-width: 100%; max-height: 500px; width: 700px; height: 500px;" 
                class="img-fluid rounded shadow"
                >
            </div>
            `;

      } else if (['mp4', 'webm'].includes(fileType)) {
        fileUrl = fileUrl.replace('/storage/', '/public/storage/');
        previewContent.innerHTML = `
          <video controls>
            <source src="${fileUrl}" type="video/${fileType}">
            Your browser does not support the video tag.
          </video>`;
      } else if (['mp3', 'wav'].includes(fileType)) {
        fileUrl = fileUrl.replace('/storage/', '/public/storage/');
        previewContent.innerHTML = `
          <audio controls>
            <source src="${fileUrl}" type="audio/${fileType === 'mp3' ? 'mpeg' : fileType}">
            Your browser does not support the audio tag.
          </audio>`;
      } else if (fileType === 'pdf') {
        fileUrl = fileUrl.replace('/storage/', '/public/storage/');
        previewContent.innerHTML = `<iframe src="${fileUrl}" title="PDF Preview"></iframe>`;
      }
    });
  });
</script>
</body>
</html>