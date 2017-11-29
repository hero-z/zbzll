@extends("layouts.merchantcontent")
@section("title","账单统计")
@section('css')
    <link href="{{asset('/adminui/css/chosen.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
@endsection
@section("content")
    {{--遮罩层--}}
    <div id="mask" class="mask" style="height: 200%"></div>
    <div class="col-sm-12" style="margin-bottom: -20px">
        <div class="ibox col-sm-9">
            <form action="{{url('merchant/billquery')}}" method="post" style="position: relative;margin-top: 20px;">
                {{csrf_field()}}
                <div class="form-group" style="float: left" id="data_5">
                    <div class="input-daterange input-group" id="datepicker">
                        <input type="text" class="input-sm form-control" id="time_start" name="time_start" placeholder="账单开始日期" value="@if(isset($time_start)){{$time_start or ''}}@endif" />
                        <span class="input-group-addon">到</span>
                        <input type="text" class="input-sm form-control" id="time_end" name="time_end" placeholder="账单结束日期" value="@if(isset($time_end)){{$time_end or ''}}@endif" />
                    </div>
                </div>
                <div class="form-group" style="float: left;width: 250px;" id="data_1">
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control" id="time" name="time" placeholder="所属账期" value="@if(isset($time)){{$time or ''}}@endif">
                    </div>
                </div>
                <div class="form-group" style="float: left">
                    <div class="input-group">
                        <div class="form-group" style="float: left">
                            <div class="input-group">
                                <select name="merchant_id" id="merchant_id" style="width:250px;">
                                    <option value="" >请选择员工</option>
                                    @if($merchants)
                                        @foreach($merchants as $v)
                                            <option value="{{$v->id}}"  @if(isset($merchant_id)&&$merchant_id==$v->id) selected @endif >{{$v->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <select name="out_community_id" id="out_community_id" style="width:250px;">
                                    <option value="" >请选择小区名称</option>
                                    @if($communityInfo)
                                        @foreach($communityInfo as $v)
                                            <option value="{{$v->out_community_id}}"  @if(isset($out_community_id)&&$out_community_id==$v->out_community_id) selected @endif >{{$v->community_name}}</option>
                                        @endforeach
                                    @endif
                                </select>

                                <input class="sky form-control ant-input ant-input-lg" name="room" value="@if(isset($room)&&$room){{$room}}@endif"  id="room" type="text" style="width: 200px;margin-left: 10px; float: right;border-radius: 4px" placeholder="按房间号搜索">
                            </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="form-group" style="float: left">
                            <div class="input-group">
                                <select name="bill_cost_type" id="bill_cost_type" style="width:250px;">
                                    <option value="" >请选择费用类型</option>
                                    <option value="1" @if($bill_cost_type&&$bill_cost_type=='1') selected @endif >物业费</option>
                                    <option value="2" @if($bill_cost_type&&$bill_cost_type=='2') selected @endif >物业费公摊</option>
                                    <option value="3" @if($bill_cost_type&&$bill_cost_type=='3') selected @endif >垃圾费</option>
                                    <option value="4" @if($bill_cost_type&&$bill_cost_type=='4') selected @endif >电梯费</option>
                                </select>
                                <select name="bill_type" id="bill_type" style="width:250px;">
                                    <option value="" >请选择支付方式</option>
                                    <option value="1" @if($bill_type&&$bill_type=='1') selected @endif >物业官方支付宝</option>
                                    <option value="2" @if($bill_type&&$bill_type=='2') selected @endif >现金</option>
                                </select>
                                <select name="bill_status" id="bill_status" style="width:250px;">
                                    <option value="" >请选择账单状态</option>
                                    <option value="1" @if($bill_status&&$bill_status=='1') selected @endif >已同步</option>
                                    <option value="2" @if($bill_status&&$bill_status=='2') selected @endif >未同步</option>
                                    <option value="3" @if($bill_status&&$bill_status=='3') selected @endif >线下结算审核中</option>
                                    <option value="4" @if($bill_status&&$bill_status=='4') selected @endif >已结算</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="'submit" class="btn btn-outline btn-primary" style="margin-left: 10px">筛选</button>
                <button type="button" onclick="exportdata()" class="btn btn-outline btn-success" style="margin-left: 10px">导出Excel</button>
            </form>
        </div>
        <div class="col-sm-3">
            <div class="ibox ">
                <span style="color: green;font-size: 14px;display: block;margin-top: 30px">
                    总金额(元):
                    <span id="totalje" style="font-size: 24px;color: #be2924;margin-left: 20px">计算中...</span>
                </span>
                <span style="color: green;font-size: 14px;display: block;">
                    条数:
                    <span style="font-size: 24px;color: #be2924;margin-left: 20px">@if(isset($count)){{$count}}@endif</span>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th >所属员工</th>
                            <th >所属房间</th>
                            <th >金额</th>
                            <th >支付方式</th>
                            <th>费用类型</th>
                            <th>账单状态</th>
                            <th>出账日期</th>
                            <th>所属账期</th>
                            <th>缴费截止日期</th>
                            <th>备注</th>
                            <th>更新时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lists)&&!$lists->isEmpty())
                            @foreach($lists as $v )
                                <tr class="gradeA">
                                    <td>{{$v->merchant_name}}</td>
                                    <td>{{$v->community_name.(array_key_exists($v->out_room_id,$roominfos)?$roominfos[$v->out_room_id]:'')}}</td>
                                    <td>{{$v->bill_entry_amount}}</td>
                                    <td>
                                        @if($v->type=='alipay')
                                            物业官方支付宝
                                        @elseif($v->type=='money')
                                            现金
                                        @endif
                                    </td>
                                    <td>
                                        @if($v->cost_type=='property_fee')
                                            物业费
                                        @elseif($v->cost_type=='public_property_fee')
                                            物业费公摊
                                        @elseif($v->cost_type=='rubbish_fee')
                                            垃圾费
                                        @elseif($v->cost_type=='elevator_fee')
                                            电梯费
                                        @endif
                                    </td>
                                    <td>
                                        @if($v->bill_status=="ONLINE")
                                            <span style="color:green;">
                                             已同步
                                            </span>
                                        @endif
                                        @if($v->bill_status=="NONE")
                                            <span style="color:grey;">
                                             未同步
                                            </span>
                                        @endif
                                        @if($v->bill_status=="UNDERREVIEW"||$v->bill_status=="ONLINE_UNDERREVIEW")
                                            线下结算审核中
                                        @endif
                                        @if($v->bill_status=="TRADE_SUCCESS")
                                            <span style="color: red;">
                                             已结算
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{$v->release_day}}</td>
                                    <td>{{$v->acct_period}}</td>
                                    <td>{{$v->deadline}}</td>
                                    <td>{{$v->remark_str}}</td>
                                    <td>{{$v->updated_at}}</td>
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
                @if(isset($lists)&&!$lists->isEmpty())
                    {{$lists->appends([
                    'out_community_id'=>isset($out_community_id)?$out_community_id:'',
                    'room'=>isset($room)?$room:'',
                    'merchant_id'=>isset($merchant_id)?$merchant_id:'',
                    'bill_cost_type'=>isset($bill_cost_type)?$bill_cost_type:'',
                    'bill_type'=>isset($bill_type)?$bill_type:'',
                    'bill_status'=>isset($bill_status)?$bill_status:'',
                    'time'=>isset($time)?$time:'',
                    'time_start'=>isset($time_start)?$time_start:'',
                    'time_end'=>isset($time_end)?$time_end:''
                    ])->render()}}
                @endif
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="{{asset('/adminui/js/chosen.jquery.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/cropper/cropper.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            render();
            function render() {
                $('#out_community_id').chosen();
                $('#merchant_id').chosen();
                $('#bill_cost_type').chosen();
                $('#bill_type').chosen();
                $('#bill_status').chosen();
            }
            $("body").css("overflow-y","scroll");
            $.post("{{url('merchant/billquery')}}", {
                    _token: "{{csrf_token()}}",

                    total_amount:1,
                    merchant_id:$('#merchant_id').val(),
                    out_community_id:$('#out_community_id').val(),
                    room:$('#room').val(),
                    bill_cost_type:$('#bill_cost_type').val(),
                    bill_type:$('#bill_type').val(),
                    bill_status:$('#bill_status').val(),
                    time:$('#time').val(),
                    time_end:$('#time_end').val(),
                    time_start:$('#time_start').val()
                },
                function (data) {
                    if(data.success){
                        $('#totalje').text(data.totalje);
                    }
                }, 'json');
//            $("#data_1 .input-group.date").datepicker({
//                minViewMode:1,
//                keyboardNavigation:!1,
//                forceParse:1,
//                autoclose:!0
////                todayHighlight:!0
////                startView:1,
////                showMeridian:true,
//            });
            $("#data_1 .input-group.date").datepicker({
                todayBtn:"linked",
                keyboardNavigation:!1,
                forceParse:1,
                todayHighlight:!0,
                autoclose:!0,
                minViewMode:1,
                startView:0,
                format:"yyyy-mm"
//                startView:1,
//                showMeridian:true,
            });
            $("#data_5 .input-daterange").datepicker({
                todayBtn:"linked",
                keyboardNavigation:!1,
                forceParse:1,
                calendarWeeks:!0,
                todayHighlight:!0,
                minView:0,
                minViewMode:0,
                startView:0,
                showMeridian:true,
                autoclose:!0
            });
//            $("#data_1 .input-group.date").datepicker('setStartDate',new Date());
            $("#data_5 .input-daterange").datepicker('setStartDate',new Date());
        });

    </script>
    <script>
        function exportdata() {
            window.location.href="{{url('merchant/billquery')}}"
                +"?merchant_id="+$('#merchant_id').val()
                +"&out_community_id="+$('#out_community_id').val()
                +"&room="+$('#room').val()
                +"&bill_cost_type=" +$('#bill_cost_type').val()
                +"&bill_type="+$('#bill_type').val()
                +"&store_type="+$('#store_type').val()
                +"&bill_status="+$('#bill_status').val()
                +"&time="+$('#time').val()
                +"&time_start="+$('#time_start').val()
                +"&time_end="+$('#time_end').val()
                +"&export="+1;
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

