@extends("layouts.admincontentpublic")
@section("title","物业公司管理")
@section('css')
{{--    <link href="{{asset('/adminui/js/plugins/fancybox/jquery.fancybox.css')}}" rel="stylesheet">--}}
    {{--<link href="{{asset('upload_imgs/css/index.css')}}" type="text/css" rel="stylesheet"/>--}}
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
    <div id="mask" class="mask"></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div style="display: block;float: left;">
                    </div>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        {{--<a class="close-link">--}}
                        {{--<i class="fa fa-times"></i>--}}
                        {{--</a>--}}
                    </div>
                </div>
                <div class="ibox-content">
                    <form action="{{url('admin/companysinfo')}}" method="POST">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-sm-8 m-b-xs"></div>
                            <div class="col-sm-2 m-b-xs">
                                <select name="companystatus" class="input-sm form-control input-s-sm inline">
                                    <option value="0">请选择审核状态</option>
                                    <option value="1" @if(isset($companystatus)&&$companystatus==1)selected @endif>已通过</option>
                                    <option value="2" @if(isset($companystatus)&&$companystatus==2)selected @endif>已拒绝</option>
                                    <option value="3" @if(isset($companystatus)&&$companystatus==3)selected @endif>待审核</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" value="@if(isset($searchname)){{$searchname}} @endif" name="searchname" placeholder="请输入物业公司名称" class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        @if(Auth::guard('admin')->user()->hasRole('root'))
                            <thead>
                            <tr>
                                <th >物业公司名称</th>
                                <th >联系人</th>
                                <th >联系方式</th>
                                <th >地址</th>
                                <th >授权人</th>
                                <th >审核状态</th>
                                <th >授权AppId</th>
                                <th >最新授权时间</th>
                                <th >操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($company)&&!$company->isEmpty())
                                @foreach($company as $v )
                                    <tr class="gradeA">
                                        <td>{{$v->company_name}}</td>
                                        <td>{{$v->name}}</td>
                                        <td>{{$v->phone}}</td>
                                        <td>{{$v->province.$v->city.$v->district.$v->address}}</td>
                                        <td>{{$v->admin_name}}</td>
                                        <td>
                                            @switch($v->status)
                                                @case(0)
                                                未审核
                                                @break
                                                @case(1)
                                                启用中
                                                @break
                                                @case(2)
                                                已关闭
                                                @break
                                                @default
                                                未审核
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{$v->auth_app_id}}</td>
                                        <td>{{$v->updated_at}}</td>
                                        <td class="center">
                                            @if($v->status==0)
                                                @permission('CompanyCheck')
                                                <button type="button" onclick="changestatus('{{$v->id}}',1)"
                                                        class="btn jurisdiction btn-outline btn-success">通过审核
                                                </button>
                                                @endpermission
                                                <button type="button" onclick='del("{{$v->id}}")'
                                                        class="btn btn-outline btn-danger">删除
                                                </button>
                                            @elseif($v->status==1)
                                                @permission('CompanyClose')
                                                <button type="button" onclick="changestatus('{{$v->id}}',2)"
                                                        class="btn jurisdiction btn-outline btn-danger">关闭服务
                                                </button>
                                                @endpermission
                                            @elseif($v->status==2)
                                                <button type="button" onclick="changestatus('{{$v->id}}',1)"
                                                        class="btn jurisdiction btn-outline btn-primary">开启服务
                                                </button>
                                            @endif
                                                @role('root')
                                                <button  id="add-factor" type="button" onclick="ShowDiv('changeOwner_box','mask');setdetail({{$v->id.',"'.$v->admin_name.'"'}});getagents({{$v->admin_id}})"
                                                         class="btn btn-outline btn-success">变更授权人
                                                </button>
                                                @endrole
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                没有数据
                            @endif
                            </tbody>

                            <tfoot></tfoot>
                        @else
                            <thead>
                            <tr>
                                <th >物业公司名称</th>
                                <th >联系人</th>
                                <th >联系方式</th>
                                <th >地址</th>
                                <th >审核状态</th>
                                <th >授权AppId</th>
                                <th >最新授权时间</th>
                                <th >操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($company)&&!$company->isEmpty())
                                @foreach($company as $v )
                                    <tr class="gradeA">
                                        <td>{{$v->company_name}}</td>
                                        <td>{{$v->name}}</td>
                                        <td>{{$v->phone}}</td>
                                        <td>{{$v->province.$v->city.$v->district.$v->address}}</td>
                                        <td>
                                            @switch($v->status)
                                                @case(0)
                                                未审核
                                                @break
                                                @case(1)
                                                启用中
                                                @break
                                                @case(2)
                                                已关闭
                                                @break
                                                @default
                                                未审核
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{$v->auth_app_id}}</td>
                                        <td>{{$v->updated_at}}</td>
                                        <td class="center">
                                            @if($v->status==0)
                                                @permission('CompanyCheck')
                                                <button type="button" onclick="changestatus('{{$v->id}}',1)"
                                                        class="btn jurisdiction btn-outline btn-success">通过审核
                                                </button>
                                                @endpermission
                                                <button type="button" onclick='del("{{$v->id}}")'
                                                        class="btn btn-outline btn-danger">删除
                                                </button>
                                            @elseif($v->status==1)
                                                @permission('CompanyClose')
                                                <button type="button" onclick="changestatus('{{$v->id}}',2)"
                                                        class="btn jurisdiction btn-outline btn-danger">关闭服务
                                                </button>
                                                @endpermission
                                            @elseif($v->status==2)
                                                <button type="button"  title="当前功能不可用,请联系管理员启用服务"
                                                      disabled  class="btn jurisdiction btn-outline btn-primary">开启服务
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                            @else
                                没有数据
                            @endif
                            </tfoot>
                        @endif
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
                @if(isset($company)&&$company)
                    {{$company->appends(compact('companystatus','searchname'))->render()}}
                @endif
            </div>
        </div>
    </div>
    <div id="changeOwner_box" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button  class="ant-modal-close"  onclick="CloseDiv('changeOwner_box','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >变更授权人</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" id="company_id">
                    <div class="ant-row ant-form-item">
                        <div id="ibox-content"  class="" style="padding-left: 30px;">
                            <label class="col-sm-3 control-label">当前授权人:</label>
                            <div class="col-sm-6" style="text-align: center">
                                <label id="targetagent" class="col-sm-12 control-label">无</label>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div id="ibox-content"  class="" style="padding-left: 30px;">
                            <label class="col-sm-3 control-label">输入登录密码:</label>
                            <input type="text" style="display: none;">
                            <input type="password" style="display: none;">
                            <div class="col-sm-6 ant-form-item-control ">
                                <input type="password" autocomplete="off" class="input ant-input ant-input-lg" id="password" name="password" placeholder="请输入登录密码确保是本人操作">
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div id="ibox-content"  class="" style="padding-left: 30px;">
                            <label class="col-sm-3 control-label">转移到</label>
                            <div class="col-sm-8">
                                    <select class="select" name="selectagent" id="selectagent"  {{--style="width:250px;height:200px;" --}}  data-am-selected="{searchBox: 1,maxHeight:200}" >

                                    </select>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" onclick="changeowner()"><span>确定</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    {{--<script src="{{asset('/adminui/js/chosen.jquery.js')}}"></script>--}}
    <script src="{{asset('/adminui/js/amazeui.chosen.js')}}"></script>
    <script>
        $(document).ready(function () {
            render();
            function render() {
                $('body').css('overflow-y','scroll');
                $('#selectagent').chosen();
            }

        });
        //修改状态
        function changestatus(companyid,status) {
            $.post("{{url('admin/changestatus')}}", {_token: "{{csrf_token()}}",
                    company_id: companyid,
                    status: status
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
        //删除物业公司
        function del(companyid) {
            layer.confirm('确定删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('admin/delcompany')}}", {_token: "{{csrf_token()}}", company_id: companyid },
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
        function setdetail(id,name) {
            $('#company_id').val(id);
            $('#targetagent').text(name);
        }
        //获取代理商列表
        function getagents(tig) {
            $.post("{{url('admin/getallagents')}}", {_token: "{{csrf_token()}}" },
                function (data) {
                    if (data.success) {
                        var admins=data.data;
                        for(admin in admins){
                            var display_name=' <option class=""'+(admin==tig?"disabled":'')+'  value='+ admin + '>'+admins[admin]+'</option>';
                            $(' #selectagent').append(display_name);
//                            $(' #changeOwner_box').find('select').append(display_name);
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');
        }
        function changeowner() {
            $.post("{{url('admin/changeowner')}}", {_token: "{{csrf_token()}}" ,
                    company_id:  $('#company_id').val(),
                    password:  $('#password').val(),
                    targetagent:  $('#selectagent').val()
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
        //弹出隐藏层
        function ShowDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='block';
            document.getElementById(bg_div).style.display='block' ;
            var bgdiv = document.getElementById(bg_div);
            bgdiv.style.width = document.body.scrollWidth;


        }
        //关闭弹出层
        function CloseDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='none';
            document.getElementById(bg_div).style.display='none';
            $('#selectagent >option').remove();
            $("#password").val('');
        }
        function CloseAll(show_div,bg_div){
            document.getElementById(show_div).style.display='none';
            document.getElementById(bg_div).style.display='none';
            $('#getUser_box').hide();
        }
        {{--修改用户密码--}}
        function show_eq() {
            $("#getUser_box").hide();
            $("#eq_box").show();
        }

        function CloseModel() {
            $('#model_box').hide()
        }
        $("#input").each(function() {
            $(this).click(function () {
                $(this).css({"border": "1px solid #d9d9d9"});
                $(this).next().hide()
            });
        });
    </script>
@endsection

