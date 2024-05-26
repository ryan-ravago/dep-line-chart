<?php require_once './assets/php/header.php'; ?>
<main>
  <div class="container-fluid">
    <div class="d-flex justify-content-between">
      <div class="d-flex align-items-center">
        <label class="form-label">Election Event:</label>
        <select id="elect-event" class="form-select form-select-sm d-inline-block ms-2" style="width: 220px"></select>
        <button class="btn btn-success btn-sm ms-2" id="voter-filter">Filter</button>
      </div>
      <button data-bs-toggle="modal" data-bs-target="#add-voter-modal" class="float-end btn btn-success">Add Voter</button>
    </div>
    <div class="mt-3">
      <a href="#" target="_blank" id="print-voters" class="btn btn-secondary">Print</a>
      <a href="#" id="download-voters" class="btn btn-danger">PDF</a>
    </div>

    <!-- Add Voter Modal -->
    <div class="modal fade" id="add-voter-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h1 class="modal-title fs-5">Add Voter</h1>
            <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="add-voter-form">
              <label class="form-label">Election Event:</label>
              <select name="elec-event-select" id="elec-event-select" class="form-select mb-2"></select>

              <label class="form-label">First name:</label>
              <input type="text" name="voter-fname" id="voter-fname" class="form-control mb-2" required>
              
              <label class="form-label">Middle name:</label>
              <input type="text" name="voter-mname" id="voter-mname" class="form-control mb-2">
              
              <label class="form-label">Last name:</label>
              <input type="text" name="voter-lname" id="voter-lname" class="form-control mb-2" required>
              
              <label class="form-label">Course:</label>
              <select name="voter-course" id="voter-course" class="form-select mb-2"></select>
              
              <label class="form-label">Year Level:</label>
              <select name="voter-year-level" id="voter-year-level" class="form-select mb-2">
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
                <!-- <option value="5">5th Year</option> -->
              </select>
              
              <label class="form-label">Gender:</label>
              <select name="voter-gender" id="voter-gender" class="form-select">
                <option value="1">Male</option>
                <option value="0">Female</option>
              </select>

              <input type="submit" value="Add Voter" id="add-voter-btn" class="btn btn-success w-100 mt-3">
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- View Position Modal -->
    <div class="modal fade" id="view-voter-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-info">
            <h1 class="modal-title fs-5">Voter Details</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="d-flex flex-column align-items-center">
                <span>Election Event:</span>
                <h5 class="mb-3" id="view-elecev"></h5>
                
                <span>Name:</span>
                <h5 class="mb-3" id="view-voter-name"></h5>
                
                <span>Course:</span>
                <h5 class="mb-3 text-center" id="view-voter-course"></h5>
                
                <span>Year Level:</span>
                <h5 class="mb-3" id="view-voter-yearlvl"></h5>
                
                <span>Gender:</span>
                <h5 class="mb-3" id="view-voter-gender"></h5>
                
                <span>Have voted:</span>
                <h5 class="mb-3" id="view-voter-voted"></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Voter Modal -->
    <div class="modal fade" id="edit-voter-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h1 class="modal-title fs-5">Edit Voter</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-voter-form">
              <input type="hidden" name="edit-voter-id" id="edit-voter-id">
              <label class="form-label">Election Event:</label>
              <select name="edit-elec-event-select" id="edit-elec-event-select" class="form-select mb-2"></select>

              <label class="form-label">First name:</label>
              <input type="text" name="edit-voter-fname" id="edit-voter-fname" class="form-control mb-2" required>
              
              <label class="form-label">Middle name:</label>
              <input type="text" name="edit-voter-mname" id="edit-voter-mname" class="form-control mb-2">
              
              <label class="form-label">Last name:</label>
              <input type="text" name="edit-voter-lname" id="edit-voter-lname" class="form-control mb-2" required>
              
              <label class="form-label">Course:</label>
              <select name="edit-voter-course" id="edit-voter-course" class="form-select mb-2"></select>
              
              <label class="form-label">Year Level:</label>
              <select name="edit-voter-year-level" id="edit-voter-year-level" class="form-select mb-2">
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
                <!-- <option value="5">5th Year</option> -->
              </select>
              
              <label class="form-label">Gender:</label>
              <select name="edit-voter-gender" id="edit-voter-gender" class="form-select">
                <option value="1">Male</option>
                <option value="0">Female</option>
              </select>

              <input type="submit" value="Update Voter" id="update-voter-btn" class="btn btn-warning w-100 mt-3">
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card rounded-0 shadow-sm mt-2">
          <div class="card-header bg-success rounded-0">
            <span class="fs-5 text-white">Voters</span>
          </div>
          <div class="card-body rounded-0 table-responsive" id="data-wrapper">
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

    async function renEvents() {
      let [elecEvents, curElecEv, notStartedElecEvents] = await Promise.all([
        fetchElecEvs(), 
        fetchCurElec(), 
        fetchNotStartedElecEvs()
      ])

      if (curElecEv.length > 0) {
        let eventsMapped = elecEvents.map(elec => `
          <option value="${elec.el_id}" ${curElecEv[0].cur_el_id == elec.el_id ? 'selected' : ''}>${elec.el_name}</option>
        `)
        let eventsJoined = eventsMapped.join('')
        $('#elect-event').html(eventsJoined)
        $('#print-voters').attr('href', `./voterinvoice.php?el_id=${curElecEv[0].cur_el_id}`)
        $('#download-voters').attr('href', `./voterpdf.php?el_id=${curElecEv[0].cur_el_id}`)
      } 

      let eventsMapped = elecEvents.map(elec => `
          <option value="${elec.el_id}">${elec.el_name}</option>
        `)
      let eventsJoined = eventsMapped.join('')
      $('#elec-event-select').html(eventsJoined)
      
      let nseventsMapped = notStartedElecEvents.map(elec => `
          <option value="${elec.el_id}">${elec.el_name}</option>
        `)
      let nseventsJoined = nseventsMapped.join('')
      $('#edit-elec-event-select').html(nseventsJoined)
      // $('#edit-elec-event-select').html(eventsJoined)
    }

    // Fetch Courses
    function fetchCourses() {
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchCourses' },
        success: res => {
          let courses = JSON.parse(res)
          coursesGlobal = courses

          let coursesMapped = courses.map(course => `
            <option value="${course.course_id}">${course.course_desc} (${course.course_name})</option>
          `)
          let coursesJoined = coursesMapped.join('')
          $('#voter-course').html(coursesJoined)
          $('#edit-voter-course').html(coursesJoined)
        }
      })
    }
    fetchCourses()

    // Fetch Voters
    async function fetchVoters(filtered = false) {
      if (!filtered) {
        await renEvents()
      }

      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'fetchVotersBasedOnElec',
          elecEv: $('#elect-event').val()
        })
      })
      
      let votersOfEvent = await res.json()
      console.log(votersOfEvent)

      if (votersOfEvent.length < 1) {
        $("#data-wrapper").html(`
          <h4 class="text-center text-secondary fst-italic">No Voters.</h4>
        `)
        return;
      }
      let votersMapped = votersOfEvent.map(voter => `
        <tr>
          <td>${voter.v_lname}</td>
          <td>${voter.v_fname}</td>
          <td>${voter.v_mname}</td>
          <td>${voter.course_name}</td>
          <td>${voter.v_yrlvl}</td>
          <td>
            <a href="#" title="View" class="view-voter text-decoration-none" id="view-voter-${voter.v_id}" data-bs-toggle="modal" data-bs-target="#view-voter-modal">
              <i class="bi bi-info-circle-fill text-info fs-5"></i>
            </a>
            <a href="#" title="Edit" class="edit-voter text-decoration-none" id="edit-voter-${voter.v_id}" data-bs-toggle="modal" data-bs-target="#edit-voter-modal">
              <i class="bi bi-pencil-square text-warning fs-5"></i>
            </a>
            <a href="#" title="Delete" class="del-voter text-decoration-none" id="del-voter-${voter.v_id}" data-el-id="${voter.el_id}">
              <i class="bi bi-trash-fill text-danger fs-5"></i>
            </a>
          </td>
        </tr>
      `)

      let votersJoined = votersMapped.join('')
      let voterTable = `
        <table class="table table-striped table-bordered w-100" id="voters-table">
          <thead>
            <tr>
              <th>Last name</th>
              <th>First name</th>
              <th>Middle name</th>
              <th>Course</th>
              <th>Year Level</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${votersJoined}
          </tbody>
        </table>
      `
      $('#data-wrapper').html(voterTable)
      // *******
      // *******
      // Setup - add a text input to each footer cell
      $('#voters-table thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#voters-table thead');
  
      var table = $('#voters-table').DataTable({
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
    fetchVoters()

    // Filter Election Event
    $('#voter-filter').click(function(e) {
      e.stopPropagation()
      e.preventDefault()
      $(this).text('Filtering...')
      $(this).prop('disabled', true)
      fetchVoters(filtered = true)
      $(this).text('Filter')
      $(this).prop('disabled', false)
      $('#print-voters').attr('href', `./voterinvoice.php?el_id=${$('#elect-event').val()}`)
      $('#download-voters').attr('href', `./voterpdf.php?el_id=${$('#elect-event').val()}`)
      
      // $.ajax({
      //   url: './assets/php/action.php',
      //   method: 'post',
      //   data: {
      //     action: 'fetchVotersBasedOnElec',
      //     elecEv: $('#elect-event').val()
      //   },
      //   success: res => {
      //     let votrs = JSON.parse(res)
      //     fetchVoters(filtered = true)
      //     $('#voter-filter').text('Filter')
      //     $('#voter-filter').prop('disabled', false)
      //   }
      // })
    })

    // Add Voter
    $('#add-voter-btn').click(function(e) {
      if ($('#add-voter-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Adding Voter...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#add-voter-form').serialize() + '&action=addVoter',
          success: res => {
            if (res == '1') {
              fetchVoters()
              $('#add-voter-form')[0].reset()
              $('#add-voter-modal').modal('hide')
              swal('success', 'Added!', 'Voter was successfully added.')
            } else if (res == 'election event not started anymore') {
              swal('error', 'Oops!', "Can add a party only if election event hasn't started yet.")
            }
            else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $('#add-voter-btn').val('Add Voter')
            $('#add-voter-btn').prop('disabled', false)
          }
        })
      }
    })

    // View Voter
    $('body').on('click', '.view-voter', function(e) {
      e.stopPropagation()

      let id = $(this).attr('id')
      id = id.substr(11)

      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchVoter', id },
        success: res => {
          let voter = JSON.parse(res)

          $('#view-elecev').text(voter.el_name)
          $('#view-voter-name').text(`
            ${voter.v_fname} ${voter.v_mname ? `${voter.v_mname[0]}.` : ''} ${voter.v_lname}
          `)
          $('#view-voter-course').text(`${voter.course_desc} (${voter.course_name})`)
          $('#view-voter-yearlvl').text(voter.v_yrlvl)
          $('#view-voter-gender').text(voter.v_gender ? 'Male' : 'Female')
          $('#view-voter-voted').text(voter.v_voted ? 'Done' : 'Not yet')
        }
      })
    })
    
    // Edit Voter
    $('body').on('click', '.edit-voter', function(e) {
      e.stopPropagation()

      let id = $(this).attr('id')
      id = id.substr(11)

      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchVoter', id },
        success: res => {
          let voter = JSON.parse(res)

          $('#edit-voter-id').val(voter.v_id)
          $('#edit-elec-event-select').val(voter.el_id)
          $('#edit-voter-fname').val(voter.v_fname)
          $('#edit-voter-mname').val(voter.v_mname)
          $('#edit-voter-lname').val(voter.v_lname)
          $('#edit-voter-course').val(voter.course_id)
          $('#edit-voter-year-level').val(voter.v_yrlvl)
          $('#edit-voter-gender').val(voter.v_gender)
        }
      })
    })

    // Update Voter
    $('#update-voter-btn').click(function(e) {
      if ($('#edit-voter-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Updating Voter...')
        $(this).prop('disabled', true)
        
        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#edit-voter-form').serialize() + '&action=updateVoter',
          success: res => {
            if (res == '1') {
              fetchVoters()
              $('#edit-voter-form')[0].reset()
              $('#edit-voter-modal').modal('hide')
              swal('success', 'Updated!', 'Voter was successfully updated!')
            } else if (res == 'not not-started anymore') {
              swal('error', 'Oops!', "Can update a voter only if election event hasn't started yet.")
            } else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $('#update-voter-btn').val('Update Voter')
            $('#update-voter-btn').prop('disabled', false)
          }
        })
      }
    })

    // Delete Voter
    $('body').on('click', '.del-voter', async function(e) {
      let id = $(this).attr('id')
      id = id.substr(10)

      let elId = $(this).attr('data-el-id')

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
              action: 'delVoter',
              id,
              elId
            },
            success: function(res) {
              if (res == '1') {
                fetchVoters()
                swal('success', 'Deleted!', 'Voter was successfully deleted.')
              } else if (res == 'not not-started anymore') {
                swal('error', 'Oops!', 'Deleting of voters is only allowed if election event has not started yet.')
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