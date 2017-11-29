<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('ft5/images/logoico.ico')}}" /> <link href="{{asset('/adminui/css/bootstrap.min.css?v=3.3.5')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/font-awesome.min.css?v=4.4.0')}}" rel="stylesheet">

    <!-- Morris -->
    <link href="{{asset('/adminui/css/plugins/morris/morris-0.4.3.min.css')}}" rel="stylesheet">

    <!-- Gritter -->
    <link href="{{asset('/adminui/js/plugins/gritter/jquery.gritter.css')}}" rel="stylesheet">

    <link href="{{asset('/adminui/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/style.min.css?v=4.0.0')}}" rel="stylesheet"><base target="_blank">

</head>

<body class="gray-bg">
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <a href="{{url('merchant/billquery')}}"class="label label-success pull-right" target='_self'>查看详情</a>
                    <h5>累计收款</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><span style="color:green">{{$total_amount}}</span>元</h1>
                    <div class="stat-percent font-bold text-success">缴费率<span style="color: red">{{$total_amount_rate}}</span> <i class="fa fa-bolt"></i>
                    </div>
                    <small>总收入</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <a href="{{url('merchant/billquery')}}"class="label label-primary pull-right" target='_self'>查看详情</a>

                    <h5>月累计收款</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><span style="color: #953b39">{{$month_total_amount}}</span>元</h1>
                    <div class="stat-percent font-bold text-info">缴费条数<span style="color:goldenrod">{{$month_total_amount_count}}</span>条 <i class="fa fa-bolt"></i>
                    </div>
                    <small>月收入</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <a href="{{url('merchant/billquery')}}"class="label label-warning pull-right" target='_self'>查看详情</a>
                    <h5>日收款</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><span style="color: red">{{$today_total_amount}}</span>元</h1>
                    <div class="stat-percent font-bold text-navy">缴费条数<span style="color: rebeccapurple">{{$today_total_amount_count}}</span>条 <i class="fa fa-bolt"></i>
                    </div>
                    <small>今日收入</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-danger pull-right">总计</span>
                    <h5>累计线上缴费用户数</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><span style="color: indianred">{{$total_user}}</span>户</h1>
                    <div class="stat-percent font-bold text-danger"><span style="color: blue">{{$off_total_user}}</span>户 <i class="fa fa-bolt"></i>
                    </div>
                    <small>线下缴费用户数</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>详情图</h5>
                    <div class="pull-right">

                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="flot-dashboard-chart"></div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <ul class="stat-list">
                                <li>
                                    <h2 class="no-margins">{{$online_total_amount}}元</h2>
                                    <small>线上缴费总额</small>
                                    <div class="stat-percent">{{$online_total_amount_rate}}% <i class="fa fa-bolt text-navy"></i>
                                    </div>
                                    <div class="progress progress-mini">
                                        <div style="width: {{$online_total_amount_rate}}%;" class="progress-bar"></div>
                                    </div>
                                </li>
                                <li>
                                    <h2 class="no-margins ">{{$offline_total_amount}}元</h2>
                                    <small>线下缴费总额</small>
                                    <div class="stat-percent">{{$offline_total_amount_rate}}% <i class="fa fa-bolt text-navy"></i>
                                    </div>
                                    <div class="progress progress-mini">
                                        <div style="width: {{$offline_total_amount_rate}}%;" class="progress-bar"></div>
                                    </div>
                                </li>
                                <li>
                                    <h2 class="no-margins "><span style="color:red">{{$overdue_total_amount}}</span>元</h2>
                                    <small>逾期未缴金额</small>
                                    <div class="stat-percent">{{$overdue_total_amount_rate}} <i class="fa fa-bolt text-navy"></i>
                                    </div>
                                    <div class="progress progress-mini">
                                        <div style="width: {{$overdue_total_amount_rate}};" class="progress-bar"></div>
                                    </div>
                                </li>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>消息</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content ibox-heading">
                    <h3><i class="fa fa-envelope-o"></i>消息模块(功能待开发中)</h3>
                    <small><i class="fa fa-tim"></i> 您有22条未读消息</small>
                </div>
                <div class="ibox-content">
                    <div class="feed-activity-list">

                        <div class="feed-element">
                            <div>
                                <small class="pull-right text-navy">1月前</small>
                                <strong></strong>
                                <div>功能待开发</div>
                                <small class="text-muted">11月28日 00:00</small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-8">

            <div class="row">
                <div class="col-sm-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>小区本月缴费排行</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <table class="table table-hover no-margins">
                                <thead>
                                <tr>
                                    <th>小区</th>
                                    <th>排名</th>
                                    <th>已缴金额</th>
                                    <th>缴费率</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($community_amount as $k =>$v)
                                <tr>
                                    <td><small>{{$v['name']}}</small>
                                    </td>
                                    <td> {{$k+1}}</td>
                                    <td>{{$v['amount']}}元</td>
                                    <td class="text-navy"> <i class="fa fa-bolt"></i> {{$v['rate']}}%</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>待办事项(功能待开发中)</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <ul class="todo-list m-t small-list ui-sortable">
                                <li>
                                    <a href="widgets.html#" class="check-link"><i class="fa fa-check-square"></i> </a>
                                    <span class="m-l-xs todo-completed">开会</span>

                                </li>
                                <li>
                                    <a href="widgets.html#" class="check-link"><i class="fa fa-check-square"></i> </a>
                                    <span class="m-l-xs  todo-completed">项目开发</span>

                                </li>
                                <li>
                                    <a href="widgets.html#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">修改bug</span>
                                    <small class="label label-primary"><i class="fa fa-clock-o"></i> 1小时</small>
                                </li>
                                <li>
                                    <a href="widgets.html#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">修改bug</span>
                                    <small class="label label-primary"><i class="fa fa-clock-o"></i> 1小时</small>
                                </li>
                                <li>
                                    <a href="widgets.html#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">修改bug</span>
                                    <small class="label label-primary"><i class="fa fa-clock-o"></i> 1小时</small>
                                </li>
                                <li>
                                    <a href="widgets.html#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">修改bug</span>
                                    <small class="label label-primary"><i class="fa fa-clock-o"></i> 1小时</small>
                                </li>
                                <li>
                                    <a href="widgets.html#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">修改bug</span>
                                    <small class="label label-primary"><i class="fa fa-clock-o"></i> 1小时</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('/adminui/js/jquery.min.js?v=2.1.4')}}"></script>
<script src="{{asset('/adminui/js/bootstrap.min.js?v=3.3.5')}}"></script>
<script src="{{asset('/adminui/js/plugins/flot/jquery.flot.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/flot/jquery.flot.tooltip.min.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/flot/jquery.flot.spline.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/flot/jquery.flot.resize.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/flot/jquery.flot.pie.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/flot/jquery.flot.symbol.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/peity/jquery.peity.min.js')}}"></script>
<script src="{{asset('/adminui/js/demo/peity-demo.min.js')}}"></script>
<script src="{{asset('/adminui/js/content.min.js?v=1.0.0')}}"></script>
<script src="{{asset('/adminui/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/easypiechart/jquery.easypiechart.js')}}"></script>
<script src="{{asset('/adminui/js/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
<script src="{{asset('/adminui/js/demo/sparkline-demo.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $(".chart").easyPieChart({
            barColor:"#f8ac59",scaleLength:5,lineWidth:4,size:80
        });
        $(".chart2").easyPieChart({
            barColor:"#1c84c6",scaleLength:5,lineWidth:4,size:80
        });
          data_1=[];
          data_2=[];
          data_3=[];
        $.ajax({
        url : "{{url('merchant/bill_month')}}",
        data:{_token:"{{csrf_token()}}"},
        async : false,
        type : "POST",
        dataType : 'json',
        success : function (result){
            if(result.success){
                data_1=result.data1;
                data_2=result.data2;
                data_3=result.data3;
            }
        }
        });
        var data2=getdata(data_2);
        var data3=getdata(data_1);
        var dataset=[{
            label:"月账单总金额",
            data:data3,
            color:"#1ab394",
            bars:{show:true,align:"center",barWidth:24*60*60*600*30,lineWidth:0
            }},
            {
                label:"已缴金额",
                data:data2,
                yaxis:2,
                color:"#464f88",
                lines:{
                    lineWidth:1,show:true,fill:true,
                    fillColor:{
                        colors:[{opacity:0.2},{opacity:0.2}]
                    }
                },
                splines:{
                    show:false,
                    tension:0.6,
                    lineWidth:1,
                    fill:0.1},
            }];
        var options={
            xaxis:{
                mode:"time",
                tickSize:[1,"month"],
                timeformat: "%y年%m月",
                tickLength:0,
                axisLabel:"Date",
                axisLabelUseCanvas:true,
                axisLabelFontSizePixels:12,
                axisLabelFontFamily:"Arial",
                axisLabelPadding:10,
                color:"#838383"
            },
            yaxes:[{
                position:"left",
                max:data_3,
                color:"#838383",
                axisLabelUseCanvas:true,
                axisLabelFontSizePixels:12,
                axisLabelFontFamily:"Arial",
                axisLabelPadding:3
            },{
                position:"right",
                max:data_3,
                clolor:"#838383",
                axisLabelUseCanvas:true,
                axisLabelFontSizePixels:12,
                axisLabelFontFamily:" Arial",
                axisLabelPadding:67}],
            legend:{
                noColumns:1,
                labelBoxBorderColor:"#000000",
                position:"nw"
            },
            grid:{
                hoverable:false,
                borderWidth:0,
                color:"#838383"}
        };
        function getdata(arr) {
            var data=new Date();
            var resarr=[];
            for(var i=0;i<12;i++){
                if(i>0){
                    data.setMonth(data.getMonth()-1);
                }
                console.log(data.getFullYear(),data.getMonth()+1);
                resarr[i]=[gd(data.getFullYear(),data.getMonth()+1),arr[i]];
            }
            return resarr;
        }
        function gd(year,month){
            return new Date(year,month-1).getTime()
        }
        var previousPoint=null,previousLabel=null;
        $.plot($("#flot-dashboard-chart"),dataset,options);
        var mapData={"US":298,"SA":200,"DE":220,"FR":540,"CN":120,"AU":760,"BR":550,"IN":200,"GB":120,};
        $("#world-map").vectorMap({
            map:"world_mill_en",backgroundColor:"transparent",
            regionStyle:{
                initial:{
                    fill:"#e4e4e4","fill-opacity":0.9,
                    stroke:"none","stroke-width":0,"stroke-opacity":0
                }
            },
            series:{
                regions:[{
                    values:mapData,
                    scale:["#1ab394","#22d6b1"],
                    normalizeFunction:"polynomial"}]
            },
        })});
</script>
</body>

</html>