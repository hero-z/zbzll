@extends("layouts.admincontentpublic")
@section("title","代理商管理")
@section('css')
    <style>
        label{
            padding:15px;
        }
    </style>
@endsection
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>配置信息</h5>
                    <div class="ibox-tools">

                    </div>
                </div>
                <div class="ibox-content">
                    @if(isset($config)&&$config)
                        <form class="form-horizontal m-t" id="signupForm" action="" method="post">
                            <input type="hidden" id="config_id" value="{{$config->id}}">
                            <div class="form-group">
                                <label class="control-label label-">阿里云appkey</label>

                                <div class="col-sm-12">
                                    <input id="aliyun_app_key" name="input" value="{{$config->aliyun_app_key}}" class="form-control" type="text" aria-required="true" aria-invalid="true" class="error" placeholder="请输入阿里云云通信appkey">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="control-label">阿里云appsecret</label>

                                <div class="col-sm-12">
                                    <input id="aliyun_app_secret" name="input" value="{{$config->aliyun_app_secret}}" class="form-control" type="text" aria-required="true" aria-invalid="true" class="error" placeholder="请输入阿里云云通信appsecret">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class=" control-label">短信签名</label>
                                <div class="col-sm-12">
                                    <input id="sign_name" name="input" value="{{$config->sign_name}}" class="form-control" type="text" aria-required="true" aria-invalid="true" class="error" placeholder="请输入阿里云云通信短信签名">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class=" control-label">验证码模板id</label>
                                <div class="col-sm-12">
                                    <input id="template_code" name="input" value="{{$config->template_code}}" class="form-control" type="text" aria-required="true" aria-invalid="true" class="error" placeholder="请输入阿里云云通信短信模板ID">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class=" control-label">缴费提醒模板id</label>
                                <div class="col-sm-12">
                                    <input id="template_msg" name="input" value="{{$config->template_msg}}" class="form-control" type="text" aria-required="true" aria-invalid="true" class="error" placeholder="请输入阿里云云通信缴费通知模板ID">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <button class="btn btn-primary" type="button" id="zfb_keep">保存</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{--成功弹窗--}}
    <div id="model_box" class="ant-modal" style="display:none;width: 30%;height: 20%;border-radius: 4px;text-align: center;padding: 20px 30px">
        <div class="ant-modal-content" id="model" style="border-color: #4cae4c; background-color: #4cae4c;color: #fff;padding: 20px 30px;">

        </div>
    </div>

@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#zfb_keep').click(function () {
                var input= $("[name='input']").val();
                if($.trim(input)==""){
                    alert("不能为空");
                    return false;
                }
                $.post("{{url('admin/setsms')}}", {
                        _token: "{{csrf_token()}}",
                        id:$('#config_id').val(),
                        aliyun_app_key:$('#aliyun_app_key').val(),
                        aliyun_app_secret:$('#aliyun_app_secret').val(),
                        sign_name:$('#sign_name').val(),
                        template_code:$('#template_code').val(),
                        template_msg:$('#template_msg').val()
                    },
                    function (data) {
                        layer.msg(data.msg,{time:2000});
                    }, 'json');
            });

        });

        function CloseModel() {
            $('#model_box').hide()
        }
    </script>
@endsection