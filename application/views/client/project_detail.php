<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title); ?> - Digital Asset Management</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
    <style>
        .media-preview {
            max-height: 300px;
            width: 100%;
            object-fit: contain;
        }
        .csv-table {
            max-height: 200px;
            overflow-y: auto;
        }
        .card-img-top {
            max-height: 150px;
            object-fit: cover;
        }
        .card-body {
            min-height: 150px;
        }
        .accordion-button {
            font-weight: bold;
        }
        .accordion-item {
            margin-bottom: 10px;
        }
    </style>
    <?php $this->load->view('client/header'); ?>
</head>
<body>
    <div class="container">
        <h1 class="my-4"><?php echo htmlspecialchars($project['project_name']); ?></h1>
        
        <a href="<?php echo site_url('client'); ?>" class="btn btn-secondary mt-3 float-end">Back to Projects</a>
        
        <div class="row">
            <div class="col-md-8">
                <?php if ($project['project_thumbnail']): ?>
                    <img src="<?php echo base_url('public/' . $project['project_thumbnail']); ?>" class="img-fluid mb-3" alt="<?php echo htmlspecialchars($project['project_name']); ?>">
                <?php endif; ?>
                <p><strong>Short Description:</strong> <?php echo htmlspecialchars($project['project_short_description'] ?: 'N/A'); ?></p>
                <p><strong>Long Description:</strong> <?php echo htmlspecialchars($project['project_long_description'] ?: 'N/A'); ?></p>
                <p><strong>Language:</strong> <?php echo htmlspecialchars($project['language'] ?: 'N/A'); ?></p>
                <p><strong>Year of Publish:</strong> <?php echo $project['year_of_publish'] ? date('Y', strtotime($project['year_of_publish'])) : 'N/A'; ?></p>
                <p><strong>Created by:</strong> <?php echo htmlspecialchars($project['username'] ?? 'Unknown'); ?></p>
            </div>
        </div>
        <h3 class="my-4">Media Files</h3>
        <?php
        // Group media files by type
        $media_groups = [
            'Audio' => [],
            'Video' => [],
            'Documents' => [],
            'Images' => [],
            "Apk" => [],
            'Others' => []
        ];
        $media_types = [
            'Audio' => ['mp3'],
            'Video' => ['mp4', '3gp'],
            'Documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt', 'xls', 'xlsx', 'csv', 'ppt', 'pptx'],
            'Images' => ['jpg', 'jpeg', 'png', 'gif'],
            'Apk' => ['apk', 'zip'],
        ];
        foreach ($media_files as $media) {
            $file_extension = strtolower($media['file_extension']);
            $placed = false;
            foreach ($media_types as $type => $extensions) {
                if (in_array($file_extension, $extensions)) {
                    $media_groups[$type][] = $media;
                    $placed = true;
                    break;
                }
            }
            if (!$placed) {
                $media_groups['Others'][] = $media;
            }
        }
        ?>
        <?php if (array_sum(array_map('count', $media_groups)) > 0): ?>
            <div class="accordion" id="mediaAccordion">
                <?php $first_non_empty = true; ?>
                <?php foreach ($media_groups as $type => $files): ?>
                    <?php if (!empty($files)): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $type; ?>">
                                <button class="accordion-button <?php echo $first_non_empty ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $type; ?>" aria-expanded="<?php echo $first_non_empty ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $type; ?>">
                                    <?php echo $type; ?> (<?php echo count($files); ?>)
                                </button>
                            </h2>
                            <div id="collapse<?php echo $type; ?>" class="accordion-collapse collapse <?php echo $first_non_empty ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $type; ?>" data-bs-parent="#mediaAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <?php foreach ($files as $media): ?>
                                            <div class="col-md-4 mb-4">
                                                <div class="card">
                                                    <?php
                                                    $file_url = base_url('admin/download/' . $media['id']);
                                                    $file_extension = strtolower($media['file_extension']);
                                                    ?>
                                                    <?php if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                        <img src="<?php echo base_url('public/' . $media['file_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($media['title']); ?>">
                                                    <?php elseif ($file_extension === 'mp3'): ?>
                                                        <div class="p-3">
                                                            <audio controls class="media-preview">
                                                                <source src="<?= base_url('public/' . $media['file_url']); ?>" type="<?php echo htmlspecialchars($media['mime_type'] ?: 'audio/mpeg'); ?>">
                                                                Your browser does not support the audio element.
                                                            </audio>
                                                        </div>
                                                    <?php elseif (in_array($file_extension, ['mp4', '3gp'])): ?>
                                                        <div class="p-3">
                                                            <video controls class="media-preview">
                                                                <source src="<?= base_url('public/' . $media['file_url']); ?>" type="<?php echo htmlspecialchars($media['mime_type'] ?: 'video/mp4'); ?>">
                                                                Your browser does not support the video element.
                                                            </video>
                                                        </div>
                                                    <?php elseif ($file_extension === 'pdf'): ?>
                                                        <div class="p-3">
                                                            <iframe src="<?= base_url('public/' . $media['file_url']); ?>" class="media-preview" title="<?php echo htmlspecialchars($media['title']); ?>"></iframe>
                                                        </div>
                                                    <?php elseif ($file_extension === 'csv'): ?>
                                                        <div class="p-3 csv-table" id="csv-preview-<?php echo $media['id']; ?>"></div>
                                                        <?php elseif (in_array($file_extension, ['doc', 'docx'])): ?>
                                                        <!-- <div class="p-3">
                                                            <iframe src="https://docs.google.com/viewer?url=</?php echo urlencode(base_url('public/' . $media['file_url'])); ?>&embedded=true" class="media-preview" title="</?php echo htmlspecialchars($media['title']); ?>"></iframe>
                                                           
                                                        </div> -->
                                                        <div class="card-img-top text-center p-3 bg-light">File: <?php echo htmlspecialchars($media['title']); ?></div>
                                                    <?php else: ?>
                                                        <div class="card-img-top text-center p-3 bg-light">File: <?php echo htmlspecialchars($media['title']); ?></div>
                                                    <?php endif; ?>
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($media['title'] ?: 'Untitled'); ?></h5>
                                                        <p class="card-text"><?php echo htmlspecialchars($media['description'] ?: 'No description'); ?></p>
                                                        <p>Type: <?php echo htmlspecialchars($media['file_type']); ?> | Size: <?php echo round($media['file_size'] / 1024, 2); ?> KB</p>
                                                        <p>Uploaded by: <?php echo htmlspecialchars($media['username'] ?? 'Unknown'); ?></p>
                                                        <a href="<?php echo base_url('public/' . $media['file_url']); ?>" class="btn btn-primary" download>Download</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $first_non_empty = false; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No media files found for this project.</p>
        <?php endif; ?>
      
    </div>
    <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.3.2/papaparse.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php foreach ($media_files as $media): ?>
                <?php if (strtolower($media['file_extension']) === 'csv'): ?>
                    $.ajax({
                        url: '<?php echo base_url('public/' . $media['file_url']); ?>',
                        dataType: 'text',
                        success: function(data) {
                            var result = Papa.parse(data, { header: true });
                            if (result.data.length > 0) {
                                var table = $('<table class="table table-sm table-bordered"></table>');
                                var thead = $('<thead><tr></tr></thead>');
                                var tbody = $('<tbody></tbody>');
                                var headers = Object.keys(result.data[0]);
                                headers.forEach(function(header) {
                                    thead.find('tr').append('<th>' + $('<div>').text(header).html() + '</th>');
                                });
                                result.data.forEach(function(row, index) {
                                    if (index < 5) { // Limit to 5 rows
                                        var tr = $('<tr></tr>');
                                        headers.forEach(function(header) {
                                            tr.append('<td>' + $('<div>').text(row[header] || '').html() + '</td>');
                                        });
                                        tbody.append(tr);
                                    }
                                });
                                table.append(thead).append(tbody);
                                $('#csv-preview-<?php echo $media['id']; ?>').html(table);
                            }
                        },
                        error: function() {
                            $('#csv-preview-<?php echo $media['id']; ?>').html('<p>Error loading CSV preview.</p>');
                        }
                    });
                <?php endif; ?>
            <?php endforeach; ?>
        });
    </script>
</body>
</html>