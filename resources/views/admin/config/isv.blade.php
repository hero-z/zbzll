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
                    @if(isset($cfg)&&$cfg)
                        <form class="form-horizontal m-t" id="signupForm" action="" method="post">
                            <div class="form-group">
                                <label class="control-label label-">APP_ID</label>

                                <div class="col-sm-12">
                                    <input id="app_id" name="input" value="{{$cfg->app_id}}" class="form-control" type="text" aria-required="true" aria-invalid="true" class="error" placeholder="请输入APP_ID">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="control-label">PID</label>

                                <div class="col-sm-12">
                                    <input id="pid" name="input" value="{{$cfg->pid}}" class="form-control" type="text" aria-required="true" aria-invalid="true" class="error" placeholder="请输入PID">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class=" control-label">应用网关</label>
                                <div class="col-sm-12">
                                    <input id="notify" name="input" value="{{$cfg->notify}}" class="form-control" type="text" aria-required="true" aria-invalid="true" class="error" placeholder="请输入应用网关">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class=" control-label">授权回调地址</label>
                                <div class="col-sm-12">
                                    <input id="callback_address" name="input" value="{{$cfg->callback}}" class="form-control" type="text" aria-required="true" aria-invalid="true" class="error" placeholder="授权回调地址">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class=" control-label">软件生成的应用私钥</label>
                                <div class="col-sm-12">
                                    <textarea name="input" class="" id="rsaPrivateKey"  style="min-width: 100%;min-height: 300px;border: 1px solid #e5e6e7;border-radius: 1px;" placeholder="请输入软件生成的应用私钥">{{$cfg->rsaPrivateKey}}</textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class=" control-label">软件生成的应用公钥</label>
                                <div class="col-sm-12">
                                    <textarea name="input" id="alipayrsaPublicKey"  style="min-width: 100%;min-height: 150px;border: 1px solid #e5e6e7;border-radius: 1px;" placeholder="请输入软件生成的应用公钥">{{$cfg->alipayrsaPublicKey}}</textarea>
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
                $.post("{{url('admin/setisvconfig')}}", {
                        _token: "{{csrf_token()}}",
                        app_id:$('#app_id').val(),
                        pid:$('#pid').val(),
                        notify:$('#notify').val(),
                        callback:$('#callback_address').val(),
                        rsaPrivateKey:$('#rsaPrivateKey').val(),
                        alipayrsaPublicKey:$('#alipayrsaPublicKey').val()
                    },
                    function (data) {
                        layer.msg(data.msg);
                    }, 'json');
            });

        });

        function CloseModel() {
            $('#model_box').hide()
        }
    </script>
@endsection