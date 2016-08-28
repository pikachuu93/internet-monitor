
google.charts.load("current", {packages: ["corechart"]});

function doAjax(el)
{

  xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = updateGraph;

  xhttp.open("POST", "history/ajax", true);
  xhttp.send(new FormData(el));
}

function updateGraph(e)
{
  if (!xhttp.responseText)
  {
    return;
  }

  var data = new google.visualization.arrayToDataTable(JSON.parse(xhttp.responseText));

  //data.addColumn("number", "Downtime");
  //data.addColumn("date", "Date");
  //data.addColumn({type: "string", role: "tooltip", p: {html: true}});

  //data.addRows(JSON.parse(xhttp.responseText));

  var options =
  {
    title: "Internet Downtime",
    hAxis: {title: "Date"},
    vAxis: {title: "Downtime (mins)", minValue: 0},
    pointSize: 5,
    tooltip: {isHtml: true},
    backgroundColor: "#EEE",
    //trendlines: {
    //  0: {
    //    type: 'linear',
    //    color: 'green',
    //    lineWidth: 3,
    //    opacity: 0.3,
    //    showR2: true,
    //    visibleInLegend: true
    //  }
    //}
  };
  
  var chart = new google.visualization.LineChart(document.getElementById("graph-container"));
  
  chart.draw(data, options);
}
