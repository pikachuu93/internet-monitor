function doAjax(el)
{

  xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = updateGraph;

  xhttp.open("POST", "history/ajax", true);
  xhttp.send(new FormData(el));
}

function updateGraph(e)
{
  if (xhttp.readyState !== 4)
  {
    return;
  }

  var ctx = document.getElementById("graph-container");

  ctx.width = ctx.clientWidth;

  var res = JSON.parse(xhttp.responseText);

  Chart.defaults.global.defaultFontColor = "#000";

  var timeFormat = function(value)
  {
    return new Date(value * 1000).toLocaleString();
  };

  var chartInstance = new Chart(
    ctx,
    {
      type: 'line',
      data:
      {
        datasets: [
        {
          data:            res,
          label:           "minutes",
          borderColor:     "#77A",
          backgroundColor: "rgba(136, 136, 170, 0.4)",
          lineTension:     0
        }],
      },
      options:
      {
        responsive: false,
        scales:
        {
          xAxes: [
          {
            type:     "linear",
            position: "bottom",
            ticks:
            {
              callback: timeFormat,
              min:      res[0].x,
              max:      res.slice(-1)[0].x
            }
          }]
        }
      }
    });
}
