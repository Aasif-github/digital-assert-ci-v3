<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
</head>
<body>

<form class="row g-3" action="<?php echo site_url('create-user'); ?>" method="post">
  <div class="col-12">
    
    <div class="col-md-6 mx-auto" >
        <h3>Add User</h3>
        <hr>
        <!-- session flash message  -->
        <?php echo $this->session->flashdata('message'); ?>

        <div>
            <label for="" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" id="" required>
        </div>
        <div class="mt-3">
            <label for="" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="" required>
        </div>
        <div class="mt-3">
            <label for="" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="" required>
        </div>
        <div class="mt-3 row">
        <div class="col-md-6">
            <label for="" class="form-label">Password</label>
            <input type="text" name="password" class="form-control" id="" required>
        </div>
        <div class="col-md-6">
            <label for="" class="form-label">Confirm Password</label>
            <input type="text" name="confirm_password" class="form-control" id="" required>
        </div>
        </div>        
        <div class="mt-3">
            <label for="inputState" class="form-label">Role</label>
            <select id="inputState" name="role_id" class="form-select" required>
            <option value="2" selected>Choose Role</option>
            <?php foreach ($roles as $role) { if($role->role_id != 1) { ?> <option value="<?=$role->role_id?>"><?=$role->role_name?></option> <?php } }?>            
            </select>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Submit</button>      
    </div>
  </div>
  </div>
</form>
</body>
</html>