@extends("layouts.merchantcontent")
@section("title","员工信息管理")
@section("content")

    {{--遮罩层--}}
    <div id="mask" class="mask"></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    @if($pid=='0')
                    @permission('addMerchant')
                    <div style="display: block;float: left;">
                        <button  id="add-factor" type="button" onclick="ShowDiv('addUser_box','mask')"
                                 class="btn btn-outline btn-success">添加员工
                        </button>
                    </div>
                    @endpermission
                    @endif
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        {{--<a class="close-link">--}}
                        {{--<i class="fa fa-times"></i>--}}
                        {{--</a>--}}
                    </div>
                </div>
                <div class="ibox-content">

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th >名称</th>
                            <th >联系电话</th>
                            <th >邮箱</th>
                            <th >创建时间</th>
                            <th >操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($merchantInfo)&&!$merchantInfo->isEmpty())
                            @foreach($merchantInfo as $v )
                                <tr class="gradeA">
                                    <td>{{$v->name}}</td>
                                    <td>{{$v->phone}}</td>
                                    <td>{{$v->email}}</td>
                                    <td>{{$v->created_at}}</td>
                                    <td class="center">
                                        @if($v->id!=Auth::guard('merchant')->user()->id)
                                        @if($pid=='0')
                                        @permission("assignCommunity")
                                        <button type="button" onclick="ShowDiv('assign_community','mask');getCommunity({{$v->id}})"
                                                class="btn jurisdiction btn-outline btn-success">分配小区
                                        </button>
                                        @endpermission
                                        @endif
                                        @if($v->pid!=0&&Auth::guard('merchant')->user()->pid==0)
                                        <button type="button" onclick="ShowDiv('agentSetRole_box','mask');getRoles({{$v->id}});setrole({{$v->id}})"
                                                class="btn jurisdiction btn-outline btn-primary">分配角色
                                        </button>
                                        @endif
                                        @endif
                                        @if($v->id==Auth::guard('merchant')->user()->pid||$v->id==Auth::guard('merchant')->user()->id||Auth::guard('merchant')->user()->pid==0)
                                        <button type="button" onclick="ShowDiv('editUser_box','mask');getMerchantInfo('{{$v->id}}')"
                                                class="btn jurisdiction btn-outline btn-warning">修改信息
                                        </button>
                                        @endif
                                        @if($v->id!=Auth::guard('merchant')->user()->id)
                                        @if($v->pid!=0&&Auth::guard('merchant')->user()->pid==0)
                                            <button type="button" onclick='delMerchant("{{$v->id}}")'
                                                    class="btn btn-outline btn-danger">删除
                                            </button>
                                        @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>

                        <tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8"></div>
        <div class="col-sm-4">
            <div class="dataTables_paginate paging_simple_numbers"
                 id="DataTables_Table_0_paginate">
                @if(isset($merchantInfo)&&!$merchantInfo->isEmpty())
                    {{$merchantInfo->render()}}
                @endif
            </div>
        </div>
    </div>
    {{--添加员工--}}
    <div id="addUser_box" class="ant-modal" style="width: 800px; transform-origin: 1054px 10px 0px;display: none;">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('addUser_box','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >添加员工</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >姓名</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="username" name="username" placeholder="请输入姓名" class="input ant-input ant-input-lg" required>
                                <span id="nameerror" style="color: red;font-size: 12px;display: none">请输入姓名</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >手机号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="phone" name="phone" placeholder="请输入手机号" class="input ant-input ant-input-lg" required>
                                <span id="phoneerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >邮箱</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="email" name="email" placeholder="请输入邮箱" class="input ant-input ant-input-lg" required>
                                <span id="emailerror" style="color: red;font-size: 12px;display: none">请输入邮箱</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item" id="setagentrole">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >系统角色</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="" id="role" class="select form-control" style="border-radius: 4px;" required>
                                    <option value="">请选择系统角色</option>
                                </select>
                                <span class="role_span" style="color: red;font-size: 12px; display:none;">请选择系统角色</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >初始密码</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="password" id="password" name="password" placeholder="请输入初始密码" class="input ant-input ant-input-lg" required>
                                <span id="passworderror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >确认密码</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="请再次输入密码" class="input ant-input ant-input-lg" required>
                                <span style="color: red;font-size: 12px;display: none">请再次输入密码</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="addUser"><span>确定添加</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--分配小区--}}
    <div id="assign_community" class="ant-modal" style="width:800px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button  class="ant-modal-close"  onclick="CloseDiv('assign_community','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >小区列表</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal" id="community_form">
                    <input type="hidden" id="assign_merchant_id" value="">
                    <div class="ant-row ant-form-item">
                        <div id="ibox-community"  class="" style="padding-left: 30px;">
                        </div>

                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="assign_community_submit"><span>分配小区</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--角色分配--}}
    <div id="agentSetRole_box" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button  class="ant-modal-close"  onclick="CloseDiv('agentSetRole_box','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" id="role_title">分配角色</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" id="roleagentid">
                    <div class="ant-row ant-form-item">
                        <div id="ibox-content"  class="" style="padding-left: 30px;">
                            <label class="col-sm-3 control-label">选择角色</label>
                            <div class="col-sm-6">
                                <select class="form-control m-b" id="selectrole">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" onclick="dosetrole()"><span>确定</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--修改信息--}}
    <div id="editUser_box" class="ant-modal" style="width: 800px; transform-origin: 1054px 10px 0px;display: none;">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('addUser_box','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >修改员工信息</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" id="edit-merchant_id" value="">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >姓名</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" value="" id="edit-username" name="username" placeholder="请输入姓名" class="input ant-input ant-input-lg" required>
                                <span id="edit-nameerror" style="color: red;font-size: 12px;display: none">请输入姓名</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >手机号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" value="" id="edit-phone" name="phone" placeholder="请输入手机号" class="input ant-input ant-input-lg" required>
                                <span id="edit-phoneerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >邮箱</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text"value="" id="edit-email" name="email" placeholder="请输入邮箱" class="input ant-input ant-input-lg" required>
                                <span id="edit-emailerror" style="color: red;font-size: 12px;display: none">请输入邮箱</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >初始密码</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="password" value="" id="edit-password" name="password" placeholder="请输入初始密码" class="input ant-input ant-input-lg" required>
                                <span id="edit-passworderror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >确认密码</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="password" id="edit-confirm_password" name="confirm_password" placeholder="请再次输入密码" class="input ant-input ant-input-lg" required>
                                <span style="color: red;font-size: 12px;display: none">请再次输入密码</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="editUser"><span>确定修改</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
    @section('js')
    <script>
        $(document).ready(function () {
            render();
            function render() {
                $('body').css('overflow-y','scroll');
            }
        });
        $('#add-factor').click(function () {
            /*添加用户时获取角色*/
            isroot="{{Auth::guard('merchant')->user()->hasRole('root'.Auth::guard('merchant')->user()->id)}}";
            if(isroot){
                getRoles();
            }else{
                $('#setagentrole').css('display','none');
            }
        });
        var role_id=0;
        $("#role").change(function(){
            role_id=($(this).find("option:selected").val());
        });
        $('#addUser').click(function () {
            $.post("{{url('merchant/addmerchant')}}", {_token: "{{csrf_token()}}",
                    name: $('#username').val().trim(),
                    phone: $('#phone').val().trim(),
                    password: $('#password').val().trim(),
                    password_confirmation: $('#confirm_password').val().trim(),
                    email: $('#email').val().trim(),
                    role_id :role_id
                },
                function (data) {
                    if (data.success==1) {
                        layer.msg(data.msg,{time:500});
                        setTimeout(function(){window.location.reload()},500);
                    }else if(data.success==2){
                        var errors=data.msg;
                        var msgs='';
                        console.log(errors);
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
                        layer.msg(data.msg,{time:2000});

                    }
                }, 'json');
        });
        function getRoles(id) {
            //获取角色
            $.post("{{url('merchant/getrole')}}", {_token: "{{csrf_token()}}",id:id},
                function (data) {
                    if (data.success) {
                        var roles=data.data;
                        for(var i=0;i<roles.length;i++){
                            var display_name=' <option class="role_id"  value='+ roles[i].id + '>'+roles[i].name+'</option>'
                            $(' #role').append(display_name);
                            $(' #agentSetRole_box').find('select').append(display_name);
                            if(data.id&&data.id==roles[i].id){
                                $("#role_title").text("当前角色:"+roles[i].name);
                            }
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');
        }
        function getCommunity(id){
            //获取小区信息
             $("#assign_merchant_id").val(id);
            $.post("{{url('merchant/getcommunities')}}", {_token: "{{csrf_token()}}",},
                function (data) {
                var community=[];
                    if (data.success) {
                        community=data.data;
                        for(var i=0;i<community.length;i++){
                            var display_name= '<div class="col-sm-3"> <input type="checkbox"  name="communityCheckbox[]" class="checkbox" value="'+community[i].id+'"/> <span class="two-name" >'+community[i].community_name+'</span> </div>';
                            var merchant_id="{{Auth::guard('merchant')->user()->id}}";
                            if(id==community[i].merchant_id){
                                var display_name= '<div class="col-sm-3"> <input type="checkbox"  checked name="communityCheckbox[]" class="checkbox" value="'+community[i].id+'"/> <span class="two-name" >'+community[i].community_name+'</span> </div>';
                            }
                            $(' #ibox-community').append(display_name);
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');
        }
        //分配小区
        $('#assign_community_submit').click(function () {
            var iDarr = [];
            $("#community_form").find("input[type='checkbox']").each(function(i,e){
                if($(e).is(":checked")){
                    iDarr.push($(e).val())
                }
            });
            $.post("{{url('merchant/assigncommunity')}}", {_token: "{{csrf_token()}}",
                    merchant_id: $('#assign_merchant_id').val(),
                    community_id:iDarr
                },
                function (data) {
                    if (data.success==1) {
                        layer.msg(data.msg,{time:500});
                        setTimeout(function(){window.location.reload()},500);
                    }else{
                        layer.msg(data.msg,{time:2000});

                    }
                }, 'json');
        });
        //分配角色保存字段
        function setrole(agentid) {
            $('#roleagentid').val(agentid);
        }
        //分配角色
        function dosetrole() {
            $.post("{{url('merchant/setrole')}}", {_token: "{{csrf_token()}}",
                    agent_id: $('#roleagentid').val(),
                    role_id: $('#selectrole option:selected').val()
                },
                function (data) {
                    if (data.success) {
                        layer.msg(data.msg,{time:500});
                        setTimeout(function(){window.location.reload()},500);
                    } else {
                        layer.msg(data.msg,{time:2000});

                    }
                }, 'json');
        }
        //获取要修改的员工信息
        function getMerchantInfo(id){
            //获取角色
            $.post("{{url('merchant/getmerchantinfo')}}", {_token: "{{csrf_token()}}",id:id},
                function (data) {
                    if (data.success) {
                        var merchant=data.data;
                        $('#edit-username').val(merchant.name);
                        $('#edit-email').val(merchant.email);
                        $('#edit-phone').val(merchant.phone);
                        $("#edit-merchant_id").val(merchant.id);
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');
        }
        //执行修改员工信息
        $("#editUser").click(function () {
                id= $("#edit-merchant_id").val();
                name=$('#edit-username').val();
                email=$('#edit-email').val();
                phone=$('#edit-phone').val();
                password=$('#edit-password').val();
                password_confirmation=$('#edit-confirm_password').val();
                $.post("{{url('merchant/updatemerchantinfo')}}", {_token: "{{csrf_token()}}", id: id ,
                        name:name,email:email,phone:phone,password:password,
                        password_confirmation:password_confirmation},
                    function (data) {
                        if (data.success==1) {
                            layer.msg(data.msg,{time:500});
                            setTimeout(function(){window.location.reload()},500);
                        } else if(data.success==2){
                            var errors=data.msg;
                            var msgs='';
                            for( var error in errors){
                                var  obj=$('#'+'edit-'+error+'error');
                                obj.css('display','block');
                                for(var msg in errors[""+error]){
                                    msgs+=errors[""+error][""+msg];
                                }
                                obj.text(msgs);
                                msgs='';
                            }
                        } else {
                            layer.msg(data.msg,{time:2000});

                        }
                    }, 'json');

            });
        //删除员工
        //删除小区
        function delMerchant(id) {
            layer.confirm('确定删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/delmerchant')}}", {_token: "{{csrf_token()}}", id: id },
                    function (data) {
                        if (data.success) {
                            layer.msg(data.msg,{time:500});
                            setTimeout(function(){window.location.reload()},500);
                        } else {
                            layer.msg(data.msg,{time:2000});

                        }
                    }, 'json');
            }, function () {

            });
        }
        function ShowDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='block';
            document.getElementById(bg_div).style.display='block' ;
            var bgdiv = document.getElementById(bg_div);
            bgdiv.style.width = document.body.scrollWidth;
            $("#"+bg_div).height($(document).height());

        }
        //关闭弹出层
        function CloseDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='none';
            document.getElementById(bg_div).style.display='none';
            window.location.reload();

        }

        $('#addrole_sure').click(function () {
            $(".input").each(function () {
                var val = $(this).val();
                if (val == "") {
                    $(this).focus().css({
                        "border": "1px solid red"
                    });
                    $(this).next().show()

                }

            });
        });



        $(".input").each(function() {
            $(this).click(function () {
                $(this).css({"border": "1px solid #d9d9d9"});
                $(this).next().hide()
            });
        });
    </script>
@endsection

