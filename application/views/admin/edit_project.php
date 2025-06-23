<h1 class="my-4">Edit Project: <?php echo htmlspecialchars($project['project_name']); ?></h1>
<?php echo form_open_multipart('admin/update/' . $project['id']); ?>
    <input type="hidden" name="deleted_media_ids" id="deleted_media_ids">
    <div class="mb-3">
        <label for="project_name" class="form-label">Project Name</label>
        <input type="text" class="form-control" id="project_name" name="project_name" value="<?php echo htmlspecialchars(set_value('project_name', $project['project_name'])); ?>" required>
    </div>
    <div class="mb-3">
        <label for="project_thumbnail" class="form-label">Project Thumbnail (jpg, jpeg, png, max 2MB)</label>
        <input type="file" class="form-control" id="project_thumbnail" name="project_thumbnail" accept="image/jpeg,image/png">
        <?php if ($project['project_thumbnail']): ?>
            <img src="<?php echo base_url('public/' . $project['project_thumbnail']); ?>" class="img-thumbnail mt-2" style="max-width: 200px;" alt="Project Thumbnail">
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label for="project_short_description" class="form-label">Short Description</label>
        <input type="text" class="form-control" id="project_short_description" name="project_short_description" value="<?php echo htmlspecialchars(set_value('project_short_description', $project['project_short_description'])); ?>">
    </div>
    <div class="mb-3">
        <label for="project_long_description" class="form-label">Long Description</label>
        <textarea class="form-control" id="project_long_description" name="project_long_description"><?php echo htmlspecialchars(set_value('project_long_description', $project['project_long_description'])); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="language" class="form-label">Language</label>
        <input type="text" class="form-control" id="language" name="language" value="<?php echo htmlspecialchars(set_value('language', $project['language'])); ?>">
    </div>
    <div class="mb-3">
        <label for="year_of_publish" class="form-label">Year of Publish</label>
        <input type="date" class="form-control" id="year_of_publish" name="year_of_publish" value="<?php echo htmlspecialchars(set_value('year_of_publish', $project['year_of_publish'])); ?>">
    </div>
    <h4>Existing Media Files</h4>
    <div id="existing-media-files">
        <?php foreach ($project['mediaFiles'] as $index => $media): ?>
            <div class="mb-3 media-file" data-id="<?php echo $media['id']; ?>">
                <input type="hidden" name="existing_media_ids[<?php echo $index; ?>]" value="<?php echo $media['id']; ?>">
                <label class="form-label">Media File: <?php echo htmlspecialchars($media['title']); ?></label>
                <?php if (in_array(strtolower($media['file_extension']), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="<?php echo base_url('public/' . $media['file_url']); ?>" class="img-thumbnail mt-2" style="max-width: 200px;" alt="Media File">
                <?php else: ?>
                    <p>File: <?php echo htmlspecialchars($media['title']); ?> (<?php echo htmlspecialchars($media['file_type']); ?>)</p>
                <?php endif; ?>
                <input type="text" class="form-control mt-2" name="existing_media_titles[<?php echo $index; ?>]" value="<?php echo htmlspecialchars(set_value('existing_media_titles['.$index.']', $media['title'])); ?>" required>
                <textarea class="form-control mt-2" name="existing_media_descriptions[<?php echo $index; ?>]"><?php echo htmlspecialchars(set_value('existing_media_descriptions['.$index.']', $media['description'])); ?></textarea>
                <input type="file" class="form-control mt-2" name="existing_media_files[<?php echo $index; ?>]" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.rtf,.odt,.xls,.xlsx,.csv,.ppt,.pptx,.apk,.zip">
                <button type="button" class="btn btn-danger mt-2" onclick="deleteMediaFile(<?php echo $media['id']; ?>, this)">Delete</button>
            </div>
        <?php endforeach; ?>
    </div>
    <h4>Add New Media Files</h4>
    <div id="media-files-container">
        <div class="mb-3 media-file">
            <input type="file" class="form-control" name="new_media_files[]" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.rtf,.odt,.xls,.xlsx,.csv,.ppt,.pptx,.apk,.zip">
            <input type="text" class="form-control mt-2" name="new_media_titles[]" placeholder="Media Title">
            <textarea class="form-control mt-2" name="new_media_descriptions[]" placeholder="Media Description"></textarea>
            <button type="button" class="btn btn-danger mt-2" onclick="this.parentElement.remove()">Remove</button>
        </div>
    </div>
    <button type="button" class="btn btn-secondary mb-3" onclick="addMediaFile()">Add Another Media File</button>
    <button type="submit" class="btn btn-primary">Update Project</button>
<?php echo form_close(); ?>

<script>
function addMediaFile() {
    const container = document.getElementById('media-files-container');
    const div = document.createElement('div');
    div.className = 'mb-3 media-file';
    div.innerHTML = `
        <input type="file" class="form-control" name="new_media_files[]" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.rtf,.odt,.xls,.xlsx,.csv,.ppt,.pptx,.apk,.zip">
        <input type="text" class="form-control mt-2" name="new_media_titles[]" placeholder="Media Title">
        <textarea class="form-control mt-2" name="new_media_descriptions[]" placeholder="Media Description"></textarea>
        <button type="button" class="btn btn-danger mt-2" onclick="this.parentElement.remove()">Remove</button>
    `;
    container.appendChild(div);
}

function deleteMediaFile(id, button) {
    const deletedIdsInput = document.getElementById('deleted_media_ids');
    let deletedIds = deletedIdsInput.value ? deletedIdsInput.value.split(',') : [];
    deletedIds.push(id);
    deletedIdsInput.value = deletedIds.join(',');
    button.parentElement.remove();
}
</script>