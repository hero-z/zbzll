@extends("layouts.admincontentpublic")
@section("title","角色管理")
@section("content")

{{--遮罩层--}}
<div id="mask" class="mask"></div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div style="display: block;float: left;">
                    <button  id="add-factor" type="button" onclick="ShowDiv('add_role','mask')"
                             class="btn btn-outline btn-success">添加角色
                    </button>
                </div>
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
                        <th >角色名称</th>
                        <th >角色说明</th>
                        <th >角色描述</th>
                        <th >创建时间</th>
                        <th >操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($roles)&&!$roles->isEmpty())
                        @foreach($roles as $v )
                            <tr class="gradeA">
                                <td>{{$v->name}}</td>
                                <td>{{$v->display_name}}</td>
                                <td>{{$v->description}}</td>
                                <td>{{$v->created_at}}</td>
                                <td class="center">
                                    <button type="button" onclick='getpermission("{{$v->id}}")'
                                            class="btn jurisdiction btn-outline btn-success">分配权限
                                    </button>
                                    @if($v->id!=1)
                                        <button type="button" onclick='del("{{$v->id}}")'
                                                class="btn btn-outline btn-danger">删除
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        没有数据
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
            @if(isset($roles)&&!$roles->isEmpty())
                {{$roles->render()}}
            @endif
        </div>
    </div>
</div>
<div id="add_role" class="ant-modal" style="width: 790px; transform-origin: 1054px 10px 0px;display: none">
    <div class="ant-modal-content">
        <button  class="ant-modal-close"  onclick="CloseDiv('add_role','mask')">
            <span class="ant-modal-close-x"></span>
        </button>
        <div class="ant-modal-header">
            <div class="ant-modal-title" >添加角色</div>
        </div>
        <div class="ant-modal-body">
            <form class="ant-form ant-form-horizontal">

                <div class="ant-row ant-form-item">
                    <div class="ant-col-6 ant-form-item-label">
                        <label  class="ant-form-item-required" >角色名称</label>
                    </div>
                    <div class="ant-col-16 ant-form-item-control-wrapper">
                        <div class="ant-form-item-control ">
                            <input type="text" id="name" name="name" placeholder="请输入角色名称" class="input ant-input ant-input-lg" required>
                            <span style="color: red;font-size: 12px;display: none">请输入角色名称</span>

                        </div>
                    </div>
                </div>
                <div class="ant-row ant-form-item">
                    <div class="ant-col-6 ant-form-item-label">
                        <label  class="ant-form-item-required" >显示名称</label>
                    </div>
                    <div class="ant-col-16 ant-form-item-control-wrapper">
                        <div class="ant-form-item-control ">
                            <input type="text" id="display_name" name="display_name" placeholder="请输入显示名称" class="input ant-input ant-input-lg" required>
                            <span style="color: red;font-size: 12px;display: none">请输入显示名称</span>
                        </div>
                    </div>
                </div>
                <div class="ant-row ant-form-item">
                    <div class="ant-col-6 ant-form-item-label">
                        <label  class="ant-form-item-required" >角色描述</label>
                    </div>
                    <div class="ant-col-16 ant-form-item-control-wrapper">
                        <div class="ant-form-item-control ">
                            <input type="text" id="description" name="description" placeholder="请输入角色描述" class=" ant-input ant-input-lg" required>
                        </div>
                    </div>
                </div>


                <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                    <div class="ant-col-22 ant-form-item-control-wrapper">
                        <div class="ant-form-item-control ">
                            <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" onclick="addrole()" id="addrole_sure"><span>确定添加</span></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{--权限分配--}}
<div id="jurisdiction_box" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
    <div class="ant-modal-content">
        <button  class="ant-modal-close"  onclick="CloseDiv('jurisdiction_box','mask')">
            <span class="ant-modal-close-x"></span>
        </button>
        <div class="ant-modal-header">
            <div class="ant-modal-title" >权限列表</div>
        </div>
        <div class="ant-modal-body">
            <form class="ant-form ant-form-horizontal">

                <div class="ant-row ant-form-item">
                    <div id="ibox-content"  class="" style="padding-left: 30px;">
                        <div class="box" style="display: none;">
                            <div class="one">
                                <input type="checkbox" name="" id="" value="" style="display: none"/>
                                <div class="add"></div>
                                <span class="one-name"></span>
                            </div>
                        </div>
                        <div class="two" style="display: none;">
                            <input type="checkbox"  name="" class="checkbox" value=""/>
                            <span class="two-name" data-id=' ' ></span>
                        </div>

                    </div>
                    <div class="two" style="display: none;">
                        <input type="checkbox"  name="" class="checkbox" value=""/>
                        <span class="two-name" data-id=' ' ></span>
                    </div>

                </div>

                <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                    <div class="ant-col-22 ant-form-item-control-wrapper">
                        <div class="ant-form-item-control ">
                            <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="sure_submit"><span>确定提交</span></button>
                        </div>
                    </div>
                </div>
            </form>
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
        $(function () {
            //获取权限列表
            $('.jurisdiction').click(function () {
                ShowDiv('jurisdiction_box','mask')
            })
        });
        function del(id) {
            layer.confirm('确定删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('admin/delrole')}}", {_token: "{{csrf_token()}}", id: id },
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
        function addrole() {
            $.post("{{url('admin/addrole')}}", {_token: "{{csrf_token()}}",
                    name: $('#name').val(),
                    display_name: $('#display_name').val(),
                    description: $('#description').val()
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
        var permission_idArr = [];
        var oneArr=[];
        var twoArr=[];
        function getpermission(id) {
            $.post("{{url('admin/getpermission')}}", {_token: "{{csrf_token()}}",
                    id: id
                },
                function (data) {
                    if (data.success) {
                        var permissions=data.permissions;
                        var rolepermission=data.rolepermission;
                        //权限分层
                        $.each(permissions,function(i,v){
                            console.log(v);
                            if(v.pid == 0){
                                oneArr.push(v);
                            }else{
                                twoArr.push(v);
                            }
                        });
                        //取出角色的权限id
                        for(var i =0;i < rolepermission.length;i++){
                            permission_idArr.push(rolepermission[i].permission_id)
                        }
                        console.log(permission_idArr);
                        console.log(oneArr);
                        console.log(twoArr);
                        //获得数据后开始渲染视图
                        $.each(oneArr, function(i,v) {
                            var htmlStr = $(".box:first").clone();
                            htmlStr.show();
                            htmlStr.find(".one").attr("data-id",v.id);
                            htmlStr.find(".one-name").text( v.display_name);
                            $.each(twoArr, function(index,value) {
//                            			console.log(v.pid)
//                             			console.log(value.id)
                                if(v.id == value.pid){
                                    var htmlStrTwo = $(".two:first").clone();
                                    htmlStrTwo.attr("data-id",value.id);
                                    htmlStrTwo.find(".two-name").text(value.display_name);
                                    htmlStr.append(htmlStrTwo);
                                    $.each(permission_idArr, function(i1,v1) {
                                        if(value.id == v1){
                                            htmlStrTwo.find("input").prop("checked",true);
                                        }
                                    });
                                }
                            });
                            $("#ibox-content").append(htmlStr)
                        });
                        //默认是收起来的 点击名称可以下拉
                        $(".one").click(function(){
                            $(this).nextAll(".two").toggle();
                        });

                    } else {
                        layer.msg(data.msg);
                    }
                }, 'json');

            //角色权限分配
            $('#sure_submit').click(function () {
                var iDarr = [];
                $(".box").find("input[type='checkbox']").each(function(i,e){
                    if($(e).is(":checked")){
                        iDarr.push($(e).parent().attr("data-id"))
                    }
                });
                $.post("{{url('admin/setrolepermission')}}", {_token: "{{csrf_token()}}",
                        role_id: id ,
                        id:iDarr
                    },
                    function (data) {
                        if (data.success) {
                            layer.msg(data.msg,{time:800});
//                        CloseDiv('jurisdiction_box','mask');
                            setTimeout(function(){window.location.reload()},800);
                        } else {
                            layer.msg(data.msg,{time:2500});
                        }
                    }, 'json');
            });
        }

        function CloseModel() {
            $('#model_box').hide()
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
            window.location.reload()

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

