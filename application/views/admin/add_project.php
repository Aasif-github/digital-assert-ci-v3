<button class="btn btn-secondary btn-sm float-end" onclick="window.history.back()"><i class="fa-solid fa-arrow-left"></i>&nbsp;Back</button>
<h2 class="my-4">Add New Project</h2>
<?php echo form_open_multipart('admin/store'); ?>
    <div class="mb-3">
        <label for="project_name" class="form-label">Project Name</label>
        <input type="text" class="form-control" id="project_name" name="project_name" value="<?php echo set_value('project_name'); ?>" required>
    </div>
    <div class="mb-3">
        <label for="project_thumbnail" class="form-label">Project Thumbnail (jpg, jpeg, png, max 2MB)</label>
        <input type="file" class="form-control" id="project_thumbnail" name="project_thumbnail">
    </div>
    <div class="mb-3">
        <label for="project_short_description" class="form-label">Short Description</label>
        <input type="text" class="form-control" id="project_short_description" name="project_short_description" value="<?php echo set_value('project_short_description'); ?>">
    </div>
    <div class="mb-3">
        <label for="project_long_description" class="form-label">Long Description</label>
        <textarea class="form-control" id="project_long_description" name="project_long_description"><?php echo set_value('project_long_description'); ?></textarea>
    </div>
    <!-- <div class="mb-3">
        <label for="language" class="form-label">Language</label>
        <input type="text" class="form-control" id="language" name="language" value="</?php echo set_value('language'); ?>">
    </div>
    <div class="mb-3">
        <label for="year_of_publish" class="form-label">Year of Publish</label>
        <input type="date" class="form-control" id="year_of_publish" name="year_of_publish" value="</?php echo set_value('year_of_publish'); ?>">
    </div> -->

    <div class="row">
    <div class="col-md-6 mb-3">
        <label for="language" class="form-label">Language</label>
        <input type="text" class="form-control" id="language" name="language" value="<?php echo set_value('language'); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label for="year_of_publish" class="form-label">Year of Publish</label>
        <input type="date" class="form-control" id="year_of_publish" name="year_of_publish" value="<?php echo set_value('year_of_publish'); ?>">
    </div>
</div>


    <div id="media-files-container">
        <div class="mb-3 media-file">
            <label class="form-label">Media File</label>
            <input type="file" class="form-control" name="new_media_files[]">
            <input type="text" class="form-control mt-2" name="new_media_titles[]" placeholder="Media Title" required>
            <textarea class="form-control mt-2" name="new_media_descriptions[]" placeholder="Media Description"></textarea>
        </div>
    </div>
    <button type="button" class="btn btn-secondary" onclick="addMediaFile()">Add Another Media File</button>
    <button type="submit" class="btn btn-primary">Create Project</button>
<?php echo form_close(); ?>
<br/>
<br/>
<script>
function addMediaFile() {
    const container = document.getElementById('media-files-container');
    const div = document.createElement('div');
    div.className = 'mb-3 media-file';
    div.innerHTML = `
        <label class="form-label">Media File</label>
        <input type="file" class="form-control" name="new_media_files[]">
        <input type="text" class="form-control mt-2" name="new_media_titles[]" placeholder="Media Title" required>
        <textarea class="form-control mt-2" name="new_media_descriptions[]" placeholder="Media Description"></textarea>
        <button type="button" class="btn btn-danger mt-2" onclick="this.parentElement.remove()">Remove</button>
    `;
    container.appendChild(div);
}
</script>