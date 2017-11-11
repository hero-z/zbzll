<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>物业系统服务商端</title>
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <link rel="shortcut icon" href="favicon.ico">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>


    <link rel="stylesheet" href="{{asset('/ft5/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('/ft5/css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('/ft5/css/style.css')}}">
    <![endif]-->
    <script src="{{asset('/ft5/js/modernizr-2.6.2.min.js')}}"></script>
</head>
<body class="style-3">

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-push-8">

            <!-- Start Sign In Form -->
            <form action="{{route('merchantoauth.register')}}" class="fh5co-form animate-box" data-animate-effect="fadeInRight" method="post">
                {{ csrf_field() }}
                <h2>注册</h2>
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 5px">
                    <label for="name" class="sr-only">用户名</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="请输入你的用户名" autocomplete="off">
                    @if ($errors->has('name'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}" style="margin-bottom: 5px">
                    <label for="email" class="sr-only">邮箱</label>
                    <input type="email" class="form-control" id="email" name='email' placeholder="请输入你的邮箱" autocomplete="off">
                    @if ($errors->has('email'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}" style="margin-bottom: 5px">
                    <label for="phone" class="sr-only">手机号</label>
                    <input type="phone" name="phone" class="form-control" id="phone" placeholder="请输入你的手机号" autocomplete="off" >
                    @if ($errors->has('phone'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}" style="margin-bottom: 5px">
                    <label for="password" class="sr-only">密码</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="请输入密码" autocomplete="off">
                    @if ($errors->has('password'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group" style="margin-bottom: 5px">
                    <label for="re-password" class="sr-only">确认密码</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"  placeholder="请再次输入你的密码" autocomplete="off">
                </div>
                <div class="form-group" style="margin-bottom: 5px">
                    <p>已经注册? <a href="{{url('admin/oauthlogin')}}">登录</a></p>
                </div>
                <div class="form-group" style="margin-bottom: 5px">
                    <input type="submit" value="注册" class="btn btn-primary">
                </div>
            </form>
            <!-- END Sign In Form -->


        </div>
    </div>
    <div class="row" style="padding-top: 60px; clear: both;">
        <div class="col-md-12 text-center"><p><small>&copy; All Rights Reserved. More Templates <a href="http://www.cssmoban.com/" target="_blank" title=""></a> - Collect from <a href="http://www.cssmoban.com/" title="" target="_blank"></a></small></p></div>
    </div>
</div>

<!-- jQuery -->
<script src="{{asset('/ft5/js/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{asset('/ft5/js/bootstrap.min.js')}}"></script>
<!-- Placeholder -->
<script src="{{asset('/ft5/js/jquery.placeholder.min.js')}}"></script>
<!-- Waypoints -->
<script src="{{asset('/ft5/js/jquery.waypoints.min.js')}}"></script>
<!-- Main JS -->
<script src="{{asset('/ft5/js/main.js')}}"></script>
</body>
</html>

