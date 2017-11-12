<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>服务商登录</title>
    <meta name="keywords" content="物业服务商登录后台" />
    <link rel="stylesheet" href="{{asset('ft5/css/reset.css')}}" />
    <link rel="stylesheet" href="{{asset('ft5/css/login.css')}}" />
    <link rel="shortcut icon" href="{{asset('ft5/images/logoico.ico')}}" />
    <script type="text/javascript" src="{{asset('ft5/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('ft5/js/login.js')}}"></script>
</head>
<body style="overflow: hidden">
<div class="page">
    <div class="loginwarrp">
        <div class="logo" style="height:100px;"><img src="{{url($adminlogo->logo2)}}" style="width: 250px;" alt="往知来"></div>
        <div class="login_form">
            <form id="Login" name="Login" action="{{route('admin.login')}}" method="post" onsubmit="">
                {{csrf_field()}}
                <li class="login-item" style="padding-left:30px;">
                    <span>用户名：</span>
                    <input type="text" required name="phone"  class="login_input" style="padding-left:20px;"  value="{{ old('phone') }}"  autofocus  placeholder="用户名" >
                    <span id="count-msg"  {{$errors->has('phone')?:'hidden'}} style="display:block;margin-left:60px" class="error errorinfo">
                        @if ($errors->has('phone'))
                            {{ $errors->first('phone') }}
                        @endif
                    </span>
                </li>
                <li class="login-item" style="padding-left:30px;">
                    <span>密　码：</span>
                    <input type="password" required placeholder="密码" id="password"style="padding-left:20px;"  name="password" class="login_input">
                    <span id="password-msg" {{$errors->has('password')?:'hidden'}} style="display:block;margin-left:60px" class="error errorinfo">
                        @if ($errors->has('password'))
                            {{ $errors->first('password') }}
                        @endif
                    </span>
                </li>
                <li class="login-sub">
                    <input type="submit" name="Submit" value="登录" />
                </li>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    window.onload = function() {
        var config = {
            vx : 4,
            vy : 4,
            height : 2,
            width : 2,
            count : 100,
            color : "121, 162, 185",
            stroke : "100, 200, 180",
            dist : 6000,
            e_dist : 20000,
            max_conn : 10
        }
        CanvasParticle(config);
    }
</script>
<script type="text/javascript" src="{{asset('ft5/js/canvas-particle.js')}}"></script>
</body>
</html>