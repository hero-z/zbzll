@extends("layouts.merchantcontent")
@section("title",'住户信息管理')
@section('css')
    <link href="{{asset('/adminui/css/chosen.css')}}" rel="stylesheet">
@endsection
@section("content")

    {{--遮罩层--}}
    <div id="mask" class="mask" style="height: 200%"></div>
    <div class="col-sm-12" style="margin-bottom: -20px">
        <div class="ibox ">
            <form action="{{url('merchant/householdinfo')}}" method="post" style="position: relative;margin-top: 20px;">
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

                                <input class="sky form-control ant-input ant-input-lg" name="name" value="@if(isset($name)&&$name){{$name}}@endif"   type="text" style="width: 200px;margin-left: 10px; float: right;border-radius: 4px" placeholder="请输入户主名称或者房间号">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" id="'submit" class="btn btn-outline btn-primary" style="margin-left: 10px">筛选</button>
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
                            <th >楼宇单元</th>
                            <th >房号</th>
                            <th >户主</th>
                            <th >联系方式</th>
                            <th >备注</th>
                            <th >创建时间</th>
                            <th >操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($household)&&!$household->isEmpty())
                            @foreach($household as $k=>$v )
                                <tr class="gradeA">
                                    <td>
                                        @if(array_key_exists($v->out_room_id,$community))
                                        {{$community[$v->out_room_id]}}
                                        @endif
                                    </td>
                                    <td>{{$v->address}}</td>
                                    <td>{{$v->room}}</td>
                                    <td>{{$v->name}}</td>
                                    <td>{{$v->phone}}</td>
                                    <td>{{$v->remark}}</td>
                                    <td>{{$v->created_at}}</td>
                                    <td class="center">
                                        <button type="button" onclick='ShowDiv("household_edit","mask");gethousehold("{{$v->id}}")'
                                                class="btn jurisdiction btn-outline btn-success">编辑
                                        </button>
                                        <button type="button" onclick='ShowDiv("house_edit","mask");gethouse("{{$v->out_room_id}}")'
                                                class="btn jurisdiction btn-outline btn-primary">住户信息
                                        </button>
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
                @if(isset($household)&&!$household->isEmpty())
                    {{$household->appends(compact('out_community_id','name'))->render()}}
                @endif
            </div>
        </div>
    </div>

    {{--编辑户主--}}
    <div id="household_edit" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('household_edit','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">住户信息</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">

                    <input type="hidden" id="edithouseholdid">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">户主</label>
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
                            <label class="ant-form-item-required"> 	联系方式</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_phone" name="amend_phone"
                                       class="ant-input ant-input-lg ">
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">备注</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_remark" name="amend_remark"
                                       class="ant-input ant-input-lg unnecessary">
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
    {{--住户信息--}}
    <div id="house_edit" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('house_edit','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">
                    户 主 :
                    <span id="householdname"></span>
                </div>
                <button  type="button" class="add_btn ant-btn ant-btn-ghost l-saas-buildingunit-isunit" style="float: right; margin-right: 70px; margin-top: -22px;">
                    <span>添加住户</span>
                </button>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">

                    <input type="hidden" id="editoutroomid">


                    {{--添加楼宇里面的列表--}}
                    <div class="ant-table-wrapper l-saas-buildingunit-isunit" style="">
                        <div class="ant-spin-nested-loading">
                            <div class="ant-spin-container">
                                <div class="ant-table ant-table-middle ant-table-bordered ant-table-empty ant-table-scroll-position-left">
                                    <div class="ant-table-content">
                                        <div class="ant-table-body">
                                            <table class="add_table" style="">
                                                <colgroup>
                                                    <col style="width: 80px; min-width: 80px;">
                                                    <col style="width: 100px; min-width: 100px;">
                                                    <col style="width: 120px; min-width: 120px;">
                                                    <col style="width: 120px; min-width: 120px;">
                                                    <col style="width: 60px; min-width: 60px;">
                                                </colgroup>
                                                <thead class="ant-table-thead">
                                                <tr>
                                                    <th class="">身份</th>
                                                    <th class="">名称</th>
                                                    <th class="">联系方式</th>
                                                    <th class="">备注</th>
                                                    <th class="">操作</th>
                                                </tr>
                                                </thead>
                                                <tbody class="ant-table-tbody">
                                                <tr style="display: none;" class="have">
                                                    <td class="sf">
                                                        {{--<input type="hidden" name="holdtype" value="">--}}
                                                    </td>
                                                    <td><input type="text" class="unit_name ant-input ant-input-lg"/></td>
                                                    <td><input type="text" class="unit_phone ant-input ant-input-lg"/></td>
                                                    <td><input type="text" class="unit_remark ant-input ant-input-lg unnecessary"/></td>
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
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="edit_house"><span>确定修改</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
        //获取住户信息
        function gethousehold(id) {
            $.post("{{url('merchant/gethousehold')}}", {
                    _token: "{{csrf_token()}}",
                    hid: id
                },
                function (data) {
                    if (data.success) {
                        var household=data.data;
                        if(household){
                            $('#edithouseholdid').val(household.id);
                            $('#amend_name').val(household.name);
                            $('#amend_phone').val(household.phone);
                            $('#amend_remark').val(household.remark);
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');
        }
        //编辑
        $('#amend_sure').click(function () {
            var obj=$("#household_edit");
            var ck=true;
            obj.find('input').not('.unnecessary ').each(function () {
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
                edithouseholdid= $("#edithouseholdid").val();
                $.post("{{url('merchant/edithousehold')}}", {
                        _token: "{{csrf_token()}}",
                        hid: edithouseholdid,
                        name : $('#amend_name').val(),
                        phone: $('#amend_phone').val(),
                        remark: $('#amend_remark').val()
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
        //获取住户信息
        function gethouse(out_room_id){
            $.post("{{url('merchant/gethouse')}}", {
                    _token: "{{csrf_token()}}",
                    out_room_id: out_room_id
                },
                function (data) {
                    if (data.success) {
                        var house=data.data;
                        if(house.length){
                            var eobj=$('#house_edit');
                            $.each(house, function(i,v) {
                                editId=v.id;
                                var htmlStr = eobj.find(".have:first").clone();
                                htmlStr.show();
                                if(v.type==1){
                                    eobj.find(".table_sky").show();
                                    $('#editoutroomid').val(v.out_room_id);
                                    $('#householdname').text(v.name);
                                }else{
                                    eobj.find(".table_sky").hide();
                                    htmlStr.find(".sf").text(v.type==2?'家人':'租客');
                                    htmlStr.find(".unit_name").val(v.name);
                                    htmlStr.find(".unit_phone").val(v.phone);
                                    htmlStr.find(".unit_remark").val(v.remark);
                                    htmlStr.find(".unit_name").attr('id',editId);
                                    eobj.find(".add_table").append(htmlStr);
                                }
                            });
                            //删除住户
                            eobj.find('.delete').click(function () {
                                delid=$(this).prev().prev().prev().children('input').attr('id');
                                console.log(delid);
                                $.post("{{url('merchant/deletehouse')}}", {
                                        _token: "{{csrf_token()}}",
                                        houseid:delid
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
                        }
//                        layer.msg(data.msg,{time:500});
//                        setTimeout(function(){window.location.reload()},500);
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');
        }
        //添加住户
        $(".add_btn").click(function () {
            layer.confirm(
                "请选择住户的身份.",
                {
                    btn: ['家人', '租客'] ,//按钮
                    title:'确认住户类型',
                    btn1: function () {
                        $(".table_sky").hide();
                        var htmlStr = $(".have:first").clone();
                        htmlStr.show();
                        htmlStr.addClass("have-item");
                        htmlStr.find(".sf").text('家人');
//                        htmlStr.find(".holdtype").val(1);
                        $(".add_table").append(htmlStr);
                    },
                    btn2:function () {
                        $(".table_sky").hide();
                        var htmlStr = $(".have:first").clone();
                        htmlStr.show();
                        htmlStr.addClass("have-item");
//                        htmlStr.find(".holdtype").val(2);
                        htmlStr.find(".sf").text('租客');
                        $(".add_table").append(htmlStr);
                    }
                }
            )
        });
        //编辑住户
        $('#edit_house').click(function () {
            var obj=$("#house_edit");
            var ck=true;
            obj.find('input').not('.unnecessary ').each(function (i,v) {
                console.log(this);
                if(i>2){
                    var val = $(this).val();
                    console.log(val);
                    if (val == "") {
                        $(this).focus().css({
                            "border": "1px solid red"
                        });
                        $(this).next().show();
                        layer.msg('请完善资料',{time:1000});
                        ck= false;
                    }
                }
            });
            if(ck){
                var unitArr=[];
                var upunitArr=[];
                obj.find('.have').not(':first').each(function(i,e){
                    var sf=$(this).children('td:first').text();
                    var inputs=$(this).find('input');
                    if($(inputs[0]).val()!==''){
                        var id=$(inputs[0]).attr('id');
                        if(id){
                            var arr=[];
                            arr.push(id);
                            arr.push($(inputs[0]).val());
                            arr.push($(inputs[1]).val());
                            arr.push($(inputs[2]).val());
                            upunitArr.push(arr);
                        }else{
                            var  arr2=[];
                            arr2.push($(inputs[0]).val());
                            arr2.push($(inputs[1]).val());
                            arr2.push($(inputs[2]).val());
                            arr2.push(sf=='家人'?2:3);
                            arr2.push($('#editoutroomid').val());
                            unitArr.push(arr2);
                        }
                    }
                });
                console.log(unitArr);
                console.log(upunitArr);
                $.post("{{url('merchant/edithouses')}}", {
                        _token: "{{csrf_token()}}",
                        holds:upunitArr,
                        holds_n:unitArr
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
            $('#house_edit ').find('input').val('');
            $('#house_edit ').find('.have').not(":first").remove();
            $('#house_edit ').find('.table_sky').show();

//            window.location.reload();
        }

        function CloseModel() {
            $('#model_box').hide()
        }

    </script>
@endsection

