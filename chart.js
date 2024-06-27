$.ajax({
  url: "assets/php/action2.php",
  method: "post",
  data: {
    startDate: $("#dates").val().substr(0, 10),
    endDate: $("#dates").val().substr(13),
    action: "fetchTransactCountsByDateRangeOfSales",
  },
  success: (res) => {
    let { transactCounts, dates } = JSON.parse(res);

    let dateLabels = transactCounts.map((transact) => transact.enteredDate);
    let dateData = transactCounts.map((transact) => transact.transactCount);
    let colors = ["#e74c3c", "#3498db", "#f1c40f", "#2ecc71", "#e67e22"];

    const config = {
      type: "line",
      data: {
        labels: ["Mon", "Tue", "Wed"],
        // labels: dateLabels,
        datasets: [
          {
            label: "Line Chart",
            data: [1, 2, 3],
            // data: dateData,
            fill: false,
            borderColor: "#e74c3c",
            tension: 0.1,
          },
          {
            label: "Line Chart 2",
            data: [3, 6, 7],
            // data: dateData,
            fill: false,
            borderColor: "rgb(75, 192, 192)",
            tension: 0.1,
          },
          {
            label: "Line Chart 3",
            data: [8, 18, 19],
            // data: dateData,
            fill: false,
            borderColor: "rgb(100, 100, 192)",
            tension: 0.1,
          },
        ],
      },
    };

    let chartCon = document.querySelector("#line-chart-result");
    let chart = new Chart(chartCon, config);

    $("#dates").change(function () {
      $.ajax({
        url: "assets/php/action2.php",
        method: "post",
        data: {
          startDate: $("#dates").val().substr(0, 10),
          endDate: $("#dates").val().substr(13),
          action: "fetchTransactCountsByDateRangeOfSales",
        },
        success: (res2) => {
          let { transactCounts, dates } = JSON.parse(res2);

          let groupedTransacts = Object.groupBy(
            transactCounts,
            (grTran) => grTran.hisStatCode
          );
          console.log(groupedTransacts);

          let updatedDatasets = [];

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

            dates.forEach((date, i) => {
              if (datesOfTransaction.includes(date)) {
                let indexOfTransaction = datesOfTransaction.indexOf(date);
                data.push(countsOfTransaction[indexOfTransaction]);
              } else {
                data.push(0);
              }
            });

            updatedDatasets.push({
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

          console.log(updatedDatasets);

          chart.data.labels = dates;
          chart.data.datasets = updatedDatasets;
          chart.update();
        },
      });
    });
  },
});
