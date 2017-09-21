<!DOCTYPE html>
<html>
  <head>
    <title>@yield('title', 'Sample App')</title>
    <!-- <link rel="stylesheet" href="/bootstrap/css/bootstrap.css"> -->
    <link rel="stylesheet" href="/css/app.css">
  </head>
  <body>
     <!--头部-->
     @include('layouts._header')

    <div class="container">
      <!--提示信息-->
      @include('shared._messages')
      <!--内容-->
      @yield('content')
      <!--尾部-->
      @include('layouts._footer')
    </div>

    <!--引入js前先引入jq-->
    <!-- <script src="/bootstrap/jquery/jquery-3.2.1.js"></script> -->
    <!-- <script src="/bootstrap/js/bootstrap.js"></script> -->
    <script src="/js/app.js"></script>
  </body>
</html>
