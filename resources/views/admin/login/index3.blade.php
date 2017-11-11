<!DOCTYPE html>
<html>
<head>
    <title>服务商登录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <meta name="keywords" content="物业服务商登录后台" />
    <link href="{{asset('admin/css/style.css')}}" rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="{{asset('/ft5/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('/ft5/css/animate.css')}}">
    <!--webfonts-->
    {{--<link href='http://fonts.useso.com/css?family=PT+Sans:400,700,400italic,700italic|Oswald:400,300,700' rel='stylesheet' type='text/css'>--}}
    {{--<link href='http://fonts.useso.com/css?family=Exo+2' rel='stylesheet' type='text/css'>--}}
    <!--//webfonts-->
    <script src="{{asset('adminui/js/jquery.min.js')}}"></script>
</head>
<body style="overflow-y: hidden">
<script>$(document).ready(function(c) {
        $('.close').on('click', function(c){
            $('.login-form').fadeOut('slow', function(c){
                $('.login-form').remove();
            });
        });
    });
</script>
<!--SIGN UP-->
{{--<h1>往知来服务商登录后台</h1>--}}
<div style="height: 10%;width: 10%;margin: 0 45%;margin-bottom: -3%">
    {{--<img src="{{asset('./ft5/images/logo2.png')}}" alt="往知来">--}}
</div>
<div class="login-form">
    <div class="close"> </div>
    <div class="head-info">
        <label class="lbl-1"> </label>
        <label class="lbl-2"> </label>
        <label class="lbl-3"> </label>
    </div>
    <div class="clear"> </div>
    <div class="avtar">
        <img src="{{asset('./ft5/images/logo2.png')}}}}" />
        {{--<img src="{{asset('admin/images/avtar.png')}}" />--}}
    </div>
    <form action="{{route('admin.login')}}" method="post">
        {{csrf_field()}}
        <input type="text" required class="text" name="phone" value="{{ old('phone') }}"  autofocus  placeholder="用户名" >
        <div class="errorinfo" {{$errors->has('phone')?:'hidden'}}>
            @if ($errors->has('phone'))
                <span >
                        <strong>{{ $errors->first('phone') }}</strong>
            </span>
            @endif
        </div>
        <div class="key" >
            <input type="password" required placeholder="密码" name="password">

        </div>
        <div class="errorinfo" {{$errors->has('phone')?:'hidden'}}>
            @if ($errors->has('password'))
                <span >
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
            @endif
        </div>
        <div class="signin">
            <input type="submit" value="登录" >
        </div>
    </form>

</div>
<div class="copy-rights">
    <p>Copyright &copy; 2017.WangZhiLai All rights reserved.More Information <a href="http://wzl.qimengweixin.com" target="_blank" title='官网'>往知来</a></p>
</div>

</body>
</html>