<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <title>温馨提醒页</title>

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
        <div class="col-sm-12">
            {{--<div class="ibox-title">--}}
            {{--<h5>授权给支付宝</h5>--}}
            {{--</div>--}}
            <div class="well col-sm-12">
                <div style="text-align: center" >
                    <div class="well">
                        <h3>请扫码授权并完善真实的公司资料,等待服务商审核!</h3>
                    </div>
                    <img id="img" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate($code_url)) !!} ">

                </div>
                {{--<div class="well well-lg">--}}
                {{--<h3>--}}
                {{--开口碑店流程--}}
                {{--</h3> 1.商户扫描上面这个二维码授权 2.在口碑开店列表提交资料 3.口碑开店成功自动签约当面付 4.在门店列表生成收款码--}}
                {{--</div>--}}
                <div class="well">
                    <h4>
                        第三方应用授权说明:
                    </h4>
                    <h4>1.该二维码可用于代理商或员工发展物业公司,物业公司扫码授权后,该公司将自动归属于推广人员</h4>
                    <h4>2.物业公司账号需为企业账号,授权完成后,会有注册的操作,请物业公司务必填写真实信息.</h4>
                    <h4>3.物业公司如未签约支付宝相关业务,请扫右侧二维码,或点击右侧二维码下链接,进行签约授权</h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{--<script src="{{asset('assets/js/input.js')}}"></script>--}}
</body>

</html>
