<?php
require_once './assets/php/db.php';
$db = new Database();
$sql = "SELECT * FROM `current`
INNER JOIN `election`
  ON `current`.cur_el_id = `election`.el_id";
$stmt = $db->conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (sizeof($result) == 0) {
  header('location: dashboard.php');
  exit();
}
?>
<?php require_once './assets/php/header.php'; ?>

<main>
  <div class="container-fluid">
    <!-- Add Party Modal -->
    <div class="modal fade" id="add-party-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h1 class="modal-title fs-5">Add Party</h1>
            <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="add-party-form">
              <label class="form-label">Election Event:</label>
              <select name="elec-event-select" id="elec-event-select" class="form-select mb-3"></select>

              <label class="form-label">Party name:</label>
              <input type="text" name="party-name" id="party-name" class="form-control mb-3" required>

              <label class="form-label">Party Platform:</label>
              <textarea name="party-platform" id="party-platform" cols="30" rows="10" style="resize: none;"></textarea>

              <input type="submit" value="Add Party" id="add-party-btn" class="btn btn-success w-100 mt-3">
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Party Modal -->
    <div class="modal fade" id="edit-party-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h1 class="modal-title fs-5">Edit Party</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-party-form">
              <input type="hidden" name="edit-party-id" id="edit-party-id">
              <label class="form-label">Election Event:</label>
              <select name="edit-elec-event-select" id="edit-elec-event-select" class="form-select mb-3"></select>

              <label class="form-label">Party name:</label>
              <input type="text" name="edit-party-name" id="edit-party-name" class="form-control mb-3" required>

              <label class="form-label">Party Platform:</label>
              <textarea name="edit-party-platform" id="edit-party-platform" cols="30" rows="10" style="resize: none;"></textarea>

              <input type="submit" value="Update Party" id="update-party-btn" class="btn btn-warning w-100 mt-3">
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between">
      <div class="d-flex align-items-center">
        <label class="form-label">Election Event:</label>
        <select id="elect-event" class="form-select form-select-sm d-inline-block ms-2" style="width: 220px"></select>
        <button class="btn btn-success btn-sm ms-2" id="party-filter">Filter</button>
      </div>
      <button data-bs-toggle="modal" data-bs-target="#add-party-modal" class="float-end btn btn-success">Add Party</button>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card rounded-0 shadow-sm mt-3">
          <div class="card-header bg-success rounded-0">
            <span class="fs-5 text-white">Parties</span>
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
    // Fetch Parties of Election Event
    async function fetchPartiesOfElecEvent(filtered = false) {
      if (!filtered) {
        await renEvents()
      }
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'fetchPartiesOfElecEvent',
          elecEv: $('#elect-event').val()
        })
      })
      let partiesOfEvent = await res.json()

      if (partiesOfEvent.length < 1) {
        $("#data-wrapper").html(`
          <h4 class="text-center text-secondary fst-italic">No Parties.</h4>
        `)
        return;
      }

      let partiesMapped = partiesOfEvent.map(party => `
        <tr>
          <td>${party.par_name}</td>
          <td>
            <a href="#" title="Edit" class="edit-party text-decoration-none" id="edit-party-${party.par_id}" data-bs-toggle="modal" data-bs-target="#edit-party-modal">
              <i class="bi bi-pencil-square text-warning fs-5"></i>
            </a>
            <a href="#" title="Delete" class="del-party text-decoration-none" id="del-party-${party.par_id}">
              <i class="bi bi-trash-fill text-danger fs-5"></i>
            </a>
          </td>
        </tr>
      `)
      let partiesJoined = partiesMapped.join('')
      let partyTable = `
        <table class="table table-striped table-bordered w-100" id="parties-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${partiesJoined}
          </tbody>
        </table>
      `
      $('#data-wrapper').html(partyTable)
      // *******
      // *******
      // Setup - add a text input to each footer cell
      $('#parties-table thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#parties-table thead');
  
      var table = $('#parties-table').DataTable({
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
    fetchPartiesOfElecEvent()

    // Summernote rich text editor
    $('#party-platform').summernote({
      height: 250
    })
    $('#edit-party-platform').summernote({
      height: 250
    })

    // SweetAlert
    function swal(icon, title, text) {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
      })
    }

    async function fetchElecEvents() {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchElecEvents' })
      })
      let elecEvents = await res.json()
      return elecEvents
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
    
    async function fetchCurrentEvent() {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchCurrentElec' })
      })
      let curElecEv = await res.json()
      return curElecEv
    }

    async function renEvents() {
      let [elecEvents, curElecEv, notStartedElecEvents] = await Promise.all([
        fetchElecEvents(), 
        fetchCurrentEvent(),
        fetchNotStartedElecEvs(),
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

    // Add Party
    $('#add-party-btn').click(function(e) {
      if ($('#add-party-form')[0].checkValidity()) {
        e.preventDefault()
        
        $(this).val('Adding Party...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#add-party-form').serialize() + '&action=addParty',
          success: res => {
            if (res == '1') {
              fetchPartiesOfElecEvent(true)
              swal('success', 'Added!', 'Party was successfully added.')
              $('#add-party-form')[0].reset()
              $('#add-party-modal').modal('hide')
              $('#party-platform').summernote('code', '<p><br></p>');
            } else if (res == 'election event not started anymore') {
              swal('error', 'Oops!', "Can add a party only if election event hasn't started yet.")
            } else if (res == 'exceed') {
              swal('error', 'Oops!', "Parties' many can't excced on the number setted up on its election event.")
            }
            else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $('#add-party-btn').val('Add Party')
            $('#add-party-btn').prop('disabled', false)
          }
        })
      }
    })

    // Filter Election Event
    $('#party-filter').click(function(e) {
      e.stopPropagation()
      e.preventDefault()
      $(this).text('Filtering...')
      $(this).prop('disabled', true)
      fetchPartiesOfElecEvent(filtered = true)
      $(this).text('Filter')
      $(this).prop('disabled', false)

      // $.ajax({
      //   url: './assets/php/action.php',
      //   method: 'post',
      //   data: {
      //     action: 'filterParty',
      //     elecEvId: $('#elect-event').val()
      //   },
      //   success: res => {
      //     let parties = JSON.parse(res)
      //     fetchPartiesOfElecEvent(true)
      //     $('#party-filter').text('Filter')
      //     $('#party-filter').prop('disabled', false)
      //   }
      // })
    })

    // Edit Party
    $('body').on('click', '.edit-party', function(e) {
      e.stopPropagation()

      let id = $(this).attr('id')
      id = id.substr(11)

      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: {
          id,
          action: 'editParty'
        },
        success: res => {
          let party = JSON.parse(res)
          $('#edit-party-id').val(party.par_id)
          $('#edit-elec-event-select').val(party.par_el_id)
          $('#edit-party-name').val(party.par_name)
          $('#edit-party-platform').summernote('code', party.par_platform)
        }
      })
    })

    // Update Party
    $('#update-party-btn').click(function(e) {
      if ($('#edit-party-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Updating Party...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#edit-party-form').serialize() + '&action=updateParty',
          success: res => {
            console.log(res)
            if (res == '1') {
              fetchPartiesOfElecEvent(true)
              swal('success', 'Updated!', 'Party was successfully updated.')
              $('#edit-party-form')[0].reset()
              $('#edit-party-modal').modal('hide')
            } else if (res == 'not not-started anymore') {
              swal('error', 'Oops!', "Can update party only if election event hasn't started yet.")
            } else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $('#update-party-btn').val('Update Party')
            $('#update-party-btn').prop('disabled', false)
          }
        })
      }
    })

    // Delete Election Event
    $('body').on('click', '.del-party', function(e) {
      let id = $(this).attr('id')
      id = id.substr(10)

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
            data: { action: 'delParty', id },
            success: function(res) {
              if (res == '1') {
                fetchPartiesOfElecEvent(true)
                swal('success', 'Deleted!', 'Party was successfully deleted.')
              } else if (res == 'election not started anymore') {
                swal('error', 'Oops!', 'Deleting of parties is only allowed if election event has not started yet.')
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