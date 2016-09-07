var speedTest = null;

function startEvents(start = false)
{
  if (speedTest !== null)
  {
    speedTest.close();
  }

  if (start)
  {
    speedTest = new EventSource("/speed-test/events");
  }
  else
  {
    speedTest = new EventSource("/speed-test/events/no-start");
  }

  var p = document.getElementById("event-output");

  p.innerHTML = "";

  speedTest.onmessage = function(e)
  {
    var c = JSON.parse(e.data);

    if (c === false)
    {
      return;
    }

    p.innerHTML += c;
  };

  speedTest.addEventListener("finish", function(e)
  {
    if (start)
    {
      p.innerHTML += "--- Speed Test finished, closing event stream ---";
    }
    else
    {
      p.innerHTML += "--- Speed Test ---";
    }

    speedTest.close();
    speedTest = null;
  });
}

window.onload = function(){startEvents()};
