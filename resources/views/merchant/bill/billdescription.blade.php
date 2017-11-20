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
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th >费用类型</th>
                            <th>应收金额</th>
                            <th >归属账期</th>
                            <th >出账日期</th>
                            <th>截止日期</th>
                            <th >缴费标识</th>
                            <th>状态</th>
                            <th>支付类型</th>
                            <th>支付账户</th>
                            <th >导入日期</th>
                            <th >操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($billInfo)&&!$billInfo->isEmpty())
                            @foreach($billInfo as $v )
                                <tr class="gradeA">
                                    <td>
                                        @if($v->cost_type=='property_fee')
                                            物业费
                                        @endif
                                        @if($v->cost_type=='public_property_fee')
                                            物业费公摊
                                        @endif
                                        @if($v->cost_type=='rubbish_fee')
                                            垃圾费
                                        @endif
                                        @if($v->cost_type=='elevator_fee')
                                            电梯费
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
                                            线下结算
                                        @endif
                                    </td>
                                    <td>{{$v->buyer_logon_id}}</td>
                                    <td>{{$v->created_at}}</td>
                                    <td class="center">
                                        @if($v->bill_status=="NONE"&&$v->status!="NONE")
                                            @mpermission('uploadBill')
                                            <button type="button" onclick='uploadBill("{{$v->id}}","{{$v->out_community_id}}")'
                                                    class="btn jurisdiction btn-outline btn-success">同步至支付宝
                                            </button>
                                            @endpermission
                                            @mpermission('deleteBill')
                                            <button type="button" onclick="deleteBill('{{$v->id}}')"
                                                    class="btn btn-outline btn-danger">删除
                                            </button>
                                            @endpermission
                                        @endif
                                        @if($v->bill_status!='ONLINE_UNERREVIEW'&&$v->bill_status!='UNERREVIEW'&&$v->bill_status!='SUCCESS'&&Auth::guard('merchant')->user()->pid!=0)
                                            @mpermission('editLineBill')
                                            <button type="button" onclick='editLineBill("{{$v->id}}","{{$v->bill_status}}")'
                                                    class="btn btn-outline btn-success">线下结算
                                            </button>
                                            @endpermission
                                            @mpermission('questionBill')
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
                    {{$billInfo->appends(compact('out_room_id'))->render()}}
                @endif
            </div>
        </div>
    </div>

    {{--账单存疑管理--}}
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
    <script>
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
        //删除账单
        function deleteBill(id){
            layer.confirm('确定删除账单吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/deletebill')}}", {_token: "{{csrf_token()}}", id: id },
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
    <script src="{{asset("/adminui/js/plugins/switchery/switchery.js")}}"></script>
@endsection

