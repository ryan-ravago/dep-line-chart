<?php require_once './assets/php/header.php'; ?>
<main>
  <div class="container-fluid">
    <!-- View Campaign Modal -->
    <div class="modal fade" id="view-campaign-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-info">
            <h1 class="modal-title fs-5">Campaign Details</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex flex-column align-items-center">
              <span>Election Event:</span>
              <h5 class="mb-3" id="view-elecev"></h5>
              
              <span>Start:</span>
              <h5 class="mb-3" id="view-start"></h5>
              
              <span>End:</span>
              <h5 class="mb-3" id="view-end"></h5>
              
              <span>Status:</span>
              <h5 class="mb-3" id="view-status"></h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Campaign Modal -->
    <div class="modal fade" id="edit-campaign-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h1 class="modal-title fs-5">Edit Campaign</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="edit-campaign-form">
              <input type="hidden" name="edit-elecev-id" id="edit-elecev-id">
              <input type="hidden" name="edit-elec-id" id="edit-elec-id">
              <label class="form-label">Election name:</label>
              <input type="text" id="edit-elec-name" class="form-control mb-4" disabled>
              
              <div class="row">
                <div class="col-6">
                  <label class="form-label">Start Campaign Date:</label>
                  <input type="date" name="edit-elec-startdate" id="edit-elec-startdate" class="form-control mb-4" required>
                </div>
                <div class="col-6">
                  <label class="form-label">End Campaign Date:</label>
                  <input type="date" name="edit-elec-enddate" id="edit-elec-enddate" class="form-control mb-4" required>
                </div>
              </div>

              <div class="row">
                <div class="col-6">
                  <label class="form-label">Start time:</label>
                  <input type="time" name="edit-elec-timestart" id="edit-elec-timestart" class="form-control mb-4" required>
                </div>
                <div class="col-6">
                  <label class="form-label">End time:</label>
                  <input type="time" name="edit-elec-timeend" id="edit-elec-timeend" class="form-control mb-4" required>
                </div>
              </div>

              <label class="form-label">Status:</label>
              <select name="edit-status" id="edit-status" class="form-select">
                <option value="1">Enable</option>
                <option value="0">Disable</option>
              </select>

              <input type="submit" id="update-campaign-btn" class="mt-3 btn btn-warning w-100" value="Update Campaign">
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card rounded-0 shadow-sm mt-3">
          <div class="card-header bg-success rounded-0">
            <span class="fs-5 text-white">Campaign</span>
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
  $(document).ready(function(e) {
    // $('#edit-campaign-modal').modal('show')
    function swal(icon, title, text) {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
      })
    }

    // Fetch Campaign
    function fetchCampaigns() {
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchCampaigns' },
        success: function(res) {
          let campaigns = JSON.parse(res)
          // console.log(campaigns)

          if (campaigns.length > 0) {
            let campaignsMapped = campaigns.map(elec => `
              <tr>
                <td>${elec.el_name}</td>
                <td>${elec.status ? 'Enabled' : 'Disabled'}</td>
                <td>
                  <a href="#" title="View" class="view-campaign text-decoration-none" id="view-campaign-${elec.cam_id}" data-bs-toggle="modal" data-bs-target="#view-campaign-modal">
                    <i class="bi bi-info-circle-fill text-info fs-5"></i>
                  </a>
                  <a href="#" title="Edit" class="edit-campaign text-decoration-none" id="edit-campaign-${elec.cam_id}" data-bs-toggle="modal" data-bs-target="#edit-campaign-modal">
                    <i class="bi bi-pencil-square text-warning fs-5"></i>
                  </a>
                </td>
              </tr>
            `)
            let campaignsJoined = campaignsMapped.join('')
            let campaignsTable = `
              <table class="table table-striped table-bordered w-100" id="campaign-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>${campaignsJoined}</tbody>
              </table>
            `
            $('#data-wrapper').html(campaignsTable)

            // *******
            // *******
            // Setup - add a text input to each footer cell
            $('#campaign-table thead tr')
              .clone(true)
              .addClass('filters')
              .appendTo('#campaign-table thead');
        
            var table = $('#campaign-table').DataTable({
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

          }
        }
      })
    }
    fetchCampaigns()

    // View Campaign
    $('body').on('click', '.view-campaign', function(e) {
      e.stopPropagation()

      let id = $(this).attr('id')
      id = id.substr(14)
      
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchCampaignView', id },
        success: res => {
          let campaign = JSON.parse(res)
          // console.log(campaign)
          $('#view-elecev').text(campaign.el_name)
          $('#view-start').text(`${campaign.cam_start_month} ${campaign.cam_start_date}, ${campaign.cam_start_year}`)
          $('#view-end').text(`${campaign.cam_end_month} ${campaign.cam_end_date}, ${campaign.cam_end_year}`)
          $('#view-status').text(campaign.status ? 'Enabled' : 'Disabled')
        }
      })
    })
    
    // Edit Campaign
    $('body').on('click', '.edit-campaign', function(e) {
      e.stopPropagation()

      let id = $(this).attr('id')
      id = id.substr(14)
      
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchCampaignEdit', id },
        success: res => {
          let campaign = JSON.parse(res)
          console.log(campaign)
          
          let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
          
          let startmonth = months.indexOf(campaign.cam_start_month) + 1
          startmonth = parseInt(startmonth) > 9 ? startmonth : `0${startmonth}`
          
          let endmonth = months.indexOf(campaign.cam_end_month) + 1
          endmonth = parseInt(endmonth) > 9 ? endmonth : `0${endmonth}`

          $('#edit-elecev-id').val(campaign.el_id)
          $('#edit-elec-id').val(campaign.cam_id)
          $('#edit-elec-name').val(campaign.el_name)

          $('#edit-elec-startdate').val(`${campaign.cam_start_year}-${startmonth}-${campaign.cam_start_date}`)
          $('#edit-elec-enddate').val(`${campaign.cam_end_year}-${endmonth}-${campaign.cam_end_date}`)

          $('#edit-elec-timestart').val(campaign.cam_start_time)
          $('#edit-elec-timeend').val(campaign.cam_end_time)

          $('#edit-status').val(campaign.status)
        }
      })
    })

    // Update Campaign
    $('#update-campaign-btn').click(function(e) {
      if ($('#edit-campaign-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Updating Campaign...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#edit-campaign-form').serialize() + '&action=updateCampaign',
          success: res => {
            // console.log(res)
            if (res == '1') {
              fetchCampaigns()
              $('#edit-campaign-modal').modal('hide')
              $('#edit-campaign-form')[0].reset()
              swal('success', 'Updated!', 'Campaign was successfully updated.')
            } else if (res == 'not not-started anymore') {
              swal('error', 'Oops!', 'If an election event hasn\'t started yet, then updating campaign(s) will be allowed.')
            } else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $(this).val('Update Campaign')
            $(this).prop('disabled', false)
          }
        })
      }
    })
  })
</script>