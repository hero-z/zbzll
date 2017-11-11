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
    <link rel="shortcut icon" href="{{asset('ft5/images/logoico.ico')}}" />

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="{{asset('/ft5/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('/ft5/css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('/ft5/css/style.css')}}">
</head>
<body class="style-3" style="background: #ffffff {{url('../images/geometry2.png')}} ;">

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <ul class="menu">
                <li><a href=""></a></li>
                <li><a href=""></a></li>
                <li class="active"><a href=""></a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-push-8">


            <!-- Start Sign In Form -->
            <form class="fh5co-form animate-box" data-animate-effect="fadeInRight" method="POST" action="{{ route('oauthmerchant.login') }}">
                <h2>登录</h2>
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                    <label for="phone" class="sr-only">手机号</label>
                    <input type="phone" class="form-control" id="phone" placeholder="请输入手机号" autocomplete="off" name="phone" value="{{ old('phone') }}" required autofocus>
                    @if ($errors->has('phone'))
                        <span class="help-block">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="sr-only">密码</label>
                    <input type="password" class="form-control" id="password" placeholder="请输入密码" autocomplete="off" name="password" value="{{ old('password') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label for="remember"> <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> <p>记住我| <a href="">忘记密码?</a>|没有账号? <a href="{{route('oauthmerchant.register')}}">注册</a> </p></label>
                </div>
                <div class="form-group">
                    <input type="submit" value="登录" class="btn btn-primary">
                </div>
            </form>
            <!-- END Sign In Form -->


        </div>
    </div>
    <div class="row" style="padding-top: 60px; clear: both;">
        <div class="col-md-12 text-center"><p><small>&copy; All Rights Reserved. More Templates <a href="http://www.cssmoban.com/" target="_blank" title=""></a> - Collect from <a href="http://www.cssmoban.com/" title="网页模板" target="_blank"></a></small></p></div>
    </div>
</div>
</body>
</html>




