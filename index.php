<?php
session_start();
require_once './admin/assets/php/query.php';
$query = new Query();

if (isset($_SESSION["eboto_student"])) {
  header('location: vote.php');
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
  <link rel="stylesheet" href="./libs/bootstrap.min.css">
  <link rel="stylesheet" href="./libs/icons-1.11.1/font/bootstrap-icons.css">
  <title>Student|Voting System</title>
</head>

<body>
  <header></header>
  <main>
    <div class="container">
      <div class="row justify-content-center align-items-center" style="height:100dvh;">
        <div class="col-12">
          <input type="hidden" id="loginErr" value="<?=$msg?>">
          <div class="d-flex justify-content-center align-items-center mb-5 w-100">
            <img src="./admin/assets/img/siit.png" class="img-fluid me-5" alt="siit logo" style="width:120px;">
            <img src="./admin/assets/img/ssc logo sm.png" class="img-fluid" alt="siit ssc logo" style="width:120px;">
          </div>
          <div class="card mx-auto rounded-0 border border p-md-3 p-1 shadow" style="max-width:400px;">
            <div class="card-body">
              <h3 class="text-success fw-bold text-center">Voting System</h3>
              <p class="text-center text-success mb-4">Student Panel</p>
              <form id="stud-login-form">
                <!-- <label class="form-label text-muted" for="">Username:</label>
                <input type="text" name="user-stud" class="form-control" autofocus required> -->

                <label class="form-label text-muted mt-2" for="">Password:</label>
                <input autofocus type="password" name="user-pass" class="form-control" required>

                <div class="d-grid">
                  <button type="submit" id="stud-login-btn" class="btn btn-success mt-3 rounded-0">Login</button>
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
  <footer>
  </footer>
  <script src="./libs/bootstrap.bundle.min.js"></script>
  <script src="./libs/jquery.min.js"></script>
  <script src="./libs/sweetalert2.all.min.js"></script>
  <script>
    $(document).ready(function() {
      if ($('#loginErr').val() == 'login-first') {
        Swal.fire({
          icon: 'info',
          title: 'Oops...',
          text: 'Please log in first!'
        })
      }
      $('#stud-login-btn').click(function(e) {
        if ($('#stud-login-form')[0].checkValidity()) {
          e.preventDefault()
          $(this).attr('disabled', true)
          $(this).html(`
            <span class="spinner-border spinner-border-sm"></span> Logging in...
          `)
          $.ajax({
            url: './admin/assets/php/action.php',
            method: 'post',
            data: $('#stud-login-form').serialize() + '&action=student-login',
            success: function(res) {
              console.log(res)
              if (res == 'student login') {
                window.location = 'vote.php'
              } else if (res == 'invalid pass') {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Incorrect password!',
                })
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Something went wrong, try again.',
                })
              }
              $('#stud-login-btn').html('Login')
              $('#stud-login-btn').attr('disabled', false)
            }
          })
        }
      })
    })
  </script>
</body>

</html>