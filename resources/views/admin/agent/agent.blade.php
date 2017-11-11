@extends("layouts.admincontentpublic")
@section("title","代理商管理")
@section('css')
    <link href="{{asset('/adminui/js/plugins/fancybox/jquery.fancybox.css')}}" rel="stylesheet">
    <link href="{{asset('upload_imgs/css/index.css')}}" type="text/css" rel="stylesheet"/>
    <style>
        /*.ant-form-item-control{*/
        /*line-height: normal;*/
        /*}*/
        .fl{
            float: left;
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
                        <button  id="add-factor" type="button" onclick="ShowDiv('addUser_box','mask')"
                                 class="btn btn-outline btn-success">添加代理商
                        </button>
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
                    <form action="{{url('admin/agentsinfo')}}" method="POST">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-sm-8 m-b-xs"></div>
                            <div class="col-sm-2 m-b-xs">
                                <select name="agentstatus" class="input-sm form-control input-s-sm inline">
                                    <option value="0">请选择审核状态</option>
                                    <option value="1" @if(isset($agentstatus)&&$agentstatus==1)selected @endif>已通过</option>
                                    <option value="2" @if(isset($agentstatus)&&$agentstatus==2)selected @endif>已拒绝</option>
                                    <option value="3" @if(isset($agentstatus)&&$agentstatus==3)selected @endif>待审核</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" value="@if(isset($searchname)){{$searchname}} @endif" name="searchname" placeholder="请输入代理商名称" class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        @if(Auth::guard('admin')->user()->hasRole('root'))
                            <thead>
                            <tr>
                                <th >代理商ID</th>
                                <th >代理商名称</th>
                                <th >上级代理</th>
                                <th >邮箱</th>
                                <th >联系方式</th>
                                <th >审核状态</th>
                                <th >创建时间</th>
                                <th >操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($agents)&&!$agents->isEmpty())
                                @foreach($agents as $v )
                                    <tr class="gradeA">
                                        <td>{{$v->id}}</td>
                                        <td>{{$v->name}}</td>
                                        <td>
                                            @if($v->pid==0)
                                                无
                                            @else
                                                @if(isset($agentsarr)&&array_key_exists($v->pid,$agentsarr))
                                                    {{$agentsarr[$v->pid]}}
                                                @endif
                                            @endif</td>
                                        <td>{{$v->email}}</td>
                                        <td>{{$v->phone}}</td>
                                        <td>
                                            @switch($v->status)
                                                @case(0)
                                                待审核
                                                @break
                                                @case(1)
                                                已通过
                                                @break
                                                @case(2)
                                                已拒绝
                                                @break
                                                @default
                                                待审核
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{$v->created_at}}</td>
                                        <td class="center">
                                            <button type="button" onclick="ShowDiv('agentFile_box','mask');getagentinfo({{$v->id}});checkstatus({{$v->status}})"
                                                    class="btn jurisdiction btn-outline btn-success">@if($v->status==1)查看资料@else查看审核@endif
                                            </button>
                                            @if(Auth::guard('admin')->user()->hasRole('root')||Auth::guard('admin')->user()->id==$v->pid&&$v->status!=1)
                                                <button type="button" onclick="ShowDiv('agentUpdateFile_box','mask');getagentinfo({{$v->id}})"
                                                        class="btn jurisdiction btn-outline btn-success">更新资料
                                                </button>
                                            @endif
                                            @if(Auth::guard('admin')->user()->hasRole('root')&&$v->status==1)
                                                <button type="button" onclick="ShowDiv('agentSetRole_box','mask');getRoles({{$v->id}});setrole({{$v->id}})"
                                                        class="btn jurisdiction btn-outline btn-success">分配角色
                                                </button>
                                            @endif
                                            @if($v->status==2||$v->status==0)
                                                <button type="button" onclick='del("{{$v->id}}")'
                                                        class="btn btn-outline btn-danger">删除
                                                </button>
                                            @endif
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
                                <th >代理商ID</th>
                                <th >代理商名称</th>
                                <th >邮箱</th>
                                <th >联系方式</th>
                                <th >审核状态</th>
                                <th >创建时间</th>
                                <th >操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($agents)&&!$agents->isEmpty())
                                @foreach($agents as $v )
                                    <tr class="gradeA">
                                        <td>{{$v->id}}</td>
                                        <td>{{$v->name}}</td>
                                        <td>{{$v->email}}</td>
                                        <td>{{$v->phone}}</td>
                                        <td>
                                            @switch($v->status)
                                                @case(0)
                                                待审核
                                                @break
                                                @case(1)
                                                已通过
                                                @break
                                                @case(2)
                                                已拒绝
                                                @break
                                                @default
                                                待审核
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{$v->created_at}}</td>
                                        <td class="center">
                                            <button type="button" onclick="ShowDiv('agentFile_box','mask');getagentinfo({{$v->id}})"
                                                    class="btn jurisdiction btn-outline btn-success">查看资料
                                            </button>
                                            @if(Auth::guard('admin')->user()->hasRole('root')||Auth::guard('admin')->user()->id==$v->pid&&$v->status!=1)
                                                <button type="button" onclick="ShowDiv('agentUpdateFile_box','mask');getagentinfo({{$v->id}})"
                                                        class="btn jurisdiction btn-outline btn-success">更新资料
                                                </button>
                                            @endif
                                            @if($v->status==2||$v->status==0)
                                                <button type="button" onclick='del("{{$v->id}}")'
                                                        class="btn btn-outline btn-danger">删除
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
                @if(isset($agents)&&$agents)
                    {{$agents->appends(compact('searchname','agentstatus'))->render()}}
                @endif
            </div>
        </div>
    </div>
    {{--添加代理商  --}}
    <div id="addUser_box" class="ant-modal" style="width: 800px; transform-origin: 1054px 10px 0px;display: none;">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('addUser_box','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >添加代理商</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">

                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >姓名</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="username" name="username" placeholder="请输入姓名" class="input ant-input ant-input-lg" required>
                                <span id="nameerror" style="color: red;font-size: 12px;display: none">请输入姓名</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >手机号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="phone" name="phone" placeholder="请输入手机号" class="input ant-input ant-input-lg" required>
                                <span id="phoneerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >邮箱</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="email" name="email" placeholder="请输入邮箱" class="input ant-input ant-input-lg" required>
                                <span id="emailerror" style="color: red;font-size: 12px;display: none">请输入邮箱</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item" id="setagentrole">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >系统角色</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <select name="" id="role" class="select form-control" style="border-radius: 4px;" required>
                                    <option value="">请选择系统角色</option>
                                </select>
                                <span class="role_span" style="color: red;font-size: 12px; display:none;">请选择系统角色</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >初始密码</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="password" id="password" name="password" placeholder="请输入初始密码" class="input ant-input ant-input-lg" required>
                                <span id="passworderror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >确认密码</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="请再次输入密码" class="input ant-input ant-input-lg" required>
                                <span style="color: red;font-size: 12px;display: none">请再次输入密码</span>
                            </div>
                        </div>
                    </div>

                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="addUser"><span>确定添加</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--查看审核  --}}
    <div id="agentFile_box" class="ant-modal" style="width: 800px; transform-origin: 1054px 10px 0px;display: none;">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('agentFile_box','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >查看资料</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" id="agentid" value="">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >姓名</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="agentname" name="agentname" readonly class="input ant-input ant-input-lg" required>
                                <span id="agentnameerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >身份证号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="agentid_card_no" name="agentid_card_no" readonly class="input ant-input ant-input-lg" required>
                                <span id="agentid_card_noerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >银行卡号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="agentbank_card_no" name="agentbank_card_no" readonly class="input ant-input ant-input-lg" required>
                                <span id="agentbank_card_noerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="wrapper wrapper-content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-content">
                                        <h5>图片信息(身份证、银行卡)</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <div>
                                            <a class="fancybox" style="width: 214px; height: 160.5px;" id="agentid_card_front"  href="" title="身份证正面">
                                                <img alt="身份证正面"  style=" background-color: rgb(102, 102, 102); background-repeat: no-repeat; background-position: center center; background-size: contain;" src="" />
                                            </a>
                                            <a class="fancybox" style="width: 214px; height: 160.5px;" id="agentid_card_hold" href="" title="手持身份证">
                                                <img alt="手持身份证" style=" background-color: rgb(102, 102, 102); background-repeat: no-repeat; background-position: center center; background-size: contain;" src="" />
                                            </a>
                                            <a class="fancybox" style="width: 214px; height: 160.5px;" id="agentbank_card_front" href="" title="银行卡面">
                                                <img alt="银行卡面"  style=" background-color: rgb(102, 102, 102); background-repeat: no-repeat; background-position: center center; background-size: contain;" src="" />
                                            </a>
                                            <a class="fancybox" style="width: 214px; height: 160.5px;" id="agentbank_card_hold" href="" title="手持银行卡">
                                                <img alt="手持银行卡" style=" background-color: rgb(102, 102, 102); background-repeat: no-repeat; background-position: center center; background-size: contain;" src="" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @if(Auth::guard('admin')->user()->hasRole('root'))
                    <div id="ckeckAgent" class="ant-row ant-form-item modal-btn form-button" style=" text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" onclick="checkpass(1)" id=""><span>审核通过</span></button>
                                <button type="button" class="ant-btn ant-btn-danger ant-btn-lg" onclick="checkpass(2)" id=""><span>审核驳回</span></button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    {{--更新资料--}}
    <div id="agentUpdateFile_box" class="ant-modal" style="width: 800px; transform-origin: 1054px 10px 0px;display: none;">
        <div class="ant-modal-content">
            <button  class="ant-modal-close" onclick="CloseDiv('agentUpdateFile_box','mask')">
                <span class="ant-modal-close-x" ></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >更新资料</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" id="upagentid" value="">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >姓名</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="upagentname" name="upagentname" value="" class="input ant-input ant-input-lg" required>
                                <span id="upagentnameerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >身份证号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="upagentid_card_no" name="upagentid_card_no"  class="input ant-input ant-input-lg" required>
                                <span id="upagentid_card_noerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-6 ant-form-item-label">
                            <label  class="ant-form-item-required" >银行卡号</label>
                        </div>
                        <div class="ant-col-16 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" id="upagentbank_card_no" name="upagentbank_card_no"  class="input ant-input ant-input-lg" required>
                                <span id="upagentbank_card_noerror" style="color: red;font-size: 12px;display: none"></span>
                            </div>
                        </div>
                    </div>
                    <div class="wrapper wrapper-content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-content">
                                        <h5>重新上传图片信息(身份证正面、手持身份证、银行卡正反面)</h5>
                                    </div>
                                    <div class="img-box full">
                                        <section class=" img-section">
                                            <div class="z_photo upimg-div clear" >
                                                <!--<section class="up-section fl">
                                                        <span class="up-span"></span>
                                                        <img src="/img/buyerCenter/a2.png" class="close-upimg">
                                                        <img src="/img/buyerCenter/3c.png" class="type-upimg" alt="添加标签">
                                                        <img src="/img/test2.jpg" class="up-img">
                                                        <p class="img-namep"></p>
                                                        <input id="taglocation" name="taglocation" value="" type="hidden">
                                                        <input id="tags" name="tags" value="" type="hidden">
                                                    </section>-->
                                                <section class="z_file fl " >
                                                    <img src="{{asset('upload_imgs/img/a11.png')}}"  class="add-img">
                                                    <input type="file" name="file" id="file" class="file" value="" accept="image/jpg,image/jpeg,image/png,image/bmp" multiple />
                                                </section>
                                            </div>
                                        </section>
                                    </div>
                                    <aside class="mask works-mask">
                                        <div class="mask-content">
                                            <p class="del-p">您确定要删除作品图片吗？</p>
                                            <p class="check-p"><span class="del-com wsdel-ok">确定</span><span class="wsdel-no">取消</span></p>
                                        </div>
                                    </aside>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style=" text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" onclick="editagentinfo()" id=""><span>重新提交</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--角色分配--}}
    <div id="agentSetRole_box" class="ant-modal" style="width: 520px; transform-origin: 1054px 10px 0px;display: none">
        <div class="ant-modal-content">
            <button  class="ant-modal-close"  onclick="CloseDiv('agentSetRole_box','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title" >分配角色</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <input type="hidden" id="roleagentid">
                    <div class="ant-row ant-form-item">
                        <div id="ibox-content"  class="" style="padding-left: 30px;">
                            <label class="col-sm-3 control-label">当前角色:</label>
                            <div class="col-sm-6" style="text-align: center">
                                <label id="targetrole" class="col-sm-12 control-label">无</label>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div id="ibox-content"  class="" style="padding-left: 30px;">
                            <label class="col-sm-3 control-label">选择角色</label>
                            <div class="col-sm-6">
                                <select class="form-control m-b" id="selectrole">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button" style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" onclick="dosetrole()"><span>确定</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{asset('/adminui/js/plugins/peity/jquery.peity.min.js')}}"></script>
    <script src="{{asset('/adminui/js/plugins/fancybox/jquery.fancybox.js')}}"></script>
    <script src="{{asset('/adminui/js/content.min.js?v=1.0.0')}}"></script>
    <script>
        $(document).ready(function(){$(".fancybox").fancybox({openEffect:"none",closeEffect:"none"})});
    </script>
    <script type="text/javascript">
        //处理图片上传
        $('#file').takungaeImgup({
            formData:{
                _token : "{{csrf_token()}}",
                path:'agent/'
            },
            url:"{{url('admin/uploadimg')}}",
            success:function(data){
//                console.log(data);

            },
            error:function (err) {
                console.log(err)
            }
        });
    </script>
    <script>
        $('#add-factor').click(function () {
            /*添加用户时获取角色*/
            isroot="{{Auth::guard('admin')->user()->hasRole('root')}}";
            if(isroot){
                getRoles();
            }else{
                $('#setagentrole').css('display','none');
            }
        });
        var role_id=0;
        $("#role").change(function(){
            role_id=($(this).find("option:selected").val());
        });
        //添加用户
        $('#addUser').click(function () {
            $.post("{{url('admin/addagent')}}", {_token: "{{csrf_token()}}",
                    name: $('#username').val().trim(),
                    phone: $('#phone').val().trim(),
                    password: $('#password').val().trim(),
                    password_confirmation: $('#confirm_password').val().trim(),
                    email: $('#email').val().trim(),
                    role_id :role_id
                },
                function (data) {
                    if (data.success==1) {
                        layer.msg(data.msg,{time:500});
                        setTimeout(function(){window.location.reload()},500);
                    }else if(data.success==2){
                        var errors=data.msg;
                        var msgs='';
                        for( var error in errors){
                            var  obj=$('#'+error+'error');
                            obj.css('display','block');
                            for(var msg in errors[""+error]){
                                msgs+=errors[""+error][""+msg];
                            }
                            obj.text(msgs);
                            msgs='';
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});

                    }
                }, 'json');
        });
        function getRoles(id) {
            //获取角色
            $.post("{{url('admin/getroles')}}", {_token: "{{csrf_token()}}",agent_id:id},
                function (data) {
                    if (data.success) {
                        var roles=data.data;
                        var target=data.target;
                        if(target){
                            target=target.role_id;
                        }
                        for(var i=0;i<roles.length;i++){
                            var display_name=' <option class="role_id"  value='+ roles[i].id + '>'+roles[i].name+'</option>'
                            $(' #role').append(display_name);
                            $(' #agentSetRole_box').find('select').append(display_name);
                            if(target==roles[i].id){
                                $(' #targetrole').text(roles[i].name);
                            }
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');
        }
        function del(id) {
            layer.confirm('确定删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{url('admin/delagent')}}", {_token: "{{csrf_token()}}", id: id },
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
        //加载用户资料
        function getagentinfo(id) {
            $('#agentid').val(id);
            $('#upagentid').val(id);
            $.post("{{url('admin/getagentinfo')}}", {_token: "{{csrf_token()}}", id: id },
                function (data) {
                    if (data.success) {
                        var pathurl="{{url('/')}}";
                        var infos=data.data;
                        for( var key in infos){
                            var  obj=$('#'+'agent'+key);
                            var  upobj=$('#'+'upagent'+key);
                            if(key=='id'||key=='created_at'){
                                continue;
                            }else if(key=='id_card_front'||key=="id_card_back"||key=='id_card_hold'||key=='bank_card_hold'||key=='bank_card_front'){
                                obj.attr('href',infos[""+key]);
                                obj.find('img').attr('src',pathurl+infos[""+key]);
                            }else{
                                obj.val(infos[""+key]);
                                upobj.val(infos[""+key]);
                            }

                        }
                    } else {
                        layer.msg(data.msg,{time:2000});

                    }
                }, 'json');
        }
        //修改资料
        function editagentinfo() {
            var imgs=[];
            var array=$(".taglocation");//单引号 的name替换为相应的name
            for(var i=0;i<array.length;i++) {
                var value = $(array[i]).val();
                imgs.push(value);
            }
            $.post("{{url('admin/editagentinfo')}}", {_token: "{{csrf_token()}}",
                    images: imgs,
                    admin_id: $('#upagentid').val(),
                    name: $('#upagentname').val(),
                    id_card_no: $('#upagentid_card_no').val(),
                    bank_card_no: $('#upagentbank_card_no').val()
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
        //处理审核显示按钮
        function checkstatus(status) {
            var statusobj=$('#ckeckAgent');
            if(status==1){
                statusobj.css('display','none');
                statusobj.find('button').eq(1).css('display','');
            }else if(status==2){
                statusobj.css('display','block');
                statusobj.find('button').eq(1).css('display','none');
            }else{
                statusobj.css('display','block');
                statusobj.find('button').eq(1).css('display','');
            }
        }
        //审核
        function checkpass(status) {
            $.post("{{url('admin/checkagent')}}", {_token: "{{csrf_token()}}",
                    agent_id: $('#agentid').val(),
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
        //分配角色保存字段
        function setrole(agentid) {
            $('#roleagentid').val(agentid);
        }
        function dosetrole() {
            $.post("{{url('admin/setagentrole')}}", {_token: "{{csrf_token()}}",
                    agent_id: $('#roleagentid').val(),
                    role_id: $('#selectrole option:selected').val()
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
            $('.input').css({'border':'1px solid #d9d9d9'});
            $('.select').css({'border':'1px solid #d9d9d9'});
            $('.input').next().hide();
            $('#agentFile_box').find('img').attr('src','');
            $('#agentFile_box').find('input').val('');
            $('#agentUpdateFile_box').find('input').val('');
            $('.up-section').hide();
            $('.select').next().hide();
            $('.role_id').remove();
            $("#agentFile_box").find('input').val('');
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

