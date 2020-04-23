# laravel_pusher
Create web notifications using laravel and pusher channels

- Giống như tiêu đề bài viết thì mục tiêu của mình sẽ tạo được notification realtime như sau:
![](https://images.viblo.asia/03fa92ad-fe9a-4b1f-914d-edaf3efdecb5.gif)
- Tại thời điểm viết bài mình sử dụng
    - Ubuntu 16.04
    - PHP 7.2
    - Back-end: Laravel 7
    - Front-end: Jquery 3.5, pusher.min.js 5.1

### Tạo tài khoản pusher và channel
- Tạo tài khoản pusher tại link: https://pusher.com/ và sau đó tạo channel app như bên dưới
![](https://images.viblo.asia/bb1c951f-b698-4cf8-9cc7-b57189a27bda.png)
- Ở đây front-end mình sử dụng Jquery còn back-end mình sử dụng Laravel nhé.
### Tạo laravel app
- Tạo project laravel với câu lệnh sau:
    ```
    composer create-project --prefer-dist laravel/laravel laravel_pusher
    ```
- Cài đặt Pusher PHP SDK:
    ```
    composer require pusher/pusher-php-server
    ```
- Cập nhật file môi trường **.env**
    ```
    BROADCAST_DRIVER=pusher

    // APP_ID, APP_KEY, SECRET lấy từ channel app vừa tạo bên trên
    PUSHER_APP_ID=XXXXX
    PUSHER_APP_KEY=abcxyz
    PUSHER_APP_SECRET=XXXXXXX
    PUSHER_APP_CLUSTER=ap1
    ```
- Sửa file **config/broadcasting.php** như sau:
    ```
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        ],
    ],
    ```
- Tạo file **app/Events/MyEvent.php**
    ```PHP
    <?php

    namespace App\Events;

    use Illuminate\Queue\SerializesModels;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

    class MyEvent implements ShouldBroadcast
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public $message;

        public function __construct($message)
        {
          $this->message = $message;
        }

        public function broadcastOn()
        {
          return ['my-channel'];
        }

        public function broadcastAs()
        {
          return 'my-event';
        }
    }
    ```
- Ta tạo thêm cái route trong file **route/web.php** để lát test notification nhé:
    ```PHP
    Route::get('/test', function () {
        event(new App\Events\MyEvent('hello world'));

        return "Event has been sent!";
    });
    ```
- Nếu bạn không muốn tạo route test thì có thể test bằng tinker
    ```
    $ php artisan tinker
    >>> event(new App\Events\MyEvent('hello world'));
    ```
- Như vậy phần back-end đã xong, tiếp đến ta xử lý phần front-end.
- File **resource/views/welcome.blade.php** ta sửa lại như sau:
    ```HTML
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
    ```
- Đến đây thì bạn chỉ cần khởi chạy server `php artisan serve`. Sau đó mở 2 trình duyệt, một gọi đến `localhost:8000` và một trình duyệt ẩn danh gọi đến `localhost:8000/test` để xem kết quả.

> **Tài liệu tham khảo**
    >
    > [CREATE WEB NOTIFICATIONS USING LARAVEL AND PUSHER CHANNELS](https://pusher.com/tutorials/web-notifications-laravel-pusher-channels)
    >
    > [Laravel broadcasting](https://laravel.com/docs/master/broadcasting#driver-prerequisites)
