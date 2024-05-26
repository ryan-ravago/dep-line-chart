<?php
  $members = [
    array(
      'name' => 'Harold N. Dongallo',
      'img' => 'harold.jpg',
      'role' => 'Programmer',
      'saying' => '"Programming is the closest thing we have to magic; we create something out of nothing with the stroke of a keyboard."'
    ),
    array(
      'name' => 'Jannah Luz T. Polican',
      'img' => 'jannah.jpg',
      'role' => 'Web Designer',
      'saying' => '"In the language of the web, designers are poets, creating verses with color, code, and creativity."'
    ),
    array(
      'name' => 'Michelle Ann T. Antoni',
      'img' => 'michelle.jpg',
      'role' => 'System Analyst',
      'saying' => '"A system analyst\'s vision sees beyond the code, envisioning a future where systems empower and transform."'
    ),
  ]
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Developers</title>
  <link rel="stylesheet" href="../libs/bootstrap.min.css">
  <link rel="stylesheet" href="../libs/icons-1.11.1/font/bootstrap-icons.css">
</head>
<body>
  <div class="bg-light border border-bottom-5 p-3">
    <h1 class="text-center">Combatech Team Members</h1>
  </div>

  <div class="container">
    <div class="row justify-content-center gap-3 my-4">
      <?php 
        $output = '';
        foreach ($members as $member) {
          $output .= "<div class='col-md-3'>
            <div class='card rounded-0 h-100'>
              <img src='./assets/img/{$member['img']}' class='card-img-top' alt='...' style='object-fit:cover;height:260px;'>
              <div class='card-body bg-success'>
                <h5 class='card-title text-white'>{$member['name']}</h5>
                <h6 class='card-subtitle' style='color:#232F13'>{$member['role']}</h6>
              </div>
              <div class='card-footer h-100'>
                <small class='card-text text-body-secondary fw-semibold'>{$member['saying']}</small>
              </div>
            </div>
          </div>";
        }
        echo $output;
      ?>
    </div>
    <a href="./index.php" class="text-center d-block my-4 fs-6">
      <i class="bi bi-arrow-left"></i> Go back
    </a>
  </div>
</body>
</html>