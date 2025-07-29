<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Admin - Digital Asset Management'; ?> - DAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Offline Font Awesome CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/fontawesome-free-6.7.2-web/css/all.min.css'); ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo site_url('admin'); ?>">ZMQ Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="</?php echo site_url('client'); ?>">Home</a>
                    </li>     -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('admin'); ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('admin/show'); ?>">Add Project</a>
                    </li>
                    <div class="dropdown">
                        <button type="button" class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown">
                            Manage Users
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo site_url('add-user'); ?>">Add User</a></li>
                            <li><a class="dropdown-item" href="<?php echo site_url('view-user'); ?>">View Users</a></li>
                            <!-- <li><a class="dropdown-item" href="#">Assign Roles</a></li>     -->
                        </ul>
                    </div>

                    <!-- Add more admin links as needed -->
                </ul>
            </div>
            <!-- User Dropdown -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavRight">
                <ul class="navbar-nav">
                    <?php if ($this->session->userdata('logged_in')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $this->session->userdata('username'); ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <!-- <a class="dropdown-item" href="#">Profile</a> -->
                                <a class="dropdown-item" href="<?php echo site_url('logout'); ?>">Logout</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('login'); ?>">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <?php
    $session_data = $this->session->all_userdata();
    // print_r($session_data);
    ?>

    <div class="container mt-4">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>
    <!-- Bootstrap JS (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>