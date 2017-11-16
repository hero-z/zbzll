@extends("layouts.merchantcontent")
@section("title","房屋管理")
@section('css')
    <link href="{{asset('/adminui/css/chosen.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/amazeui.chosen.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/plugins/jasny/jasny-bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
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
    <div id="mask" class="mask" style="height: 150%"></div>
    <div class="col-sm-12" style="margin-bottom: -20px">
        <div class="ibox ">
            <form action="{{url('merchant/billinfo')}}" method="get" style="position: relative;margin-top: 20px;">
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

                                <input class="sky form-control ant-input ant-input-lg" value="@if(isset($room)&&$room){{$room}}@endif" name="room" id="room" type="text" style="width: 200px;margin-left: 10px; float: right;border-radius: 4px" placeholder="请输入房间号">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" id="'submit" class="btn btn-outline btn-primary" style="margin-left: 10px">筛选</button>
                @permission('addBill')
                <button class="btn btn-outline btn-primary " type="button" onclick="ShowDiv('add_bills','mask')" style="float: right">批量导入账单</button>
                <button type="button" onclick="ShowDiv('down_bill','mask')"
                        class="btn  btn-outline btn-success" style="float: right;margin-right: 15px;">下载模板
                </button>
                <button type="button" onclick="ShowRom('add_bill','mask')"  class="btn btn-outline btn-warning" style="float: right;margin-right: 15px;">添加账单</button>
                @endpermission
                @permission('uploadBill')
                <button class="btn btn-outline btn-default" type="button" onclick="ShowDiv('bill_async','mask')" style="float: right;margin-right: 15px;">批量同步</button>
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
                            <th >归属房屋</th>
                            <th >费用类型</th>
                            <th>应收金额</th>
                            <th >归属账期</th>
                            <th >出账日期</th>
                            <th>截止日期</th>
                            <th >缴费标识</th>
                            <th>状态</th>
                            <th>支付类型</th>
                            <th >导入日期</th>
                            <th >操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($billInfo)&&!$billInfo->isEmpty())
                            @foreach($billInfo as $v )
                                <tr class="gradeA">
                                    <td>{{$v->community_name}}</td>
                                    <td>{{$v->building_name}}{{$v->unit_name}}{{$v->room}}</td>
                                    <td>
                                    @if($v->cost_type=='property_fee')
                                        物业费
                                    @endif
                                    @if($v->cost_type=='public_property_fee')
                                        物业费公摊
                                    @endif
                                    </td>
                                    <td><span style="color: mediumvioletred">{{$v->bill_entry_amount}}</span>元</td>
                                    <td>{{$v->acct_period}}</td>
                                    <td>{{$v->release_day}}</td>
                                    <td>{{$v->deadline}}</td>
                                    <td>{{$v->remark_str}}</td>
                                    <td>
                                        @if($v->bill_status=="ONLINE")
                                            <span style="color: green">已同步</span>
                                        @endif
                                        @if($v->bill_status=="NONE")
                                               未同步
                                        @endif
                                        @if($v->bill_status=="UNDERREVIEW"||$v->bill_status=="ONLINE_UNDERREVIEW")
                                          线下结算审核中
                                        @endif
                                        @if($v->bill_status=="TRADE_SUCCESS")
                                           <span style="color:red">已结算</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($v->type=='alipay')
                                            物业官方支付宝
                                        @endif
                                        @if($v->type=='money')
                                            现金
                                        @endif
                                    </td>
                                    <td>{{$v->created_at}}</td>
                                    <td class="center">
                                        @if($v->bill_status=="NONE"&&$v->alipay_status!="NONE"&&$v->alipay_status!="OFFLINE"&&$v->basicservice_status!="NONE"&&$v->basicservice_status!="OFFLINE")
                                           @permission('uploadBill')
                                            <button type="button" onclick='uploadBill("{{$v->id}}","{{$v->out_community_id}}")'
                                                    class="btn jurisdiction btn-outline btn-success">同步至支付宝
                                            </button>
                                            @endpermission
                                        @endif
                                        @if($v->bill_status!='ONLINE_UNERREVIEW'&&$v->bill_status!='UNERREVIEW'&&$v->bill_status!='SUCCESS'&&Auth::guard('merchant')->user()->pid!=0)
                                        @permission('editLineBill')
                                        <button type="button" onclick='editLineBill("{{$v->id}}","{{$v->bill_status}}")'
                                                class="btn btn-outline btn-success">线下结算
                                        </button>
                                        @endpermission
                                        @permission('questionBill')
                                        <button type="button" onclick='ShowDiv("questionBill","mask");questionBill("{{$v->id}}")'
                                                class="btn btn-outline btn-default">账单存疑
                                        </button>
                                        @endpermission
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
                @if(isset($billInfo)&&!$billInfo->isEmpty())
                    {{$billInfo->appends(compact('room','out_community_id'))->render()}}
                @endif
            </div>
        </div>
    </div>
    {{--添加账单--}}
    <div id="add_bill" class="ant-modal" style="width: 900px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('add_bill','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">添加账单</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" value="simple" name="type" id="type">

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="" id="bill_community" class="select" style="width:250px;" data-am-selected="{searchBox: 1}">
                                    <option value="">请选择小区名称</option>
                                    @if($communityInfo)
                                        @foreach($communityInfo as $v)
                                            <option value="{{$v->out_community_id}}"  @if(isset($out_community_id)&&$out_community_id==$v->out_community_id) selected @endif>{{$v->community_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在楼栋</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select data-am-selected="{searchBox: 1}" id="bill_building" class="select">
                                    <option value="" id="">请选择房屋所在楼栋</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在单元</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="" id="bill_unit"  data-am-selected="{searchBox: 1}" class="select">
                                    <option value="" id="" >请选择房屋所在单元</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在房号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="" id="bill_room"  data-am-selected="{searchBox: 1}" class="select">
                                    <option value="" id="" >请输入房屋所在房号</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">费用类型</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="" id="cost_type" class="ant-input select" style="width: 80%;color: #9e9e9e">
                                    <option style="" >请选择费用类型</option>
                                    <option value="property_fee" id="" >物业管理费</option>
                                    <option value="public_property_fee" id="" >物业管理费公摊</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">金额</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="bill_entry_amount" name="bill_entry_amount" class="input ant-input ant-input-lg" placeholder="请输入金额" style="width:463px">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入金额</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="data_4">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">归属账期</label>
                        </div>
                        <div class="input-group date" style="width:463px">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control input" value="2017-10-01"  id="acct_period">
                            <span class="span" style="color:red;font-size: 12px;display: none">请选择归属账期</span>
                        </div>
                    </div>
                    <div class="form-group" id="data_3">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">出账日期</label>
                        </div>
                        <div class="input-group date" style="width:463px">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control input" value="2017-10-01" id="release_day">
                            <span class="span" style="color:red;font-size: 12px;display: none">请选择出账日期</span>
                        </div>
                    </div>
                    <div class="form-group" id="data_3">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">截止日期</label>
                        </div>
                        <div class="input-group date" style="width:463px">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control input" value="2017-10-01" id="deadline">
                            <span class="span" style="color:red;font-size: 12px;display: none">请选择截止日期</span>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">推送备注</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" style="width:463px" id="remark_str" name="remark_str" class=" input ant-input ant-input-lg" placeholder="请输入推送备注">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入推送备注</span>
                            </div>
                        </div>
                    </div>



                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="bill_submit"><span>提交</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--下载模板--}}
    <div id="down_bill" class="ant-modal" style="width: 900px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('down_bill','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">下载模板</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" value="simple" name="type" id="type">

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="" id="down_community" class="select" style="width:250px;" data-am-selected="{searchBox: 1}">
                                    <option value="">请选择小区名称</option>
                                    @if($communityInfo)
                                        @foreach($communityInfo as $v)
                                            <option value="{{$v->out_community_id}}"  @if(isset($out_community_id)&&$out_community_id==$v->out_community_id) selected @endif>{{$v->community_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在楼栋</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select data-am-selected="{searchBox: 1}" id="down_building" class="select">
                                    <option value="" id="">请选择房屋所在楼栋</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">房屋所在单元</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="" id="down_unit"  data-am-selected="{searchBox: 1}" class="select">
                                    <option value="" id="" >请选择房屋所在单元</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="data_4">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">归属账期</label>
                        </div>
                        <div class="input-group date" style="width:463px">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control input" value="2017-10-01"  id="down_acct_period">
                            <span class="span" style="color:red;font-size: 12px;display: none">请选择归属账期</span>
                        </div>
                    </div>
                    <div class="form-group" id="data_3">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">出账日期</label>
                        </div>
                        <div class="input-group date" style="width:463px">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control input" value="2017-10-01" id="down_release_day">
                            <span class="span" style="color:red;font-size: 12px;display: none">请选择出账日期</span>
                        </div>
                    </div>
                    <div class="form-group" id="data_3">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">截止日期</label>
                        </div>
                        <div class="input-group date" style="width:463px">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control input" value="2017-10-01" id="down_deadline">
                            <span class="span" style="color:red;font-size: 12px;display: none">请选择截止日期</span>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">物业费用单价</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" value="" id="down_bill_entry_amount" name="bill_entry_amount" class="input ant-input ant-input-lg" placeholder="请直接输入数字,如1元每平,输入1即可" style="width:463px">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入物业费用单价</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <a  id="down_bill_submit" class="btn btn-outline btn-primary">
                                    下载模板
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--批量添加账单--}}
    <div id="add_bills" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('add_bills','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >批量导入账单</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">费用类型</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="" id="cost_types" class="ant-input select" style="width: 80%;color: #9e9e9e" >
                                    <option style="" >请选择费用类型</option>
                                    <option value="property_fee" id="" >物业管理费</option>
                                    <option value="public_property_fee" id="" >物业管理费公摊</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >上传账单</label>
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
                                <button type="button"  id="bills_submit" class="btn btn-outline btn-warning" >批量导入</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--批量同步--}}
    <div id="bill_async" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('bill_async','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >批量同步账单</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select class="select" name="" id="upload_community"    data-am-selected="{searchBox: 1}" >
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
                                <select class="select" data-am-selected="{searchBox: 1}" id="upload_building">
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
                                <select class="select" name="" id="upload_unit"  data-am-selected="{searchBox: 1}" >
                                    <option value="" id="unit" >请选择房屋所在单元</option>
                                </select>
                                <span class="span" style="color:red;font-size: 12px;display: none">请选择房屋所在单元</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">

                                <button type="button"  id="bills_upload" class="btn btn-outline btn-warning" >批量同步</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--账单存疑管理--}}
    {{--添加账单--}}
    <div id="questionBill" class="ant-modal" style="width: 900px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('questionBill','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">矫正账单申请</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" value="simple" name="type" id="bill_id">

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">矫正后金额</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" style="width:463px" id="correct_bill_amount" name="" class=" input ant-input ant-input-lg" placeholder="请输入矫正后金额">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入矫正后金额</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">描述</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" style="width:463px" id="description" name="" class=" input ant-input ant-input-lg" placeholder="请输入描述">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入描述</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="question_bill_submit"><span>提交申请</span>
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
    <script src="{{asset('/adminui/js/amazeui.chosen.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
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
        //添加账单 传out_community_id
        $('#bill_community').change(function () {
            $('.unit_bill').remove();
            $('.building_bill').remove();
            out_community_id=$(this).val();
            $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",out_community_id:out_community_id},
                function (data) {
                    var building=[];
                    if (data.success) {
                        building=data.data;
                        for(var i=0;i<building.length;i++){
                            var option='<option  value='+ building[i].id + ' class="building_bill">'+building[i].building_name+'</option>';
                            $('#bill_building').append(option);
                        }
                    } else {
                        console.log(data.msg);
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');

        });
        //楼栋
        $('#bill_building ').change(function () {
            $('.unit_bill').remove();
            out_community_id=$('#bill_community').val();
            building_id=$('#bill_building').val();
            if(building_id){
                var _this=this;
                $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",building_id:building_id},
                    function (data) {
                        var building=[];
                        if (data.success) {
                            building=data.data;
                            for(var i=0;i<building.length;i++){
                                var option='<option  value='+ building[i].id + ' class="unit_bill">'+building[i].unit_name+'</option>';
                                $('#bill_unit').append(option);
                            }
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }
        });
        //房屋
        $('#bill_unit ').change(function () {
            $('.room_bill').remove();
            out_community_id=$('#rom_name').val();
            unit_id=$('#bill_unit').val();
            if(unit_id){
                var _this=this;
                $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",unit_id:unit_id},
                    function (data) {
                        var building=[];
                        if (data.success) {
                            building=data.data;
                            for(var i=0;i<building.length;i++){
                                var option='<option  value='+ building[i].out_room_id + ' class="room_bill">'+building[i].room+'</option>';
                                $('#bill_room').append(option);
                            }
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }
        });
        //添加账单
        $('#bill_submit').click(function () {
            out_community_id=$("#bill_community").val();
            building_id=$("#bill_building").val();
            unit_id=$("#bill_unit").val();
            var obj=$("#add_bill");
            var  ck=true;
            obj.find('.select').each(function () {
                var select = $(this).val();
                if ( select == "" ) {
                    layer.msg('请选择完整的小区,楼宇,单元信息以及费用类型',{time:1000});
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
                $.post("{{url('merchant/addbill')}}", {
                        _token: "{{csrf_token()}}",
                        out_community_id: out_community_id,
                        out_room_id: $("#bill_room").val(),
                        cost_type:$("#cost_type").val(),
                        acct_period:$("#acct_period").val(),
                        release_day:$("#release_day").val(),
                        deadline:$("#deadline").val(),
                        bill_entry_amount:$("#bill_entry_amount").val(),
                        remark_str:$("#remark_str").val()
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
        //下载模板
        $('#down_community').change(function () {
            $('.unit_down').remove();
            $('.building_down').remove();
            out_community_id=$(this).val();
            $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",out_community_id:out_community_id},
                function (data) {
                    var building=[];
                    if (data.success) {
                        building=data.data;
                        for(var i=0;i<building.length;i++){
                            var option='<option  value='+ building[i].id + ' class="building_down">'+building[i].building_name+'</option>';
                            $('#down_building').append(option);
                        }
                    } else {
                        console.log(data.msg);
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');

        });
        //楼栋
        $('#down_building ').change(function () {
            $('.unit_down').remove();
            out_community_id=$('#down_community').val();
            building_id=$('#down_building').val();
            if(building_id){
                var _this=this;
                $.post("{{url('merchant/getroominfo')}}", {_token: "{{csrf_token()}}",building_id:building_id},
                    function (data) {
                        var building=[];
                        if (data.success) {
                            building=data.data;
                            for(var i=0;i<building.length;i++){
                                var option='<option  value='+ building[i].id + ' class="unit_down">'+building[i].unit_name+'</option>';
                                $('#down_unit').append(option);
                            }
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }
        });
        $('#down_bill_submit').click(function () {
            out_community_id=$("#down_community").val();
            building_id=$("#down_building").val();
            unit_id=$("#down_unit").val();
            var obj=$("#down_bill");
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
                unit_id = unit_id;
                acct_period = $("#down_acct_period").val();
                release_day = $("#down_release_day").val();
                deadline = $("#down_deadline").val();
                down_bill_entry_amount = $("#down_bill_entry_amount").val();
                $(this).prop('href', location.protocol + '//' + document.domain + '/merchant/billExcel?unit_id=' + unit_id + "&acct_period=" + acct_period + "&release_day=" + release_day + "&deadline=" + deadline + "&down_bill_entry_amount=" + down_bill_entry_amount);
            }
        });



        //批量添加房屋
        $('#bills_submit').click(function () {

            var obj=$("#add_bills");
            file=$("#key_path").val();
            var  ck=true;
            obj.find('.select').each(function () {
                var select = $(this).val();
                if ( select == "" ) {
                    layer.msg('请选择费用类型',{time:1000});
                    ck= false;
                }
            });
            if(file==''){
                layer.msg('导入数据不能为空',{time:1000});
                ck= false;
            }
            if(ck) {
                $.post("{{url('merchant/addbills')}}", {
                        _token: "{{csrf_token()}}",
                        file:file,
                        cost_type:$("#cost_types").val(),
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

        });
        //下载模板
        //单个同步房屋到支付宝
        function uploadBill(id,out_community_id){
            layer.confirm('确定同步账单信息到支付宝吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/uploadbill')}}", {_token: "{{csrf_token()}}", id: id ,out_community_id:out_community_id},
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
        $('#bills_upload').click(function () {
            out_community_id=$("#upload_community").val();
            building_id=$("#upload_building").val();
            unit_id=$("#upload_unit").val();
            var obj=$("#bill_async");
            var  ck=true;
            obj.find('.select').each(function () {
                var select = $(this).val();
                if ( select == "" ) {
                    layer.msg('请选择完整的小区,楼宇,单元信息',{time:1000});
                    ck= false;
                }
            });
            if(ck) {
                $.post("{{url('merchant/uploadbills')}}", {
                        _token: "{{csrf_token()}}",
                        unit_id: unit_id,
                        out_community_id:out_community_id
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

        });
        //线下结算申请
        function editLineBill(id,bill_status){
            layer.confirm('确定线下已结算,并提交审核吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/editlinebill')}}", {_token: "{{csrf_token()}}", id: id ,bill_status:bill_status},
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
        //存疑账单处理
        function questionBill(id){
            $("#bill_id").val(id);
        }
        $("#question_bill_submit").click(function(){
            var obj=$("#questionBill");
            var  ck=true;
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
                $.post("{{url('merchant/questionbillsubmit')}}", {
                        _token: "{{csrf_token()}}",
                      bill_id:$("#bill_id").val(),
                      correct_bill_amount:$("#correct_bill_amount").val(),
                     description:$("#description").val()
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
            $('#template').hide()

        }
    </script>
    <script src="{{asset('/adminui/js/plugins/cropper/cropper.min.js')}}"></script>
    <script src="{{asset('/adminui/js/demo/form-advanced-demo.min.js')}}"></script>
    <script src="{{asset("/adminui/js/plugins/switchery/switchery.js")}}"></script>
@endsection

