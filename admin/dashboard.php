<?php require_once './assets/php/header.php'; ?>
<main>
  <div class="container-fluid">
    <!-- Loading modal -->
    <div class="modal" id="dashboard-loading-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-0">
          <div class="modal-body text-center">
            <h2 class="text-success mt-2 mb-3">Loading...</h2>
            <div class="spinner-border text-success mb-2" style="width:80px;height:80px;">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p>Please wait...</p>
          </div>
        </div>
      </div>
    </div>

    <div id="dashboard-content-wrapper" class="mt-4">
      <div class="row gy-3">
        <div class="col-md-4 col-6">
          <div class="card bg-success rounded-0 border-0">
            <div class="card-body mx-auto">
              <i class="bi bi-people text-white" style="font-size: 50px;"></i>
            </div>
            <div class="card-footer text-white text-center" id="elec-count">Loading...</div>
          </div>
        </div>
        <div class="col-md-4 col-6">
          <div class="card bg-warning rounded-0 border-0">
            <div class="card-body mx-auto">
              <i class="bi bi-people text-white" style="font-size: 50px;"></i>
            </div>
            <div class="card-footer text-white text-center" id="course-count">Loading...</div>
          </div>
        </div>
        <div class="col-md-4 col-6">
          <div class="card bg-primary rounded-0 border-0">
            <div class="card-body mx-auto">
              <i class="bi bi-people text-white" style="font-size: 50px;"></i>
            </div>
            <div class="card-footer text-white text-center" id="voter-count">Loading...</div>
          </div>
        </div>
        <div class="col-md-4 col-6">
          <div class="card bg-danger rounded-0 border-0">
            <div class="card-body mx-auto">
              <i class="bi bi-people text-white" style="font-size: 50px;"></i>
            </div>
            <div class="card-footer text-white text-center" id="party-count">Loading...</div>
          </div>
        </div>
        <div class="col-md-4 col-6">
          <div class="card bg-info rounded-0 border-0">
            <div class="card-body mx-auto">
              <i class="bi bi-people text-white" style="font-size: 50px;"></i>
            </div>
            <div class="card-footer text-white text-center" id="position-count">Loading...</div>
          </div>
        </div>
        <div class="col-md-4 col-6">
          <div class="card bg-dark rounded-0 border-0">
            <div class="card-body mx-auto">
              <i class="bi bi-people text-white" style="font-size: 50px;"></i>
            </div>
            <div class="card-footer text-white text-center" id="candidate-count">Loading...</div>
          </div>
        </div>
      </div>

      <br>

      <!-- Population of voters bar chart & Current Election Event -->
      <div class="row">
        <div class="col-md-7">
          <div class="card rounded-0">
            <div class="card-header">
              <h5>Population of Voters by Department</h5>
            </div>
            <div class="card-body" id="card-body-dep-pops">
              <canvas id="dep-populations"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="card rounded-0">
            <div class="card-header bg-light rounded-0">
              <h5>Current Election Event</h5>
            </div>
            <div class="card-body">
              <form id="cur-elec-form">
                <label class="form-label">Current Election Event:</label>
                <select name="cur-elec" id="cur-elec" class="form-select"></select>
                <small id="empty-elec-current" class="text-danger d-block ms-2"></small>

                <input type="submit" value="Update Current Election Event" id="update-cur-elec-btn" class="mt-3 btn btn-outline-secondary w-100">
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Voting results -->
      <div class="row">
        <div class="col-md-12">
          <div class="card rounded-0 my-4">
            <div class="card-header bg-success rounded-0 text-white">
              <h5>Voting Results for <span id="elect-name"></span></h5>
            </div>
            <div class="card-body" id="card-body-results">
              <div class="row gy-4" id="voting-results-wrapper"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Voters who already voted -->
      <div class="row">
        <div class="col-md-6">
          <div class="card rounded-0 mb-4">
            <div class="card-header bg-primary rounded-0 text-white">
              <h5>Voters who already voted</h5>
            </div>
            <div class="card-body" id="card-body-voted-voters">
              <div class="row gy-4" id="voted-voters-table-wrapper"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card rounded-0 mb-4">
            <div class="card-header bg-danger rounded-0 text-white">
              <h5>Voters have not voted</h5>
            </div>
            <div class="card-body" id="card-body-not-voted-voters">
              <div class="row gy-4" id="not-voted-voters-table-wrapper"></div>
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
    // $('#dashboard-loading-modal').modal('show')

    // SweetAlert
    function swal(icon, title, text) {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
      })
    }

    // Fetch voters
    async function fetchVoters(elecEv) {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchVotersBasedOnElec', elecEv })
      })
      let votersCount = await res.json()
      return votersCount
    }

    // Fetch courses count
    async function fetchCoursesCount() {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchCoursesCount' })
      })
      let coursesCount = await res.json()
      return coursesCount
    }

    // Fetch position count
    async function fetchPositionCount(elId) {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchPositionCount',elId })
      })
      let positionCount = await res.json()
      return positionCount
    }

    // Fetch parties count
    async function fetchPartiesCount(elId) {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchPartiesCount', elId })
      })
      let partiesCount = await res.json()
      return partiesCount
    }

    // Fetch Elections
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

    async function fetchVotedVoters(elId) {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchVotedVoters', elId })
      })
      let votedVoters = await res.json()
      return votedVoters
    }
    
    async function fetchHaveNotVotedVoters(elId) {
      let res = await fetch('./assets/php/action.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ action: 'fetchHaveNotVotedVoters', elId })
      })
      let votedVoters = await res.json()
      return votedVoters
    }

    // Fetch Current Election Event
    function fetchCurrentElec() {
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchCurrentElec' },
        success: async function(res) {
          res = JSON.parse(res)
          // console.log(res)
          let elecEvents = await fetchElecEvents()
          if (res.length > 0) {
            let elecMapped = elecEvents.map(elec => `
              <option value="${elec.el_id}" ${res[0].cur_el_id == elec.el_id ? 'selected' : ''}>
                ${elec.el_name}
              </option>
            `)
            let elecJoined = elecMapped.join('')
            $('#cur-elec').html(elecJoined)
            $('#elect-name').text(res[0].el_name)
            $('#empty-elec-current').text(``)
            fetchCandidates(res[0].cur_el_id)
          } else {
            $('#empty-elec-current').text(`No any current election event setted up yet.`)
            let elecMapped = elecEvents.map(elec => `<option value="${elec.el_id}">${elec.el_name}</option>`)
            let elecJoined = elecMapped.join('')
            $('#cur-elec').html(elecJoined)
          }

        }
      })
    }
    fetchCurrentElec()

    // Update Current Election Event
    $('#update-cur-elec-btn').click(function(e) {
      if ($('#cur-elec-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Updating Current Election Event...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#cur-elec-form').serialize() + '&action=updateCurElecEvent',
          success: function(res) {

            if (res == '1') {
              swal('success', 'Updated!', 'Current election event successfully updated.')
            } else {
              swal('error', 'Oops!', 'Something went wrong. Try again.')
            }
            fetchCurrentElec()
            $('#update-cur-elec-btn').val('Update Current Election Event')
            $('#update-cur-elec-btn').prop('disabled', false)
          }
        })
      }
    })

    // Fetch Candidates
    function fetchCandidates(elId) {
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchAllCandidates', elId },
        success: async res => {
          let cands = JSON.parse(res)
          let [elecs, coursesCount, voters, parties, positions, votedVoters, haveNotVotedVoters] = await Promise.all([
            fetchElecEvents(), 
            fetchCoursesCount(),
            fetchVoters(elId),
            fetchPartiesCount(elId),
            fetchPositionCount(elId),
            fetchVotedVoters(elId),
            fetchHaveNotVotedVoters(elId),
          ])

          elecsCount = elecs.length
          candsCount = cands.length
          votersCount = voters.length
          let uniqueVotedVoters = []
          
          // votedVoters.forEach(voter => {
          //   if (uniqueVotedVoters.length == 0) {
          //     uniqueVotedVoters.push(voter)
          //   } else {
          //     if (uniqueVotedVoters[uniqueVotedVoters.length - 1].vo_v_id != voter.vo_v_id) {
          //       uniqueVotedVoters.push(voter)
          //     }
          //   }
          // })
          votedVoters.forEach(voter => {
            if (uniqueVotedVoters.length == 0) {
              uniqueVotedVoters.push(voter)
            } else {
              let count = 0
              uniqueVotedVoters.forEach(uVoter => {
                if (uVoter.vo_v_id == voter.vo_v_id) {
                  count++
                }
              })

              if (count == 0) {
                uniqueVotedVoters.push(voter)
              }
            }
          })

          $('#elec-count').text(`${elecsCount} Election Events`)
          $('#course-count').text(`${coursesCount.courses_count} Courses`)
          $('#voter-count').text(`${votersCount} Voters`)
          $('#party-count').text(`${parties.parties_count} Parties`)
          $('#position-count').text(`${positions.positions_count} Positions`)
          $('#position-count').text(`${positions.positions_count} Positions`)
          $('#candidate-count').text(`${candsCount} Candidates`)

          $('#voting-results-wrapper').html('')
          $('#card-body-dep-pops').html('')
          $('#voted-voters-table-wrapper').html('')
          $('#not-voted-voters-table-wrapper').html('')

          if (cands.length > 0) {
            let scored = Object.groupBy(cands, cand => cand.pos_name)
            $('#print-results').remove()
            $('#pdf-results').remove()
            $('#print-proc-cert').remove()
            $('#card-body-results').prepend(`
              <a href="#" target="_blank" id="print-results" class="btn btn-secondary mb-4">Print</a>
              <a href="#" id="pdf-results" class="btn btn-danger mb-4">PDF</a>
              <a href="./proc_cert.php?el_id=${cands[0].el_id}" target="_blank" id="print-proc-cert" class="btn btn-success mb-4">Certificate</a>
            `)
            $('#print-results').attr('href', `./resultsinvoice.php?el_id=${cands[0].el_id}`)
            $('#pdf-results').attr('href', `./resultspdf.php?el_id=${cands[0].el_id}`)
            for (let arr in scored) {
              // console.log(scored[arr])
  
              let vLabels = []
              let vData = []
              scored[arr].forEach(cand => {
                vLabels.push(`${cand.v_fname} (${cand.par_name})`)
                vData.push(cand.voteCount)
              })
  
              let data = {
                labels: vLabels,
                datasets: [{
                  label: `Result for ${scored[arr][0].pos_name}`,
                  data: vData,
                  backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                  ],
                  borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                  ],
                  borderWidth: 1
                }]
              };
              let config = {
                type: 'bar',
                data: data,
                options: {
                  scales: {
                    y: {
                      beginAtZero: true
                    }
                  }
                },
              };
  
              $('#voting-results-wrapper').append(`
                <div class="col-md-6">
                  <canvas id="result-pos-${scored[arr][0].pos_id}"></canvas>
                </div>
              `)
  
              let chartCon = document.querySelector(`#result-pos-${scored[arr][0].pos_id}`)
              new Chart(chartCon, config)
            }
          } else {
            $('#print-results').remove()
            $('#pdf-results').remove()
            $('#voting-results-wrapper').html('<h4 class="text-center text-secondary fst-italic">No voting results.</h4>')
          }
          
          // Population of voters by department 
          let candsByPos = Object.groupBy(voters, voter => voter.course_name)
          let courses = []
          let population = []
          // console.log(candsByPos)

          for (let key in candsByPos) {
            courses.push(key)
            population.push(candsByPos[key].length)
          }

          const data = {
            labels: courses,
            datasets: [{
              label: 'Department Population',
              data: population,
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 205, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(201, 203, 207, 0.2)'
              ],
              borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'
              ],
              borderWidth: 1
            }]
          };

          const config = {
            type: 'bar',
            data: data,
            options: {
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            },
          };

          $('#card-body-dep-pops').append(`<canvas id="dep-populations"></canvas>`)
          let popWrapper = document.querySelector('#dep-populations')

          new Chart(popWrapper, config)
          // Voted Voters
          if (uniqueVotedVoters.length < 1) {
            $('#print-voted-voters').remove()
            $('#pdf-voted-voters').remove()
            $("#voted-voters-table-wrapper").html(`
              <h4 class="text-center text-secondary fst-italic">No voted voters yet.</h4>
            `)
          } else {
            $('#print-voted-voters').remove()
            $('#pdf-voted-voters').remove()
            $('#card-body-voted-voters').prepend(`
              <a href="#" target="_blank" id="print-voted-voters" class="btn btn-secondary mb-4">Print</a>
              <a href="#" id="pdf-voted-voters" class="btn btn-danger mb-4">PDF</a>
            `)
            $('#print-voted-voters').attr('href', `./votedvotersinvoice.php?el_id=${cands[0].el_id}`)
            $('#pdf-voted-voters').attr('href', `./votedvoterspdf.php?el_id=${cands[0].el_id}`)
            let uniqueVotedVotersMapped = uniqueVotedVoters.map(voter => `
              <tr>
                <td>${voter.v_lname}, ${voter.v_fname}${voter.v_mname ? ` ${voter.v_mname}` : ''}</td>
                <td>${voter.course_name}</td>
                <td>${voter.v_yrlvl}</td>
                <td>${voter.vo_month} ${voter.vo_date}, ${voter.vo_year} (${voter.vo_day})</td>
                <td>${voter.vo_time}</td>
              </tr>
            `)

            let uniqueVotedVotersJoined = uniqueVotedVotersMapped.join('')
            let votedVotersTable = `
              <table class="table table-striped table-bordered w-100" id="voted-voters-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Date</th>
                    <th>Time</th>
                  </tr>
                </thead>
                <tbody>
                  ${uniqueVotedVotersJoined}
                </tbody>
              </table>
            `
            $('#voted-voters-table-wrapper').html(votedVotersTable)
            // *******
            // *******
            // Setup - add a text input to each footer cell
            $('#voted-voters-table thead tr')
              .clone(true)
              .addClass('filters')
              .appendTo('#voted-voters-table thead');
        
            var table = $('#voted-voters-table').DataTable({
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

          // Voters have not voted
          if (haveNotVotedVoters.length < 1) {
            $("#not-voted-voters-table-wrapper").html(`
              <h4 class="text-center text-secondary fst-italic">All voters have voted.</h4>
            `)
            $('#print-not-voted-voters').remove()
            $('#pdf-not-voted-voters').remove()
          } else {
            $('#print-not-voted-voters').remove()
            $('#pdf-not-voted-voters').remove()
            $('#card-body-not-voted-voters').prepend(`
              <a href="#" target="_blank" id="print-not-voted-voters" class="btn btn-secondary mb-4">Print</a>
              <a href="#" id="pdf-not-voted-voters" class="btn btn-danger mb-4">PDF</a>
            `)
            $('#print-not-voted-voters').attr('href', `./notvotedvotersinvoice.php?el_id=${haveNotVotedVoters[0].el_id}`)
            $('#pdf-not-voted-voters').attr('href', `./notvotedvoterspdf.php?el_id=${haveNotVotedVoters[0].el_id}`)

            let haveNotVotedVotersMapped = haveNotVotedVoters.map(voter => `
              <tr>
                <td>${voter.v_lname}, ${voter.v_fname}${voter.v_mname ? ` ${voter.v_mname}` : ''}</td>
                <td>${voter.course_name}</td>
                <td>${voter.v_yrlvl}</td>
              </tr>
            `)

            let haveNotVotedVotersJoined = haveNotVotedVotersMapped.join('')
            let notVotedVotersTable = `
              <table class="table table-striped table-bordered w-100" id="have-not-voted-voters-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Year</th>
                  </tr>
                </thead>
                <tbody>
                  ${haveNotVotedVotersJoined}
                </tbody>
              </table>
            `
            $('#not-voted-voters-table-wrapper').html(notVotedVotersTable)
            // *******
            // *******
            // Setup - add a text input to each footer cell
            $('#have-not-voted-voters-table thead tr')
              .clone(true)
              .addClass('filterss')
              .appendTo('#have-not-voted-voters-table thead');
        
            $('#have-not-voted-voters-table').DataTable({
              orderCellsTop: true,
              fixedHeader: true,
              initComplete: function () {
                let api = this.api();
  
                // For each column
                api
                  .columns()
                  .eq(0)
                  .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    let cell = $('.filterss th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    var title = $(cell).text();
                    $(cell).html(`<input type="text" placeholder="${title}" class="form-control form-control-sm ${title == 'Actions' ? 'd-none' : ''}" />`);
  
                    // On every keypress in this input
                    $('input', $('.filterss th').eq($(api.column(colIdx).header()).index()))
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
  })
</script>