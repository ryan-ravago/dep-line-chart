<?php
session_start();
if (isset($_SESSION["eboto_admin"])) {
  header('location: dashboard.php');
  exit();
}

$msg = '';

if (isset($_GET['message']) && $_GET['message'] == 'loginError') {
  $msg = 'login-first';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="author" content="SNSU">
  <meta name="description" content="SNSU Voting System">
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../libs/bootstrap.min.css">
  <link rel="stylesheet" href="../libs/icons-1.11.1/font/bootstrap-icons.css">
  <title>Admin|Voting System</title>
</head>

<body>
  <header></header>
  <main>
    <div class="container">
      <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-12 my-auto w-100">
          <input type="hidden" id="loginErr" value="<?=$msg?>">
          <div class="d-flex justify-content-center align-items-center mb-5">
            <img src="assets/img/siit.png" class="img-fluid me-5" alt="siit logo" style="width:120px;">
            <img src="assets/img/ssc logo sm.png" class="img-fluid" alt="siit ssc logo" style="width:120px;">
          </div>
          <div class="card mx-auto rounded-0 border border p-md-3 p-1 shadow" style="max-width:400px;">
            <div class="card-body">
              <h3 class="text-success fw-bold text-center">E-BOTO SYSTEM</h3>
              <p class="text-center text-success mb-4">Admin Panel</p>
              <form id="admin-login-form">
                <label class="form-label text-muted" for="">Username:</label>
                <input type="text" name="user-admin" class="form-control" autofocus required>

                <label class="form-label text-muted mt-2" for="">Password:</label>
                <input type="password" name="user-pass" class="form-control" required>

                <div class="d-grid">
                  <button type="submit" id="admin-login-btn" class="btn btn-success mt-3 rounded-0">Login</button>
                </div>
              </form>
            </div>
          </div>
          <p class="text-center mt-4">Developed by: 
            <a href="https://www.facebook.com/profile.php?id=100008149626501" target="_blank" class="">Click to see</a>
          </p>
        </div>
      </div>
    </div>
  </main>
  <footer></footer>
  <script src="../libs/bootstrap.bundle.min.js"></script>
  <script src="../libs/jquery.min.js"></script>
  <script src="../libs/sweetalert2.all.min.js"></script>
  <script>
    $(document).ready(function() {
      if ($('#loginErr').val() == 'login-first') {
        Swal.fire({
          icon: 'info',
          title: 'Oops...',
          text: 'Please log in first!'
        })
      }
      $('#admin-login-btn').click(function(e) {
        if ($('#admin-login-form')[0].checkValidity()) {
          e.preventDefault()
          $(this).attr('disabled', true)
          $(this).html(`
            <span class="spinner-border spinner-border-sm"></span> Logging in...
          `)
          
          $.ajax({
            url: './assets/php/action.php',
            method: 'post',
            data: $('#admin-login-form').serialize() + '&action=admin-login',
            success: function(res) {
              if (res) {
                window.location = 'dashboard.php'
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Incorrect username or password!',
                })
              }
              $('#admin-login-btn').html('Login')
              $('#admin-login-btn').attr('disabled', false)
            }
          })
        }
      })
    })
  </script>
</body>

</html>