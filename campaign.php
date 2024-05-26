<?php require_once './admin/assets/php/studheader.php'; ?>
<main>
  <div class="container-fluid">
    <div class="d-flex align-items-center">
      <label class="form-label">Party:</label>
      <select id="party-select" class="form-select form-select-sm d-inline-block ms-2" style="width: 150px"></select>
      <button class="btn btn-success btn-sm ms-2" id="cand-filter">Filter</button>
    </div>

    <h2 class="my-4 text-center fst-italic fw-bold" style="color:darkgreen">Candidates</h2>
    
    <div class="row g-4" id="data-wrapper">
      <!-- Loading Candidates -->
      <div class="d-flex align-items-center justify-content-center">
        <div class="spinner-border text-secondary" role="status"></div>
        <h3 class="text-secondary ms-2">Loading...</h3>
      </div>
    </div>

    <hr class="my-5">

    <h2 class="mb-3 text-center fst-italic fw-bold" style="color:darkgreen">Party Platform</h2>
    <div id="platform-data" class="text-center mb-5">
      <!-- Loading Platform -->
      <div class="d-flex align-items-center justify-content-center">
        <div class="spinner-border text-secondary" role="status"></div>
        <h3 class="text-secondary ms-2">Loading...</h3>
      </div>
    </div>
  </div>
</main>
<?php require_once './admin/assets/php/studfooter.php'; ?>
<script>
  $(document).ready(function(e) {
    let parties = JSON.parse('<?= json_encode($parties);?>')

    let partiesMapped = parties.map(par => `<option value="${par.par_id}">${par.par_name}</option>`)
    let partiesJoined = partiesMapped.join('')
    $('#party-select').html(partiesJoined)

    // Fetch Candidates
    async function fetchCandidates() {
      $.ajax({
        url: './admin/assets/php/action.php',
        method: 'post',
        data: {
          action: 'fetchCands',
          elId: '<?= $elId?>',
          partyId: $('#party-select').val(),
        },
        success: res => {
          let cands = JSON.parse(res)
          // console.log(cands)

          if (cands.length > 0){
            let candsFiltered = cands.filter(cand => cand.exist_cand == 1)
            let candsMapped = candsFiltered.map(cand => `
              <div class="col-6 col-md-4 text-center">
                <img src="${cand.c_img ? `./admin/assets/img/${cand.c_img}` : cand.v_gender ? './admin/assets/img/avatar man.png' : './admin/assets/img/avatar woman.png'}" alt="candidate image" class="img-thumbnail img-fluid mx-auto mb-2 rounded-circle" style="object-fit:cover;height:auto;width:320px;">
                <small class="fw-bold text-decoration-underline">${cand.pos_name} - ${cand.par_name}</small>
                <h6 class="m-0 mb-2 text-success fw-bold">${cand.v_fname} ${cand.v_mname ? `${cand.v_mname[0]}. ` : ''}${cand.v_lname}</h6>
                <small>${cand.course_name} ${cand.v_yrlvl}</small>
                <small class="d-block" style="margin-top:-5px;">${cand.course_desc}</small>
              </div>
            `)
            let candsJoined = candsMapped.join('')
            $('#data-wrapper').html(candsJoined)
          } else {
            $('#data-wrapper').html(`<h4 class="text-center text-secondary fst-italic">No candidates.</h4>`)
          }
        }
      })
    }

    async function fetchPlatformBasedOnElecAndParty(elId = $('#elect-event').val(), partyId = $('#party-select').val()) {
      let res = await fetch('./admin/assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchPlatformBasedOnElecAndParty', elId, partyId })
      })
      return await res.text()
    }

    // Fetch Platform of Election Event and Party
    async function fetchPlatform(filtered = false) {
      if (!filtered) {
        await fetchCandidates()
      }
      let res = await fetchPlatformBasedOnElecAndParty()
      let platform = JSON.parse(res)
      // console.log(platform)

      if (platform[0]?.par_platform && platform[0]?.par_platform != '<p><br></p>') {
        $('#platform-data').html(platform[0]?.par_platform)
      } else {
        $('#platform-data').html(`<h4 class="text-center text-secondary fst-italic">No Platform.</h4>`)
      }
    }
    fetchPlatform()

    $('#cand-filter').click(function(e) {
      e.stopPropagation()
      e.preventDefault()
      $(this).text('Filtering...')
      $(this).prop('disabled', true)
      fetchCandidates(filtered = true)
      fetchPlatform(filtered = true)
      $(this).text('Filter')
      $(this).prop('disabled', false)
    })
  })
</script>
</body>
</html>