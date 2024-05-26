<?php require_once './admin/assets/php/studheader.php'; ?>
<main>
  <div class="container-fluid">
    <br>
    <h2 class="mb-4 text-center fst-italic fw-bold" style="color:darkgreen">Voted Candidates</h2>
    
    <div class="row g-4" id="data-wrapper">
      <!-- Loading Candidates -->
      <div class="d-flex align-items-center justify-content-center">
        <div class="spinner-border text-secondary" role="status"></div>
        <h3 class="text-secondary ms-2">Loading...</h3>
      </div>
    </div>
    <small id="date-voted" class="text-center d-block my-4 text-secondary"></small>
  </div>
</main>
<?php require_once './admin/assets/php/studfooter.php'; ?>
<script>
  $(document).ready(function() {
    let elec = JSON.parse('<?=json_encode($elec)?>')
    console.log(elec)

    function fetchVotedCands() {
      $.ajax({
        url: './admin/assets/php/action.php',
        method: 'post',
        data: {
          vId: '<?= $studId?>',
          action: 'fetchVotedCands'
        },
        success: res => {
          let cands = JSON.parse(res)
          // console.log(cands)

          if (elec.el_status == 'not-started') {
            $('#data-wrapper').html(`
              <h4 class="text-center text-secondary fst-italic">Voting not yet started.</h4>
              <h5 class="text-secondary text-center">
                Voting will be on ${elec.el_month} ${elec.el_date}, ${elec.el_year} (<?=date('h:ia', strtotime($elec['el_time_start']))?> - <?=date('h:ia', strtotime($elec['el_time_end']))?>)
              </h5>
            `)
            return
          }

          if (elec.el_status == 'done') {
            if (cands.length > 0) {
              let candsFiltered = cands.filter(cand => cand.exist_cand == 1)
              let candsMapped = candsFiltered.map(cand => `
                <div class="col-6 col-md-4 text-center">
                  <img src="${cand.c_img ? `./admin/assets/img/${cand.c_img}` : cand.v_gender ? './admin/assets/img/avatar man.png' : './admin/assets/img/avatar woman.png'}" alt="candidate image" class="img-thumbnail img-fluid mx-auto mb-2 rounded-circle" style="object-fit:cover;height:auto;width:320px;">
                  <small class="fw-bold text-decoration-underline">${cand.pos_name} - ${cand.par_name}</small>
                  <h6 class="text-success fw-bold">${cand.v_fname} ${cand.v_mname ? `${cand.v_mname[0]}. ` : ''}${cand.v_lname}</h6>
                  <small>${cand.course_name} ${cand.v_yrlvl}</small>
                  <small class="d-block" style="margin-top:-5px;">${cand.course_desc}</small>
                </div>
              `)
              let candsJoined = candsMapped.join('')
              $('#data-wrapper').html(candsJoined)
              $('#date-voted').text(`Voted on ${cands[0].vo_month} ${cands[0].vo_date}, ${cands[0].vo_year} on ${cands[0].vo_time} (${cands[0].vo_day})`)
            } else {
              $('#data-wrapper').html(`<h4 class="text-center text-secondary fst-italic">You did not vote.</h4>`)
            }
            return
          }
          
          if (elec.el_status != 'done' || elec.el_status != 'not-started') {
            if (cands.length > 0) {
              let candsFiltered = cands.filter(cand => cand.exist_cand == 1)
              let candsMapped = candsFiltered.map(cand => `
                <div class="col-sm-6 col-md-4 text-center">
                  <img src="${cand.c_img ? `./admin/assets/img/${cand.c_img}` : cand.v_gender ? './admin/assets/img/avatar man.png' : './admin/assets/img/avatar woman.png'}" alt="candidate image" class="img-thumbnail img-fluid mx-auto mb-2 rounded-circle" style="object-fit:cover;height:auto;width:320px;">
                  <small class="fs-5 fw-bold text-decoration-underline">${cand.pos_name} - ${cand.par_name}</small>
                  <h4 class="m-0 mb-2 text-success fw-bold">${cand.v_fname} ${cand.v_mname ? `${cand.v_mname[0]}. ` : ''}${cand.v_lname}</h4>
                  <small>${cand.course_name} ${cand.v_yrlvl}</small>
                  <small class="d-block" style="margin-top:-5px;">${cand.course_desc}</small>
                </div>
              `)
              let candsJoined = candsMapped.join('')
              $('#data-wrapper').html(candsJoined)
              $('#date-voted').text(`Voted on ${cands[0].vo_month} ${cands[0].vo_date}, ${cands[0].vo_year} on ${cands[0].vo_time} (${cands[0].vo_day})`)
            }
            return
          }
        }
      })
    }
    fetchVotedCands()
  })
</script>
</body>
</html>