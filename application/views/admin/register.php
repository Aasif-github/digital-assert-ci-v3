<!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- ✅ Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .back {
        background: linear-gradient(to right, #6a11cb, #2575fc);
        min-height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        }

        .div-center {
        width: 400px;
        height: 400px;
        background-color: #fff;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        margin: auto;
        max-width: 100%;
        max-height: 100%;
        overflow: auto;
        padding: 1em 2em;
        border-bottom: 2px solid #ccc;
        display: table;
        }

        div.content {
        display: table-cell;
        vertical-align: middle;
        }
        label {
        font-weight: bold;
        color: #212529;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        }

    </style>
 </head>
 <body>
 <div class="back">


<div class="div-center">
  <div class="content">
    <h3 style="text-align: center;">Signup</h3>
    <hr />
    <form method="post" action="<?php echo site_url('userRegistration'); ?>">
    <div class="form-group">
        <label for="exampleInputEmail1">Username</label>
        <input type="text" class="form-control" name="username" id="username" placeholder="Username">
      </div>
      <br>
      <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" class="form-control" name="email" id="" placeholder="Email">
      </div>
      <br>
      <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <div class="input-group mb-3">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        <div class="input-group-append">
          <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
            👁️
          </button>
        </div>
      </div>
      </div>
      <br>
      <button type="submit" class="btn btn-outline-primary">Sign up</button>
      <hr />
      <a type="link" class="btn btn-link" href="<?php echo site_url('login'); ?>"   >Login</a>

      <!-- <button type="button" class="btn btn-link">Reset Password</button> -->      
    </form>

  </div>


  </span>
  <script>
  function togglePassword() {
    const passwordInput = document.getElementById("password");
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
  }
</script>
</div>
 </body>
 </html>