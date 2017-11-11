@extends("layouts.merchantcontent")
@section("title","楼宇管理")
@section('css')
    <link href="{{asset('/adminui/css/chosen.css')}}" rel="stylesheet">
@endsection
@section("content")

    {{--遮罩层--}}
    <div id="mask" class="mask" style="height: 200%"></div>
    <div class="col-sm-12" style="margin-bottom: -20px">
        <div class="ibox ">
            <form action="{{url('merchant/buildinginfo')}}" method="post" style="position: relative;margin-top: 20px;">
                {{csrf_field()}}
                <div class="form-group" style="float: left">
                    <div class="input-group">
                        <div class="form-group" style="float: left">
                            <div class="input-group">
                                <select name="out_community_id" id="out_community_id" style="width:250px;">
                                    <option value="" >请选择小区名称</option>
                                    @if($communityInfo)
                                    @foreach($communityInfo as $v)
                                    <option value="{{$v->out_community_id}}"  @if(isset($out_community_id)&&$out_community_id==$v->out_community_id) selected @endif >{{$v->community_name}}</option>
                                    @endforeach
                                    @endif
                                </select>

                                <input class="sky form-control ant-input ant-input-lg" name="building_name" value="@if(isset($building_name)&&$building_name){{$building_name}}@endif"  id="building_name" type="text" style="width: 200px;margin-left: 10px; float: right;border-radius: 4px" placeholder="请输入楼栋名称">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" id="'submit" class="btn btn-outline btn-primary" style="margin-left: 10px">筛选</button>
                @permission('addBuilding')
                <button type="button" class="btn btn-outline btn-warning"   onclick="ShowDiv('add_building','mask')" style="float: right">添加楼宇</button>
                @endpermission
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th >所属小区</th>
                            <th >楼宇名称</th>
                            <th >楼宇层数</th>
                            <th >楼宇类型</th>
                            <th>描述</th>
                            <th >创建时间</th>
                            <th >操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($buildingInfo)&&!$buildingInfo->isEmpty())
                            @foreach($buildingInfo as $v )
                                <tr class="gradeA">
                                    <td>{{$v->community_name}}</td>
                                    <td>{{$v->building_name}}</td>
                                    <td>{{$v->level}}</td>
                                    <td>{{$v->type}}</td>
                                    <td>{{$v->description}}</td>
                                    <td>{{$v->created_at}}</td>
                                    <td class="center">
                                        <button type="button" onclick='ShowDiv("building_amend","mask");getbuilding("{{$v->id}}")'
                                                class="btn jurisdiction btn-outline btn-success">编辑
                                        </button>
                                        @permission('deleteBuilding')
                                        @if($v->alipay_status=='NONE'&&$v->basicservice_status=='NONE')
                                            <button type="button" onclick='del("{{$v->id}}")'
                                                    class="btn btn-outline btn-danger">删除
                                            </button>
                                        @endif
                                        @endpermission
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
                @if(isset($buildingInfo)&&!$buildingInfo->isEmpty())
                    {{$buildingInfo->appends(compact('building_name','out_community_id'))->render()}}
                @endif
            </div>
        </div>
    </div>
    {{--添加楼宇--}}
    <div id="add_building" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('add_building','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">添加楼宇</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">楼栋名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="out_id" id="out_id" style="width:250px;border: 1px solid red;"  required="required" class="select ">
                                    <option value="" class="sky ant-input ant-input-lg">请选择小区名称</option>
                                    @if($communityInfo)
                                        @foreach($communityInfo as $v)
                                            <option value="{{$v->out_community_id}}" >{{$v->community_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span  style="color:red;font-size:12px;display: none" class="span">请选择小区名称</span>

                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">楼栋名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="building_n" name="building_n" placeholder="如:一号楼"
                                       class="input sky ant-input ant-input-lg"  required="required" >
                                <span style="color:red;font-size:12px;display: none" class="span">请输入楼宇名称</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">楼栋层数</label>
                        </div>
                        <div class="ant-col-15 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="building_level" name="building_level" placeholder="如:20"
                                       class="input ant-input ant-input-lg " required="required">
                                <span style="color:red;font-size:12px;display: none" class="span">请输入楼栋层数</span>

                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">楼栋类型</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="building_type" name="building_type" placeholder="如:高层"
                                       class="input ant-input ant-input-lg" required="required">
                                <span style="color:red;font-size:12px;display: none" class="span">请输入楼栋类型</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">楼栋描述</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="building_description" name="building_description"
                                       placeholder="请输入楼栋描述" class="ant-input ant-input-lg">
                            </div>
                        </div>
                    </div>
                    {{--多选框--}}
                    <label class="ant-checkbox-wrapper" style="padding-left: 24px;">
                        <span class="ant-checkbox ant-checkbox-checked">
                            <input type="checkbox" class=" checkbox">
                        </span>
                        <span>是否多单元</span>
                    </label>
                    {{--添加按钮--}}
                    <button  type="button" class="add_btn ant-btn ant-btn-ghost l-saas-buildingunit-isunit" style="float: right; margin-right: 10px; margin-top: -5px;display: none">
                        <span>添 加</span>
                    </button>
                    {{--添加楼宇里面的列表--}}
                    <div class="ant-table-wrapper l-saas-buildingunit-isunit" style="margin-top: 15px;">
                        <div class="ant-spin-nested-loading">
                            <div class="ant-spin-container">
                                <div class="ant-table ant-table-middle ant-table-bordered ant-table-empty ant-table-scroll-position-left">
                                    <div class="ant-table-content">
                                        <div class="ant-table-body">
                                            <table class="add_table" style="display: none;">
                                                <colgroup>
                                                    <col style="width: 80px; min-width: 80px;">
                                                    <col style="width: 250px; min-width: 250px;">
                                                    <col style="width: 60px; min-width: 60px;">
                                                </colgroup>
                                                <thead class="ant-table-thead">
                                                <tr>
                                                    <th class="">序号</th>
                                                    <th class="">名称</th>
                                                    <th class="">操作</th>
                                                </tr>
                                                </thead>
                                                <tbody class="ant-table-tbody">
                                                <tr style="display: none;" class="have">
                                                    <td class="order">0</td>
                                                    <td>
                                                        <input type="text"  value="" class="unit_name ant-input ant-input-lg" placeholder="如:几单元"/></td>
                                                    <td style="color: #1ab394;" class="delete">删除</td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ant-table-placeholder table_sky" style="display: none">
                                            <span><i class="anticon anticon-frown-o"></i>暂无数据</span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="addbui_sure"><span>确定添加</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--编辑楼宇--}}
    <div id="building_amend" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('building_amend','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">楼栋管理</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">

                    <input type="hidden" id="editbid">
                    <input type="hidden" id="editout_community_id">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">楼栋名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_name" name="amend_name"
                                       class="ant-input ant-input-lg" required="required">
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">楼栋层数</label>
                        </div>
                        <div class="ant-col-15 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_level" name="amend_level"
                                       class="ant-input ant-input-lg ">
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">楼栋类型</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_type" name="amend_type"
                                       class="ant-input ant-input-lg">
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">楼栋描述</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_description" name="amend_description" class="ant-input ant-input-lg unnecessary">
                            </div>
                        </div>
                    </div>

                    {{--多选框--}}
                    <label class="ant-checkbox-wrapper" style="padding-left: 24px;">
                        <span class="ant-checkbox ant-checkbox-checked">
                            <input type="checkbox" class=" checkbox">
                        </span>
                        <span>是否多单元</span>
                    </label>
                    {{--添加按钮--}}
                    <button  type="button" class="add_btn ant-btn ant-btn-ghost l-saas-buildingunit-isunit" style="float: right; margin-right: 10px; margin-top: -5px;display: none">
                        <span>添 加</span>
                    </button>


                    {{--添加楼宇里面的列表--}}
                    <div class="ant-table-wrapper l-saas-buildingunit-isunit" style="margin-top: 15px;">
                        <div class="ant-spin-nested-loading">
                            <div class="ant-spin-container">
                                <div class="ant-table ant-table-middle ant-table-bordered ant-table-empty ant-table-scroll-position-left">
                                    <div class="ant-table-content">
                                        <div class="ant-table-body">
                                            <table class="add_table" style="display: none;">
                                                <colgroup>
                                                    <col style="width: 80px; min-width: 80px;">
                                                    <col style="width: 250px; min-width: 250px;">
                                                    <col style="width: 60px; min-width: 60px;">
                                                </colgroup>
                                                <thead class="ant-table-thead">
                                                <tr>
                                                    <th class="">序号</th>
                                                    <th class="">名称</th>
                                                    <th class="">操作</th>
                                                </tr>
                                                </thead>
                                                <tbody class="ant-table-tbody">
                                                <tr style="display: none;" class="have">
                                                    <td class="order">0</td>
                                                    <td>
                                                        <input type="text" class="unit_name ant-input ant-input-lg"/></td>
                                                    <td class="delete" >删除</td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ant-table-placeholder table_sky" style="display: none">
                                            <span><i class="anticon anticon-frown-o"></i>暂无数据</span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="amend_sure"><span>确定修改</span>
                                </button>
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
    <script src="{{asset('/adminui/js/chosen.jquery.js')}}"></script>
    <script>
        $(document).ready(function () {
            render();
            function render() {
                $('#out_community_id').chosen();

            }
            $("body").css("overflow-y","scroll")
        });
    </script>
    <script>
        //多单元
        $(".checkbox").change(function () {
            var bol = $(this).is(":checked");
            if (bol) {
                $(".add_btn").show();
                $(".add_table").show();
                $(".table_sky").show();
                $(".have-item").remove();
            } else {
                $(".add_btn").hide();
                $(".add_table").hide();
                $(".table_sky").hide();

            }
        });
        //添加楼宇
        var  ck=true;
        $('#addbui_sure').click(function () {
            var obj=$("#add_building");
            obj.find('.select option:selected').each(function () {
                var select = $(this).text();
                if ( select == "请选择小区名称" ) {
                    $(this).parent().css({
                        "border": "1px solid red"
                    });
                    $(this).parent().next().show();
                    layer.msg('请选择小区',{time:1000});
                    ck= false;
                }
            });
            obj.find('.input').each(function () {
                var val = $(this).val();
                if (val == "") {
                    $(this).focus().css({
                        "border": "1px solid red"
                    });
                    $(this).next().show();
                    layer.msg('请完善资料',{time:1000});
                    ck= false;
                }
            });
            if(ck){
                var unitArr=[];
                $('.unit_name').each(function(i,e){
                    if($(e).val()!== ""){
                        unitArr.push($(e).val());
                    }

                });
                out_community_id= $("#out_id").val();
                $.post("{{url('merchant/createbuilding')}}", {
                        _token: "{{csrf_token()}}",
                        out_community_id: out_community_id,
                        building_name : $('#building_n').val(),
                        level: $('#building_level').val(),
                        type: $('#building_type').val(),
                        description:$('#building_description').val(),
                        unit_name:unitArr
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
            ck=true;

        });
        //编辑楼宇
        function getbuilding(bid) {
            $.post("{{url('merchant/getbuilding')}}", {
                    _token: "{{csrf_token()}}",
                    bid: bid
                },
                function (data) {
                    if (data.success) {
                        var building=data.building;
                        var units=data.units;
                        if(building){
                            $('#editbid').val(building.id);
                            $('#amend_name').val(building.building_name);
                            $('#amend_level').val(building.level);
                            $('#amend_type').val(building.type);
                            $('#amend_description').val(building.description);
                            $('#editout_community_id').val(building.out_community_id);
                        }
                        var eobj=$('#building_amend ');
                        eobj.find(".checkbox").attr("checked",'true');
                        eobj.find(".add_table").show();
                        eobj.find(".add_btn").show();
                        if(units.length){
                            eobj.find(".table_sky").remove();
                            $.each(units, function(i,v) {
                                editunitId=v.id;
                                var htmlStr = eobj.find(".have:first").clone();
                                htmlStr.show();
                                if(v.unit_name !== ''){
                                    htmlStr.find(".unit_name").val(v.unit_name);
                                    htmlStr.find(".unit_name").attr('id',editunitId);
                                    htmlStr.find(".order").text(Number($(".have:last").find(".order").text()) + 1);
                                    eobj.find(".add_table").append(htmlStr);
                                }
                            });
                            //删除单元
                            eobj.find('.delete').click(function () {
                                delunitid=$(this).prev().children('input').attr('id');
//                                console.log(delunitid);
                                $.post("{{url('merchant/deleteunit')}}", {
                                        _token: "{{csrf_token()}}",
                                        unitid:delunitid
                                    },
                                    function (data) {
                                        if (data.success) {
                                            layer.msg(data.msg,{time:500});
//                                            setTimeout(function(){window.location.reload()},500);
                                        } else {
                                            layer.msg(data.msg,{time:2000});
                                        }
                                    }, 'json');
                            });
                        }else{
                            eobj.find(".table_sky").show();
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');
        }
        //编辑
        $('#amend_sure').click(function () {
            var obj=$("#building_amend");
            obj.find('input').not('.unnecessary').not('.have ').not('.unit_name').not(' .checkbox').each(function () {
                var val = $(this).val();
                if (val == "") {
                    $(this).focus().css({
                        "border": "1px solid red"
                    });
                    $(this).next().show();
                    layer.msg('请完善资料',{time:1000});
                    ck= false;
                }
            });
            if(ck){
                var unitArr=[];
                var upunitArr=[];
                obj.find('.unit_name').each(function(i,e){
                    var thisv=$(e).val();
                    console.log(this);
                    if(thisv!== ""){
                        console.log(this.id);
                        if(this.id){
                            upunitArr.push(thisv+"***"+this.id);
                        }else{
                            unitArr.push(thisv);
                        }
                    }

                });
                console.log(upunitArr);
                console.log(unitArr);
                editbid= $("#editbid").val();
                $.post("{{url('merchant/editbuilding')}}", {
                        _token: "{{csrf_token()}}",
                        building_id: editbid,
                        out_community_id: $('#editout_community_id').val(),
                        building_name : $('#amend_name').val(),
                        level: $('#amend_level').val(),
                        type: $('#amend_type').val(),
                        description:$('#amend_description').val(),
                        units:upunitArr,
                        units_n:unitArr
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
            ck=true;
        });
        //添加单元
        $(".add_btn").click(function () {
            $(".table_sky").hide();
            var htmlStr = $(".have:first").clone();
            htmlStr.show();
            htmlStr.addClass("have-item");
//            console.log($(".have:last").text());
            htmlStr.find(".order").text(Number($(".have:last").find(".order").text()) + 1);
            $(".add_table").append(htmlStr);
        });
        //删除
        $("body").on("click", ".delete", function () {
            $(this).parents("tr").remove();
            $(".have").each(function (i, e) {
                $(e).find(".order").text(i);
            });
            if ($(".have:last").css("display") == "none") {
                $(".no").show();
            }
        });
        //删除楼宇
        function del(bid) {
            layer.confirm('确定删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/deletebuilding')}}", {_token: "{{csrf_token()}}", bid: bid },
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
        //弹出隐藏层
        function ShowDiv(show_div, bg_div) {
            document.getElementById(show_div).style.display = 'block';
            document.getElementById(bg_div).style.display = 'block';
            var bgdiv = document.getElementById(bg_div);
            bgdiv.style.width = document.body.scrollWidth;
            $("#" + bg_div).height($(document).height());
            $('#out_id').chosen();


        }

        //关闭弹出层
        function CloseDiv(show_div, bg_div) {
            document.getElementById(show_div).style.display = 'none';
            document.getElementById(bg_div).style.display = 'none';
            $('.input').css({'border':'1px solid #d9d9d9'});
            $('.input').next().hide();
            $('#building_amend ').find('input').val('');
            $('#building_amend ').find('.have').not(":first").remove();
            $('#building_amend ').find('.table_sky').show();

//            window.location.reload();
        }

        function CloseModel() {
            $('#model_box').hide()
        }

    </script>
@endsection

