function doAjax(el)
{

  xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = updateGraph;

  xhttp.open("POST", "history/ajax", true);
  xhttp.send(new FormData(el));
}

function loadGraph(data)
{
  if (!data)
  {
    return;
  }

  var ctx = document.getElementById("graph-container");

  ctx.width = ctx.clientWidth;

  var timeFormat = function(value)
  {
    return new Date(value * 1000).toLocaleString();
  };

  if (typeof chartInstance !== "undefined")
  {
    chartInstance.destroy();
  }

  chartInstance = new Chart(
    ctx,
    {
      type: 'line',
      data:
      {
        datasets: [
        {
          data:            data,
          label:           "percent",
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
              min:      data[0].x,
              max:      data.slice(-1)[0].x
            }
          }]
        }
      }
    });
}

function updateGraph(e)
{
  if (xhttp.readyState !== 4)
  {
    return;
  }

  var res = JSON.parse(xhttp.responseText);

  loadGraph(res);
}
