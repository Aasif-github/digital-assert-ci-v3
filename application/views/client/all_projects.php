<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
<!-- ✅ Bootstrap CSS CDN -->


<!-- ✅ Font Awesome -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
<style>
  /* Center and widen the search bar */
  .dt-search {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1em;
  }

  .dt-search input {
    width: 500px !important;
    /* Adjust width as needed */
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
</style>
<div class="container">
  <h2 class="my-4">All Projects</h2>
  <span class="float-end">
    <a href="<?php echo base_url('index.php/client') ?>" class="btn btn-outline-secondary btn-sm mb-3">back</a>
  </span>
  <p>Total Projects: <?php echo $total_projects; ?></p>
  
  <table class="table table-bordered table-striped" id="myTable">
    <thead class="table-success">
      <tr>
        <th>Sr_No</th>
        <th>Project Thumbnail</th>
        <th>Project_Name</th>
        <th>Assets</th>
        <th>Uploaded_By</th>
        <th>Published_At</th>
        <th>Last Updated</th>
        <th>View</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($projects)): ?>
        <!-- <pre></?php var_dump($projects); ?></pre> -->

        <?php $sr = 1; ?>
        <?php foreach ($projects as $project): ?>
          <tr>
            <td><?php echo $sr++; ?></td>
            <td>
              <?php if ($project['project_thumbnail']): ?>
                <img src="<?php echo base_url('public/' . $project['project_thumbnail']); ?>"
                  alt="<?php echo $project['project_name']; ?>" style="height: 60px;">
              <?php else: ?>
                <span>No Image</span>
              <?php endif; ?>
            </td>
            <td>
              <?php echo $project['project_name']; ?>
            </td>
            <td>

          <?php foreach ($project['file_types'] as $type => $info): ?>
            <div class="mb-2">
              <span class="badge bg-success me-2">
                <?= strtoupper($type); ?>
              :
                <?= $info['count']; ?> <?= $info['count'] > 1 ? '' : ''; ?>
              </span>
              <!-- <small class="text-muted ms-2">
              </?= date("d M Y, h:i A", strtotime($info['updated_at'])); ?>
              </small> -->
            </div>
          <?php endforeach; ?>
            </td>
            <td>
              <?php echo $project['uploaded_by_name'] ?? 'Admin'; ?>
            </td>
            <td>
            <?php $timestamp = $project['year_of_publish'] ?? null;
                    echo $timestamp 
                    ? date('F j, Y \a\t g:i A', strtotime($timestamp)) 
              : 'N/A'; ?>              
            </td>
            <td>
              <?php $timestamp = $project['updated_at'] ?? null;
                    echo $timestamp 
                    ? date('F j, Y \a\t g:i A', strtotime($timestamp)) 
              : 'N/A'; ?>
            </td>
            <td>
              <a href="<?php echo site_url('client/project/' . $project['id']); ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-eye"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" class="text-center">No projects found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<script>
  let table = new DataTable('#myTable');

</script>