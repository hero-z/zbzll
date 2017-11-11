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
                            <th>原收金额</th>
                            <th>申请矫正金额</th>
                            <th >归属账期</th>
                            <th>描述</th>
                            <th>状态</th>
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
                                    <td>{{$v->bill_entry_amount}}元</td>
                                    <td>{{$v->correct_bill_amount}}元</td>
                                    <td>{{$v->acct_period}}</td>
                                    <td>{{$v->description}}</td>
                                    <td>
                                        @if($v->status=="OK")
                                            已处理
                                        @endif
                                        @if($v->status=="NONE")
                                            未处理
                                        @endif
                                    </td>
                                    <td>{{$v->created_at}}</td>
                                    <td class="center">
                                        @if($v->status=="NONE")
                                        <button type="button" onclick='Check("{{$v->bill_id}}","{{$v->correct_bill_amount}}")'
                                                class="btn btn-outline btn-success">矫正账单
                                        </button>
                                        @endif
                                        <button type="button" onclick='CheckDEL("{{$v->id}}")'
                                                class="btn btn-outline btn-danger">忽略
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
                @if(isset($billInfo)&&!$billInfo->isEmpty())
                    {{$billInfo->appends(compact('room','out_community_id'))->render()}}
                @endif
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        //线下结算申请
        function Check(bill_id,correct_bill_amount){
            layer.confirm('确定要矫正账单吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/checkbill')}}", {_token: "{{csrf_token()}}", bill_id: bill_id ,correct_bill_amount:correct_bill_amount},
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
        //忽略账单
        function CheckDEL(id){
            layer.confirm('确定要忽略账单吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/delcheckbill')}}", {_token: "{{csrf_token()}}", id: id },
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

    </script>
@endsection

