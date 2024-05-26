<?php require_once './assets/php/header.php'; ?>
<main>
  <div class="container-fluid">
    <div class="d-flex justify-content-between">
      <div class="d-flex align-items-center flex-wrap">
        <label class="form-label">Election Event:</label>
        <select id="elect-event" class="form-select form-select-sm d-inline-block ms-2" style="width: 220px"></select>
        
        <label class="form-label ms-md-3">Party:</label>
        <select id="party-select" class="form-select form-select-sm d-inline-block ms-2" style="width: 150px"></select>
        <button class="btn btn-success btn-sm ms-2" id="cand-filter">Filter</button>
      </div>
      <button data-bs-toggle="modal" data-bs-target="#add-cand-modal" class="float-end btn btn-success">Add Candidate</button>
    </div>

    <!-- Add Candidate Modal -->
    <div class="modal fade" id="add-cand-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h1 class="modal-title fs-5">Add Candidate</h1>
            <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="add-cand-form">
              <div class="row">
                <div class="col-md-6">
                  <!-- <input type="hidden" name="add-election-id" id="add-election-id"> -->
                  <label class="form-label">Election Event:</label>
                  <select name="add-elec-event-select" id="add-elec-event-select" class="form-select mb-2" required></select>
                 
                  <label class="form-label">Party:</label>
                  <select name="add-party-select" id="add-party-select" class="form-select mb-2" required></select>
                  
                  <label class="form-label">Position:</label>
                  <select name="add-pos-select" id="add-pos-select" class="form-select mb-2" required></select>
                  
                  <label class="form-label">Voter:</label>
                  <input type="hidden" name="add-voter-value" id="add-voter-value" required>
                  <input type="search" name="add-voter" id="add-voter" class="form-control mb-3" required>
                  <ul class="list-group" id="searched-results" style="position:relative;top:-10px;">
                    <!-- <li class="list-group-item">An item</li> -->
                  </ul>
                  <input type="file" name="add-img" id="add-img" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                  <img src="./assets/img/avatar man.png" id="add-img-show" alt="image of candidate" class="mx-auto img-thumbnail object-fit-contain mb-2" style="width:360px;height:360px;">
                </div>
              </div>
              <input type="submit" value="Add Candidate" id="add-cand-btn" class="btn btn-success w-100 mt-2">
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Edit Candidate Modal -->
    <div class="modal fade" id="edit-cand-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h1 class="modal-title fs-5">Edit Candidate</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-cand-form">
              <div class="row">
                <div class="col-md-6">
                  <input type="hidden" name="edit-cand-id" id="edit-cand-id">
                  <label class="form-label">Election Event:</label>
                  <input type="hidden" name="edit-elec-id" id="edit-elec-id">
                  <input type="text" class="form-control mb-2" name="edit-elec-event-select" id="edit-elec-event-select" disabled readonly>
                 
                  <label class="form-label">Party:</label>
                  <input type="text" class="form-control mb-2" name="edit-party-select" id="edit-party-select" disabled readonly>
                  
                  <label class="form-label">Position:</label>
                  <input type="text" class="form-control mb-2" name="edit-pos-select" id="edit-pos-select" disabled readonly>

                  <label class="form-label">Voter:</label>
                  <input type="hidden" name="edit-old-voter-value" id="edit-old-voter-value" required>
                  <input type="hidden" name="edit-voter-value" id="edit-voter-value" required>
                  <input type="search" name="edit-voter" id="edit-voter" class="form-control mb-3" required>
                  <ul class="list-group" id="edit-searched-results" style="position:relative;">
                    <!-- <li class="list-group-item">An item</li> -->
                  </ul>
                  
                  <label class="form-label">Existence:</label>
                  <select name="edit-exist" id="edit-exist" class="form-select">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                  </select>

                  <input type="file" name="edit-img" id="edit-img" class="form-control mt-3" accept="image/*">
                </div>
                <div class="col-md-6">
                  <img src="./assets/img/avatar man.png" id="edit-img-show" alt="image of candidate" class="mx-auto img-thumbnail object-fit-contain mb-2" style="width:360px;height:360px;">
                </div>
              </div>
              <input type="submit" value="Update Candidate" id="update-cand-btn" class="btn btn-warning w-100 mt-2">
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- View Candidate Modal -->
    <div class="modal fade" id="view-cand-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-info">
            <h1 class="modal-title fs-5">Candidate Details</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <small class="h5 d-block mb-2">
              <span id="view-cand-elec">Halalan 2023</span> -
              <span id="view-cand-party">Mabagsik</span>
            </small>
            <img src="./assets/img/avatar man.png" id="view-cand-img" alt="candidate image" class="img-thumbnail mx-auto mb-2" style="object-fit:cover;width:310px;height:310px">
            <small id="view-cand-pos" class="h5 d-block">Governor</small>
            <small id="view-cand-name" class="h5 d-block mb-4">Ryan Albert S. Masungsong</small>
            <span id="view-cand-course-year" class="">BSIT 4</span><br>
            <span id="view-cand-course-desc" class="">Bachelor of Science in Information Technology</span>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card rounded-0 shadow-sm mt-3">
          <div class="card-header bg-success rounded-0">
            <span class="fs-5 text-white">Candidates</span>
          </div>
          <div class="card-body rounded-0 table-responsive" id="data-wrapper">
            <!-- Loading Candidates -->
            <div class="d-flex align-items-center justify-content-center">
              <div class="spinner-border text-secondary" role="status"></div>
              <h2 class="text-secondary ms-2">Loading...</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require_once './assets/php/footer.php'; ?>
<script>
  $(document).ready(function() {
    let coursesGlobal = []
    let addVoterField = document.querySelector('#add-voter')
    let editVoterField = document.querySelector('#edit-voter')

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
    
    async function fetchNotStartedElecEvs() {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchNotStartedElecEvents'})
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
    
    async function fetchPositionsBasedOnElecEv(elecEv = $('#elect-event').val()) {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'fetchPositionsBasedOnElecEv',
          elecEv,
        })
      })
      return await res.json()
    }

    function viewImg(candImgInp, candImgShow) {
      candImgInp.change(function(e) {
        e.preventDefault()

        let [file] = this.files
        if (file) {
          candImgShow.attr('src', URL.createObjectURL(file))
        }
      })
    }

    async function renEvents() {
      let [elecEvents, curElecEv] = await Promise.all([fetchElecEvs(), fetchCurElec(), fetchNotStartedElecEvs()])

      if (curElecEv.length > 0) {
        let eventsMapped = elecEvents.map(elec => `
          <option value="${elec.el_id}" ${curElecEv[0].cur_el_id == elec.el_id ? 'selected' : ''}>${elec.el_name}</option>
        `)
        let eventsJoined = eventsMapped.join('')
        $('#elect-event').html(eventsJoined)
        $('#add-elec-event-select').html(eventsJoined)
      }  else {
        
        let eventsMapped = elecEvents.map(elec => `
        <option value="${elec.el_id}">${elec.el_name}</option>
        `)
        let eventsJoined = eventsMapped.join('')
        $('#elec-event').html(eventsJoined)
        $('#add-elec-event-select').html(eventsJoined)
      }

      let parties = await fetchParties()

      let partiesMapped = parties.map(party => `
        <option value="${party.par_id}">${party.par_name}</option>
      `)
      let partiesJoined = partiesMapped.join('')
      $('#party-select').html(partiesJoined)
      $('#add-party-select').html(partiesJoined)

      let positions = await fetchPositionsBasedOnElecEv()

      let positionsMapped = positions.map(pos => `
        <option value="${pos.pos_id}">${pos.pos_name}</option>
      `)
      let positionsJoined = positionsMapped.join('')
      $('#add-pos-select').html(positionsJoined)

      addVoterField.addEventListener('input', e => {
        if (e.target.value.trim()) {
          $.ajax({
            url: './assets/php/action.php',
            method: 'post',
            data: { 
              action: 'searchVoter',
              elId: $('#add-elec-event-select').val(),
              name: e.target.value
            },
            success: res => {
              let voters = JSON.parse(res)
              // console.log(res)
              let votersMapped = voters.map(voter => `<button class="list-group-item searched-result-btn" style="width:270px;" data-id="${voter?.v_id}" data-gender="${voter?.v_gender}">${voter?.v_fname} ${voter?.v_mname ? `${voter?.v_mname[0]}. ` : ''}${voter?.v_lname}</button>`)
              let votersJoined = votersMapped.join('')
              $('#searched-results').html(`<div style="position:absolute;z-index:1;top:-10px;">${votersJoined}</div>`)
              $('#add-voter-value').val('')
            }
          })
        } else {
          $('#searched-results').html('')
        }
      })
    }

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
          // console.log(cands)
          if (cands.length < 1) {
            $("#data-wrapper").html(`
              <h4 class="text-center text-secondary fst-italic">No candidates yet.</h4>
            `)
            return;
          } else {
            let candsMapped = cands.map(cand => `
              <tr>
                <td>${cand.pos_sort_num}</td>
                <td>${cand.pos_name}</td>
                <td>${cand.v_fname} ${cand.v_mname ? `${cand.v_mname[0]}.` : ''} ${cand.v_lname}</td>
                <td>${cand.course_name} ${cand.v_yrlvl}</td>
                <td>${cand.exist_cand ? 'Yes' : 'No'}</td>
                <td>
                  <a href="#" title="View" class="view-cand text-decoration-none" id="view-cand-${cand.c_id}" data-bs-toggle="modal" data-bs-target="#view-cand-modal">
                    <i class="bi bi-info-circle-fill text-info fs-5"></i>
                  </a>
                  <a href="#" title="Edit" class="edit-cand text-decoration-none" id="edit-cand-${cand.c_id}" data-election="data-el-${cand.v_el_id}" data-bs-toggle="modal" data-bs-target="#edit-cand-modal">
                    <i class="bi bi-pencil-square text-warning fs-5"></i>
                  </a>
                  <a href="#" title="Delete" class="del-cand text-decoration-none" id="del-cand-${cand.c_id}" data-el-id="${cand.v_el_id}" data-voter-id="${cand.v_id}">
                    <i class="bi bi-trash-fill text-danger fs-5"></i>
                  </a>
                </td>
              </tr>
            `)
            let candsJoined = candsMapped.join('')
            let candTable = `
              <table class="table table-striped table-bordered w-100" id="cands-table">
                <thead>
                  <tr>
                    <th>Sort #</th>
                    <th>Position</th>
                    <th>Name</th>
                    <th>Course & Year Level</th>
                    <th>Existing</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  ${candsJoined}
                </tbody>
              </table>
            `
            $('#data-wrapper').html(candTable)
            // *******
            // *******
            // Setup - add a text input to each footer cell
            $('#cands-table thead tr')
              .clone(true)
              .addClass('filters')
              .appendTo('#cands-table thead');
        
            var table = $('#cands-table').DataTable({
              orderCellsTop: true,
              fixedHeader: true,
              initComplete: function () {
                var api = this.api();

                // For each column
                api
                  .columns()
                  .eq(0)
                  .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    var title = $(cell).text();
                    $(cell).html(`<input type="text" placeholder="${title}" class="form-control form-control-sm ${title == 'Actions' ? 'd-none' : ''}" />`);

                    // On every keypress in this input
                    $('input', $('.filters th').eq($(api.column(colIdx).header()).index()))
                      .off('keyup change')
                      .on('change', function (e) {
                        // Get the search value
                        $(this).attr('title', $(this).val());
                        var regexr = '({search})'; //$(this).parents('th').find('select').val();

                        var cursorPosition = this.selectionStart;
                        // Search the column for that value
                        api
                          .column(colIdx)
                          .search(
                            this.value != ''
                                ? regexr.replace('{search}', '(((' + this.value + ')))')
                                : '',
                            this.value != '',
                            this.value == ''
                          )
                          .draw();
                      })
                      .on('keyup', function (e) {
                        e.stopPropagation();

                        $(this).trigger('change');
                        $(this)
                          .focus()[0]
                          // .setSelectionRange(cursorPosition, cursorPosition);
                      });
                  });
              },
            });
          }
        }
      })
    }
    fetchCandidates()

    // Change election event then parties will filter
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
    
    // Change election event then parties will filter in add candidate modal
    $('#add-elec-event-select').change(function(e) {
      e.stopPropagation()

      let elId = e.target.value
      // $('#add-election-id').val(elId)

      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'changeElecEvOnCandidates', elId },
        success: async res => {
          let parties = JSON.parse(res)
          
          let partiesMapped = parties.map(party => `
            <option value="${party.par_id}">${party.par_name}</option>
          `)
          let partiesJoined = partiesMapped.join('')
          $('#add-party-select').html(partiesJoined)
          
          let positions = await fetchPositionsBasedOnElecEv($('#add-elec-event-select').val())

          let positionsMapped = positions.map(pos => `
            <option value="${pos.pos_id}">${pos.pos_name}</option>
          `)
          let positionsJoined = positionsMapped.join('')
          $('#add-pos-select').html(positionsJoined)

          $('#add-voter').val('')
          $('#searched-results').html('')
        }
      })
    })

    viewImg($('#add-img'), $('#add-img-show'))
    viewImg($('#edit-img'), $('#edit-img-show'))

    function searcVoterField(field, elecId, resultsCon, voterValCon, resultsClass) {
      field.addEventListener('input', e => {
        if (e.target.value.trim()) {
          $.ajax({
            url: './assets/php/action.php',
            method: 'post',
            data: { 
              action: 'searchVoter',
              elId: elecId,
              name: e.target.value
            },
            success: res => {
              // let voters = JSON.parse(res)
              console.log(res)
              let votersMapped = voters.map(voter => `<button class="list-group-item ${resultsClass}" style="position:absolute;z-index:1;top:-10px;" data-id="${voter?.v_id}" data-gender="${voter?.v_gender}">${voter?.v_fname} ${voter?.v_mname ? `${voter?.v_mname[0]}. ` : ''}${voter?.v_lname}</button>`)
              let votersJoined = votersMapped.join('')
              resultsCon.html(votersJoined)
              voterValCon.val('')
            }
          })
        } else {
          resultsCon.html('')
        }
      })
    }

    function selectSearchedVoter(btnClass, voterVal, voter, imgInp, imgShow, resultsCon) {
      $('body').on('click', btnClass, function(e) {
        e.preventDefault()
        e.stopPropagation()

        let id = $(this).attr('data-id')
        let result = $(this).text()
        
        let gender = $(this).attr('data-gender')
        
        let img = 'avatar man.png'

        if (gender == '0') {
          img = 'avatar woman.png'
        }

        voterVal.val(id)
        voter.val(result)

        if (!imgInp.val()) {
          imgShow.attr('src', `./assets/img/${img}`)
        }

        resultsCon.html('')
      })
    }
    selectSearchedVoter('.searched-result-btn', $('#add-voter-value'), $('#add-voter'), $('#add-img'), $('#add-img-show'), $('#searched-results'))
    selectSearchedVoter('.edit-searched-result-btn', $('#edit-voter-value'), $('#edit-voter'), $('#edit-img'), $('#edit-img-show'), $('#edit-searched-results'))

    $('#add-cand-btn').click(function(e) {
      if ($('#add-cand-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Adding Candidate...')
        $(this).prop('disabled', true)

        data = new FormData($('#add-cand-form')[0])
        data.append('action', 'addCandidate')

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          processData: false,
          contentType: false,
          cache: false,
          data: data,
          success: res => {
            // console.log(res)
            if (res == 'no voter') {
              swal('error', 'Oops!', 'Please select voter.')
            } else if (res == '1') {
              fetchCandidates()
              swal('success', 'Added!', 'Candidate was successfully added.')
              $('#add-cand-form')[0].reset()
              $('#add-cand-modal').modal('hide')
            } else if (res == 'voter is already a candidate') {
              swal('error', 'Oops!', 'Voter is already a candidate.')
            } else if (res == 'not started anymore') {
              swal('error', 'Oops!', 'If an election event hasn\'t started yet, then adding candidate(s) will be allowed.')
            } else if (res == 'exceed candidate many') {
              swal('error', 'Oops!', 'Position can\'t exceed on how many it should be.')
            } else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $('#add-cand-btn').val('Add Candidate')
            $('#add-cand-btn').prop('disabled', false)
          }
        })
      }
    })

    $('body').on('click', '.view-cand', function(e) {
      let id = $(this).attr('id')
      id = id.substr(10)
      
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchCand', id },
        success: res => {
          let cand = JSON.parse(res)
          let img = './assets/img/avatar man.png'
          let name = `${cand.v_fname} ${cand.v_mname ? `${cand.v_mname[0]}.` : ''} ${cand.v_lname}`

          $('#view-cand-elec').text(cand.el_name)
          $('#view-cand-party').text(cand.par_name)

          if (cand.c_img) {
            img = `./assets/img/${cand.c_img}`
          } else {
            if (cand.v_gender == 0) {
              img = './assets/img/avatar woman.png'
            }
          }

          $('#view-cand-img').attr('src', img)
          $('#view-cand-pos').text(cand.pos_name)
          $('#view-cand-name').text(name)
          $('#view-cand-course-year').text(`${cand.course_name} ${cand.v_yrlvl}`)
          $('#view-cand-course-desc').text(`${cand.course_desc}`)
        }
      })
    })

    $('body').on('click', '.edit-cand', function(e) {
      let elId = $(this).attr('data-election')
      elId = elId.substr(8)

      let id = $(this).attr('id')
      id = id.substr(10)

      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchCand', elId, id },
        success: async res => {
          let cand = JSON.parse(res)

          // let [elecs, parties, positions] = await Promise.all([
          //   fetchNotStartedElecEvs(), 
          //   fetchParties(cand.el_id), 
          //   fetchPositionsBasedOnElecEv(cand.el_id)
          // ])

          // // Render election events
          // let elecsMapped = elecs.map(elec => `
          // <option value="${elec.el_id}">${elec.el_name}</option>
          // `)
          // let elecsJoined = elecsMapped.join('')
          // $('#edit-elec-event-select').html(elecsJoined)

          // // Render parties
          // let partiesMapped = parties.map(party => `
          //   <option value="${party.par_id}">${party.par_name}</option>
          // `)
          // let partiesJoined = partiesMapped.join('')
          // $('#edit-party-select').html(partiesJoined)

          // // Render positions
          // let positionsMapped = positions.map(pos => `
          //   <option value="${pos.pos_id}">${pos.pos_name}</option>
          // `)
          // let positionsJoined = positionsMapped.join('')
          // $('#edit-pos-select').html(positionsJoined)

          let img = './assets/img/avatar man.png'
          let name = `${cand.v_fname} ${cand.v_mname ? `${cand.v_mname[0]}. ` : ''}${cand.v_lname}`

          if (cand.c_img) {
            img = `./assets/img/${cand.c_img}`
          } else {
            if (cand.v_gender == 0) {
              img = './assets/img/avatar woman.png'
            }
          }
          
          $('#edit-cand-id').val(cand.c_id)
          $('#edit-elec-id').val(cand.el_id)
          $('#edit-elec-event-select').val(cand.el_name)
          $('#edit-party-select').val(cand.par_name)
          $('#edit-pos-select').val(cand.pos_name)
          $('#edit-exist').val(cand.exist_cand)
          $('#edit-old-voter-value').val(cand.v_id)
          $('#edit-voter-value').val(cand.v_id)
          $('#edit-voter').val(name)
          $('#edit-img-show').attr('src', img)
          // searcVoterField(editVoterField, cand.el_id, $('#edit-searched-results'), $('#edit-voter-value'), 'edit-searched-result-btn')
          editVoterField.addEventListener('input', e => {
            if (e.target.value.trim()) {
              $.ajax({
                url: './assets/php/action.php',
                method: 'post',
                data: { 
                  action: 'searchVoter',
                  elId: cand.el_id,
                  name: e.target.value
                },
                success: res => {
                  let voters = JSON.parse(res)
                  // console.log(res)
                  let votersMapped = voters.map(voter => `<button class="list-group-item edit-searched-result-btn" style="position:absolute;z-index:1;top:-10px;" data-id="${voter?.v_id}" data-gender="${voter?.v_gender}">${voter?.v_fname} ${voter?.v_mname ? `${voter?.v_mname[0]}. ` : ''}${voter?.v_lname}</button>`)
                  let votersJoined = votersMapped.join('')
                  $('#edit-searched-results').html(votersJoined)
                  $('#edit-voter-value').val('')
                }
              })
            } else {
              $('#edit-searched-results').html('')
            }
          })
        }
      })
    })

    $('#edit-elec-event-select').change(function(e) {
      e.stopPropagation()

      let elId = $(this).val()

      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'changeElecEvOnCandidates', elId },
        success: async res => {
          let parties = JSON.parse(res)

          let partiesMapped = parties.map(party => `
            <option value="${party.par_id}">${party.par_name}</option>
          `)
          let partiesJoined = partiesMapped.join('')
          $('#edit-party-select').html(partiesJoined)


          let positions = await fetchPositionsBasedOnElecEv($('#edit-elec-event-select').val())

          let positionsMapped = positions.map(pos => `
            <option value="${pos.pos_id}">${pos.pos_name}</option>
          `)
          let positionsJoined = positionsMapped.join('')
          $('#edit-pos-select').html(positionsJoined)
        }
      })
    })

    $('#cand-filter').click(function(e) {
      e.stopPropagation()
      e.preventDefault()
      $(this).text('Filtering...')
      $(this).prop('disabled', true)
      fetchCandidates(filtered = true)
      $(this).text('Filter')
      $(this).prop('disabled', false)
    })

    // Update Voter
    $('#update-cand-btn').click(function(e) {
      if ($('#edit-cand-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Updating Candidate...')
        $(this).prop('disabled', true)

        let data = new FormData($('#edit-cand-form')[0])
        data.append('action', 'updateCand')

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          processData: false,
          contentType: false,
          cache: false,
          data,
          success: res => {
            // console.log(res)
            if (res == 'no voter') {
              swal('error', 'Oops!', 'Please select voter.')
            } else if (res == '1') {
              fetchCandidates()
              swal('success', 'Updated!', 'Candidate was successfully updated.')
              $('#edit-cand-form')[0].reset()
              $('#edit-cand-modal').modal('hide')
            } else if (res == 'voter is already a candidate') {
              swal('error', 'Oops!', 'Voter is already a candidate.')
            } else if (res == 'not started anymore') {
              swal('error', 'Oops!', 'If an election event hasn\'t started yet, then updating candidate(s) will be allowed.')
            } else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $('#update-cand-btn').val('Update Candidate')
            $('#update-cand-btn').prop('disabled', false)
          }
        })

      }
    })

    // Delete Voter
    $('body').on('click', '.del-cand', async function(e) {
      let id = $(this).attr('id')
      id = id.substr(9)

      let elId = $(this).attr('data-el-id')
      let vId = $(this).attr('data-voter-id')

      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: './assets/php/action.php',
            method: 'post',
            data: { 
              action: 'delCand',
              id,
              elId,
              vId
            },
            success: function(res) {
              if (res == '1') {
                fetchCandidates()
                swal('success', 'Deleted!', 'Candidate was successfully deleted.')
              } else if (res == 'not not-started anymore') {
                swal('error', 'Oops!', 'Deleting of candidates is only allowed if election event has not started yet.')
              } else {
                swal('error', 'Oops!', 'Something went wrong, try again.')
              }
            }
          })
        }
      })
    })
  })
</script>