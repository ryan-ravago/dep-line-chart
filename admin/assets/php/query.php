<?php

require_once 'db.php';

class Query extends Database {
  // Fetch Admin through username
  public function fetchAdminUser($user) {
    $sql = "SELECT * FROM admin WHERE username = :user";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['user' => $user]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

  public function addElectionEvent($el_uniqid, $elecName, $year, $month, $day, $date, $elecTimeStart, $elecTimeEnd, $elecPartyNum) {
    $sql = "INSERT INTO election (el_uniqid, el_name, el_year, el_month, el_day, el_date, el_time_start, el_time_end, el_party_many)
            VALUES (:el_uniqid, :el_name, :el_year, :el_month, :el_day, :el_date, :el_time_start, :el_time_end, :el_party_many)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'el_uniqid' => $el_uniqid,
      'el_name' => $elecName,
      'el_year' => $year,
      'el_month' => $month,
      'el_day' => $day,
      'el_date' => $date,
      'el_time_start' => $elecTimeStart,
      'el_time_end' => $elecTimeEnd,
      'el_party_many' => $elecPartyNum
    ]);
    return true;
  }

  // Check if election event haven't started yet
  public function checkIfElecHaveNotStarted($elId) {
    $sql = "SELECT * FROM `election` WHERE el_id = ? AND el_status = 'not-started'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Check if there is an on-going election event
  public function checkOnGoingElec() {
    $sql = "SELECT * FROM `election` WHERE el_status = 'on-going' OR el_status = 'pause'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Fetch Election Events
  public function fetchElecEvents() {
    $sql = "SELECT * FROM `election`";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch an Election Event
  public function fetchElecEvent($id) {
    $sql = "SELECT * FROM `election` WHERE el_id = :el_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['el_id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

  // Fetch Current Election Event
  public function fetchCurrentElec() {
    $sql = "SELECT * FROM `current`
            INNER JOIN `election`
              ON `current`.cur_el_id = `election`.el_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Fetch Current Election Event with ID
  public function fetchCurrentElecWithId($id) {
    $sql = "SELECT * FROM `current` WHERE cur_el_id = :cur_el_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['cur_el_id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
  }
  
  // Add Current Election Event
  public function addCurrentElec($curElId) {
    $sql = "INSERT INTO `current` (cur_el_id) 
            VALUES (:cur_el_id)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['cur_el_id' => $curElId]);
    return true;
  }

  // Update Current Election Event
  public function updateCurrentElec($curElId) {
    $sql = "UPDATE `current` SET cur_el_id = :cur_el_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['cur_el_id' => $curElId]);
    return true;
  }
  
  // Update Election Event
  public function updateElecEvent($id, $name, $year, $month, $day, $date, $timestart, $timeend, $partyNum, $status) {
    $sql = "UPDATE `election`
            SET el_name = :el_name,
              el_year = :el_year,
              el_month = :el_month,
              el_day = :el_day,
              el_date = :el_date,
              el_time_start = :el_time_start,
              el_time_end = :el_time_end,
              el_party_many = :el_party_many,
              el_status = :el_status
            WHERE el_id = :el_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'el_name' => $name,
      'el_year' => $year,
      'el_month' => $month,
      'el_day' => $day,
      'el_date' => $date,
      'el_time_start' => $timestart,
      'el_time_end' => $timeend,
      'el_party_many' => $partyNum,
      'el_status' => $status,
      'el_id' => $id
    ]);
    return true;
  }

  // Delete Election Event
  public function delElecEv($id) {
    $sql = "DELETE FROM `election` WHERE el_id = :el_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['el_id' => $id]);
    return true;
  }

  // Add Party
  public function addParty($elecEv, $name, $platform) {
    $sql = "INSERT INTO `partylist` (par_name, par_platform, par_el_id) 
            VALUES (:par_name, :par_platform, :par_el_id)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'par_name' => $name,
      'par_platform' => $platform,
      'par_el_id' => $elecEv
    ]);
    return true;
  }

  // Fetch Parties of certain Election Event
  public function fetchPartiesOfElecEvent($id) {
    $sql = "SELECT * FROM partylist WHERE par_el_id = :par_el_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['par_el_id' => $id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch Party
  public function fetchParty($id) {
    $sql = "SELECT * FROM `partylist`
            INNER JOIN election
              ON partylist.par_el_id = election.el_id
            WHERE par_id = :par_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['par_id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

  // Update Election Event
  public function updateParty($id, $elecEvId, $partyName, $partyPlatform) {
    $sql = "UPDATE `partylist`
            SET par_name = :par_name,
              par_platform = :par_platform,
              par_el_id = :par_el_id
            WHERE par_id = :par_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'par_name' => $partyName,
      'par_platform' => $partyPlatform,
      'par_el_id' => $elecEvId,
      'par_id' => $id
    ]);
    return true;
  }

  // Delete Party
  public function delParty($id) {
    $sql = "DELETE FROM `partylist` WHERE par_id = :par_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['par_id' => $id]);
    return true;
  }
  
  // Delete Parties Based on election
  public function delPartiesBasedOnElec($id) {
    $sql = "DELETE FROM `partylist` WHERE par_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    return true;
  }
  
  // Add Course
  public function addCourse($name, $desc) {
    $sql = "INSERT INTO course (course_name, course_desc) VALUES (:course_name, :course_desc)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'course_name' => $name,
      'course_desc' => $desc
    ]);
    return true;
  }
  
  // Fetch Courses
  public function fetchCourses() {
    $sql = "SELECT * FROM course";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch Course
  public function fetchCourse($id) {
    $sql = "SELECT * FROM course WHERE course_id = :course_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['course_id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
  }
  
  // Update Course
  public function updateCourse($id, $name, $desc) {
    $sql = "UPDATE course
            SET course_name = :course_name,
              course_desc = :course_desc
            WHERE course_id = :course_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'course_name' => $name,
      'course_desc' => $desc,
      'course_id' => $id
    ]);
    return true;
  }

  // Delete Course
  public function delCourse($id) {
    $sql = "DELETE FROM course WHERE course_id = :course_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['course_id' => $id]);
    return true;
  }

  // Fetch Positions of certain Election Event
  public function fetchPositionsOfElecEvent($id) {
    $sql = "SELECT * FROM position WHERE pos_el_id = ? ORDER BY pos_sort_num";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Add Position
  public function addPosition($uniqid, $posName, $candMany, $sortNum, $elId) {
    $sql = "INSERT INTO position (pos_uniqid, pos_name, pos_cand_many, pos_sort_num, pos_el_id)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$uniqid, $posName, $candMany, $sortNum, $elId]);
    return true;
  }
 
  // Add Canvote Position
  public function canVotePosition($uniqid, $courseId) {
    $sql = "INSERT INTO canvote_position (canpos_pos_id, canpos_course_id) VALUES (?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$uniqid, $courseId]);
    return true;
  }

  // Fetch Position
  public function fetchPosition($id) {
    $sql = "SELECT * FROM position
            INNER JOIN election
              ON election.el_id = position.pos_el_id
            WHERE pos_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
  }
  
  // Fetch Courses allowed to vote in the position
  public function fetchCoursesOfPosition($id) {
    $sql = "SELECT course_id, course_name, course_desc
            FROM position
            INNER JOIN canvote_position
              ON position.pos_uniqid = canvote_position.canpos_pos_id
            INNER JOIN course
              ON course.course_id = canvote_position.canpos_course_id
            WHERE pos_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch positions with canvote_position
  public function fetchPositionsWithCanVotePos($id) {
    $sql = "SELECT pos_uniqid
            FROM position
            INNER JOIN canvote_position
              ON position.pos_uniqid = canvote_position.canpos_pos_id
            WHERE pos_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch canvote_positions for voting
  public function fetchCanVotePositionsForVoting($uniqid) {
    $sql = "SELECT * FROM position
            INNER JOIN canvote_position
              ON position.pos_uniqid = canvote_position.canpos_pos_id
            WHERE pos_uniqid = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$uniqid]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Update position
  public function updatePosition($id, $posName, $posMany, $sort, $elId) {
    $sql = "UPDATE position
            SET pos_name = ?,
              pos_cand_many = ?,
              pos_sort_num = ?,
              pos_el_id = ?
            WHERE pos_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$posName, $posMany, $sort, $elId, $id]);
    return true;
  }

  // Delete Can Vote Position
  public function delCanVotePositions($uniqid) {
    $sql = "DELETE FROM canvote_position WHERE canpos_pos_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$uniqid]);
    return true;
  }

  // Delete Position
  public function delPositions($uniqid) {
    $sql = "DELETE FROM position WHERE pos_uniqid = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$uniqid]);
    return true;
  }
  
  // Delete Positions Based on election
  public function delPositionsBasedOnElec($id) {
    $sql = "DELETE FROM position WHERE pos_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    return true;
  }

  // Fetch Voters
  public function fetchVotersBasedOnElecEv($elId) {
    $sql = "SELECT * FROM voter
            INNER JOIN course
              ON voter.v_course_id = course.course_id
            INNER JOIN election
              ON election.el_id = voter.v_el_id
            WHERE election.el_id = ?
            ORDER BY course_name, v_yrlvl, v_lname";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch Voter
  public function fetchVoter($id) {
    $sql = "SELECT * FROM voter
            INNER JOIN course
              ON voter.v_course_id = course.course_id
            INNER JOIN election
              ON election.el_id = voter.v_el_id
            WHERE voter.v_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

  // Insert Voter
  public function addVoter($elId, $pass, $fname, $mname, $lname, $course, $yearLevel, $gender) {
    $sql = "INSERT INTO voter (v_pass, v_fname, v_mname, v_lname, v_course_id, v_yrlvl, v_gender, v_el_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$pass, $fname, $mname, $lname, $course, $yearLevel, $gender, $elId]);
    return true;
  }

  // Update Voter
  public function updateVoter($vid, $elId, $fname, $mname, $lname, $course, $yearLevel, $gender) {
    $sql = "UPDATE voter 
            SET v_el_id = ?,
              v_fname = ?,
              v_mname = ?,
              v_lname = ?,
              v_course_id = ?,
              v_yrlvl = ?,
              v_gender = ?
            WHERE v_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId, $fname, $mname, $lname, $course, $yearLevel, $gender, $vid]);
    return true;
  }

  // Delete Voter
  public function delVoter($id) {
    $sql = "DELETE FROM voter WHERE v_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    return true;
  }
  
  // Delete Voters
  public function delVotersBasedOnElec($id) {
    $sql = "DELETE FROM voter WHERE v_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    return true;
  }
  
  // Fetch Candidates
  public function fetchCandidates($elId, $partyId) {
    $sql = "SELECT * FROM candidate
            INNER JOIN voter
              ON candidate.c_v_id = voter.v_id
            INNER JOIN position
              ON candidate.c_pos_id = position.pos_id
            INNER JOIN partylist
              ON candidate.c_par_id = partylist.par_id
            WHERE partylist.par_el_id = ?
              AND partylist.par_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId, $partyId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch All Candidates Based On Election Event
  public function fetchAllCandidates($elId) {
    $sql = "SELECT * FROM candidate
            INNER JOIN voter
              ON candidate.c_v_id = voter.v_id
            INNER JOIN position
              ON candidate.c_pos_id = position.pos_id
            INNER JOIN partylist
              ON candidate.c_par_id = partylist.par_id
            INNER JOIN election
              ON election.el_id = voter.v_el_id
            WHERE el_id = ? AND exist_cand = 1
            ORDER BY pos_sort_num";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Fetch Positions based on Election Event
  public function fetchPositionsBasedOnElecEv($elId) {
    $sql = "SELECT * FROM position WHERE pos_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Search Voter
  public function searchVoter($elId, $name) {
    $sql = "SELECT * FROM voter 
            WHERE (v_fname LIKE CONCAT('%', :name, '%')
              OR v_lname LIKE CONCAT('%', :name, '%'))
              AND v_el_id = :v_el_id
            ORDER BY v_fname
            LIMIT 8";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['name' => $name, 'v_el_id' => $elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Add Candidate 
  public function addCandidate($voterId, $posId, $partyId, $img) {
    $sql = "INSERT INTO candidate (c_v_id, c_pos_id, c_par_id, c_img)
            VALUES (?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$voterId, $posId, $partyId, $img]);
    return true;
  }

  // Check if voter is already a candidate
  public function checkVoterIsCandidate($voterId, $elId) {
    $sql = "SELECT * FROM candidate
            INNER JOIN voter
              ON candidate.c_v_id = voter.v_id
            INNER JOIN election
              ON election.el_id = voter.v_el_id
            WHERE c_v_id = ? AND v_el_id = ? AND el_status = 'not-started'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$voterId, $elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Select existing positions in candidate in a partylist to check if there is vacant
  public function checkPosIsOcc($posId, $partyId, $elId) {
    $sql = "SELECT * FROM candidate
            INNER JOIN partylist
              ON candidate.c_par_id = partylist.par_id
            WHERE par_el_id = ? AND par_id = ? AND c_pos_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId, $partyId, $posId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Select many of candidates
  public function selectManyOfCands($posId) {
    $sql = "SELECT * FROM position WHERE pos_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$posId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  // Delete candidates of a partylist
  public function delCandsOfParty($partyId) {
    $sql = "DELETE FROM candidate WHERE c_par_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$partyId]);
    return true;
  }
  
  // Delete candidates based on position
  public function delCandsBasedOnPos($posId) {
    $sql = "DELETE FROM candidate WHERE c_pos_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$posId]);
    return true;
  }

  // Fetch Candidates
  public function fetchCands($elId, $partyId) {
    $sql = "SELECT * FROM candidate
            INNER JOIN voter
              ON candidate.c_v_id = voter.v_id
            INNER JOIN course
              ON voter.v_course_id = course.course_id
            INNER JOIN position
              ON candidate.c_pos_id = position.pos_id
            INNER JOIN partylist
              ON candidate.c_par_id = partylist.par_id
            WHERE par_el_id = ? AND par_id = ?
            ORDER BY pos_sort_num";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId, $partyId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch Candidates for deleting election event
  public function fetchCandsBasedOnDelElec($elId) {
    $sql = "SELECT * FROM candidate INNER JOIN position
            ON candidate.c_pos_id = position.pos_id
            WHERE pos_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch Candidate
  public function fetchCand($id) {
    $sql = "SELECT * FROM candidate
            INNER JOIN voter
              ON candidate.c_v_id = voter.v_id
            INNER JOIN course
              ON voter.v_course_id = course.course_id
            INNER JOIN position
              ON candidate.c_pos_id = position.pos_id
            INNER JOIN partylist
              ON candidate.c_par_id = partylist.par_id
            INNER JOIN election
              ON election.el_id = partylist.par_el_id
            WHERE c_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  /* Select election events that have not started yet to prevent 
  updating candidate's election event to a not "not-started" election event */
  public function fetchNotStartedElecEvents() {
    $sql = "SELECT * FROM `election` WHERE el_status = 'not-started'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Update Candidate With Image
  public function updateCandWithImg($vId, $img, $exist, $id) {
    $sql = "UPDATE candidate SET c_v_id = ?,
              c_img = ?,
              exist_cand = ?
            WHERE c_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$vId, $img, $exist, $id]);
    return true;
  }
  
  // Update Candidate Without Image
  public function updateCandWithoutImg($vId, $exist, $id) {
    $sql = "UPDATE candidate SET c_v_id = ?,
              exist_cand = ?
            WHERE c_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$vId, $exist, $id]);
    return true;
  }

  // Delete Candidate
  public function delCand($id) {
    $sql = "DELETE FROM candidate WHERE c_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    return true;
  }

  // Update voter iscandidate
  public function updateVoterIsCandidate($vId, $isCand) {
    $sql = "UPDATE voter SET iscandidate = ? WHERE v_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$isCand, $vId]);
    return true;
  }
  
  // Fetch Platform of Election Event and Party
  public function fetchPlatformBasedOnElecAndParty($partyId) {
    $sql = "SELECT * FROM partylist WHERE par_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$partyId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Fetch Campaigns
  public function addCampaign($uniqid) {
    $sql = "INSERT INTO campaign (cam_el_uniqid) VALUES(?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$uniqid]);
    return true;
  }
  
  // Fetch Campaigns
  public function fetchCampaigns() {
    $sql = "SELECT cam_id, el_name, status FROM campaign
            INNER JOIN election
              ON campaign.cam_el_uniqid = election.el_uniqid";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch Campaign
  public function fetchCampaign($id) {
    $sql = "SELECT * FROM campaign
            INNER JOIN election
              ON campaign.cam_el_uniqid = election.el_uniqid
            WHERE cam_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Update Campaign
  public function updateCampaign($syear, $smonth, $sdate, $stime, $eyear, $emonth, $edate, $etime, $status, $id) {
    $sql = "UPDATE campaign
            SET cam_start_year = ?,
              cam_start_month = ?,
              cam_start_date = ?,
              cam_start_time = ?,
              cam_end_year = ?,
              cam_end_month = ?,
              cam_end_date = ?,
              cam_end_time = ?,
              status = ?
            WHERE cam_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$syear, $smonth, $sdate, $stime, $eyear, $emonth, $edate, $etime, $status, $id]);
    return true;
  }

  // Delete Campaign
  public function delCampaign($uniqid) {
    $sql = "DELETE FROM campaign WHERE cam_el_uniqid = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$uniqid]);
    return true;
  }

  // Count total many of positions (exist_cand = 1) based on election event and then to be multiplied by election parties
  public function countManyOfPositionsBasedOnElId($id) {
    $sql = "SELECT SUM(pos_cand_many) AS total FROM position WHERE pos_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  // Count total many of non existing candidates(exist_cand = 0) based on election event
  public function countManyOfNonExistingCandsBasedOnElId($id) {
    $sql = "SELECT COUNT(*) AS 'nonExistCount' FROM candidate 
            INNER JOIN position
              ON candidate.c_pos_id = position.pos_id
            WHERE position.pos_el_id = ? AND candidate.exist_cand = 0";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Count all existing candidates based on election event
  public function countManyOfExistingCandsBasedOnElId($id) {
    $sql = "SELECT COUNT(*) AS 'existCount' FROM candidate 
            INNER JOIN position
              ON candidate.c_pos_id = position.pos_id
            WHERE position.pos_el_id = ? AND candidate.exist_cand = 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  // Fetch Candidates based on position id
  public function fetchCandsBasedOnPos($posId) {
    $sql = "SELECT * FROM candidate WHERE c_pos_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$posId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Fetch Candidates based on election id
  public function fetchCandsBasedOnElec($elId) {
    $sql = "SELECT * FROM candidate
            INNER JOIN voter
              ON candidate.c_v_id = voter.v_id
            INNER JOIN position
              ON candidate.c_pos_id = position.pos_id
            INNER JOIN partylist
              ON candidate.c_par_id = partylist.par_id
            INNER JOIN course
              ON voter.v_course_id = course.course_id
            WHERE pos_el_id = ? AND exist_cand = 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Fetch Student Based on Password
  public function fetchVotersBasedOnPass($pass) {
    $sql = "SELECT * FROM voter WHERE v_pass = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$pass]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Submit ballot
  public function submitBallot($vId, $candId, $year, $month, $day, $date, $time) {
    $sql = "INSERT INTO vote(vo_v_id, vo_c_id, vo_year, vo_month, vo_day, vo_date, vo_time)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$vId, $candId, $year, $month, $day, $date, $time]);
    return true;
  }

  // Update Voter into voted
  public function updateVoterIntoVoted($vId) {
    $sql = "UPDATE voter SET v_voted = 1 WHERE v_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$vId]);
    return true;
  }

  // Fetch Voted Candidates of a Student
  public function votedCandsOfStud($vId) {
    $sql = "SELECT * FROM vote 
            INNER JOIN candidate
              ON vote.vo_c_id = candidate.c_id
            INNER JOIN voter
              ON candidate.c_v_id = voter.v_id
            INNER JOIN course
            ON course.course_id = voter.v_course_id
            INNER JOIN position
              ON candidate.c_pos_id = position.pos_id
            INNER JOIN partylist
              ON candidate.c_par_id = partylist.par_id
            INNER JOIN election
              ON election.el_id = position.pos_el_id
            WHERE vote.vo_v_id = ?
            ORDER BY pos_sort_num";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$vId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Count votes of candidate
  public function countVotesOfCand($cId) {
    $sql = "SELECT COUNT(*) AS vote_count FROM vote WHERE vo_c_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$cId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  // Count Coures in elec event
  public function countCourses() {
    $sql = "SELECT COUNT(*) AS courses_count FROM course";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Count parties in elec event
  public function countParties($elId) {
    $sql = "SELECT COUNT(*) AS parties_count FROM partylist WHERE par_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Count positions in elec event
  public function countPositions($elId) {
    $sql = "SELECT COUNT(*) AS positions_count FROM position WHERE pos_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Select Voters who already voted
  public function fetchVotedVoters($elId) {
    $sql = "SELECT * FROM voter
            INNER JOIN course
              ON voter.v_course_id = course.course_id
            INNER JOIN election
              ON election.el_id = voter.v_el_id
            INNER JOIN vote
              ON vote.vo_v_id = voter.v_id
            WHERE election.el_id = ?
            ORDER BY course_name, v_yrlvl, v_lname";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
 
  // Select Voters who have not voted
  public function fetchHaveNotVotedVoters($elId) {
    $sql = "SELECT * FROM voter
            INNER JOIN course
              ON voter.v_course_id = course.course_id
            INNER JOIN election
              ON election.el_id = voter.v_el_id
            WHERE election.el_id = ? AND v_voted = 0
            ORDER BY course_name, v_yrlvl, v_lname";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  
  // Update Status of Election Event
  public function updateElecEventOnlyStatus($elId) {
    $sql = "UPDATE election SET el_status = 'not-started' WHERE el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    return true;
  }
  
  // Update Voters' Voted Status Based on Election Event
  public function updateVotersVotedStatus($elId) {
    $sql = "UPDATE voter SET v_voted = 0 WHERE v_el_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    return true;
  }
  
  // Select Votes Records Based on Election Event
  public function selectVotesBasedOnElId($elId) {
    $sql = "SELECT vo_v_id FROM vote
            INNER JOIN voter
              ON vote.vo_v_id = voter.v_id
            WHERE v_el_id = ?
            ORDER BY vo_v_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$elId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  // Delete Vote Records Based Voter id
  public function updateVotesBasedOnVoter($vId) {
    $sql = "DELETE FROM vote WHERE vo_v_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$vId]);
    return true;
  }
}