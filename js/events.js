function startEvents()
{
  events = new EventSource("http://192.168.0.49/speed-test/events");

  var p = document.getElementById("event-output");

  p.innerHTML = "";

  events.onmessage = function(e)
  {
    var c = JSON.parse(e.data);

    if (c === false)
    {
      return;
    }

    p.innerHTML += c;
  };

  events.addEventListener("finish", function(e)
  {
    p.innerHTML += "--- Speed Test finished, closing event stream ---";
    events.close();
  });
}
