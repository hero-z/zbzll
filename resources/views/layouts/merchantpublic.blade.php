<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    @yield('meta')
    <title>@yield('title')</title>

    <meta name="keywords" content="@yield('keywords')">
    <meta name="description" content="@yield('description')">
    {{--<meta http-equiv="Cache-Control" content="no-siteapp" />--}}
    <!--[if lt IE 8]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    <link rel="shortcut icon" href="{{asset('ft5/images/logoico.ico')}}" />
    <link href="{{asset('/adminui/css/bootstrap.min.css?v=3.3.5')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/font-awesome.min.css?v=4.4.0')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/style.min.css?v=4.0.0')}}" rel="stylesheet">
    @yield("css")
    <script src="{{asset('/adminui/js/jquery.min.js?v=2.1.4')}}"></script>
    <script src="{{asset('/adminui/js/bootstrap.min.js?v=3.3.5')}}"></script>
    <script src="{{asset('/adminui/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/layer/layer.min.js')}}"></script>
    <script src="{{asset('/adminui/js/hplus.min.js?v=4.0.0')}}"></script>
    <script type="text/javascript" src="{{asset('/adminui/js/contabs.min.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/pace/pace.min.js')}}"></script>
    <script src="{{asset('/adminui/js/content.min.js?v=1.0.0')}}"></script>
    <script src="{{asset('/adminui/js/plugins/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/validate/messages_zh.min.js')}}"></script>
    <script src="{{asset('/adminui/js/demo/form-validate-demo.min.js')}}"></script>
    {{--<script type="text/javascript" src="http://tajs.qq.com/stats?sId=9051096" charset="UTF-8"></script>--}}
    @yield("js")
</head>

<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
@yield("content")

</body>

</html>