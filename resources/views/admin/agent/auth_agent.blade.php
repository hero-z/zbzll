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
    {{--更新资料--}}
    <div id="agentUpdateFile_box" class="ant-modal" style="width: 800px; transform-origin: 1054px 10px 0px;display: none;">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('agentUpdateFile_box','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >更新资料</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" id="upagentid" value="">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >姓名</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="upagentname" name="upagentname" value="" class="input ant-input ant-input-lg" required>
                                <span id="upagentnameerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >身份证号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="upagentid_card_no" name="upagentid_card_no"  class="input ant-input ant-input-lg" required>
                                <span id="upagentid_card_noerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >银行卡号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="upagentbank_card_no" name="upagentbank_card_no"  class="input ant-input ant-input-lg" required>
                                <span id="upagentbank_card_noerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="wrapper wrapper-content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-content">
                                        <h5>重新上传图片信息(身份证正面、手持身份证、银行卡正反面)</h5>
                                    </div>
                                    <div class="img-box full">
                                        <section class=" img-section">
                                            <div class="z_photo upimg-div clear" >
                                                <!--<section class="up-section fl">
                                                        <span class="up-span"></span>
                                                        <img src="/img/buyerCenter/a2.png" class="close-upimg">
                                                        <img src="/img/buyerCenter/3c.png" class="type-upimg" alt="添加标签">
                                                        <img src="/img/test2.jpg" class="up-img">
                                                        <p class="img-namep"></p>
                                                        <input id="taglocation" name="taglocation" value="" type="hidden">
                                                        <input id="tags" name="tags" value="" type="hidden">
                                                    </section>-->
                                                <section class="z_file fl " >
                                                    <img src="{{asset('upload_imgs/img/a11.png')}}"  class="add-img">
                                                    <input type="file" name="file" id="file" class="file" value="" accept="image/jpg,image/jpeg,image/png,image/bmp" multiple />
                                                </section>
                                            </div>
                                        </section>
                                    </div>
                                    <aside class="mask works-mask">
                                        <div class="mask-content">
                                            <p class="del-p">您确定要删除作品图片吗？</p>
                                            <p class="check-p"><span class="del-com wsdel-ok">确定</span><span class="wsdel-no">取消</span></p>
                                        </div>
                                    </aside>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style=" text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" onclick="editagentinfo()" id=""><span>重新提交</span></button>
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
    <script type="text/javascript">
        //处理图片上传
        $('#file').takungaeImgup({
            formData:{
                _token : "{{csrf_token()}}",
                path:'agent/'
            },
            url:"{{url('admin/uploadimg')}}",
            success:function(data){
//                console.log(data);

            },
            error:function (err) {
                console.log(err)
            }
        });
    </script>
    <script>
        $(function () {
            var agentid="{{Auth::guard('admin')->user()->id}}";
            ShowDiv('agentUpdateFile_box','mask');
            getagentinfo(agentid);
        });
        //加载用户资料
        function getagentinfo(id) {
            $('#agentid').val(id);
            $('#upagentid').val(id);
            $.post("{{url('admin/getagentinfo')}}", {_token: "{{csrf_token()}}", id: id },
                function (data) {
                    if (data.success) {
                        var pathurl="{{url('/')}}";
                        var infos=data.data;
                        for( var key in infos){
                            var  obj=$('#'+'agent'+key);
                            var  upobj=$('#'+'upagent'+key);
                            if(key=='id'||key=='created_at'){
                                continue;
                            }else if(key=='id_card_front'||key=="id_card_back"||key=='id_card_hold'||key=='bank_card_hold'||key=='bank_card_front'){
                                obj.attr('href',infos[""+key]);
                                obj.find('img').attr('src',pathurl+infos[""+key]);
                            }else{
                                obj.val(infos[""+key]);
                                upobj.val(infos[""+key]);
                            }

                        }
                    } else {
                        layer.msg(data.msg,{time:2000});

                    }
                }, 'json');
        }
        //修改资料
        function editagentinfo() {
            var imgs=[];
            var array=$(".taglocation");//单引号 的name替换为相应的name
            for(var i=0;i<array.length;i++) {
                var value = $(array[i]).val();
                imgs.push(value);
            }
            $.post("{{url('admin/editagentinfo')}}", {_token: "{{csrf_token()}}",
                    images: imgs,
                    admin_id: $('#upagentid').val(),
                    name: $('#upagentname').val(),
                    id_card_no: $('#upagentid_card_no').val(),
                    bank_card_no: $('#upagentbank_card_no').val()
                },
                function (data) {
                    if (data.success) {
                        layer.msg(data.msg,{time:500});
                        setTimeout(function(){window.location.href="{{url('/')}}"},500);
                    } else {
                        layer.msg(data.msg,{time:2000});

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

