<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

<h2 class="my-4">Admin Dashboard</h2>
<p>Total Projects: <?php echo $total_projects; ?></p>

<table class="table table-bordered table-striped" id="myTable">
    <thead class="table-dark">
        <tr>
            <th>Sr_No</th>
            <th>Project Thumbnail</th>
            <th>Project_Name</th>
            <th>Assets</th>
            <th>Uploaded_By</th>
            <th>Published_At</th>
            <th>____Actions____</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($projects)): ?>
            <?php $sr = 1; ?>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td>
                        <?php if ($project['project_thumbnail']): ?>
                            <img src="<?php echo base_url('public/' . $project['project_thumbnail']); ?>" alt="<?php echo $project['project_name']; ?>" style="height: 60px;">
                        <?php else: ?>
                            <span>No Image</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $project['project_name']; ?>
                    </td>
                    <td>
                        
                    
                        <?php foreach ($project['file_types'] as $type => $count): ?>
                            <span class="badge rounded-pill bg-success me-1">
                                <?php echo "$type: $count"; ?>
                            </span>
                        <?php endforeach; ?>
                   
                         
                    </td>
                    <td>
                        <?php echo $project['uploaded_by_name'] ?? 'Admin'; ?>
                    </td>
                    <td>
                        <?php echo $project['year_of_publish'] ? date('Y', strtotime($project['year_of_publish'])) : 'N/A'; ?>
                    </td>
                    <td>
                        <a href="<?php echo site_url('admin/project/' . $project['id']); ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-eye"></i></a>
                        <a href="<?php echo site_url('admin/edit/' . $project['id']); ?>" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="<?php echo site_url('admin/destroy/' . $project['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this project?');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No projects found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
 
<script>
    let table = new DataTable('#myTable');

</script>