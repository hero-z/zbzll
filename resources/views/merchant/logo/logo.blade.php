@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
    <style type="text/css">
        /* 图片展示样式 */
        .images_zone {
            position: relative;
            width: 120px;
            height: 120px;
            overflow: hidden;
            float: left;
            margin: 3px 5px 3px 0;
            background: #f0f0f0;
            border: 5px solid #f0f0f0;
        }

        .images_zone span {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            width: 120px;
            height: 120px;
        }

        .images_zone span img {
            width: 120px;
            vertical-align: middle;
        }

        .images_zone a {
            text-align: center;
            position: absolute;
            bottom: 0px;
            left: 0px;
            background: rgba(255, 255, 255, 0.5);
            display: block;
            width: 100%;
            height: 20px;
            line-height: 20px;
            display: none;
            font-size: 12px;
        }

        /* 进度条样式 */
        .up_progress, .up_progress1, .up_progress2, .up_progress3, .up_progress4, .up_progress5, .up_progress6, .up_progress7, .up_progress8 {
            width: 300px;
            height: 13px;
            font-size: 10px;
            line-height: 14px;
            overflow: hidden;
            background: #e6e6e6;
            margin: 5px 0;
            display: none;
        }

        .up_progress .progress-bar, .up_progress1 .progress-bar1, .up_progress2 .progress-bar2, .up_progress3 .progress-bar3, .up_progress4 .progress-bar4, .up_progress5 .progress-bar5, .up_progress6 .progress-bar6, .up_progress7 .progress-bar7, .up_progress8 .progress-bar8 {
            height: 13px;
            background: #11ae6f;
            float: left;
            color: #fff;
            text-align: center;
            width: 0%;
        }
    </style>
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>物业公司头像设置</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            <input type="hidden" value="@if($list){{$list->id}}@endif" id="id" name="id">
                            {{csrf_field()}}
                            <div class="form-group col-sm-6">
                                <script src="{{asset('uploadify/jquery.uploadify.min.js')}}"
                                        type="text/javascript"></script>
                                <link rel="stylesheet" type="text/css" href="{{asset('uploadify/uploadify.css')}}">
                                <label>物业公司头像(最佳分辨率100*100)</label>
                                <input type="hidden" required="required" size="50" value="" name="logo1" id="logo1">
                                <input type="hidden" required="required" size="50" value="@if($list){{$list->logo1}}@endif" name="oldpic1" id="oldpic1">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload" type="file" name="image" data-url="{{route('uploadlogo')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true" >
                                <!-- 图片展示模块 -->
                                <div class="files" ><img class="images_zone" id="oldimg1" width="30px" src="@if($list){{url($list->logo1)}}@endif"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress">
                                    <div class="progress-bar"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div style="text-align: center">
                                <button class="btn btn-sm btn-primary  m-t-n-xs"
                                        type="button" onclick="addpost()" style="width: 100px;height:30px">
                                    <strong>确认设置</strong>
                                </button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="con"></div>
@endsection
@section('js')
    <script>
        function addpost() {
            $.post("{{url('merchant/setcompanylogo')}}",
                {
                    _token: '{{csrf_token()}}',
                    id:$("#id").val(),
                    logo1:$("#logo1").val(),
                    oldpic1:$("#oldpic1").val(),
                },
                function (result) {
                    if (result.success==1) {
                        //询问框
                        layer.confirm('设置成功', {
                            btn: ['确定', '返回'] //按钮
                        }, function () {
                            window.location.href = "{{url('merchant/companylogo')}}";
                        }, function () {
                            layer.msg('正在浏览提交的logo');
                        });
                    } else {
                        layer.msg(result.sub_msg);
                    }
                }, "json")

        }

    </script>

    <script type="text/javascript">
        publicfileupload("#fileupload", ".files", "#logo1", ".up_progress .progress-bar", ".up_progress","#oldimg1");
        function publicfileupload(fileid, imgid, postimgid, class1, class2,oldimg) {
            //图片上传
            $(fileid).fileupload({
                dataType: 'json',
                add: function (e, data) {
                    var numItems = $('.files .images_zone').length;
                    if (numItems >= 10) {
                        alert('提交照片不能超过3张');
                        return false;
                    }
                    $(class1).css('width', '0px');
                    $(class2).show();
                    $(class1).html('上传中...');
                    data.submit();
                },
                done: function (e, data) {
                    $(class2).hide();
                    $('.upl').remove();
                    var d = data.result;
                    if (d.status == 0) {
                        alert("上传失败");
                    } else {
                        $(oldimg).remove();
                        var imgshow = '<div class="images_zone"><input type="hidden" name="imgs[]" value="' + d.image_url + '" /><span><img src="' + d.image_url + '"  /></span><a href="javascript:;">删除</a></div>';
                        jQuery(imgid).append(imgshow);
                        jQuery(postimgid).val(d.image_url);
                    }
                },
                progressall: function (e, data) {
                    console.log(data);
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(class1).css('width', progress + '%');
                }
            });

            //图片删除
            $(imgid).on({
                mouseenter: function () {
                    $(this).find('a').show();
                },
                mouseleave: function () {
                    $(this).find('a').hide();
                },
            }, '.images_zone');
            $(imgid).on('click', '.images_zone a', function () {
                $(this).parent().remove();
            });
        }
    </script>
@endsection