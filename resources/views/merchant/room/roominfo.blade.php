@extends("layouts.merchantcontent")
@section("title","房屋管理")
@section('css')
    <link href="{{asset('/adminui/css/chosen.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/amazeui.chosen.css')}}" rel="stylesheet">
    <style>


        .ant-modal-close-x{
            background-color: #fff !important;
        }
        .ant-modal-close-x:hover{
            background-color: #fff !important;
        }
        th{
            font-size: 11px;
            font-weight: 600;
        }
        .am-selected-status{
            font-size: 12px !important;
            color: #9e9e9e;
        }
        [class*=am-icon-]:before {
            color: #999;
            font-size: 11px;

        }
        .am-selected-btn.am-btn-default {
            border-radius: 5px;
            height: 32px;
        }
        .am-selected {
            width: 80%;
        }
        .am-selected-list .am-selected-text{
            color: #999;
            font-size: 11px;
        }
        [v-cloak]{
            display: none;
        }

    </style>
@endsection
@section("content")

    {{--遮罩层--}}
    <div id="mask" class="mask" style="height: 120%"></div>
    <div class="col-sm-12" style="margin-bottom: -20px">
        <div class="ibox ">
            <form action="{{url('merchant/roominfo')}}" method="get" style="position: relative;margin-top: 20px;">
                {{csrf_field()}}
                <div class="form-group" style="float: left">
                    <div class="input-group">
                        <div class="form-group" style="float: left">
                            <div class="input-group">
                                <select name="out_community_id" id="out_community_id" style="width:250px;">
                                    <option value="" >请选择小区名称</option>
                                    @if($communityInfo)
                                        @foreach($communityInfo as $v)
                                            <option value="{{$v->out_community_id}}"  @if(isset($out_community_id)&&$out_community_id==$v->out_community_id) selected @endif>{{$v->community_name}}</option>
                                        @endforeach
                                    @endif
                                </select>

                                <input class="sky form-control ant-input ant-input-lg" value="@if(isset($room)&&$room){{$room}}@endif" name="room" id="room" type="text" style="width: 200px;margin-left: 10px; float: right;border-radius: 4px" placeholder="请输入房间号,单元或者住户信息">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" id="'submit" class="btn btn-outline btn-primary" style="margin-left: 10px">筛选</button>
                @mpermission('addRoom')
                <button class="btn btn-outline btn-primary " type="button" onclick="ShowDiv('add_room','mask')" style="float: right">批量导入房屋</button>
                <button type="button" onclick="ShowRom('add_rom','mask')"  class="btn btn-outline btn-warning" style="float: right;margin-right: 15px;">添加房屋</button>
                @endpermission
                @mpermission('uploadRoom')
                <button class="btn btn-outline btn-default" type="button" onclick="ShowDiv('room_async','mask')" style="float: right;margin-right: 15px;">批量同步</button>
                @endpermission
                @mpermission('delRoom')
                <button class="btn btn-outline btn-danger" type="button" onclick="ShowDiv('delRoom','mask')" style="float: right;margin-right: 15px;">批量删除</button>
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
                            <th >房屋面积</th>
                            <th >房屋具体地址</th>
                            <th>物业系统编号</th>
                            <th >支付宝状态</th>
                            <th >业主</th>
                            <th >创建时间</th>
                            <th >操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($roomInfo)&&!$roomInfo->isEmpty())
                            @foreach($roomInfo as $v )
                                <tr class="gradeA">
                                    <td>{{$v->community_name}}</td>
                                    <td>{{$v->area}}平方米</td>
                                    <td>{{$v->address}}</td>
                                    <td>{{$v->out_room_id}}</td>
                                    <td>
                                        @if($v->status=="ONLINE")
                                         <span style="color: red">已同步</span>
                                        @else
                                            未同步
                                        @endif
                                    </td>
                                    <td>
                                        @if(array_key_exists($v->out_room_id,$residentInfo))
                                        {{$residentInfo[$v->out_room_id]}}
                                        @endif
                                    </td>
                                    <td>{{$v->created_at}}</td>
                                    <td class="center">
                                        @if($v->status=="NONE"&&$v->alipay_status!="NONE"&&$v->alipay_status!="OFFLINE"&&$v->basicservice_status!="NONE"&&$v->basicservice_status!="OFFLINE")
                                        @mpermission('uploadRoom')
                                        <button type="button" onclick='uploadRoom("{{$v->id}}")'
                                                class="btn jurisdiction btn-outline btn-success">同步至支付宝
                                        </button>
                                        @endpermission
                                        @endif
                                        @mpermission('delRoom')
                                        <button type="button" onclick='del("{{$v->out_room_id}}")'
                                                class="btn btn-outline btn-danger">删除
                                        </button>
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
                @if(isset($roomInfo)&&!$roomInfo->isEmpty())
                    {{$roomInfo->appends(compact('room','out_community_id'))->render()}}
                @endif
            </div>
        </div>
    </div>
    {{--添加房屋--}}
    <div id="add_rom" class="ant-modal" style="margin-top:5px; width: 750px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('add_rom','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">添加房屋</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" name="" id="rom_name"    data-am-selected="{searchBox: 1,maxHeight: 200}" >
                                    <option value="">请选择小区名称</option>
                                    @if($communityInfo)
                                        @foreach($communityInfo as $v)
                                            <option value="{{$v->out_community_id}}" >{{$v->community_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span style="color: #f04134;display: none" class="span">请选择小区名称</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在楼栋</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" data-am-selected="{searchBox: 1,maxHeight: 200}" id="room_building">
                                    <option value="" id="">请选择房屋所在楼栋</option>
                                </select>
                                <span class="span" style="color:red;font-size: 12px;display: none">请选择房屋所在楼栋</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在单元</label>

                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" name="" id="room_unit"  data-am-selected="{searchBox: 1,maxHeight: 200}" >
                                    <option value="" id="unit" >请选择房屋所在单元</option>
                                </select>
                                <span class="span" style="color:red;font-size: 12px;display: none">请选择房屋所在单元</span>
                            </div>
                        </div>
                    </div>


                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在房号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="room_number" name="room_number"
                                       placeholder="请输入房屋所在房号" class="input ant-input ant-input-lg">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入房屋所在房号</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋面积</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="room_area" name="room_area"
                                       placeholder="请输入房屋面积" class="input ant-input ant-input-lg">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入房屋面积</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房主姓名</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="room_owner" name="room_owner"
                                       placeholder="请输入房主姓名" class="input ant-input ant-input-lg">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入房主姓名</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房主联系方式</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="owner_phone" name="owner_phone"
                                       placeholder="请输入房主联系方式" class="input ant-input ant-input-lg">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入房主联系方式</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="rom_submit" ><span>添加</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--批量添加房屋--}}
    <div id="add_room" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('add_room','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >批量导入房屋</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" name="" id="rooms_name"    data-am-selected="{searchBox: 1,maxHeight: 200}" >
                                    <option value="">请选择小区名称</option>
                                    @if($communityInfo)
                                        @foreach($communityInfo as $v)
                                            <option value="{{$v->out_community_id}}" >{{$v->community_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span style="color: #f04134;display: none" class="span">请选择小区名称</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在楼栋</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" data-am-selected="{searchBox: 1,maxHeight: 200}" id="rooms_building">
                                    <option value="" id="">请选择房屋所在楼栋</option>
                                </select>
                                <span class="span" style="color:red;font-size: 12px;display: none">请选择房屋所在楼栋</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在单元</label>

                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" name="" id="rooms_unit"  data-am-selected="{searchBox: 1,maxHeight: 200}" >
                                    <option value="" id="unit" >请选择房屋所在单元</option>
                                </select>
                                <span class="span" style="color:red;font-size: 12px;display: none">请选择房屋所在单元</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >上传房屋</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <div class="col-sm-3">

                                    <input type="text" size="20" name="key_path" value="" id="key_path" class="file" style="margin-left: 2%">
                                    <!-- 图片上传按钮 -->
                                    <input id="fileupload1" type="file" name="file" data-url="{{route('upload')}}"
                                           data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true" class="btn_file" style="margin-left:3%">
                                </div>
                                <!-- 图片展示模块 -->
                                <div class="files1"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress1">
                                    <div class="progress-bar1"></div>
                                </div>
                                <div style="clear:both;">

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <a href="" id="down" class="btn btn-outline btn-primary">
                                    下载模板
                                </a>
                                <button type="button"  id="rooms_submit" class="btn btn-outline btn-warning" >批量导入</button>
                                <a href="" id="errorDown" class="btn btn-outline btn-primary">
                                    错误信息导出
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--批量同步--}}
    <div id="room_async" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('room_async','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >批量同步房屋</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" name="" id="upload_community"    data-am-selected="{searchBox: 1,maxHeight: 200}" >
                                    <option value="">请选择小区名称</option>
                                    @if($communityInfo)
                                        @foreach($communityInfo as $v)
                                            <option value="{{$v->out_community_id}}" >{{$v->community_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span style="color: #f04134;display: none" class="span">请选择小区名称</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在楼栋</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" data-am-selected="{searchBox: 1,maxHeight: 200}" id="upload_building">
                                    <option value="" id="">请选择房屋所在楼栋</option>
                                </select>
                                <span class="span" style="color:red;font-size: 12px;display: none">请选择房屋所在楼栋</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在单元</label>

                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" name="" id="upload_unit"  data-am-selected="{searchBox: 1,maxHeight: 200}" >
                                    <option value="" id="unit" >请选择房屋所在单元</option>
                                </select>
                                <span class="span" style="color:red;font-size: 12px;display: none">请选择房屋所在单元</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">

                                <button type="button"  id="rooms_upload" class="btn btn-outline btn-warning" >批量同步</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--批量删除--}}
    <div id="delRoom" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('delRoom','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >批量删除房屋</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" name="" id="del_community"    data-am-selected="{searchBox: 1,maxHeight: 200}" >
                                    <option value="">请选择小区名称</option>
                                    @if($communityInfo)
                                        @foreach($communityInfo as $v)
                                            <option value="{{$v->out_community_id}}" >{{$v->community_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span style="color: #f04134;display: none" class="span">请选择小区名称</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在楼栋</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" data-am-selected="{searchBox: 1,maxHeight: 200}" id="del_building">
                                    <option value="" id="">请选择房屋所在楼栋</option>
                                </select>
                                <span class="span" style="color:red;font-size: 12px;display: none">请选择房屋所在楼栋</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在单元</label>

                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" name="" id="del_unit"  data-am-selected="{searchBox: 1,maxHeight: 200}" >
                                    <option value="" id="unit" >请选择房屋所在单元</option>
                                </select>
                                <span class="span" style="color:red;font-size: 12px;display: none">请选择房屋所在单元</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">

                                <button type="button"  id="rooms_del" class="btn btn-outline btn-warning" >批量删除</button>
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
    <script src="{{asset('/adminui/js/amazeui.chosen.js')}}"></script>
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        publicfileupload("#fileupload1", ".files1", "#key_path", '.up_progress1 .progress-bar1', ".up_progress1");
        function publicfileupload(fileid, imgid, postimgid, class1, class2) {
            //图片上传
            $(fileid).fileupload({
                dataType: 'json',
                add: function (e, data) {
                    var numItems = $('.files .images_zone').length;
                    if (numItems >= 10) {
                        alert('提交文件过多');
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
                        alert("上传失败,文件格式有误");
                    } else {
                        jQuery(postimgid).val(d.path);
                    }
                },
                progressall: function (e, data) {
                    console.log(data);
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(class1).css('width', progress + '%');
                }
            });
        }
    </script>
    <script>
        $(document).ready(function () {
            render();
            function render() {
                $('body').css('overflow-y','scroll');
                $('#out_community_id').chosen();
            }

        });
        //添加房屋 传out_community_id
        $('#rom_name').change(function () {
            $('.unit_option').remove();
            $('.building_option').remove();
            out_community_id=$(this).val();
            $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",out_community_id:out_community_id},
                function (data) {
                    var building=[];
                    if (data.success) {
                        building=data.data;
                        for(var i=0;i<building.length;i++){
                        var option='<option  value='+ building[i].id + ' class="building_option">'+building[i].building_name+'</option>';
                        $('#room_building').append(option);
                        }
                    } else {
                      console.log(data.msg);
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');

        });
        //楼栋
        $('#room_building ').change(function () {
            $('.unit_option').remove();
            out_community_id=$('#rom_name').val();
            building_id=$('#room_building').val();
            if(building_id){
                var _this=this;
                $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",building_id:building_id},
                    function (data) {
                        var building=[];
                        if (data.success) {
                            building=data.data;
                            for(var i=0;i<building.length;i++){
                                var option='<option  value='+ building[i].id + ' class="unit_option">'+building[i].unit_name+'</option>';
                                $('#room_unit').append(option);
                            }
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }
        });
        //添加房屋
        $('#rom_submit').click(function () {
            out_community_id=$("#rom_name").val();
            building_id=$("#room_building").val();
            unit_id=$("#room_unit").val();
            var obj=$("#add_rom");
            var  ck=true;
            obj.find('.select').each(function () {
                var select = $(this).val();
                if ( select == "" ) {
                    layer.msg('请选择完整的小区,楼宇,单元信息',{time:1000});
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
                    ck= false;
                }
            });
            if(ck) {
                $.post("{{url('merchant/createroom')}}", {
                        _token: "{{csrf_token()}}",
                        out_community_id: out_community_id,
                        building_id: building_id,
                        unit_id: unit_id,
                        room: $("#room_number").val(),
                        name: $("#room_owner").val(),
                        phone: $('#owner_phone').val(),
                        area:$("#room_area").val()
                    },
                    function (data) {
                        if (data.success) {
                            layer.msg(data.msg, {time: 500});
                            setTimeout(function () {
                                window.location.reload()
                            }, 500);
                        } else {
                            layer.msg(data.msg, {time: 2000});
                        }
                    }, 'json');
            }

        });



        //批量添加房屋 传out_community_id
        $('#rooms_name').change(function () {
            $('.units_option').remove();
            $('.buildings_option').remove();
            out_community_id=$(this).val();
            $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",out_community_id:out_community_id},
                function (data) {
                    var building=[];
                    if (data.success) {
                        building=data.data;
                        for(var i=0;i<building.length;i++){
                            var option='<option  value='+ building[i].id + ' class="buildings_option">'+building[i].building_name+'</option>';
                            $('#rooms_building').append(option);
                        }
                    } else {
                        console.log(data.msg);
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');

        });
        //楼栋
        $('#rooms_building ').change(function () {
            $('.units_option').remove();
            out_community_id=$('#rooms_name').val();
            building_id=$('#rooms_building').val();
            if(building_id){
                var _this=this;
                $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",building_id:building_id},
                    function (data) {
                        var building=[];
                        if (data.success) {
                            building=data.data;
                            for(var i=0;i<building.length;i++){
                                var option='<option  value='+ building[i].id + ' class="units_option">'+building[i].unit_name+'</option>';
                                $('#rooms_unit').append(option);
                            }
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }
        });
        //批量添加房屋
        ck2=true;
        $('#rooms_submit').click(function () {
            out_community_id=$("#rooms_name").val();
            building_id=$("#rooms_building").val();
            unit_id=$("#rooms_unit").val();
            var obj=$("#add_room");
            file=$("#key_path").val();
            var  ck=true;
            obj.find('.select').each(function () {
                var select = $(this).val();
                if ( select == "" ) {
                    layer.msg('请选择完整的小区,楼宇,单元信息',{time:1000});
                    ck= false;
                }
            });
            if(file==''){
                layer.msg('导入数据不能为空',{time:1000});
                ck= false;
            }
            if(ck&&ck2) {
                ck2=false;
                $.post("{{url('merchant/createrooms')}}", {
                        _token: "{{csrf_token()}}",
                        out_community_id: out_community_id,
                        building_id: building_id,
                        unit_id: unit_id,
                        file:file,
                        room: $("#room_number").val(),
                        name: $("#room_owner").val(),
                        phone: $('#owner_phone').val(),
                        area:$("#room_area").val()
                    },
                    function (data) {
                        ck2=true;
                        if (data.success) {
                            layer.msg(data.msg, {time: 500});
                            setTimeout(function () {
                                window.location.reload()
                            }, 500);
                        } else {
                            layer.msg(data.msg, {time: 5000});
                        }
                    }, 'json');
            }

        });
        //下载模板
        $('#down').click(function () {
            out_community_id=$("#room_name").val();
            console.log(out_community_id);
            $(this).prop('href',location.protocol+'//'+document.domain+'/merchant/roomExcel?out_community_id='+out_community_id);
        });
        $('#errorDown').click(function () {
            $(this).prop('href',location.protocol+'//'+document.domain+'/merchant/roomerror');
        });
        //单个同步房屋到支付宝
        function uploadRoom(id){
            layer.confirm('确定同步房屋信息到支付宝吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/uploadroom')}}", {_token: "{{csrf_token()}}", id: id },
                    function (data) {
                        if (data.success) {
                            layer.msg(data.msg,{time:500});
                            setTimeout(function(){window.location.reload()},500);
                        } else {
                            layer.msg(data.msg,{time:2000});

                        }
                    }, 'json');

            });
        }
        //批量同步房屋 传out_community_id
        $('#upload_community').change(function () {
            $('.units_upload').remove();
            $('.buildings_upload').remove();
            out_community_id=$(this).val();
            $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",out_community_id:out_community_id},
                function (data) {
                    var building=[];
                    if (data.success) {
                        building=data.data;
                        for(var i=0;i<building.length;i++){
                            var option='<option  value='+ building[i].id + ' class="buildings_upload">'+building[i].building_name+'</option>';
                            $('#upload_building').append(option);
                        }
                    } else {
                        console.log(data.msg);
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');

        });
        //楼栋
        $('#upload_building ').change(function () {
            $('.units_upload').remove();
            out_community_id=$('#upload_community').val();
            building_id=$('#upload_building').val();
            if(building_id){
                var _this=this;
                $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",building_id:building_id},
                    function (data) {
                        var building=[];
                        if (data.success) {
                            building=data.data;
                            for(var i=0;i<building.length;i++){
                                var option='<option  value='+ building[i].id + ' class="units_upload">'+building[i].unit_name+'</option>';
                                $('#upload_unit').append(option);
                            }
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }
        });
        //批量添加房屋
        $('#rooms_upload').click(function () {
            out_community_id=$("#upload_community").val();
            building_id=$("#upload_building").val();
            unit_id=$("#upload_unit").val();
            var obj=$("#room_async");
            var  ck=true;
            obj.find('.select').each(function () {
                var select = $(this).val();
                if ( select == "" ) {
                    layer.msg('请选择完整的小区,楼宇,单元信息',{time:1000});
                    ck= false;
                }
            });
            if(ck&&ck2) {
                ck2=false;
                $.post("{{url('merchant/uploadrooms')}}", {
                        _token: "{{csrf_token()}}",
                        unit_id: unit_id,
                    },
                    function (data) {
                        ck2=true;
                        if (data.success) {
                            layer.msg(data.msg, {time: 500});
                            setTimeout(function () {
                                window.location.reload()
                            }, 500);
                        } else {
                            layer.msg(data.msg, {time: 5000});
                        }
                    }, 'json');
            }

        });
        //删除房屋
        function del(out_room_id) {
            layer.confirm('确定删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/delroom')}}", {_token: "{{csrf_token()}}", out_room_id: out_room_id },
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
        //批量删除房屋传out_community_id
        $('#del_community').change(function () {
            $('.units_del').remove();
            $('.buildings_del').remove();
            out_community_id=$(this).val();
            $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",out_community_id:out_community_id},
                function (data) {
                    var building=[];
                    if (data.success) {
                        building=data.data;
                        for(var i=0;i<building.length;i++){
                            var option='<option  value='+ building[i].id + ' class="buildings_del">'+building[i].building_name+'</option>';
                            $('#del_building').append(option);
                        }
                    } else {
                        console.log(data.msg);
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');

        });
        //楼栋
        $('#del_building ').change(function () {
            $('.units_del').remove();
            out_community_id=$('#del_community').val();
            building_id=$('#del_building').val();
            if(building_id){
                var _this=this;
                $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",building_id:building_id},
                    function (data) {
                        var building=[];
                        if (data.success) {
                            building=data.data;
                            for(var i=0;i<building.length;i++){
                                var option='<option  value='+ building[i].id + ' class="units_del">'+building[i].unit_name+'</option>';
                                $('#del_unit').append(option);
                            }
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }
        });
        //批量删除房屋
        $('#rooms_del').click(function(){
            layer.confirm('确定删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                out_community_id = $("#del_community").val();
                building_id = $("#del_building").val();
                unit_id = $("#del_unit").val();
                var obj = $("#delRoom");
                var ck = true;
                obj.find('.select').each(function () {
                    var select = $(this).val();
                    if (select == "") {
                        layer.msg('请选择完整的小区,楼宇,单元信息', {time: 1000});
                        ck = false;
                    }
                });
                if (ck) {
                    $.post("{{url('merchant/delrooms')}}", {
                            _token: "{{csrf_token()}}",
                            unit_id: unit_id,
                        },
                        function (data) {
                            if (data.success) {
                                layer.msg(data.msg, {time: 500});
                                setTimeout(function () {
                                    window.location.reload()
                                }, 500);
                            } else {
                                layer.msg(data.msg, {time: 5000});
                            }
                        }, 'json');
                }
            }, function () {

            });

        });


        //弹出隐藏层
        function ShowDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='block';
            document.getElementById(bg_div).style.display='block' ;
            var bgdiv = document.getElementById(bg_div);
            bgdiv.style.width = document.body.scrollWidth;
            $("#"+bg_div).height($(document).height());
            $('#room_name').chosen();

        }

        function ShowRom(show_div,bg_div){
            document.getElementById(show_div).style.display='block';
            document.getElementById(bg_div).style.display='block' ;
            var bgdiv = document.getElementById(bg_div);
            bgdiv.style.width = document.body.scrollWidth;
            $("#"+bg_div).height($(document).height());
            $('#room_name').chosen();
        }

        //关闭弹出层
        function CloseDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='none';
            document.getElementById(bg_div).style.display='none';
            $('#template').hide();
            window.location.reload()

        }
    </script>
@endsection

