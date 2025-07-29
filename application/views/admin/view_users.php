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
    /* toggle button */
    .switch {
            position: relative;
            display: inline-block;
            width: 40px; /* Reduced from 60px */
            height: 24px; /* Reduced from 34px */
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;            
            background-color: #f44336; Red when off
            transition: 0.4s;
            border-radius: 24px; /* Adjusted for smaller size */
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px; /* Reduced from 26px */
            width: 18px; /* Reduced from 26px */
            left: 3px; /* Adjusted for smaller size */
            bottom: 3px; /* Adjusted for smaller size */
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #04AA6D; /* Green when on */
        }

        input:checked + .slider:before {
            transform: translateX(16px); /* Adjusted for smaller width */
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
            <th>Status</th>      
            <th>Change Status</th>                  
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
                    <td><?php echo $user['is_active'] == 1 ? 'Active' : 'Inactive'; ?></td>
                    <td>
                        <label class="switch">
                        <input type="checkbox" class="status-toggle" data-user-id="<?php echo $user['id']; ?>" 
                            <?php echo $user['is_active'] == 1 ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                        </label>
                    </td>
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

<script>
    document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                const currentStatus = this.checked ? 1 : 0;
                console.log(userId, currentStatus);

                fetch('<?php echo base_url('index.php/admin/update_status'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `user_id=${userId}&current_status=${currentStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const statusCell = this.closest('tr').children[5];
                        statusCell.textContent = data.new_status == 1 ? 'Active' : 'Inactive';
                        alert(data.message);
                    } else {
                        alert(data.message);
                        this.checked = !this.checked; // Revert toggle on failure
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating status.');
                    this.checked = !this.checked; // Revert toggle on error
                });
            });
        });
  </script>

