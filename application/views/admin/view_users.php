<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
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
        width: 500px !important; /* Adjust width as needed */
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
</style>

<h2 class="my-4">All Users</h2>
<!-- <p>Total Users: </?php echo $total_users; ?></p> -->
<!-- Display flash messages -->
<?php if ($this->session->flashdata('message')): ?>
                <div class="alert alert-info">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>
            
            <!-- Display validation errors -->
            <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
<table class="table table-bordered table-striped" id="myTable">
    <thead class="table-dark">
        <tr>
            <th>Sr_No</th>
            <th>Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>            
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php $sr = 1; ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($user['role'] ?? 'N/A'); ?></td>
                    <td>
                        <!-- <a href="</?php echo site_url('admin/user/' . $user['id']); ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-eye"></i></a> -->
                        <a href="<?php echo site_url('edit/user/' . $user['id']); ?>" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="<?php echo site_url('destroy/user/' . $user['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No users found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    let table = new DataTable('#myTable');
</script>