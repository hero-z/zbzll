@extends("layouts.merchantcontent")
@section("title","小区管理")
@section("content")

    {{--遮罩层--}}
    <div id="mask" class="mask"></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    @permission('createCommunity')
                    <div style="display: block;float: left;">
                        <button  id="add-factor" type="button" onclick="ShowDiv('add_community','mask')"
                                 class="btn btn-outline btn-success">添加小区
                        </button>
                    </div>
                    @endpermission
                </div>
                <div class="ibox-content">

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>小区名称</th>
                            <th>结算账户</th>
                            <th>小区地址</th>
                            <th>服务热线</th>
                            <th>小区上线状态</th>
                            <th>小区服务状态</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($communityInfo)
                            @foreach($communityInfo as $v )
                                <tr class="gradeA">
                                    <td>{{$v->community_name}}</td>
                                    <td>{{$v->account}}</td>
                                    <td>{{$v->province}}{{$v->city}}{{$v->district}}{{$v->community_address}}</td>
                                    <td>{{$v->hotline}}</td>
                                    <td>
                                        @if($v->alipay_status=='NONE')
                                            未同步到支付宝
                                        @endif
                                        @if($v->alipay_status=="PENDING_ONLINE")
                                            待上线
                                        @endif
                                            @if($v->alipay_status=="OFFLINE")
                                                下线中
                                            @endif
                                        @if($v->alipay_status=='ONLINE')
                                            已上线
                                        @endif
                                    </td>
                                    <td>
                                        @if($v->basicservice_status=='NONE')
                                            未同步到支付宝
                                        @endif
                                        @if($v->basicservice_status=="PENDING_ONLINE")
                                            待上线
                                        @endif
                                        @if($v->basicservice_status=="OFFLINE")
                                            下线中
                                        @endif
                                        @if($v->basicservice_status=='ONLINE')
                                            上线中
                                        @endif
                                    </td>
                                    <td>{{$v->created_at}}</td>
                                    <td class="center">
                                        @if($v->basicservice_status!='NONE')
                                        @permission('codeImage')
                                        <button type="button" onclick='CodeImage("{{$v->community_id}}")'
                                                class="btn jurisdiction btn-outline btn-warning btn-sm">测试支付码
                                        </button>
                                        @endpermission
                                        @endif
                                        @permission("uploadCommunity")
                                        @if($v->alipay_status!='ONLINE'&&$v->alipay_status!='PENDING_ONLINE'&&$v->alipay_status!="OFFLINE")
                                        <button type="button" onclick='uploadCommunity("{{$v->id}}")'
                                                class="btn  btn-outline btn-info btn-sm">同步支付宝
                                        </button>
                                        @endif
                                        @endpermission
                                        @permission("initializeBasicService")
                                        @if($v->alipay_status=='ONLINE'||$v->alipay_status=='PENDING_ONLINE')
                                        <button type="button" onclick="initializeBasicService('{{$v->id}}')"
                                                class="btn jurisdiction btn-outline btn-primary btn-sm">初始化服务
                                        </button>
                                        @endif
                                        @endpermission
                                        @permission("editCommunity")
                                        <button type="button" onclick="ShowDiv('amend_community','mask');getCommunity({{$v->id}})"
                                                class="btn  btn-outline btn-success btn-sm">编辑
                                        </button>
                                        @endpermission
                                        @permission('delCommunity')
                                        @if($v->alipay_status=="NONE")
                                        <button type="button" onclick='del("{{$v->id}}")'
                                                class="btn btn-outline btn-danger btn-sm">删除
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
                @if(isset($communityInfo)&&!$communityInfo->isEmpty())
                    {{$communityInfo->render()}}
                @endif
            </div>
        </div>
    </div>
    {{--添加小区--}}
    <div id="add_community" class="ant-modal" style="width: 790px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('add_community','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">添加小区</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="community_name" name="community_name" placeholder="请输入小区名称"
                                       class="input sky ant-input ant-input-lg"  required="required">
                                <span style="color:red;font-size:12px;display: none" id="span1" class="span">*请输入小区名称</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">地区</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <div id="region">
                                    <select id="province" class="form-control select_c" name="province" onchange="doProvAndCityRelation();">　
                                        <option id="choosePro" value="" >请选择省份</option>
                                    </select>

                                    <select id="citys" class="form-control select_c" name="city" onchange="doCityAndCountyRelation();">
                                        　　
                                        <option id='chooseCity' value=''>请选择城市</option>
                                    </select>

                                    <select id="county" class="form-control select_c" name="conty" >
                                        　
                                        <option id='chooseCounty' value=''>请选择区</option>
                                    </select>
                                </div>
                                <span style="color:red;font-size:12px;display: none" id="span2" class="span">*请选择完整的省市区信息</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">具体地址</label>
                        </div>
                        <div class="ant-col-15 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="community_address" name="community_address" placeholder="请输入具体地址"
                                       class="input ant-input ant-input-lg ">
                                <span style="color:red;font-size:12px;display: none" id="span3" class="span">*请输入具体地址</span>
                            </div>

                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">经度纬度</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input style="width: 300px;" msg="经度纬度" required="required" placeholder="经度纬度；最长15位字符（包括小数点）" class="input form-control form-ant-input ant-input-lg" name="community_locations" id="community_locations" type="text">
                                <span style="color:red;font-size:12px;display: none" id="span4" class="span">*请输入经纬度</span>
                                <a href="http://lbs.amap.com/console/show/picker" target="_blank">经纬度查询</a>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区热线</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="hotline" name="hotline" placeholder="请输入热线电话"
                                       class="input ant-input ant-input-lg" required="required">
                                <span style="color:red;font-size:12px;display: none" id="span5">*请输入小区热线</span>

                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">结算账号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="account" name="account" placeholder="请输入物业签约支付宝账号"
                                       class="input ant-input ant-input-lg" required="required">
                                <span style="color:red;font-size:12px;display: none" id="span6">*请输入物业签约支付宝账号</span>
                            </div>
                        </div>
                    </div>


                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="community_keep" ><span>保存</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--编辑小区信息--}}
    {{--编辑小区信息--}}
    <div id="amend_community" class="ant-modal" style="width: 790px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('amend_community','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">编辑小区信息</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">小区名称</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_name" name="amend_name" placeholder=""
                                       class="sky ant-input ant-input-lg"  value="">
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">地区</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <div id="region">
                                    <select id="provincea" class="form-control select_c" name="provincea" onchange="doProvAndCity();">　
                                        <option id="get_province" value="-1" >请选择省份</option>
                                    </select>

                                    <select id="city" class="form-control select_c" name="city" onchange="doCityAndCounty();">
                                        　　
                                        <option id='get_city' value="-1">请选择城市</option>
                                    </select>

                                    <select id="district" class="form-control select_c" name="district" >
                                        　
                                        <option id='get_district' value="-1">请选择区</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">具体地址</label>
                        </div>
                        <div class="ant-col-15 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_address" name="amend_address" placeholder=""
                                       class="ant-input ant-input-lg " value="">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="out_community_id" id="out_community_id" value="">
                    <input type="hidden" name="alipay_status" id="alipay_status" value="">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">经纬度</label>
                        </div>
                        <div class="ant-col-6 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input  msg="经度纬度" value='' required="required" placeholder="经度纬度；最长15位字符（包括小数点）" class="form-control form-ant-input ant-input-lg" name="longitudes" id="longitudes" type="text">
                                <a href="http://lbs.amap.com/console/show/picker" target="_blank">经纬度查询</a>
                            </div>
                        </div>
                        <div class="ant-col-4 ant-form-item-label" id="ali1" style="display: none">
                            <label class="ant-form-item-required">支付宝状态</label>
                        </div>
                        <div class="ant-col-2 ant-form-item-control-wrapper" id="ali2" style="display: none">
                            <div class="ant-form-item-control ">
                                <div id="region">
                                    <select id="status" class="form-control select_c" name="provincea" onchange="doProvAndCity();">
                                        <option  value="OFFLINE" >下线</option>
                                        <option  value="ONLINE">上线</option>
                                        <option  value="PENDING_ONLINE">待上线</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">热线电话</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_hotlines" name="amend_hotlines" placeholder=""
                                       class="ant-input ant-input-lg" value="">
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">结算账号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="amend_account" name="amend_account" placeholder="请输入结算账号"
                                       class="ant-input ant-input-lg" value="">
                            </div>
                        </div>
                    </div>


                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="amend_submit" style="margin-right: 15px;"><span>确认修改</span>
                                    </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--初始化小区服务--}}
    <div id="initial_state" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('initial_state','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">初始化小区状态</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label class="ant-form-item-required">状态</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select id="basicservice_status" class="form-control select_c" >　
                                    <option id="on_line" value="ONLINE" >上线</option>
                                    <option id="off_line" value="OFFLINE" >下线</option>
                                </select>
                                <input type="hidden">

                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="initial_keep"><span>提交</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--收款码--}}
    <div id="code" class="ant-modal" style="width: 320px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('initial_state','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header" style="border:none">
                <div class="ant-modal-title"></div>
            </div>
            <div class="ant-modal-body" style="text-align: center">
                <div id="img" >

                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('js')
    <script type="text/javascript" src="{{asset('/adminui/js/jquery.qrcode.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            render();
            function render() {
                $('body').css('overflow-y','scroll');
            }
        });
        //删除小区
        function del(id) {
            layer.confirm('确定删除吗?删除小区后将影响其所属房屋账单等所有操作!', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/delcommunity')}}", {_token: "{{csrf_token()}}", id: id },
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
        //同步到支付宝
        function uploadCommunity(id){
            layer.confirm('确定同步小区信息到支付宝吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('merchant/uploadcommunity')}}", {_token: "{{csrf_token()}}", id: id },
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
        //添加小区
        $("#community_keep").click(function(){
            var province_code=$("#province").val();
            var city_code=$("#citys").val();
            var district_code=$("#county").val();
            $.post("{{url('merchant/createcommunity')}}", {_token: "{{csrf_token()}}",
                    community_name: $('#community_name').val(),
                    province_code:province_code,
                    province:FindCityByCode(cityJson,province_code),
                    city_code:city_code,
                    city:FindCityByCode(cityJson,city_code),
                    district_code:district_code,
                    district:FindCityByCode(cityJson,district_code),
                    community_address:$('#community_address').val(),
                    community_locations:$("#community_locations").val(),
                    hotline:$("#hotline").val(),
                    account:$("#account").val()
                },
                function (data) {
                    if(!data.success){
                        if(data.community_name){
                            document.getElementById('span1').style.display='block';
                        }else{
                            document.getElementById('span1').style.display='none';
                        }
                        if(data.rovince||data.city||data.district){
                            document.getElementById('span2').style.display='block';
                        }else{
                            document.getElementById('span2').style.display='none';
                        }
                        if(data.community_address){
                            document.getElementById('span3').style.display='block';
                        }else{
                            document.getElementById('span3').style.display='none';
                        }
                        if(data.community_locations){
                            document.getElementById('span4').style.display='block';
                        }else{
                            document.getElementById('span4').style.display='none';
                        }
                        if(data.hotline){
                            document.getElementById('span5').style.display='block';
                        }else{
                            document.getElementById('span5').style.display='none';
                        }
                        if(data.account){
                            document.getElementById('span6').style.display='block';
                        }else{
                            document.getElementById('span6').style.display='none';
                        }
                    }else{
                        if (data.success) {
                            layer.msg(data.msg,{time:500});
                            setTimeout(function(){window.location.reload()},500);
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }

                }, 'json');
        });
        //获取小区信息
        function getCommunity(id) {
            $.post("{{url('merchant/getcommunity')}}", {_token: "{{csrf_token()}}",
                    id: id
                },
                function (data) {
                    if (data.success) {
                        $("#amend_name").val(data.data.community_name);
                        $("#amend_account").val(data.data.account);
                        $('#amend_address').val(data.data.community_address);
                        $("#amend_hotlines").val(data.data.hotline);
                        $("#out_community_id").val(data.data.out_community_id);
                        $("#longitudes").val(data.data.community_locations.replace('|',','));
                        $("#get_province").text(data.data.province);
                        $("#get_city").text(data.data.city);
                        $('#alipay_status').val(data.data.alipay_status);
                        if(data.data.alipay_status!='NONE'){
                            document.getElementById('ali1').style.display='block';
                            document.getElementById('ali2').style.display='block' ;
                            $("#status").val(data.data.alipay_status);
                        }
                        $("#get_district").text(data.data.district);
                    } else {
                        layer.msg(data.msg);
                    }
                }, 'json');
        };
        //修改小区信息
        $('#amend_submit').click(function () {
            layer.confirm('确定修改吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                var out_community_id=$("#out_community_id").val();
                var account=$('#amend_account').val();
                var community_name=$("#amend_name").val();
                var community_address=$("#amend_address").val();
                var hotline=$("#amend_hotlines").val();
                var community_locations=$("#longitudes").val().replace(',','|');
                var province_code=$("#provincea").val();
                var city_code=$('#city').val();
                var district_code=$('#district').val();
                var province=FindCityByCode(cityJson,province_code);
                var city=FindCityByCode(cityJson,city_code);
                var district=FindCityByCode(cityJson,district_code);
                var alipay_status=$('#alipay_status').val();
                var status=$("#status").val();
                $.post("{{url('merchant/editcommunity')}}", {_token: "{{csrf_token()}}", out_community_id: out_community_id,
                        account:account,community_name:community_name,community_address:community_address,hotline:hotline,community_locations:community_locations,
                        province_code:province_code,city_code:city_code,district_code:district_code,province:province,city:city,district:district,alipay_status:alipay_status,
                        status:status
                    },
                    function (data) {
                        if (data.success) {
                            layer.msg(data.msg,{time:500});
                            setTimeout(function(){window.location.reload()},500);
                        } else {
                            layer.msg(data.msg,{time:2000});

                        }
                    }, 'json');
            }, function () {

            })
        });
        //初始化小区服务
        function initializeBasicService(id){
            var id=id;
            var basicservice_status=$("#basicservice_status").val();
            ShowDiv('initial_state','mask');
            $("#initial_keep").click(function(){
                layer.confirm('确定初始化小区服务吗?', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.post("{{url('merchant/initializebasicservice')}}", {_token: "{{csrf_token()}}", id: id,basicservice_status:basicservice_status },
                        function (data) {
                            if (data.success) {
                                layer.msg(data.msg,{time:500});
                                setTimeout(function(){window.location.reload()},500);
                            } else {
                                layer.msg(data.msg,{time:5000});

                            }
                        }, 'json');

                });
            })

        }
        //测试支付二维码
        function CodeImage(community_id){
            $.post("{{url('merchant/getcode')}}", {_token: "{{csrf_token()}}", community_id: community_id },
                function (data) {
                    if (data.success) {
                        ShowDiv("code","mask");
                        img=data.msg;
                        $('#img').qrcode(img);
                    } else {
                        layer.msg(data.msg,{time:5000});
                        CloseDiv('code','mask');
                    }
                }, 'json');
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
        //获取省市区信息
        function FindCityByCode(mapObj, code) {
            for (var i = 0; i < mapObj.length; i++) {
                if (mapObj[i].item_code == code) {
                    return mapObj[i].item_name;
                }
            }
        }
        $("#provincea").click(function () {
            $('#get_province').text("请选择您所在省份");
            $("#get_city").text("请选择您所在城市");
            $("#get_district").text("请选择您所在区/县");
        });
        $(".input").each(function() {
            $(this).click(function () {
                $(this).css({"border": "1px solid #d9d9d9"});
                $(this).next().hide()
            });
        });
    </script>
@endsection

