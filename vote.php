<?php
session_start();
require_once './admin/assets/php/query.php';
$query = new Query();

$studId = $_SESSION['eboto_student'];
$voter = $query->fetchVoter($studId);
// $elec = 0;

if (!isset($_SESSION["eboto_student"])) {
  header('location: index.php?message=loginError');
  exit();
} else {
  $onGoingElec = $query->checkOnGoingElec();
  $elec = $onGoingElec[0];

  // if there's no election going-on then redirect to campaign.php
  if (sizeof($onGoingElec) < 1) { 
    header('location: campaign.php');
  } else {
    // Check if voter have voted already
    if ($query->fetchVoter($_SESSION["eboto_student"])['v_voted'] == 1) {
      header('location: campaign.php');
    }

    // Check if voter does not belong to on-going election event
    if ($elec['el_id'] != $voter['v_el_id']) {
      header('location: campaign.php');
    }
  }
}

$posIds = [];
$positions = $query->fetchPositionsOfElecEvent($elec['el_id']);

foreach ($positions as $pos) {
  array_push($posIds, $pos['pos_id']);
}

$oldCands = $query->fetchCandsBasedOnElec($elec['el_id']);
$cands = []; // with canVotePos
$filteredCands = [];

foreach ($oldCands as $oldCand) {
  $oldCand['canVotePos'] = $query->fetchCanVotePositionsForVoting($oldCand['pos_uniqid']);
  array_push($cands, $oldCand);
}

$studId = $_SESSION['eboto_student'];
$voter = $query->fetchVoter($studId);
$fname = $voter['v_fname'];
$mname = $voter['v_mname'];
$lname = $voter['v_lname'];
$mnameInitial = $voter['v_mname'] ? "{$voter['v_mname']}." : "";
$courseId = $voter['course_id'];

// Only show those candidates that the student can vote based on their courses
foreach ($cands as $cand) {
  $idCourses = [];
  foreach ($cand['canVotePos'] as $canVoteCourse) {
    array_push($idCourses, $canVoteCourse['canpos_course_id']);
  }
  
  // if (array_search($courseId, $idCourses)) {
  //   array_push($filteredCands, $cand);
  // }

  foreach ($idCourses as $id) {
    if ($id == $courseId) {
      array_push($filteredCands, $cand);
    }
  }
}


$result = array();
foreach ($filteredCands as $cand) {
  $result[$cand['pos_id']][] = $cand;
}

$posTabs = '';
$panes = '';

$filteredCanVotePositions = [];

$availPosIds = [];
$filtPositions = [];

foreach ($result as $key => $val) {
  array_push($availPosIds, $key);
}

foreach ($positions as $pos) {
  if (in_array($pos['pos_id'], $availPosIds)) {
    array_push($filtPositions, $pos);
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./libs/bootstrap.min.css">
  <link rel="stylesheet" href="./libs/icons-1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="./libs/Datatables/DataTables-1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="./libs/Datatables/FixedHeader-3.4.0/css/fixedHeader.bootstrap5.min.css">
  <link rel="stylesheet" href="./admin/assets/css/style.css">
  <link rel="stylesheet" href="./libs/summernote/summernote-lite.css">
  <title>
    <?php
      if (basename($_SERVER['PHP_SELF']) == 'dashboard.php') {
        echo 'Dashboard';
      } else if (basename($_SERVER['PHP_SELF']) == 'candidates.php') {
        echo 'Admin | Candidates';
      } else if (basename($_SERVER['PHP_SELF']) == 'positions.php') {
        echo 'Admin | Positions';
      }
    ?>
  </title>
  <style>
    <?php require_once "./admin/assets/css/style.css";?>
  </style>
</head>

<body>
  <header>
    <!-- Start Navbar -->
    <nav class="navbar navbar-dark navbar-expand-lg bg-success fixed-top">
      <div class="container-fluid">
        <button type="button" class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#offcanvas">
          <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand mx-auto" href="#"><?= $elec['el_name']?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link text-white" href="#">
                <i class="bi bi-person-circle"></i>
                <?= $fname?>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> 
                Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    
    <!-- Modal -->
    <div class="modal fade" id="ballot-modal-form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0">
          <div class="modal-header bg-success text-white rounded-0">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Voted Candidates</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php
              $formCands = '';
              foreach ($filtPositions as $pos) {
                $expl = explode(" ", $pos['pos_name']);
                $paneId = implode("_", $expl);
                $inpName = strtolower($paneId);
                $pluralTrue = $pos['pos_cand_many'] > 1 ? 's' : '';

                $formCands .= "<p style='line-height:1'>
                  <span class='text-success'>{$pos['pos_name']}{$pluralTrue}:</span> 
                  <span id='{$inpName}_text'></span>
                </p>
                <input type='hidden' name='{$inpName}_inp' id='{$inpName}_inp'>";
              }
              echo $formCands;
            ?>
            
            <hr>
            <div class="d-grid">
              <button class="btn btn-success mx-auto" id="double-check-ballot-btn">Submit Ballot</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Terms & Conditions -->
    <div class="modal fade" id="terms-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0">
          <div class="modal-header bg-success text-white rounded-0">
            <h1 class="modal-title fs-5">Terms and Conditions for E-BOTO SYSTEM</h1>
          </div>
          <div class="modal-body">
            <p>By using this online voting platform, you agree to the following terms and conditions:</p>
            <p>1. Account Responsibility: Users are responsible for keeping their passwords confidential and ensuring that they are not shared or disclosed to anyone else. Each user is solely responsible for all activities that occur under their account.</p>
            <p>2. Voting Confidentiality: Users are prohibited from disclosing or sharing any details, receipts, or summaries of their votes with any third party. Maintaining the confidentiality of the voting process is crucial for the integrity of the platform.</p>
            <p>3. Unauthorized Use: Users shall not allow anyone else to use their account to cast votes. Each account is intended for individual use only, and any unauthorized use of accounts is strictly prohibited.</p>
            <p>4. Security Measures: The platform will employ security measures to safeguard user data and voting integrity. Users are encouraged to report any suspicious activity or unauthorized access immediately.</p>
            <p>5. Compliance with Laws: Users must comply with all applicable laws and regulations while using the platform.</p>
            <p>6. Data Protection: User data will be handled as per the platform's Privacy Policy, outlining how information is collected, stored, and used.</p>
            <p>7. Termination of Account: The platform reserves the right to terminate or suspend user accounts found in violation of these terms and conditions.</p>
            <p>By using this online voting platform, you acknowledge that you have read, understood, and agreed to abide by these terms and conditions. Failure to comply with these terms may result in account suspension or termination.</p>
            <hr>
            <div class="d-flex justify-content-center"> 
              <input type="checkbox" id="agree-chk" class="form-check-input me-2">
              <label for="agree-chk"> I agree to App's Terms and Conditions</label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Offcanvas -->
    <div class="offcanvas offcanvas-start sidebar-nav bg-dark text-white" tabindex="-1" id="offcanvas">
      <div class="offcanvas-header" style="margin-top:-3px;" >
        <a href="#" class="text-white" style="text-decoration: none;">
          <h5 class="offcanvas-title fw-bold" id="offcanvasExampleLabel">
            <i class="bi bi-speedometer2"></i> &nbsp;Dashboard
          </h5>
        </a>
        <a href="#" class="burger text-light" data-bs-dismiss="offcanvas"><i class="bi bi-list"></i></a>
      </div>
      <hr style="margin-top:-3px">
      <div class="offcanvas-body p-0">
        <nav class="navbar-dark">
          <ul class="navbar-nav nav">
            <?php
              $paneIds = [];
              foreach ($filtPositions as $pos) {
                $expl = explode(" ", $pos['pos_name']);
                $paneId = implode("_", $expl);

                $activeLink = $pos['pos_sort_num'] == 1 ? 'active' : '';

                $posTabs .= "<li>
                                <button class='nav-link text-white px-3 {$activeLink} py-3 rounded-0 border-0 sidebar-link w-100 text-start' data-bs-toggle='tab' data-bs-target='#{$paneId}'>
                                  {$pos['pos_name']}
                                </button>
                              </li>";

                array_push($paneIds, $paneId);
              }
              echo $posTabs;
            ?>
          </ul>
        </nav>
      </div>
    </div>
    <!-- End Offcanvas -->

  </header>
  <main>
    <!-- START SUBMIT VOTED CANDIDATES SPINNER MODAL-->
    <div class="modal" data-bs-backdrop="static" data-bs-keyboard="false" id="submit-voted-candidate-spinner-modal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body bg-light rounded-0 text-center py-3" id="submit-spinner">
            <p class="h1 text-success mt-3">Submitting...</p>
            <span class="spinner-border spinner-border-sm text-success mt-2" style="width:6rem;height:6rem;"></span>
            <p class="mt-2 text-secondary">Submitting Ballot Form.</p>
          </div>
        </div>
      </div>
    </div>
      <!-- END SUBMIT VOTED CANDIDATES SPINNER MODAL -->

    <!-- START FETCH CANDIDATES SPINNER MODAL-->
    <div class="modal" data-bs-backdrop="static" data-bs-keyboard="false" id="fetch-candidates-for-voting-spinner-modal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body bg-light rounded-0 text-center py-3">
            <p class="h1 text-success mt-3">Loading...</p>
            <span class="spinner-border spinner-border-sm text-success mt-2" style="width:6rem;height:6rem;"></span>
            <p class="mt-2 text-secondary">Loading candidates for voting.</p>
          </div>
        </div>
      </div>
    </div>
    <!-- END FETCH CANDIDATES SPINNER MODAL -->
    <div class="container-fluid d-none" id="container-fluid">
      <div class="tab-content" id="myTabContent">
        <?php
          $posInd = -1;
          foreach ($filtPositions as $pos) {
            $posInd++;
            $expl = explode(" ", $pos['pos_name']);
            $paneId = implode("_", $expl);
            $inpName = strtolower($paneId);


            $showActiveClasses = $pos['pos_sort_num'] == 1 ? 'show active' : '';
            $panes .= "<div class='tab-pane fade {$showActiveClasses}' id='{$paneId}'>
                        <h1 class='text-center text-success'>{$pos['pos_name']}</h1>
                        <p class='d-block text-center mb-4'>Select only {$pos['pos_cand_many']} candidate(s)</p>
                        <div class='row gy-3 pt-3 mb-4'>";

            foreach ($result as $posId => $candsRen) {
              if ($pos['pos_id'] == $posId) {
                foreach ($candsRen as $candRen) {
                  $img = '';
                  if ($candRen['c_img']) {
                    $img = "admin/assets/img/{$candRen['c_img']}";
                  } else {
                    if ($candRen['v_gender'] == 0) {
                      $img = 'admin/assets/img/avatar woman.png';
                    } else {
                      $img = 'admin/assets/img/avatar man.png';
                    }
                  }

                  $panes .= "<div class='col-md-3 col-6 mx-auto justify-content-center d-flex'>
                              <label style='flex:1'>
                                <input type='checkbox' class='d-none {$inpName}' value='{$candRen['c_id']}' data-name='{$candRen['v_fname']} {$candRen['v_lname']}' data-inp-name='{$inpName}'>
                                <div class='card text-center'>
                                  <div class='card-header'>
                                    <h5>{$candRen['par_name']}</h5>
                                  </div>
                                  <div class='card-body'>
                                    <img src='{$img}' class='img-fluid object-fit-cover w-100' alt='candRenidate image'>
                                  </div>
                                  <div class='card-footer'>
                                    <h5>{$candRen['v_fname']} {$candRen['v_lname']}</h5>
                                    <small style='font-size:11px !important;' class='fw-bold'>{$candRen['course_desc']}</small><br>
                                    <small style='font-size:11px !important;' class='fw-bold text-secondary'>{$candRen['course_name']}-{$candRen['v_yrlvl']}</small>
                                  </div>
                                </div>
                              </label>
                            </div>";
                }
                $prevNextBtn = '';
                if ($posInd == 0) {
                  $prevNextBtn = "<div class='w-100'>
                                    <button class='prevNextBtn btn btn-secondary d-block mx-auto' data-to='{$paneIds[$posInd + 1]}'>Next</button>
                                  </div>";
                } else if ($posInd == sizeof($filtPositions) - 1) {
                  $prevNextBtn = "<div class='w-100'>
                                    <button class='prevNextBtn btn btn-secondary d-block mx-auto' data-to='{$paneIds[$posInd - 1]}'>Previous</button>
                                  </div>";
                } else {
                  $prevNextBtn = "<div class='d-flex justify-content-center'>
                                    <div>
                                      <button class='prevNextBtn btn btn-secondary' data-to='{$paneIds[$posInd - 1]}'>Previous</button>
                                      <button class='prevNextBtn btn btn-secondary' data-to='{$paneIds[$posInd + 1]}'>Next</button>
                                    </div>
                                  </div>";
                  // $prevNextBtn = "<div>
                  //                   <button data-bs-target='#{$paneIds[$posInd - 1]}'>Previous</button>
                  //                   <button data-bs-target='#{$paneIds[$posInd + 1]}'>Next</button>
                  //                 </div>";
                }
                $panes .= $prevNextBtn;
              }
            }

            $panes .=   "</div>
                      </div>";
          }
          echo $panes;
        ?>
      </div>
      <button id="btn-submit-ballot" class="shadow btn btn-success rounded-0" data-bs-toggle="modal" data-bs-target="#ballot-modal-form">Check Ballot</button>
    </div>
  </main>
<?php require_once './admin/assets/php/studfooter.php'; ?>
<script>
  $(document).ready(function() {
    $('#fetch-candidates-for-voting-spinner-modal').modal('show')
    console.log(JSON.parse('<?= json_encode($elec)?>'))
    // SweetAlert
    function swal(icon, title, text) {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
      })
    }

    let positions = '<?= json_encode($filtPositions);?>'
    positions = JSON.parse(positions)

    let results = '<?= json_encode($result);?>'
    results = JSON.parse(results)

    let state = {}
    let chkCountAll = 0
    let countChkByPos = {} 
    let datas = {}

    positions.forEach(pos => {
      let posName = pos.pos_name.split(' ')
      posName = posName.join('_')
      posName = posName.toLowerCase()

      let candsSelect = document.querySelectorAll(`.${posName}`)
      state[posName] = {
        cands: [...candsSelect],
        countSelect: pos.pos_cand_many
      }

      chkCountAll += pos.pos_cand_many
    })
    
    for (let key in state) {
      let count = state[key].countSelect
      let namesOfCands = [] 
      countChkByPos[key] = 0
      datas[key] = []

      state[key].cands.forEach(cand => {
        cand.addEventListener('click', e => {
          let candName = e.target.getAttribute('data-name')
          let inpName = e.target.getAttribute('data-inp-name')
          let val = parseInt(e.target.value)

          if (e.target.checked) {
            countChkByPos[key] += 1
            if (countChkByPos[key] > count) {
              e.target.checked = false
              countChkByPos[key] -= 1
              alert(`Please select only ${count} candidate(s) for this position.`)
            } else {
              datas[key].push(val)
              namesOfCands.push(candName)
              $(`#${inpName}_text`).text(namesOfCands)
            }
          } else {
            countChkByPos[key] -= 1
            namesOfCands = namesOfCands.filter(cand => cand != candName)
            datas[key] = datas[key].filter(data => data != val)
            $(`#${inpName}_text`).text(namesOfCands)
          }
        })
      })
    }

    let container = document.querySelector('#container-fluid')
    container.classList.remove('d-none')
    setTimeout(() => {
      $('#fetch-candidates-for-voting-spinner-modal').modal('hide')
      $('#terms-modal').modal('show')
    }, 10)

    // Submit ballot
    $('#double-check-ballot-btn').click(function(e) {
      e.preventDefault()
      e.stopPropagation()

      let allCandsId = datas
      let data = []
      
      for (let pos in allCandsId) {
        data.push(...allCandsId[pos])
      }

      // if (chkCountAll == data.length) {
      if (data.length > 0) {
        Swal.fire({
          title: 'Are you sure about submitting your ballot?',
          text: "Please double check your voted candidates!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#198754',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, submit it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $('#ballot-modal-form').modal('hide')
            $('#submit-voted-candidate-spinner-modal').modal('show')
            $.ajax({
              url: './admin/assets/php/action.php',
              method: 'post',
              data: {
                candIds: data,
                vId: '<?= $studId?>',
                elId: '<?= $elec['el_id']?>',
                action: 'submitBallot'
              },
              success: res => {
                console.log(res)
                if (res == '1') {
                  $('#submit-spinner').html(`
                    <p class="h2 text-success mt-3"><i>Thank you for voting!</i></p>
                  `)
                  setTimeout(() => window.location = 'campaign.php', 2000) 
                } else if (res == 'time is up') {
                  swal('error', 'Oops...', 'Cannot submit ballot anymore, time of voting ended.')
                  setTimeout(() => location.reload() , 3000)
                }
              }
            })
          }
        })
      } else {
        // alert('Incomplete ballot')
        alert('Please vote a candidate.')
      }
    })

    // Previous/Next Button
    $('body').on('click', '.prevNextBtn', function(e) {
      e.stopPropagation()
      
      let to = $(this).attr('data-to')

      $(`button[data-bs-target="#${to}"]`).trigger('click')
    })
    
    $('#agree-chk').click(function() {
      let checked = $(this).prop('checked')
      if (checked) {
        $('#terms-modal').modal('hide')
      }
    })
  })
</script>
