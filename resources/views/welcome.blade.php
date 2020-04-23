<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
  <script src="https://js.pusher.com/5.1/pusher.min.js"></script>
  <script
    src="https://code.jquery.com/jquery-3.5.0.min.js"
    integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ="
    crossorigin="anonymous"></script>
  <script>
    var pusher_app_key = "{{ config('broadcasting.connections.pusher.key') }}"

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher(pusher_app_key, {
      cluster: 'ap1',
      forceTLS: true
    });

    var channel = pusher.subscribe('my-channel');

    channel.bind('my-event', function(data) {
      var dataObj = JSON.stringify(data);
      var notiCount = parseInt($('.badge').text());
      var notiCountNew = notiCount + 1;

      console.log(dataObj);

      $('.badge').text(notiCountNew);
    });
  </script>
  <style>
    .notification {
      background-color: #555;
      color: white;
      text-decoration: none;
      padding: 15px 26px;
      position: relative;
      display: inline-block;
      border-radius: 2px;
    }

    .notification:hover {
      background: red;
    }

    .notification .badge {
      position: absolute;
      top: -10px;
      right: -10px;
      padding: 5px 10px;
      border-radius: 50%;
      background-color: red;
      color: white;
    }
  </style>
</head>
<body>
  <h1>Pusher Test</h1>
  <p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
    <br>
    <br>
    <a href="#" class="notification">
      <span>Inbox</span>
      <span class="badge">0</span>
    </a>
  </p>
</body>
