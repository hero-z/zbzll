@extends("layouts.admincontentpublic")
@section("title","代理商管理")
@section('css')
    <link href="{{asset('/adminui/js/plugins/fancybox/jquery.fancybox.css')}}" rel="stylesheet">
    <link href="{{asset('upload_imgs/css/index.css')}}" type="text/css" rel="stylesheet"/>
    <style>
        /*.ant-form-item-control{*/
        /*line-height: normal;*/
        /*}*/
        .fl{
            float: left;
        }
    </style>
@endsection
@section("content")

    {{--遮罩层--}}
    <div id="mask" class="mask"></div>
    {{--修改资料--}}
    <div id="agentEditFile_box" class="ant-modal" style="width: 800px; transform-origin: 1054px 10px 0px;">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('agentEditFile_box','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >重置密码</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <input type="text" style="display: none;">
                            <input type="password" style="display: none;">
                            <label  class="ant-form-item-required" >原密码</label>
                        </div>
                        <input type="hidden" id="editagentfileid" value="">
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="password" id="rpassword" name="rpassword" placeholder="请输入原密码" value="" class="input ant-input ant-input-lg" required>
                                <span id="rpassworderror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >新密码</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="password" id="password" placeholder="填写新的密码" name="password"  class="input ant-input ant-input-lg" required>
                                <span id="passworderror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >确认新密码</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="password" id="repassword" placeholder="确认新的密码" name="repassword"  class="input ant-input ant-input-lg" required>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style=" text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" onclick="editagentfile()" id=""><span>确认修改</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{asset('/adminui/js/plugins/peity/jquery.peity.min.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/fancybox/jquery.fancybox.js')}}"></script>
    <script src="{{asset('/adminui/js/content.min.js?v=1.0.0')}}"></script>
    <script>
        $(document).ready(function(){$(".fancybox").fancybox({openEffect:"none",closeEffect:"none"})});
    </script>
    <script>
        function editagentfile() {
            $.post("{{url('admin/ressetpsw')}}", {_token: "{{csrf_token()}}",
                    rpassword: $('#rpassword').val(),
                    password: $('#password').val(),
                    password_confirmation: $('#repassword').val()
                },
                function (data) {
                    if (data.success==1) {
                        layer.msg(data.msg,{time:500});
                        setTimeout(function(){window.parent.location.href='{{url('/')}}';},500);
                    }else if(data.success==2){
                        var errors=data.msg;
                        var msgs='';
                        for( var error in errors){
                            var  obj=$('#'+error+'error');
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
        //弹出隐藏层
        function ShowDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='block';
            document.getElementById(bg_div).style.display='block' ;
            var bgdiv = document.getElementById(bg_div);
            bgdiv.style.width = document.body.scrollWidth;


        }
        //关闭弹出层
        function CloseDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='none';
            document.getElementById(bg_div).style.display='none';
            $('.input').css({'border':'1px solid #d9d9d9'});
            $('.select').css({'border':'1px solid #d9d9d9'});
            $('.input').next().hide();
            $('#agentFile_box').find('img').attr('src','');
            $('#agentFile_box').find('input').val('');
            $('#agentUpdateFile_box').find('input').val('');
            $('.up-section').hide();
            $('.select').next().hide();
            $('.role_id').remove();
            $("#agentFile_box").find('input').val('');
            $("#agentEditFile_box").find('input').val('');
        }
        function CloseAll(show_div,bg_div){
            document.getElementById(show_div).style.display='none';
            document.getElementById(bg_div).style.display='none';
            $('#getUser_box').hide();
        }
        {{--修改用户密码--}}
        function show_eq() {
            $("#getUser_box").hide();
            $("#eq_box").show();
        }

        function CloseModel() {
            $('#model_box').hide()
        }
        $("#input").each(function() {
            $(this).click(function () {
                $(this).css({"border": "1px solid #d9d9d9"});
                $(this).next().hide()
            });
        });
    </script>
@endsection
