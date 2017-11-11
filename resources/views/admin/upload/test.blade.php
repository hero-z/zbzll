@extends("layouts.admincontentpublic")
@section("title","代理商管理")
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('upload_img/css/ycbootstrap.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('upload_img/css/reset.css')}}">
    <script src="{{asset('upload_img/js/jquery-2.1.3.min.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('upload_img/plugins/cover_js/iscroll-zoom.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('upload_img/plugins/cover_js/hammer.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('upload_img/plugins/cover_js/lrz.all.bundle.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('upload_img/plugins/cover_js/jquery.photoClip.min.js')}}" type="text/javascript" charset="utf-8"></script>
@endsection
@section("content")
    <div class="yc-upload-wrap">
        <div class="yc-upload-box">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0;">

                        <div class="ycupload-mainbox">
                            <div class="ycupload-main1" style="overflow:hidden;padding-left:25px;">
				        			<span style="float:left;color:#ff5a5a;font-size:14px;line-height:60px;font-weight:900;margin-right:7px;">
				        				&middot;
				        			</span>
                                <span style="float:left;color:#333;font-size:16px;line-height:60px;margin-right:28px;">
				        				上传封面
				        			</span>
                            </div>
                            <div class="ycupload-line"></div>
                            <div style="height:30px;"></div>
                            <div  style="min-height:1px;">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="padding-right:0;padding-left:36px;">
                                            <!--<a href="javascript:void(0);" class="cover-uploadBtn">
                                                <img src="../img/yc_uploadimg_06.png"/>
                                                <div id="clipArea"></div>
                                                <input type="file" id="file">
                                                <button id="clipBtn">截取</button>
                                            </a>
                                            <div id="view"></div>-->
                                            <div style="min-height:1px;line-height:160px;text-align:center;position:relative;" ontouchstart="">
                                                <div class="cover-wrap" style="display:none;position:fixed;left:0;top:0;width:100%;height:100%;background: rgba(0, 0, 0, 0.4);z-index: 10000000;text-align:center;">
                                                    <div class="" style="width:900px;height:600px;margin:100px auto;background-color:#FFFFFF;overflow: hidden;border-radius:4px;">
                                                        <div id="clipArea" style="margin:10px;height: 520px;"></div>
                                                        <div class="" style="height:56px;line-height:36px;text-align: center;padding-top:8px;">
                                                            <button id="clipBtn" style="width:120px;height: 36px;border-radius: 4px;background-color:#ff8a00;color: #FFFFFF;font-size: 14px;text-align: center;line-height: 36px;outline: none;">保存封面</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="view" style="width:214px;height:160.5px;" title="请上传 428*321 的封面图片"></div>
                                                <div style="height:10px;"></div>
                                                <div class="" style="width:140px;height:32px;border-radius: 4px;background-color:#ff8a00;color: #FFFFFF;font-size: 14px;text-align:center;line-height:32px;outline:none;margin-left:37px;position:relative;">
                                                    点击上传封面图
                                                    <input type="file" id="file" style="cursor:pointer;opacity:0;filter:alpha(opacity=0);width:100%;height:100%;position:absolute;top:0;left:0;">
                                                    <input type="hidden" id="test">
                                                </div>
                                            </div>

                                            <input type="submit" onclick="uploadimg()">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div style="height:25px;"></div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        function uploadimg() {
            $.post("{{url('admin/test')}}", {_token: "{{csrf_token()}}",
                data:$('#test').val()
                },
                function (data) {
                    if (data.success) {

                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');
        }
    </script>
    <script type="text/javascript">

        //上传封面
        //document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
        var clipArea = new bjj.PhotoClip("#clipArea", {
            size: [428, 321],// 截取框的宽和高组成的数组。默认值为[260,260]
            outputSize: [1070, 802], // 输出图像的宽和高组成的数组。默认值为[0,0]，表示输出图像原始大小
            //outputType: "jpg", // 指定输出图片的类型，可选 "jpg" 和 "png" 两种种类型，默认为 "jpg"
            file: "#file", // 上传图片的<input type="file">控件的选择器或者DOM对象
            view: "#view", // 显示截取后图像的容器的选择器或者DOM对象
            ok: "#clipBtn", // 确认截图按钮的选择器或者DOM对象
            loadStart: function() {
                // 开始加载的回调函数。this指向 fileReader 对象，并将正在加载的 file 对象作为参数传入
                $('.cover-wrap').fadeIn();
                console.log("照片读取中");
            },
            loadComplete: function() {
                // 加载完成的回调函数。this指向图片对象，并将图片地址作为参数传入
                console.log("照片读取完成");
            },
            //loadError: function(event) {}, // 加载失败的回调函数。this指向 fileReader 对象，并将错误事件的 event 对象作为参数传入
            clipFinish: function(dataURL) {
                // 裁剪完成的回调函数。this指向图片对象，会将裁剪出的图像数据DataURL作为参数传入
                $('.cover-wrap').fadeOut();
                $('#view').css('background-size','100% 100%');
                console.log(dataURL);
                $("#test").val(dataURL);
            }
        });
        //clipArea.destroy();
    </script>
@endsection