<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports</title>
  <link rel="stylesheet" href="./libs/bootstrap.min.css" />
  <link rel="stylesheet" href="./libs/daterangepicker.css" />
</head>
<body>
  <div class="container">
    <div class="row mt-5">
      <div class="d-flex gap-5 align-items-center justify-content-center">
        <div>
          <label for="">Departments:</label>
          <select id="departments" class="form-select">
            <option value="" disabled selected class="d-none">-- Select Department --</option>
            <option value="SSLS">SALES</option>
            <option value="SENGR">ENGINEERING</option>
            <option value="SOPS">OPERATION</option>
            <option value="STRAN">TRANSPORT</option>
          </select>
        </div>
        <div class="d-flex flex-row">
          <label for="">All Filter</label>
          <input type="checkbox" id="all-filter" class="form-check-input ms-2" checked>
        </div>
        <div id="filter-section" class="d-none">
          <label for="">Filter:</label>
          <select id="titles" class="form-select">
            <option value="">Loading...</option>
          </select>
        </div>
        <div>
          <label for="">Date Range:</label>
          <input type="text" class="form-control" id="dates">
        </div>
        <div class="d-flex flex-row">
          <label for="">Employee Filter</label>
          <input type="checkbox" id="employee-filter" class="form-check-input ms-2" checked>
        </div>
        <div id="employees-section" class="d-none">
          <label for="">Employees:</label>
          <select id="employees" class="form-select">
            <option value="">Loading...</option>
          </select>
        </div>
      </div>

      <div id="charts-container" class="col-md-9 mx-auto mt-2">
        <div class="d-flex justify-content-center align-items-center flex-column">
          <div class="spinner-border fs-4"></div>
          <h6 class="mt-2">Loading...</h6>
        </div>
      </div>
        
    </div>
  </div>
  
  <script src="./libs/jquery.min.js"></script>
  <script src="./libs/bootstrap.bundle.min.js"></script>
  <script src="./libs/DataTables/datatables.min.js"></script>
  <script src="./libs/DataTables/FixedHeader-3.4.0/js/dataTables.fixedHeader.js"></script>
  <script src="./libs/chart.js"></script>
  <script src="./libs/moment.min.js"></script>
  <script src="./libs/daterangepicker.min.js"></script>
  <script>
    $(document).ready(function(){
      $('#dates').daterangepicker({
        timePicker: true,
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('day').add(7, 'day'),
      });

      // let startDate = $('#dates').val().substr(0, 10)
      // let endDate = $('#dates').val().substr(13)
      // console.log(startDate)
      // console.log(endDate)
      // let startDate = '12/01/2023'
      // let endDate = '01/18/2024'
      let individualChart = []

      async function fetchSales() {
        let res = await fetch('assets/php/action2.php', { 
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
            startDate: $('#dates').val().substr(0, 10),
            endDate: $('#dates').val().substr(13),
            action: 'fetchTransactCountsByDateRangeOfSales'
          })
        })
        res = await res.json()
        return res
      }
      
      async function fetchEngr() {
        let res = await fetch('assets/php/action2.php', { 
          method: 'POST',
          headers: {
            "Content-Type": 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
            startDate: $('#dates').val().substr(0, 10),
            endDate: $('#dates').val().substr(13),
            action: 'fetchTransactCountsByDateRangeOfEngr'
          })
        })
        res = await res.json()
        return res
      }
      
      async function fetchOper() {
        let res = await fetch('assets/php/action2.php', { 
          method: 'POST',
          headers: {
            "Content-Type": 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
            startDate: $('#dates').val().substr(0, 10),
            endDate: $('#dates').val().substr(13),
            action: 'fetchTransactCountsByDateRangeOfOper'
          })
        })
        res = await res.json()
        return res
      }

      async function fetchTransactCountsByDateRange() {
        let [salesData, engrData, operData] = await Promise.all([fetchSales(), fetchEngr(), fetchOper()])
        let depData = {}
        let colors = ["#e74c3c", "#3498db", "#f1c40f", "#2ecc71", "#e67e22"];

        depData.sales = salesData
        depData.engr = engrData
        depData.oper = operData

        // console.log(depData)

        $('#charts-container').html('')

        for (dep in depData) {
          $('#charts-container').append(`
            <div id="${dep}-chart-container" class="mt-4">
              <h5 class="text-center text-secondary">${dep}</h5>
              <canvas id="${dep}-chart" class="mt-2"></canvas>
            </div>
          `)

          let groupedTransacts = Object.groupBy(depData[dep].transactCounts, grTran => grTran.hisStatCode)
          // console.log(groupedTransacts)

          let datasets = [];
          let indexFoIn = 0;

          // Looping through grouped transactions
          for (key in groupedTransacts) {
            let data = [];
            let datesOfTransaction = groupedTransacts[key].map(
              (obj) => obj.entered_date
            );
            let countsOfTransaction = groupedTransacts[key].map(
              (obj) => obj.lineNoCount
            );

            depData[dep].dates.forEach((date, i) => {
              if (datesOfTransaction.includes(date)) {
                let indexOfTransaction = datesOfTransaction.indexOf(date);
                data.push(countsOfTransaction[indexOfTransaction]);
              } else {
                data.push(0);
              }
            });

            datasets.push({
              label: key,
              data,
              fill: false,
              borderColor: colors[indexFoIn],
              tension: 0.1,
              pointRadius: 5,
              pointHoverRadius: 9,
            });

            indexFoIn++;
          }

          const config = {
            type: "line",
            data: {
              labels: depData[dep].dates,
              datasets
            },
            // options: {
            //   responsive: true,
            //   interaction: {
            //     mode: 'index',
            //     intersect: false,
            //   },
            //   stacked: false,
            //   plugins: {
            //     title: {
            //       display: true,
            //       text: 'Chart.js Line Chart - Multi Axis'
            //     }
            //   },
            //   scales: {
            //     y: {
            //       type: 'linear',
            //       display: true,
            //       position: 'left',
            //     },
            //     y1: {
            //       type: 'linear',
            //       display: true,
            //       position: 'right',

            //       // grid line settings
            //       grid: {
            //         drawOnChartArea: false, // only want the grid lines for one axis to show up
            //       },
            //     },
            //   }
            // },
          };

          let chartCon = document.querySelector(`#${dep}-chart`);
          let myChart = new Chart(chartCon, config);
          individualChart.push(myChart)
        }
      }
      fetchTransactCountsByDateRange()

      // Change DateRange
      $("#dates").change(async function () {
        fetchTransactCountsByDateRange()
      })

      // Change Departments
      $('#departments').change(function(e) {
        $('#titles').html('')

        if ($(this).val() === 'SSLS') {
          $('#titles').html(`
            <option>Ticket created</option>
            <option>Ticket linked to BP</option>
            <option>Ticket approved in quotation</option>
            <option>Ticket cancelled in link to BP</option>
            <option>Ticket cancelled for quotation</option>
            <option>All Filter</option>
            <option>All Filter per Person</option>
          `)
        } else if ($(this).val() === 'SENGR') {
          $('#titles').html(`
            <option>Ticket approved in survey</option>
            <option>Ticket rejected in survey</option>
            <option>Ticket cancelled for survey</option>
            <option>Ticket submitted for approval</option>
            <option>Ticket cancelled for quotation</option>
          `)
        } else if ($(this).val() === 'SOPS') {
          $('#titles').html(`
            <option>Ticket onsite</option>
            <option>Ticket completed</option>
          `)
        } else if ($(this).val() === 'STRAN') {
          $('#titles').html(`
            <option></option>
            <option></option>
          `)
        }

        // Fetch Employees by Department AJAX Request
        $.ajax({
          url: 'assets/php/action.php',
          method: 'post',
          data: { 
            action: 'fetchEmplyByDep', 
            appId: $('#departments').val() 
          },
          success: function(res) {
            let employees = JSON.parse(res)
            // console.log(employees)

            if (employees.length < 1) {
              $('#employees').html('No employees for this department.')
              return
            }

            let employeesMapped = employees.map(emp =>  `
              <option value="${emp.gUserName}">${emp.gUserName}</option>
            `)

            let employeesMapJoined =  employeesMapped.join('')

            $('#employees').html(employeesMapJoined)
          }
        }) 
      })

      // All Filter checkbox change
      $('#all-filter').change(function(e) {
        if (!this.checked) {
          if (!$('#departments').val()) {
            alert('Please select a department.')
            $('#all-filter').prop('checked', !$(this).prop('checked'))
            return
          }
          $('#filter-section').removeClass('d-none')
          $('#filter-section').addClass('d-block')
        } else {
          $('#filter-section').removeClass('d-block')
          $('#filter-section').addClass('d-none')
        }
      })

      // Employees Filter
      $('#employee-filter').change(function(e) {
        if (!this.checked) {
          if (!$('#departments').val()) {
            alert('Please select a department.')
            $('#employee-filter').prop('checked', !$(this).prop('checked'))
            return
          }
          $('#employees-section').removeClass('d-none')
          $('#employees-section').addClass('d-block')
        } else {
          $('#employees-section').removeClass('d-block')
          $('#employees-section').addClass('d-none')
        }
      })
    });
  </script>
</body>
</html>