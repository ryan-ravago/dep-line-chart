<?php require_once './assets/php/header.php'; ?>
<main>
  <div class="container-fluid">
    <button data-bs-toggle="modal" data-bs-target="#add-elec-modal" class="float-end btn btn-success">Add Election Event</button>
    <br><br>
    <!-- <div class="d-flex">
      <label class="form-label">Election Event:</label>
      <select id="elect-event" class="form-select form-select-sm d-inline-block ms-2" style="width: 220px"></select>
      <button class="btn btn-success btn-sm ms-2">Filter</button>
    </div> -->

    <!-- Add Election Event Modal -->
    <div class="modal fade" id="add-elec-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h1 class="modal-title fs-5">Add Election Event</h1>
            <button type="button" class="btn-close bg-light" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="add-elec-form">
              <label class="form-label">Election name:</label>
              <input type="text" name="elec-name" class="form-control mb-3" required>
              
              <label class="form-label">Election date:</label>
              <input type="date" name="elec-date" class="form-control mb-3" required>

              <div class="row">
                <div class="col-md-6">
                  <label class="form-label">Start time:</label>
                  <input type="time" name="elec-timestart" class="form-control mb-3" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">End time:</label>
                  <input type="time" name="elec-timeend" class="form-control mb-3" required>
                </div>
              </div>

              <label class="form-label">Number of parties:</label>
              <input type="number" name="elec-party-num" class="form-control mb-3" required>
              
              <input type="submit" id="add-elec-btn" class="btn btn-success w-100" value="Add Election Event">
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Election Event Modal -->
    <div class="modal fade" id="edit-elec-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h1 class="modal-title fs-5">Edit Election Event</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="edit-elec-form">
              <input type="hidden" name="edit-elec-id" id="edit-elec-id">
              <label class="form-label">Election name:</label>
              <input type="text" name="edit-elec-name" id="edit-elec-name" class="form-control mb-3" required>
              
              <label class="form-label">Election date:</label>
              <input type="date" name="edit-elec-date" id="edit-elec-date" class="form-control mb-3" required>

              <div class="row">
                <div class="col-md-6">
                  <label class="form-label">Start time:</label>
                  <input type="time" name="edit-elec-timestart" id="edit-elec-timestart" class="form-control mb-3" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">End time:</label>
                  <input type="time" name="edit-elec-timeend" id="edit-elec-timeend" class="form-control mb-3" required>
                </div>
              </div>

              <label class="form-label">Number of parties:</label>
              <input type="number" name="edit-elec-party-num" id="edit-elec-party-num" class="form-control mb-3" required>
              
              <label class="form-label">Status:</label>
              <select name="edit-status" id="edit-status" class="form-select">
                <option value="not-started">Not Started</option>
                <option value="on-going">On Going</option>
                <!-- <option value="pause">Pause</option> -->
                <option value="done">Done</option>
              </select>

              <input type="submit" id="update-elec-btn" class="mt-3 btn btn-warning w-100" value="Update Election Event">
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Reset Election Event Modal -->
    <div class="modal fade" id="reset-elec-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h1 class="modal-title fs-5">Choose Election Event to Reset</h1>
            <button type="button" class="btn-close bg-light" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">Election name:</label>
            <select name="reset-elec" id="reset-elec" class="form-select"></select>
            
            <button type="submit" id="reset-btn" class="btn btn-danger w-100 mt-2">Reset Election Event</button>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card rounded-0 shadow-sm">
          <div class="card-header bg-success rounded-0">
            <span class="fs-5 text-white">Election Events</span>
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

    <div id="reset-btn-wrapper"></div>
  </div>
</main>
<?php require_once './assets/php/footer.php'; ?>
<script>
  $(document).ready(function() {
    function swal(icon, title, text) {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
      })
    }

    // Fetch Election Events
    function fetchElecEvents() {
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchElecEvents' },
        success: function(res) {
          let elecEvents = JSON.parse(res)
          // console.log(elecEvents)

          if (elecEvents.length > 0) {
            let elecEventsMapped = elecEvents.map(elec => `
              <tr>
                <td>${elec.el_name}</td>
                <td>${elec.el_year}</td>
                <td>${elec.el_month}</td>
                <td>${elec.el_day}</td>
                <td>${elec.el_date}</td>
                <td>${elec.el_time_start}</td>
                <td>${elec.el_time_end}</td>
                <td>${elec.el_party_many}</td>
                <td class="${elec.el_status == 'on-going' || elec.el_status == 'pause' ? 'text-primary fs-5 fw-bold' : elec.el_status == 'done' ? 'text-success fs-5 fw-bold' : ''}">
                  ${elec.el_status}
                </td>
                <td>
                  <a href="#" title="Edit" class="edit-elecev text-decoration-none" id="edit-elecev-${elec.el_id}" data-bs-toggle="modal" data-bs-target="#edit-elec-modal">
                    <i class="bi bi-pencil-square text-warning fs-5"></i>
                  </a>
                  <a href="#" title="Delete" class="del-elecev text-decoration-none" id="del-elecev-${elec.el_id}" data-uniqid="${elec.el_uniqid}">
                    <i class="bi bi-trash-fill text-danger fs-5"></i>
                  </a>
                </td>
              </tr>
            `)
            let elecEventsJoined = elecEventsMapped.join('')
            let elecEventsTable = `
              <table class="table table-striped table-bordered w-100" id="elec-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Day</th>
                    <th>Date</th>
                    <th>Time Start</th>
                    <th>Time End</th>
                    <th>Parties</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>${elecEventsJoined}</tbody>
              </table>
            `
            let elecOptionsMapped = elecEvents.map(elec => `
              <option value="${elec.el_id}">${elec.el_name}</option>
            `)
            let elecOptionsJoined = elecOptionsMapped.join('')
            $('#reset-elec').html(elecOptionsJoined)

            $('#data-wrapper').html(elecEventsTable)

            // *******
            // *******
            // Setup - add a text input to each footer cell
            $('#elec-table thead tr')
              .clone(true)
              .addClass('filters')
              .appendTo('#elec-table thead');
        
            var table = $('#elec-table').DataTable({
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
                          .setSelectionRange(cursorPosition, cursorPosition);
                      });
                  });
              },
            });

            $('#reset-btn-wrapper').html(`
              <button class="btn btn-danger d-block mx-auto my-3" data-bs-toggle="modal" data-bs-target="#reset-elec-modal">Reset Election Event</button>
            `)
          } else {
            $("#data-wrapper").html(`
              <h4 class="text-center text-secondary fst-italic">No any election events.</h4>
            `)
            $('#reset-btn-wrapper').html('')
          }
        }
      })
    }
    fetchElecEvents()

    // Add Election Event
    $('#add-elec-btn').click(function(e) {
      if ($('#add-elec-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Adding Election Event...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#add-elec-form').serialize() + '&action=addElection',
          success: function(res) {
            if (res == '1') {
              fetchElecEvents()
              swal('success', 'Added', 'Election Event Added!')
              $('#add-elec-form')[0].reset()
              $('#add-elec-modal').modal('hide')
            } else {
              swal('error', 'Oops...', 'Something went wrong! Try again.')
            }

            $('#add-elec-btn').val('Add Election Event')
            $('#add-elec-btn').prop('disabled', false)
          }
        })
      }
    })

    // Edit Election Event
    $('body').on('click', '.edit-elecev', function(e) {
      let id = $(this).attr('id')
      id = id.substr(12)
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { 
          id,
          action: 'fetchElecEvent',
        },
        success: res => {
          let elecEv = JSON.parse(res)
          // console.log(res)
          let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
          let month = months.indexOf(elecEv.el_month) + 1
          month = parseInt(month) > 9 ? month : `0${month}`
          let elDate = parseInt(elecEv.el_date) > 9 ? elecEv.el_date : `0${elecEv.el_date}`

          $('#edit-elec-id').val(elecEv.el_id)
          $('#edit-elec-name').val(elecEv.el_name)
          $('#edit-elec-date').val(`${elecEv.el_year}-${month}-${elDate}`)
          $('#edit-elec-timestart').val(elecEv.el_time_start)
          $('#edit-elec-timeend').val(elecEv.el_time_end)
          $('#edit-elec-party-num').val(elecEv.el_party_many)
          $('#edit-status').val(elecEv.el_status)
        }
      })
    })

    // Update Election Event
    $('#update-elec-btn').click(function(e) {
      if ($('#edit-elec-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Updating Election Event...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#edit-elec-form').serialize() + '&action=updateElecEvent',
          success: function(res) {
            console.log(res)
            if (res == '1') {
              fetchElecEvents()
              swal('success', 'Updated!', 'Election event was updated.')
              $('#edit-elec-modal').modal('hide')
              $('#edit-elec-form')[0].reset()
            } else if (res == 'an election is on-going') {
              swal('error', 'Oops!', 'An election event is on-going.')
            } else if (res == 'not not-started anymore') {
              swal('error', 'Oops!', 'Updating of election is only allowed if election event has not started yet.')
            } else if (res == '3') {
              swal('error', 'Oops!', "Election event is selected as the current event, can't be updated.")
            } else if (res == 'incomplete') {
              swal('error', 'Oops!', "Lacking of candidates.")
            } else {
              swal('error', 'Oops!', 'Something went wrong. Try again.')
            }
            $('#update-elec-btn').val('Update Election Event')
            $('#update-elec-btn').prop('disabled', false)
          }
        })
      }
    })

    // Delete Election Event
    $('body').on('click', '.del-elecev', function(e) {
      let id = $(this).attr('id')
      id = id.substr(11)
      
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
            data: { action: 'delElecEv', id, uniqid },
            success: function(res) {
              // console.log(res)
              if (res == '1' || res.includes('1')) {
                fetchElecEvents()
                swal('success', 'Deleted!', 'An election event was deleted.')
              } else if (res == '3') {
                swal('error', 'Oops!', 'Election event is selected as the current event.')
              } else if (res == 'not not-started anymore') {
                swal('error', 'Oops!', 'Deleting of an election event is only allowed if election event has not started yet.')
              } else {
                swal('error', 'Oops!', 'Something went wrong. Try again.')
              }
            }
          })
        }
      })
    })

    // Reset Election Events
    $('#reset-btn').click(function(e) {
      e.preventDefault()

      let elecName = $('#reset-elec')[0].options[$('#reset-elec')[0].selectedIndex ].textContent
      Swal.fire({
        title: `Are you sure you want to reset ${elecName} election event?`,
        text: "This will reset the election event, voting results, and all voters who have voted.",
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
              elId: $('#reset-elec').val(),
              action: 'resetElec'
            },
            success: res => {
              if (res == 'resetted') {
                fetchElecEvents()
                $('#reset-elec-modal').modal('hide')
                swal('success', 'Resetted', 'Election Event was successfully resetted.')
              } else {
                swal('error', 'Oops...', 'Something went wrong, try again.')
              }
            }
          })
        }
      })
    })
  })
</script>