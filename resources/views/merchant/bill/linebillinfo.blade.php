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
                                    <td>{{$v->bill_entry_amount}}元</td>
                                    <td>{{$v->acct_period}}</td>
                                    <td>{{$v->release_day}}</td>
                                    <td>{{$v->deadline}}</td>
                                    <td>{{$v->remark_str}}</td>
                                    <td>
                                        @if($v->bill_status=="ONLINE")
                                            已同步
                                        @endif
                                        @if($v->bill_status=="NONE")
                                            未同步
                                        @endif
                                        @if($v->bill_status=="UNDERREVIEW"||$v->bill_status=="ONLINE_UNDERREVIEW")
                                            线下结算审核中
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
                                        <button type="button" onclick='Check("{{$v->id}}","{{$v->bill_status}}")'
                                                class="btn btn-outline btn-danger">审核
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
        function Check(id,bill_status){
            layer.confirm('确定线下已结算,并通过审核吗?', {
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


    </script>
@endsection

