<?php require_once './assets/php/header.php'; ?>
<main>
  <div class="container-fluid">
    <!-- Add Position Modal -->
    <div class="modal fade" id="add-position-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h1 class="modal-title fs-5">Add Position</h1>
            <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="add-position-form">
              <label class="form-label">Election Event:</label>
              <select name="elec-event-select" id="elec-event-select" class="form-select mb-3"></select>

              <label class="form-label">Position name:</label>
              <input type="text" name="position-name" id="position-name" class="form-control mb-3" required>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Position many:</label>
                  <input type="number" min="1" name="position-many" id="position-many" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Sort number:</label>
                  <input type="number" min="1" name="position-sort-num" id="position-sort-num" class="form-control" required>
                </div>
              </div>

              <label class="form-label mb-2">Courses can vote:</label>
              <select name="position-voter-courses[]" multiple id="position-voter-courses" class="form-select" style="height:140px;" required>
                <option value="bsit">BSIT</option>
                <option value="crim">BSCRIM</option>
                <option value="bstm">BSTM</option>
              </select>

              <input type="submit" value="Add Position" id="add-position-btn" class="btn btn-success w-100 mt-3">
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Edit Position Modal -->
    <div class="modal fade" id="edit-position-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h1 class="modal-title fs-5">Update Position</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-position-form">
              <input type="hidden" name="edit-position-id" id="edit-position-id">
              <input type="hidden" name="edit-position-uniqid" id="edit-position-uniqid">
              <label class="form-label">Election Event:</label>
              <select name="edit-elec-event-select" id="edit-elec-event-select" class="form-select mb-3"></select>

              <label class="form-label">Position name:</label>
              <input type="text" name="edit-position-name" id="edit-position-name" class="form-control mb-3" required>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Position many:</label>
                  <input type="number" min="1" name="edit-position-many" id="edit-position-many" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Sort number:</label>
                  <input type="number" min="1" name="edit-position-sort-num" id="edit-position-sort-num" class="form-control" required>
                </div>
              </div>

              <label class="form-label mb-2">Courses can vote:</label>
              <select name="edit-position-voter-courses[]" multiple id="edit-position-voter-courses" class="form-select" style="height:140px;" required></select>

              <input type="submit" value="Update Position" id="update-position-btn" class="btn btn-warning w-100 mt-3">
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- View Position Modal -->
    <div class="modal fade" id="view-position-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-info">
            <h1 class="modal-title fs-5">Position Details</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex flex-column align-items-center">
              <span>Election Event:</span>
              <h5 class="mb-3" id="view-elecev"></h5>
              
              <span>Position:</span>
              <h5 class="mb-3" id="view-pos"></h5>
              <span>
                <span id="view-cand-many"></span> Candidate(s)
              </span>
              <span class="mb-3">
                Sort number: <span id="view-sort-num"></span>
              </span>

              <span>Courses allowed to vote:</span>
              <ul id="view-canvote-courses"></ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between">
      <div class="d-flex align-items-center">
        <label class="form-label">Election Event:</label>
        <select id="elect-event" class="form-select form-select-sm d-inline-block ms-2" style="width: 220px"></select>
        <button class="btn btn-success btn-sm ms-2" id="position-filter">Filter</button>
      </div>
      <button data-bs-toggle="modal" data-bs-target="#add-position-modal" class="float-end btn btn-success">Add Position</button>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card rounded-0 shadow-sm mt-3">
          <div class="card-header bg-success rounded-0">
            <span class="fs-5 text-white">Positions</span>
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

    async function fetchPositions(filtered = false) {
      if (!filtered) {
        await renEvents()
      }

      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'fetchPositionsBasedOnElec',
          elecEv: $('#elect-event').val()
        })
      })

      let positionsOfEvent = await res.json()

      if (positionsOfEvent.length < 1) {
        $("#data-wrapper").html(`
          <h4 class="text-center text-secondary fst-italic">No Positions.</h4>
        `)
        return;
      }

      let positionsMapped = positionsOfEvent.map(position => `
        <tr>
        <td>${position.pos_sort_num}</td>
          <td>${position.pos_name}</td>
          <td>${position.pos_cand_many}</td>
          <td>
            <a href="#" title="View" class="view-position text-decoration-none" id="view-position-${position.pos_id}" data-bs-toggle="modal" data-bs-target="#view-position-modal">
              <i class="bi bi-info-circle-fill text-info fs-5"></i>
            </a>
            <a href="#" title="Edit" class="edit-position text-decoration-none" id="edit-position-${position.pos_id}" data-bs-toggle="modal" data-bs-target="#edit-position-modal">
              <i class="bi bi-pencil-square text-warning fs-5"></i>
            </a>
            <a href="#" title="Delete" class="del-position text-decoration-none" id="del-position-${position.pos_id}" data-uniqid="${position.pos_uniqid}" data-el-id="${position.pos_el_id}">
              <i class="bi bi-trash-fill text-danger fs-5"></i>
            </a>
          </td>
        </tr>
      `)
      let positionsJoined = positionsMapped.join('')
      let positionTable = `
        <table class="table table-striped table-bordered w-100" id="positions-table">
          <thead>
            <tr>
              <th>Sort Number</th>
              <th>Name</th>
              <th>Many</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${positionsJoined}
          </tbody>
        </table>
      `
      $('#data-wrapper').html(positionTable)
      // *******
      // *******
      // Setup - add a text input to each footer cell
      $('#positions-table thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#positions-table thead');
  
      var table = $('#positions-table').DataTable({
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
    fetchPositions()

    async function renEvents() {
      let [elecEvents, curElecEv, notStartedElecEvents] = await Promise.all([
        fetchElecEvs(), 
        fetchCurElec() ,
        fetchNotStartedElecEvs()
      ])

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

      let nseventsMapped = notStartedElecEvents.map(elec => `
          <option value="${elec.el_id}">${elec.el_name}</option>
        `)
      let nseventsJoined = nseventsMapped.join('')
      $('#edit-elec-event-select').html(nseventsJoined)
    }

    // Filter Election Event
    $('#position-filter').click(function(e) {
      e.stopPropagation()
      e.preventDefault()
      $(this).text('Filtering...')
      $(this).prop('disabled', true)
      fetchPositions(filtered = true)
      $(this).text('Filter')
      $(this).prop('disabled', false)

      // $.ajax({
      //   url: './assets/php/action.php',
      //   method: 'post',
      //   data: {
      //     action: 'fetchPositionsBasedOnElec',
      //     elecEv: $('#elect-event').val()
      //   },
      //   success: res => {
      //     let positions = JSON.parse(res)
      //     fetchPositions(filtered = true)
      //     $('#position-filter').text('Filter')
      //     $('#position-filter').prop('disabled', false)
      //   }
      // })
    })

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
          $('#position-voter-courses').html(coursesJoined)
          $('#edit-position-voter-courses').html(coursesJoined)
        }
      })
    }
    fetchCourses()

    // Add position and canvote courses
    $('#add-position-btn').click(function(e) {
      if ($('#add-position-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Adding Position...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#add-position-form').serialize() + '&action=addPosAndCanVotePos',
          success: res => {

            if (res == 'added') {
              fetchPositions()
              swal('success', 'Added!', 'Position successfully added.')
              $('#add-position-form')[0].reset()
              $('#add-position-modal').modal('hide')
            } else if (res == 'election event not started anymore') {
              swal('error', 'Oops!', "Can add a position only if election event hasn't started yet.")
            } else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $('#add-position-btn').val('Add Position')
            $('#add-position-btn').prop('disabled', false)
          }
        })
      }
    })

    // View position
    $('body').on('click', '.view-position', function(e) {
      e.stopPropagation()

      let id = $(this).attr('id')
      id = id.substr(14)
      
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: {
          action: 'fetchPosition',
          id
        },
        success: res => {
          let position = JSON.parse(res)
          $('#view-elecev').text(position.el_name)
          $('#view-pos').text(position.pos_name)
          $('#view-cand-many').text(position.pos_cand_many)
          $('#view-sort-num').text(position.pos_sort_num)

          let coursesMapped = position.canVoteCourses.map(course => `<li>${course.course_desc} (${course.course_name})</li>`)
          let coursesJoined = coursesMapped.join('')
          $('#view-canvote-courses').html(coursesJoined)
        }
      })
    })
    
    // Edit position
    $('body').on('click', '.edit-position', function(e) {
      e.stopPropagation()

      let id = $(this).attr('id')
      id = id.substr(14)
      
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: {
          action: 'fetchPosition',
          id
        },
        success: res => {
          let position = JSON.parse(res)
          // console.log(position)
          // console.log(coursesGlobal)
          console.log(position)
          $('#edit-position-id').val(position.pos_id)
          $('#edit-position-uniqid').val(position.pos_uniqid)
          $('#edit-elec-event-select').val(position.el_id)
          $('#edit-position-name').val(position.pos_name)
          $('#edit-position-many').val(position.pos_cand_many)
          $('#edit-position-sort-num').val(position.pos_sort_num)

          let idCanVoteCourses = position.canVoteCourses.map(course => course.course_id)

          let renCourses = coursesGlobal.map(course => `
            <option 
              value="${course.course_id}"
              ${idCanVoteCourses.includes(course.course_id) ? 'selected' : ''}
            >
              ${course.course_desc} (${course.course_name})
            </option>
          `)
          let coursesJoined = renCourses.join('')

          $('#edit-position-voter-courses').html(coursesJoined)
        }
      })
    })

    // Update position
    $('#update-position-btn').click(function(e) {
      if ($('#edit-position-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Updating Position...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#edit-position-form').serialize() + '&action=updatePosition',
          success: res => {
            console.log(res)
            if (res == 'updated') {
              fetchPositions()
              $('#edit-position-form')[0].reset()
              $('#edit-position-modal').modal('hide')
              swal('success', 'Updated!', 'Position was successfully updated!')
            } else if (res == 'not not-started anymore') {
              swal('error', 'Oops!', "Can update a position only if election event hasn't started yet.")
            } else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $('#update-position-btn').val('Update Position')
            $('#update-position-btn').prop('disabled', false)
          }
        })
        
      }
    })

    // Delete Position
    $('body').on('click', '.del-position', async function(e) {
      let id = $(this).attr('id')
      id = id.substr(13)

      let elId = $(this).attr('data-el-id')
      let uniqid = $(this).attr('data-uniqid')

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
              action: 'delPosition', 
              id, 
              uniqid,
              elId
            },
            success: function(res) {
              console.log(res)
              if (res == 'deleted') {
                fetchPositions()
                swal('success', 'Deleted!', 'Position was successfully deleted.')
              } else if (res == 'not not-started anymore') {
                swal('error', 'Oops!', 'Deleting of positions is only allowed if election event has not started yet.')
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