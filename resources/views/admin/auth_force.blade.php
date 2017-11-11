<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <title>温馨提醒</title>

    <meta name="keywords" content="">
    <meta name="description" content="">
    {{--<meta http-equiv="Cache-Control" content="no-siteapp" />--}}
    <!--[if lt IE 8]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->

    <link rel="shortcut icon" href="favicon.ico">
    <link href="{{asset('/adminui/css/bootstrap.min.css?v=3.3.5')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/style.min.css?v=4.0.0')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/total.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/btn.css')}}" rel="stylesheet">
    <script src="{{asset('/adminui/js/jquery.min.js')}}"></script>
    <script src="{{asset('/adminui/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/layer/layer.min.js')}}"></script>
    {{--<script type="text/javascript" src="http://tajs.qq.com/stats?sId=9051096" charset="UTF-8"></script>--}}
</head>
<body class="fixed-sidebar full-height-layout gray-bg" style="height: 100%;overflow-y:auto">

<div id="mask" class="mask" style="height:100%;display: block"></div>

{{--添加房屋--}}
<div id="add_rom" class="ant-modal" style="width: 750px; transform-origin: 1054px 10px 0px;display: block">
    <div class="ant-modal-content">
        <button class="ant-modal-close"  onclick="CloseDiv('add_rom','mask')">
            <span class="ant-modal-close-x"></span>
        </button>
        @if(isset($admin)&&$admin)
            <div class="ant-modal-header">
                <div class="ant-modal-title"><h3>温馨提醒</h3></div>
            </div>
            <div class="ant-modal-body">
                <div class="ant-row ant-form-item">
                    <div class="ant-col-16 ant-form-item-control-wrapper">
                        <div class="ant-form-item-control ">
                            <p style="font-size: 14px; text-align: center;">
                                <label style="font-size: 14px" class="ant-form-item-required">您好:</label>
                                @if($admin->status==0) 您还没有进行实名认证,请先进行实名认证! @else 您还没有通过实名认证,请完善资料! @endif
                                <a href="{{url('admin/auth_agent')}}" style="color: red;font-size: 18px">
                                    点我完成验证
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="ant-modal-body">
                <div class="ant-row ant-form-item">
                    <div class="ant-col-6 ant-form-item-label">
                        <label class="ant-form-item-required">友情提醒</label>
                    </div>
                    <div class="ant-col-16 ant-form-item-control-wrapper">
                        <div class="ant-form-item-control ">
                            <p style="color: red">访问异常,请联系往知来网络科技</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>


{{--成功弹窗--}}
<div id="model_box" class="ant-modal" style="display:none;width: 30%;height: 20%;border-radius: 4px;text-align: center;padding: 20px 30px">
    <div class="ant-modal-content" id="model" style="border-color: #4cae4c; background-color: #4cae4c;color: #fff;padding: 20px 30px;">

    </div>
</div>

{{--<script src="{{asset('assets/js/input.js')}}"></script>--}}
<script>
    $(document).ready(function () {

        ShowDiv('add_room','mask');

    });

    //弹出隐藏层
    function ShowDiv(show_div,bg_div){
        document.getElementById(show_div).style.display='block';
        document.getElementById(bg_div).style.display='block' ;
        var bgdiv = document.getElementById(bg_div);
        bgdiv.style.width = document.body.scrollWidth;
        $("#"+bg_div).height($(document).height());
        $('#room_name').chosen();

    }
    //关闭弹出层
    function CloseDiv(show_div,bg_div){
        document.getElementById(show_div).style.display='none';
        document.getElementById(bg_div).style.display='none';
        $('#template').hide()

    }
    function CloseModel() {
        $('#model_box').hide()
    }





</script>
</body>

</html>
