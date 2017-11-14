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
    <link href="{{asset('/adminui/css/style.min.css?v=4.0.0')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/total.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/btn.css')}}" rel="stylesheet">
    @yield("css")
    {{--<script type="text/javascript" src="http://tajs.qq.com/stats?sId=9051096" charset="UTF-8"></script>--}}
</head>
<body class="fixed-sidebar full-height-layout gray-bg" style="overflow-y:auto">
@yield("content")
</body>
<script src={{asset('/adminui/js/jquery.min.js')}}></script>
<script src="{{asset('/adminui/js/bootstrap.min.js?v=3.3.5')}}"></script>
<script src="{{asset('/adminui/js/plugins/layer/layer.min.js?v=1.0.0')}}"></script>
<script src="{{asset('/upload_imgs/js/imgUp.js')}}"></script>
<script src="{{asset('/upload_imgs/js/imgPlugin.js')}}"></script>
<script src="{{asset('/js/ajaxfileupload.js')}}" type="text/javascript"></script>

<script>
    window.onload = get;
    function get() {
        $.post("{{route('updateInfo')}}", {_token: "{{csrf_token()}}"},
            function (data) {
                    if(data.status==503){
                        window.location.href="{{url('admin/forceoauth')}}";
                    }
            }, 'json');
    }

</script>
@yield("js")
</html>