<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <h3>Update User</h3>
            <hr>
            <!-- Display flash messages -->
            <?php if ($this->session->flashdata('message')): ?>
                <div class="alert alert-info">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>
            
            <!-- Display validation errors -->
            <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

            <form action="<?php echo site_url('update/user/' . $user->id); ?>" method="post" class="row g-3">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo set_value('name', $user->name); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo set_value('email', $user->email); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo set_value('username', $user->username); ?>" required>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password <small>(leave blank to keep unchanged)</small></label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirm Password <small>(leave blank to keep unchanged)</small></label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="role_id" class="form-label">Role</label>
                    <select id="role_id" name="role_id" class="form-select" required>
                        <option value="">Choose Role</option>
                        <?php foreach ($roles as $role): ?>
                            <?php if ($role->role_id != 1): ?>
                                <option value="<?php echo $role->role_id; ?>" <?php echo ($user->role_id == $role->role_id) ? 'selected' : ''; ?>>
                                    <?php echo $role->role_name; ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="<?php echo site_url('view-user'); ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>