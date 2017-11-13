@extends("layouts.merchantpublic")
@section("title","物业公司主页")
@section("content")

    <div id="wrapper">
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span><img alt="image" class="img-circle" src="@if($merchantLogo){{url($merchantLogo->logo1)}}@endif" style="width:100px;height:100px"/></span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                               <span class="block m-t-xs"><strong class="font-bold" style="color:rosybrown">{{Auth::guard("merchant")->user()->name}}</strong></span>
                                </span>
                            </a>
                        </div>
                        <div class="logo-element">H+
                        </div>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-home"></i>
                            <span class="nav-label">主页</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/merchantindex')}}" data-index="0">主页统计</a>
                            </li>
                        </ul>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/billquery')}}" data-index="0">账单统计</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-building"></i>
                            <span class="nav-label">小区管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route('communityInfo')}}" data-index="0">小区信息管理</a>
                            </li>
                        </ul>
                    </li>
                    @permission('buildingManage')
                    <li>
                        <a href="#">
                            <i class="glyphicon glyphicon-stats"></i>
                            <span class="nav-label">楼宇管理</span>
                            <span class=""></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/buildinginfo')}}" data-index="0">楼宇信息管理</a>
                            </li>
                        </ul>
                    </li>
                    @endpermission
                    <li>
                        <a href="#">
                            <i class="fa fa-bank"></i>
                            <span class="nav-label">房屋管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/roominfo')}}" data-index="0">房屋信息管理</a>
                            </li>
                        </ul>
                    </li>
                    @permission('householdManage')
                    <li>
                        <a href="#">
                            <i class="glyphicon glyphicon-user"></i>
                            <span class="nav-label">住户管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/householdinfo')}}" data-index="0">住户信息管理</a>
                            </li>
                        </ul>
                    </li>
                    @endpermission
                    <li>
                        <a href="#">
                            <i class="fa fa-book"></i>
                            <span class="nav-label">账单管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/billinfo')}}" data-index="0">物业费</a>
                            </li>
                        </ul>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/billinfo')}}" data-index="0">水费</a>
                            </li>
                        </ul>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/billinfo')}}" data-index="0">电费</a>
                            </li>
                        </ul>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/billinfo')}}" data-index="0">燃气费</a>
                            </li>
                        </ul>
                        @if(Auth::guard('merchant')->user()->hasRole('root'.Auth::guard('merchant')->user()->id))
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/linebillinfo')}}" data-index="0">线下结算审核</a>
                            </li>
                        </ul>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/questionbillinfo')}}" data-index="0">存疑账单管理</a>
                            </li>
                        </ul>
                        @endif
                    </li>
                    @if(Auth::guard('merchant')->user()->hasRole('root'.Auth::guard('merchant')->user()->id)||Auth::guard('merchant')->user()->can('merchantInfo'))
                    <li>
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span class="nav-label">员工管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('/merchant/merchantinfo')}}" data-index="0">员工信息管理</a>
                            </li>
                        </ul>
                        @if(Auth::guard('merchant')->user()->hasRole('root'.Auth::guard('merchant')->user()->id))
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('/merchant/rolepermission')}}" data-index="0">角色权限管理</a>
                            </li>
                        </ul>
                        @endif
                    </li>
                    @endif
                    <li>
                        <a href="#">
                            <i class="fa fa-wrench"></i>
                            <span class="nav-label">系统管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        @if(Auth::guard('merchant')->user()->hasRole('root'.Auth::guard('merchant')->user()->id))

                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{url('merchant/companylogo')}}" data-index="0">物业公司头像设置</a>
                            </li>
                        </ul>
                        @endif
                        {{--<ul class="nav nav-second-level">--}}
                            {{--<li>--}}
                                {{--<a class="J_menuItem" href="" data-index="0">推送设置</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    </li>
                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                        <form role="search" class="navbar-form-custom" method="post" action="search_results.html">
                            <div class="form-group">
                                <input type="text" placeholder="请输入您需要查找的内容 …" class="form-control" name="top-search" id="top-search">
                            </div>
                        </form>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown hidden-xs">
                            <a class="right-sidebar-toggle" aria-expanded="false">
                                <i class="fa fa-tasks"></i> 主题
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="row content-tabs">
                <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
                </button>
                <nav class="page-tabs J_menuTabs">
                    <div class="page-tabs-content">
                        <a href="javascript:;" class="active J_menuTab" data-id="index_v1.html">首页</a>
                    </div>
                </nav>
                <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
                </button>
                <div class="btn-group roll-nav roll-right">
                    <button class="dropdown J_tabClose" data-toggle="dropdown">关闭操作<span class="caret"></span>

                    </button>
                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                        <li class="J_tabShowActive"><a>定位当前选项卡</a>
                        </li>
                        <li class="divider"></li>
                        <li class="J_tabCloseAll"><a>关闭全部选项卡</a>
                        </li>
                        <li class="J_tabCloseOther"><a>关闭其他选项卡</a>
                        </li>
                    </ul>
                </div>
                <a href="{{url("merchant/logout")}}" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
            </div>
            <div class="row J_mainContent" id="content-main">
                {{--主页默认打开的页面的路由--}}
                <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="{{url('merchant/merchantindex')}}" frameborder="0" data-id="index_v2.html" seamless></iframe>
            </div>
            <div class="footer">
                <div class="pull-right">&copy; <a href="" target="_blank">往知来网络科技有限公司</a>
                </div>
            </div>
        </div>
        <!--右侧部分结束-->
        <!--右侧边栏开始-->
        <div id="right-sidebar">
            <div class="sidebar-container">

                <ul class="nav nav-tabs navs-3">

                    <li class="active">
                        <a data-toggle="tab" href="#tab-1">
                            <i class="fa fa-gear"></i> 主题
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="sidebar-title">
                            <h3> <i class="fa fa-comments-o"></i> 主题设置</h3>
                            <small><i class="fa fa-tim"></i> 你可以从这里选择和预览主题的布局和样式，这些设置会被保存在本地，下次打开的时候会直接应用这些设置。</small>
                        </div>
                        <div class="skin-setttings">
                            <div class="title">主题设置</div>
                            <div class="setings-item">
                                <span>收起左侧菜单</span>
                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="collapsemenu" class="onoffswitch-checkbox" id="collapsemenu">
                                        <label class="onoffswitch-label" for="collapsemenu">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="setings-item">
                                <span>固定顶部</span>

                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="fixednavbar" class="onoffswitch-checkbox" id="fixednavbar">
                                        <label class="onoffswitch-label" for="fixednavbar">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="setings-item">
                                <span>
                        固定宽度
                    </span>

                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="boxedlayout" class="onoffswitch-checkbox" id="boxedlayout">
                                        <label class="onoffswitch-label" for="boxedlayout">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="title">皮肤选择</div>
                            <div class="setings-item default-skin nb">
                                <span class="skin-name ">
                         <a href="#" class="s-skin-0">
                             默认皮肤
                         </a>
                    </span>
                            </div>
                            <div class="setings-item blue-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-1">
                            蓝色主题
                        </a>
                    </span>
                            </div>
                            <div class="setings-item yellow-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-3">
                            黄色/紫色主题
                        </a>
                    </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--右侧边栏结束-->
    </div>
@endsection

