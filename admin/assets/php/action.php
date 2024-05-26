<?php
session_start();

require_once 'query.php';

$query = new Query();
date_default_timezone_set('Asia/Manila');

if (isset($_POST['action'])) {
  // Admin Login
  if ($_POST['action'] == 'admin-login') {
    $user = $query->testInput($_POST['user-admin']);
    $pass = $query->testInput($_POST['user-pass']);

    $admin = $query->fetchAdminUser($user);

    if ($admin) {
      if ($admin['password'] == sha1($pass)) {
        $_SESSION['eboto_admin'] = $user;
        echo json_encode($admin);
      }
    } else {
      echo false;
    }
    return;
  }

  // Adding Election Event
  if ($_POST['action'] == 'addElection') {
    $elecName = $query->testInput($_POST['elec-name']);
    $elecDate = $query->testInput($_POST['elec-date']);
    $elecTimeStart = $query->testInput($_POST['elec-timestart']);
    $elecTimeEnd = $query->testInput($_POST['elec-timeend']);
    $elecPartyNum = $query->testInput($_POST['elec-party-num']);
    $uniqid = uniqid();

    $year = date('Y', strtotime($elecDate));
    $month = date('F', strtotime($elecDate));
    $day = date('d', strtotime($elecDate));
    $date = date('j', strtotime($elecDate));

    if ($query->addElectionEvent($uniqid, $elecName, $year, $month, $day, $date, $elecTimeStart, $elecTimeEnd, $elecPartyNum)) {
      echo $query->addCampaign($uniqid);
    }
  }

  // Fetch Election Events
  if ($_POST['action'] == 'fetchElecEvents') {
    $elecEvs = $query->fetchElecEvents();
    $newElecEvs = [];

    foreach ($elecEvs as $elec) {
      $elec['el_time_start'] = date('h:ia', strtotime($elec['el_time_start']));
      $elec['el_time_end'] = date('h:ia', strtotime($elec['el_time_end']));
      array_push($newElecEvs, $elec);
    }
    echo json_encode($newElecEvs);
  }
  
  // Fetch an Election Event
  if ($_POST['action'] == 'fetchElecEvent') {
    $id = $query->testInput($_POST['id']);
    echo json_encode($query->fetchElecEvent($id));
  }

  // Fetch Current Election Event
  if ($_POST['action'] == 'fetchCurrentElec') {
    echo json_encode($query->fetchCurrentElec());
  }
  
  // Update Current Election Event
  if ($_POST['action'] == 'updateCurElecEvent') {
    $curElId = $query->testInput($_POST['cur-elec']);

    if (sizeof($query->fetchCurrentElec()) > 0) {
      echo $query->updateCurrentElec($curElId);
    } else {
      echo $query->addCurrentElec($curElId);
    }
  }

  // Update an Election Event
  if ($_POST['action'] == 'updateElecEvent') {
    $id = $query->testInput($_POST['edit-elec-id']);
    $name = $query->testInput($_POST['edit-elec-name']);
    $elecDate = $query->testInput($_POST['edit-elec-date']);
    $timestart = $query->testInput($_POST['edit-elec-timestart']);
    $timeend = $query->testInput($_POST['edit-elec-timeend']);
    $partyNum = $query->testInput($_POST['edit-elec-party-num']);
    $status = $query->testInput($_POST['edit-status']);

    $year = date('Y', strtotime($elecDate));
    $month = date('F', strtotime($elecDate));
    $day = date('l', strtotime($elecDate));
    $date = date('j', strtotime($elecDate));

    $onGoingElecs = $query->checkOnGoingElec();

    // $elecPartyMany = $query->fetchElecEvent($id)['el_party_many'];
    // $partiesLengthOnElec = sizeOf($query->fetchPartiesOfElecEvent($id));

    if (sizeof($onGoingElecs) > 0) { // If there is an 'on-going' or 'pause' election event among election events
      if ($onGoingElecs[0]['el_id'] == $id) { // If the 'on-going' or 'pause' election event among election events is equal to the election
        if (sizeof($query->checkIfElecHaveNotStarted($id)) > 0) { // If Election Event is 'not-started'
          // if ($elecPartyMany == $partiesLengthOnElec) {
          // }
          return;
        } else {
          echo $query->updateElecEvent($id, $name, $year, $month, $day, $date, $timestart, $timeend, $partyNum, $status);
          return;
        }
      } 
      echo 'an election is on-going';
      return;
    }
    // Check total many of positions (exist_cand = 1) based on election event and then multiply by election parties
    $posCountMany = $query->countManyOfPositionsBasedOnElId($id)['total'];
    $partyNum = $query->fetchElecEvent($id)['el_party_many'];
    $nonExistCands = $query->countManyOfNonExistingCandsBasedOnElId($id)['nonExistCount']; // Non existing candidates in election
    $existCands = $query->countManyOfExistingCandsBasedOnElId($id)['existCount']; // Count of existing cands

    $allCandsCount = $posCountMany * $partyNum; // Count of all candidates including non existing, formula = (all positions many sum) * num of parties
    $allCandsSlotCount = $allCandsCount - $nonExistCands; // Count of all existing candidates
    
    if ($allCandsSlotCount == $existCands) {
      echo $query->updateElecEvent($id, $name, $year, $month, $day, $date, $timestart, $timeend, $partyNum, $status);
    } else {
      echo 'incomplete';
    }
  }

  // Delete an Election Event
  if ($_POST['action'] == 'delElecEv') {
    $id = $query->testInput($_POST['id']);
    $uniqid = $query->testInput($_POST['uniqid']);

    if ($query->fetchCurrentElecWithId($id)) {
      echo 3;
    } else {
      if (sizeof($query->checkIfElecHaveNotStarted($id)) > 0) {
        if ($query->delElecEv($id)) {
          echo $query->delCampaign($uniqid);
          echo $query->delPartiesBasedOnElec($id);
          echo $query->delVotersBasedOnElec($id);

          $candsWithPos = $query->fetchCandsBasedOnDelElec($id);
          foreach ($candsWithPos as $candWithPos) {
            $query->delCand($candWithPos['c_id']);
          }

          $positionsWithCanVote = $query->fetchPositionsWithCanVotePos($id);
          foreach ($positionsWithCanVote as $positionWithCanVote) {
            $query->delCanVotePositions($positionWithCanVote['pos_uniqid']);
          }
          echo $query->delPositionsBasedOnElec($id);
          return;
        }
      } else {
        echo 'not not-started anymore';
      }
    }
  }

  // Add Party
  if ($_POST['action'] == 'addParty') {
    // print_r($_POST);
    $elecEv = $query->testInput($_POST['elec-event-select']);
    $name = $query->testInput($_POST['party-name']);
    $platform = $query->testInput($_POST['party-platform']);

    $elecPartyNum = $query->fetchElecEvent($elecEv)['el_party_many'];
    $partiesLengthOnElec = sizeOf($query->fetchPartiesOfElecEvent($elecEv));

    if (sizeof($query->checkIfElecHaveNotStarted($elecEv)) > 0) {
      // If parties length is equal to party setted-up on election, don't add party anymore
      if ($partiesLengthOnElec == $elecPartyNum) {
        echo 'exceed';
        return;
      }
      echo $query->addParty($elecEv, $name, $platform);
    } else {
      echo "election event not started anymore";
    }
  }

  // Fetch Parties based on Election Event
  if ($_POST['action'] == 'fetchPartiesOfElecEvent') {
    $elecEv = $query->testInput($_POST['elecEv']);
    $newArr = [];
    
    $parties = $query->fetchPartiesOfElecEvent($elecEv);

    foreach ($parties as $party) {
      $party['par_platform'] = html_entity_decode($party['par_platform']);
      array_push($newArr, $party);
    }
    echo json_encode($newArr);
  }

  // Filter Election Event in Parties
  if ($_POST['action'] == 'filterParty') {
    $id = $query->testInput($_POST['elecEvId']);
    echo json_encode($query->fetchPartiesOfElecEvent($id));
  }
  
  // Fetch Party
  if ($_POST['action'] == 'editParty') {
    $id = $query->testInput($_POST['id']);
    $party = $query->fetchParty($id);
    $party['par_platform'] = html_entity_decode($party['par_platform']);
    echo json_encode($party);
  }
  
  // Update Party
  if ($_POST['action'] == 'updateParty') {
    $id = $query->testInput($_POST['edit-party-id']);
    $elecEvId = $query->testInput($_POST['edit-elec-event-select']);
    $partyName = $query->testInput($_POST['edit-party-name']);
    $partyPlatform = $query->testInput($_POST['edit-party-platform']);

    if (sizeof($query->checkIfElecHaveNotStarted($elecEvId)) > 0) {
      echo $query->updateParty($id, $elecEvId, $partyName, $partyPlatform);
    } else {
      echo 'not not-started anymore';
    }
  }
  
  // Delete Party
  if ($_POST['action'] == 'delParty') {
    $id = $query->testInput($_POST['id']);
    if ($query->fetchParty($id)['el_status'] != 'not-started') {
      echo 'election not started anymore';
      return;
    }

    if ($query->delParty($id)) {
      echo $query->delCandsOfParty($id);
    }
  }

  // Add Course
  if ($_POST['action'] == 'addCourse') {
    $name = $query->testInput($_POST['course-name']);
    $desc = $query->testInput($_POST['course-desc']);

    echo $query->addCourse($name, $desc);
  }
  
  // Fetch Courses
  if ($_POST['action'] == 'fetchCourses') {
    echo json_encode($query->fetchCourses());
  }
  
  // Fetch Course
  if ($_POST['action'] == 'fetchCourse') {
    $id = $query->testInput($_POST['id']);
    echo json_encode($query->fetchCourse($id));
  }
  
  // Update Course
  if ($_POST['action'] == 'updateCourse') {
    $id = $query->testInput($_POST['edit-course-id']);
    $name = $query->testInput($_POST['edit-course-name']);
    $desc = $query->testInput($_POST['edit-course-desc']);

    echo $query->updateCourse($id, $name, $desc);
  }

  // Delete Course
  if ($_POST['action'] == 'delCourse') {
    $id = $query->testInput($_POST['id']);
    echo $query->delCourse($id);
  }

  // Fetch Positions Based on Election Event
  if ($_POST['action'] == 'fetchPositionsBasedOnElec') {
    $elecEv = $query->testInput($_POST['elecEv']);
    $positions = $query->fetchPositionsOfElecEvent($elecEv);

    echo json_encode($positions);
  }

  // Add position and canvote position
  if ($_POST['action'] == 'addPosAndCanVotePos') {
    $elId = $query->testInput($_POST['elec-event-select']);
    $posName = $query->testInput($_POST['position-name']);
    $candMany = $query->testInput($_POST['position-many']);
    $sortNum = $query->testInput($_POST['position-sort-num']);
    $courses = $_POST['position-voter-courses'];
    $uniqid = uniqid();

    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      if ($query->addPosition($uniqid, $posName, $candMany, $sortNum, $elId)) {
        foreach ($courses as $course) {
          $query->canVotePosition($uniqid, $course);
        }
        echo 'added';
      }
    } else {
      echo "election event not started anymore";
    }
  }

  // Fetch Position
  if ($_POST['action'] == 'fetchPosition') {
    $id = $query->testInput($_POST['id']);
    $position = $query->fetchPosition($id);
    $courses = $query->fetchCoursesOfPosition($id);
    
    $position['canVoteCourses'] = $courses;

    echo json_encode($position);
  }
  
  // Update Position
  if ($_POST['action'] == 'updatePosition') {
    $id = $query->testInput($_POST['edit-position-id']);
    $uniqid = $query->testInput($_POST['edit-position-uniqid']);
    $elId = $query->testInput($_POST['edit-elec-event-select']);
    $posName = $query->testInput($_POST['edit-position-name']);
    $posMany = $query->testInput($_POST['edit-position-many']);
    $sort = $query->testInput($_POST['edit-position-sort-num']);
    $courses = $_POST['edit-position-voter-courses']; // Array of id courses
  
    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      if ($query->updatePosition($id, $posName, $posMany, $sort, $elId)) {
        if ($query->delCanVotePositions($uniqid)) {
          foreach ($courses as $course) {
            $query->canVotePosition($uniqid, $course);
          }
          echo 'updated';
        }
      }
    } else {
      echo "not not-started anymore";
    }
  }

  // Delete position
  if ($_POST['action'] == 'delPosition') {
    $id = $query->testInput($_POST['id']);
    $uniqid = $query->testInput($_POST['uniqid']);
    $elId = $query->testInput($_POST['elId']);

    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      if ($query->delPositions($uniqid)) {
        if ($query->delCanVotePositions($uniqid)) {
          $candsBasedPosId = $query->fetchCandsBasedOnPos($id);

          foreach ($candsBasedPosId as $cand) {
            $query->updateVoterIsCandidate($cand['c_v_id'], 0);
          }

          if ($query->delCandsBasedOnPos($id)) {
            echo 'deleted';
          }
        }
      }
    } else {
      echo "not not-started anymore";
    }
  }

  // Fetch Voters based on Election Event
  if ($_POST['action'] == 'fetchVotersBasedOnElec') {
    $elId = $query->testInput($_POST['elecEv']);
    echo json_encode($query->fetchVotersBasedOnElecEv($elId));
  }
  
  // Add Voter
  if ($_POST['action'] == 'addVoter') {
    $elId = $query->testInput($_POST['elec-event-select']);
    $fname = $query->testInput($_POST['voter-fname']);
    $mname = $query->testInput($_POST['voter-mname']);
    $lname = $query->testInput($_POST['voter-lname']);
    $course = $query->testInput($_POST['voter-course']);
    $yearLevel = $query->testInput($_POST['voter-year-level']);
    $gender = $query->testInput($_POST['voter-gender']);

    $uniqid = str_shuffle(uniqid());
    $pass = substr($uniqid, 0, 7);

    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      echo $query->addVoter($elId, $pass, $fname, $mname, $lname, $course, $yearLevel, $gender);
    } else {
      echo "election event not started anymore";
    }
  }
  
  // Fetch Voter
  if ($_POST['action'] == 'fetchVoter') {
    $id = $query->testInput($_POST['id']);
    echo json_encode($query->fetchVoter($id));
  }
  
  // Update Voter
  if ($_POST['action'] == 'updateVoter') {
    $vid = $query->testInput($_POST['edit-voter-id']);
    $elId = $query->testInput($_POST['edit-elec-event-select']);
    $fname = $query->testInput($_POST['edit-voter-fname']);
    $mname = $query->testInput($_POST['edit-voter-mname']);
    $lname = $query->testInput($_POST['edit-voter-lname']);
    $course = $query->testInput($_POST['edit-voter-course']);
    $yearLevel = $query->testInput($_POST['edit-voter-year-level']);
    $gender = $query->testInput($_POST['edit-voter-gender']);

    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      echo $query->updateVoter($vid, $elId, $fname, $mname, $lname, $course, $yearLevel, $gender);
    } else {
      echo "not not-started anymore";
    }
  }

  // Delete Voter
  if ($_POST['action'] == 'delVoter') {
    $id = $query->testInput($_POST['id']);
    $elId = $query->testInput($_POST['elId']);
    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      echo $query->delVoter($id);
    } else {
      echo "not not-started anymore";
    }
  }
  
  // Change parties based on election event
  if ($_POST['action'] == 'changeElecEvOnCandidates') {
    $elId = $query->testInput($_POST['elId']);
    echo json_encode($query->fetchPartiesOfElecEvent($elId));
  }

  // Fetch Positions With Candidates
  if ($_POST['action'] == 'fetchPositionsBasedOnElecEv') {
    $elId = $query->testInput($_POST['elecEv']);
    echo json_encode($query->fetchPositionsBasedOnElecEv($elId));
  }
  
  // Search Voter
  if ($_POST['action'] == 'searchVoter') {
    $elId = $query->testInput($_POST['elId']);
    $name = $query->testInput($_POST['name']);
    echo json_encode($query->searchVoter($elId, $name));
  }
  
  // Add Candidate
  if ($_POST['action'] == 'addCandidate') {
    $elId = $query->testInput($_POST['add-elec-event-select']);
    $partyId = $query->testInput($_POST['add-party-select']);
    $posId = $query->testInput($_POST['add-pos-select']);
    $voterId = $query->testInput($_POST['add-voter-value']);

    // Check if election has not started
    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      // Check if there's a voter selected
      if (empty($voterId)) {
        echo 'no voter';
        return;
      }

      // Check if voter is already a candidate
      if ($query->checkVoterIsCandidate($voterId, $elId)) {
        echo 'voter is already a candidate';
        return;
      }

      $posCandMany = $query->selectManyOfCands($posId)['pos_cand_many'];
      $candMany = sizeof($query->checkPosIsOcc($posId, $partyId, $elId));

      if ($posCandMany <= $candMany) {
        echo 'exceed candidate many';
        return;
      }

      if (isset($_FILES['add-img']['name']) && $_FILES['add-img']['name'] != null) {
        $img = $_FILES['add-img']['name'];
        move_uploaded_file($_FILES['add-img']['tmp_name'], '../img/' . $img);
        
        if ($query->addCandidate($voterId, $posId, $partyId, $img)) {
          echo $query->updateVoterIsCandidate($voterId, 1);
        }
      } else {
        if ($query->addCandidate($voterId, $posId, $partyId, '')) {
          echo $query->updateVoterIsCandidate($voterId, 1);
        }
      }
    } else {
      echo 'not started anymore';
      return;
    }
  }

  // Fetch Candidates
  if ($_POST['action'] == 'fetchCands') {
    $elId = $query->testInput($_POST['elId']);
    $partyId = $query->testInput($_POST['partyId']);

    echo json_encode($query->fetchCands($elId, $partyId));
  }
  
  // Fetch Candidates Based on Election Event
  if ($_POST['action'] == 'fetchAllCandidates') {
    $elId = $query->testInput($_POST['elId']);
    $cands = $query->fetchAllCandidates($elId);
    $newCands = [];
    
    foreach ($cands as $cand) {
      $count = $query->countVotesOfCand($cand['c_id'])['vote_count'];
      $cand['voteCount'] = $count;
      array_push($newCands, $cand);
    }
    echo json_encode($newCands);
  }
  
  // Fetch Candidate
  if ($_POST['action'] == 'fetchCand') {
    $id = $query->testInput($_POST['id']);
    echo json_encode($query->fetchCand($id));
  }

  // Fetch Not Started Election Events
  if ($_POST['action'] == 'fetchNotStartedElecEvents') {
    $elecEvs = $query->fetchNotStartedElecEvents();
    $newElecEvs = [];

    foreach ($elecEvs as $elec) {
      $elec['el_time_start'] = date('h:ia', strtotime($elec['el_time_start']));
      $elec['el_time_end'] = date('h:ia', strtotime($elec['el_time_end']));
      array_push($newElecEvs, $elec);
    }
    echo json_encode($newElecEvs);
  }

  // Update Candidate
  if ($_POST['action'] == 'updateCand') {
    $id = $query->testInput($_POST['edit-cand-id']);
    $elId = $query->testInput($_POST['edit-elec-id']);
    $oldVId = $query->testInput($_POST['edit-old-voter-value']);
    $vId = $query->testInput($_POST['edit-voter-value']);
    $exist = $query->testInput($_POST['edit-exist']);

    // Check if election has started
    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      // Check if there's a voter selected
      if (empty($vId)) {
        echo 'no voter';
        return;
      }

      // Check if voter is already a candidate
      if ($query->checkVoterIsCandidate($vId, $elId)) {
        // If voter submitted is the same as the old voter then update is allowed
        if ($query->checkVoterIsCandidate($vId, $elId)[0]['c_v_id'] == $oldVId) {
          if (isset($_FILES['edit-img']['name']) && $_FILES['edit-img']['name'] != null) {
            $img = $_FILES['edit-img']['name'];
            move_uploaded_file($_FILES['edit-img']['tmp_name'], '../img/' . $img);
            echo $query->updateCandWithImg($vId, $img, $exist, $id);
          } else {
            echo $query->updateCandWithoutImg($vId, $exist, $id);
          }
        } else {
          echo 'voter is already a candidate';
          return;
        }
      }
      // If voter is not yet a candidate
      else {
        if (isset($_FILES['edit-img']['name']) && $_FILES['edit-img']['name'] != null) {
          $img = $_FILES['edit-img']['name'];
          move_uploaded_file($_FILES['edit-img']['tmp_name'], '../img/' . $img);
          
          if ($query->updateCandWithImg($vId, $img, $exist, $id)) {
            if ($query->updateVoterIsCandidate($oldVId, 0)) {
              echo $query->updateVoterIsCandidate($vId, 1);
            }
          }
        } else {
          if ($query->updateCandWithoutImg($vId, $exist, $id)) {
            if ($query->updateVoterIsCandidate($oldVId, 0)) {
              echo $query->updateVoterIsCandidate($vId, 1);
            }
          }
        }
      }
    } else {
      echo 'not started anymore';
      return;
    }
  }
  
  // Delete Candidate
  if ($_POST['action'] == 'delCand') {
    $id = $query->testInput($_POST['id']);
    $elId = $query->testInput($_POST['elId']);
    $vId = $query->testInput($_POST['vId']);
    
    // Check if election has started
    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      if ($query->delCand($id)) {
        echo $query->updateVoterIsCandidate($vId, 0);
      }
    } else {
      echo "not not-started anymore";
    }
  }

  // Fetch Platform of election event and party
  if ($_POST['action'] == 'fetchPlatformBasedOnElecAndParty') {
    $partyId = $query->testInput($_POST['partyId']);
    $parties = $query->fetchPlatformBasedOnElecAndParty($partyId);
    $newArr = [];

    foreach ($parties as $party) {
      $party['par_platform'] = html_entity_decode($party['par_platform']);
      array_push($newArr, $party);
    }
    echo json_encode($newArr);
  }

  // Fetch Campaigns
  if ($_POST['action'] == 'fetchCampaigns') {
    echo json_encode($query->fetchCampaigns());
  }
  
  // Fetch Campaign for View
  if ($_POST['action'] == 'fetchCampaignView') {
    $id = $query->testInput($_POST['id']);
    $campaign = $query->fetchCampaign($id);
    $campaign['cam_start_time'] = date('h:ia', strtotime($campaign['cam_start_time']));
    $campaign['cam_end_time'] = date('h:ia', strtotime($campaign['cam_end_time']));
    echo json_encode($campaign);
  }
  
  // Fetch Campaign for Edit
  if ($_POST['action'] == 'fetchCampaignEdit') {
    $id = $query->testInput($_POST['id']);
    $campaign = $query->fetchCampaign($id);
    echo json_encode($campaign);
  }
  
  // Update Campaign
  if ($_POST['action'] == 'updateCampaign') {
    $elId = $query->testInput($_POST['edit-elecev-id']);
    $id = $query->testInput($_POST['edit-elec-id']);
    $sdate = $query->testInput($_POST['edit-elec-startdate']);
    $edate = $query->testInput($_POST['edit-elec-enddate']);
    
    $stime = $query->testInput($_POST['edit-elec-timestart']);
    $etime = $query->testInput($_POST['edit-elec-timeend']);
    $status = $query->testInput($_POST['edit-status']);

    $syear = date('Y', strtotime($sdate));
    $smonth = date('F', strtotime($sdate));
    $sdate = date('d', strtotime($sdate));
    
    $eyear = date('Y', strtotime($edate));
    $emonth = date('F', strtotime($edate));
    $edate = date('d', strtotime($edate));

    // Check if election has started
    if (sizeof($query->checkIfElecHaveNotStarted($elId)) > 0) {
      echo $query->updateCampaign($syear, $smonth, $sdate, $stime, $eyear, $emonth, $edate, $etime, $status, $id);
    } else {
      echo "not not-started anymore";
    }
  }

  // Student Login
  if ($_POST['action'] == 'student-login') {
    $pass = $query->testInput($_POST['user-pass']);

    if (sizeof($query->fetchVotersBasedOnPass($pass)) > 0) {
      $_SESSION['eboto_student'] = $query->fetchVotersBasedOnPass($pass)[0]['v_id'];
      echo 'student login';
    } else {
      echo 'invalid pass';
    }
  }

  if ($_POST['action'] == 'submitBallot') {
    // print_r($_POST);
    $candIds = $_POST['candIds'];
    $vId = $query->testInput($_POST['vId']);
    $elId = $query->testInput($_POST['elId']);

    if ($query->fetchElecEvent($elId)['el_status'] ==  'done') {
      echo 'time is up';
      return;
    }

    $year = date('Y');
    $month = date('F');
    $day = date('l');
    $date = date('d');
    $time = date('H:i');

    foreach ($candIds as $candId) {
      $query->submitBallot($vId, $candId, $year, $month, $day, $date, $time);
    }

    echo $query->updateVoterIntoVoted($vId);
  }

  if ($_POST['action'] == 'fetchVotedCands') {
    // print_r($_POST);
    $vId = $query->testInput($_POST['vId']);
    $newCands = [];

    $cands = $query->votedCandsOfStud($vId);

    foreach ($cands as $cand) {
      $cand['vo_time'] = date('h:ia', strtotime($cand['vo_time']));
      array_push($newCands, $cand);
    }
    echo json_encode($newCands);
  }

  if ($_POST['action'] == 'fetchCoursesCount') {
    echo json_encode($query->countCourses());
  }
  
  if ($_POST['action'] == 'fetchPartiesCount') {
    $elId = $query->testInput($_POST['elId']);
    echo json_encode($query->countParties($elId));
  }
  
  if ($_POST['action'] == 'fetchPositionCount') {
    $elId = $query->testInput($_POST['elId']);
    echo json_encode($query->countPositions($elId));
  }
 
  if ($_POST['action'] == 'fetchVotedVoters') {
    $elId = $query->testInput($_POST['elId']);
    $voters = $query->fetchVotedVoters($elId);
    $conVoters = [];

    foreach ($voters as $voter) {
      $voter['vo_time'] = date('h:ia', strtotime($voter['vo_time']));
      array_push($conVoters, $voter);
    }
    echo json_encode($conVoters);
  }
  
  if ($_POST['action'] == 'fetchHaveNotVotedVoters') {
    $elId = $query->testInput($_POST['elId']);
    echo json_encode($query->fetchHaveNotVotedVoters($elId));
  }
  
  if ($_POST['action'] == 'resetElec') {
    $elId = $query->testInput($_POST['elId']);

    $votersOnVote = $query->selectVotesBasedOnElId($elId);
    $voters = [];

    if ($query->updateElecEventOnlyStatus($elId)) {
      if ($query->updateVotersVotedStatus($elId)) {
        foreach ($votersOnVote as $voter) {
          $query->updateVotesBasedOnVoter($voter['vo_v_id']);
        }
        echo 'resetted';
      }
    }
  }
}