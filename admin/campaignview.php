<?php require_once './assets/php/header.php'; ?>
<main>
  <div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
      <div class="d-flex align-items-center flex-wrap">
        <label class="form-label">Election Event:</label>
        <select id="elect-event" class="form-select form-select-sm d-inline-block ms-2" style="width: 220px"></select>
       
        <label class="form-label ms-md-3">Party:</label>
        <select id="party-select" class="form-select form-select-sm d-inline-block ms-2" style="width: 150px"></select>
        <button class="btn btn-success btn-sm ms-2" id="cand-filter">Filter</button>
      </div>
    </div>

    <h2 class="mb-4 text-center fst-italic fw-bold" style="color:darkgreen">Candidates</h2>

    <div class="row g-4" id="data-wrapper">
      <!-- Loading Candidates -->
      <div class="d-flex align-items-center justify-content-center">
        <div class="spinner-border text-secondary" role="status"></div>
        <h2 class="text-secondary ms-2">Loading...</h2>
      </div>
    </div>

    <hr class="my-5">

    <h2 class="mb-3 text-center fst-italic fw-bold" style="color:darkgreen">Party Platform</h2>
    <div id="platform-data" class="text-center mb-5">
      <!-- Loading Platform -->
      <div class="d-flex align-items-center justify-content-center">
        <div class="spinner-border text-secondary" role="status"></div>
        <h2 class="text-secondary ms-2">Loading...</h2>
      </div>
    </div>
  </div>
</main>
<?php require_once './assets/php/footer.php'; ?>
<script>
  $(document).ready(function(e) {
    function swal(icon, title, text) {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
      })
    }

    async function fetchElecEvs() {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchElecEvents'})
      })
      return await res.json()
    }

    async function fetchCurElec() {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchCurrentElec'})
      })
      return await res.json()
    }

    async function fetchParties(elecEvId = $('#elect-event').val()) {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'filterParty',
          elecEvId
        })
      })
      return await res.json()
    }

    async function renEvents() {
      let [elecEvents, curElecEv] = await Promise.all([fetchElecEvs(), fetchCurElec()])

      if (curElecEv.length > 0) {
        let eventsMapped = elecEvents.map(elec => `
          <option value="${elec.el_id}" ${curElecEv[0].cur_el_id == elec.el_id ? 'selected' : ''}>${elec.el_name}</option>
        `)
        let eventsJoined = eventsMapped.join('')
        $('#elect-event').html(eventsJoined)
      } 

      let eventsMapped = elecEvents.map(elec => `
          <option value="${elec.el_id}">${elec.el_name}</option>
        `)
      let eventsJoined = eventsMapped.join('')
      $('#elec-event-select').html(eventsJoined)

      let parties = await fetchParties()
      let partiesMapped = parties.map(party => `
        <option value="${party.par_id}">${party.par_name}</option>
      `)
      let partiesJoined = partiesMapped.join('')
      $('#party-select').html(partiesJoined)
    }

    $('#elect-event').change(function(e) {
      e.stopPropagation()

      let elId = $(this).val()

      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'changeElecEvOnCandidates', elId },
        success: res => {
          let parties = JSON.parse(res)

          let partiesMapped = parties.map(party => `
            <option value="${party.par_id}">${party.par_name}</option>
          `)
          let partiesJoined = partiesMapped.join('')
          $('#party-select').html(partiesJoined)
        }
      })
    })

    // Fetch Candidates
    async function fetchCandidates(filtered = false) {
      if (!filtered) {
        await renEvents()
      }

      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: {
          action: 'fetchCands',
          elId: $('#elect-event').val(),
          partyId: $('#party-select').val(),
        },
        success: res => {
          let cands = JSON.parse(res)
          console.log(cands)

          if (cands.length > 0){
            let candsFiltered = cands.filter(cand => cand.exist_cand == 1)
            let candsMapped = candsFiltered.map(cand => `
              <div class="col-sm-6 col-md-4 text-center">
                <img src="${cand.c_img ? `./assets/img/${cand.c_img}` : cand.v_gender ? './assets/img/avatar man.png' : './assets/img/avatar woman.png'}" alt="candidate image" class="img-thumbnail img-fluid mx-auto mb-2 rounded-circle" style="object-fit:cover;height:auto;width:320px;">
                <small class="fs-5 fw-bold text-decoration-underline">${cand.pos_name} - ${cand.par_name}</small>
                <h4 class="m-0 mb-2 text-success fw-bold">${cand.v_fname} ${cand.v_mname ? `${cand.v_mname[0]}. ` : ''}${cand.v_lname}</h4>
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
      let res = await fetch('./assets/php/action.php', {
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