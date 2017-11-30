<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>服务商找回密码</title>
    <meta name="keywords" content="服务商找回密码" />
    <link rel="stylesheet" href="{{asset('ft5/css/reset.css')}}" />
    <link rel="stylesheet" href="{{asset('ft5/css/login.css')}}" />
    <link rel="shortcut icon" href="{{asset('ft5/images/logoico.ico')}}" />
    <script type="text/javascript" src="{{asset('ft5/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('ft5/js/login.js')}}"></script>
</head>
<body style="overflow: hidden">
<div class="page">
    <div class="loginwarrp">
        <div class="logo" style="height:100px;"><img src="{{asset('ft5/images/logogreen.png')}}" style="width: 250px;" alt="往知来"></div>
        <div class="login_form">
            <form id="Login" name="Login" action="{{url('admin/selfresetpsw')}}" method="post" onsubmit="">
                {{csrf_field()}}
                <li class="login-item" style="padding-left:30px;">
                    <span>&nbsp;手机号：</span>
                    <input type="text" required name="phone" id="phone"  class="login_input" style="padding-left:20px;"  value="{{ old('phone') }}"  autofocus  placeholder="登录的手机号" >
                </li>
                <span id="phone_msg"  style="padding-left:20px;width: 140px;display: none" class="error errorinfo"></span>
                <li class="login-item" style="padding-left:30px;">
                    <span>&nbsp;验证码：</span>
                    <input type="text" required placeholder="请输入验证码" id="code" style="padding-left:20px;width: 140px;"  name="password" class="login_input">
                    <button class="login_input" type="button" id="sentcode" onclick="getcode(this)" style="width:80px;margin-right:0;padding:0;cursor:pointer">获取验证码</button>
                </li>
                <span id="code_msg"  style="display:none;margin-left:100px;color: red;" class="error errorinfo"></span>
                <input type="text" style="display: none;">
                <input type="password" style="display: none;">
                <li class="login-item" style="padding-left:30px;">
                    <span>&nbsp;新密码：</span>
                    <input type="password" required placeholder="密码" id="password" style="padding-left:20px;"  name="password" class="login_input">
                </li>
                <span id="password_msg"  style="display:none;margin-left:100px;color: red;" class="error errorinfo"></span>
                <li class="login-item" style="padding-left:30px;">
                    <span>确认密码：</span>
                    <input type="password" required placeholder="密码" id="repassword" style="padding-left:10px;"  name="password" class="login_input">
                </li>
                <span id="repassword_msg"  style="display:none;margin-left:140px;color: red;" class="error errorinfo"></span>
                <li class="login-sub">
                    <input  type="button" onclick="resetpsw()" name="Submit" value="重置密码" />
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
        };
        CanvasParticle(config);
        $("a").mouseover(function(){
            $(this).css("color", "red");
        });

        $("a").mouseout(function(){
            $(this).css("color", "grey");
        });
    }
</script>
<script>
    var clock = '';
    var nums = 60;
    var btn;
    function getcode(thisBtn) {
        btn=thisBtn;
        $.post("{{url('admin/getcode')}}", {_token: "{{csrf_token()}}",
                phone: $('#phone').val(),
                type: 1
            },
            function (data) {
                if (data.success) {
                    layer.msg(data.msg,{time:1500});
                    $(thisBtn).attr('disabled',true); //将按钮置为不可点击
                    $(thisBtn).html(nums + '秒后重试');
                    clock = setInterval(doLoop, 1000); //一秒执行一次
                    {{--setTimeout(function(){window.parent.location.href='{{url('/')}}';},500);--}}
                } else {
                    layer.msg(data.msg,{time:3000});
                }
            }, 'json');
    }
    function doLoop() {
        nums--;
        if (nums > 0) {
            $(btn).html(nums + '秒后重试');
        } else {
            clearInterval(clock); //清除js定时器
            $(btn).attr('disabled',false);
            $(btn).html('获取验证码');
            nums = 90; //重置时间
        }
    }
    function resetpsw() {
        $.post("{{url('admin/selfresetpsw')}}", {_token: "{{csrf_token()}}",
                phone: $('#phone').val(),
                code: $('#code').val(),
                type: 1,
                password: $('#password').val(),
                password_confirmation: $('#repassword').val()
            },
            function (data) {
                if (data.success==1) {
                    layer.msg(data.msg,{time:1000});
                    setTimeout(function(){window.parent.location.href='{{url('/')}}';},1000);
                }else if(data.success==2){
                    var errors=data.msg;
                    var msgs='';
                    console.log(errors);
                    for( var error in errors){
                        var  obj=$('#'+error+'_msg');
                        obj.css('display','block');
                        for(var msg in errors[""+error]){
                            msgs+=errors[""+error][""+msg];
                        }
                        obj.text(msgs);
                        msgs='';
                    }
                } else {
                    layer.msg(data.msg,{time:3000});
                }
            }, 'json');
    }
</script>
<script type="text/javascript" src="{{asset('ft5/js/canvas-particle.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/layer/layer.min.js')}}"></script>
</body>
</html>