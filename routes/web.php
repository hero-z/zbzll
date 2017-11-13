<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Admin'],function ($router)
{
    $router->get('/', 'AdminHomeController@index');
});
Auth::routes();
//物业端登录提醒a
Route::view('/info', 'error',['error'=>'服务已经关闭或者还没通过审核,请联系服务商']);
Route::view('/check', 'merchant.check',['code_url'=>url('/admin/oauth?admin_id=1')]);
Route::view('/error', 'error',['error'=>'系统异常']);
Route::view('/checkoauth', 'error',['error'=>'您还没有授权!请联系南京往知来网络科技-18251828302或者13213075470']);
//用户端
Route::get('/home', 'HomeController@index')->name('home');
//服务商端
Route::group(['prefix' => 'admin','namespace' => 'Admin'],function ($router)
{
    $router->get('login', 'LoginController@showLoginForm');
    $router->post('login', 'LoginController@login')->name('admin.login');
    $router->get('logout', 'LoginController@logout');
});
//服务商端需要登录
Route::group(['prefix' => 'admin','namespace' => 'Admin','middleware'=>'auth.admin:admin'],function ($router)
{
    $router->get('/forceoauth', 'AdminHomeController@forceOauth');
    $router->post('/updateInfo', 'AppController@updateInfo')->name("updateInfo");
    $router->post('/appUpdateFile', 'AppController@appUpdateFile')->name("appUpdateFile");
    $router->get('adminindex',"AdminHomeController@adminIndex");
    //强制补全资料
    $router->any('auth_agent',function (){ return view('admin.agent.auth_agent');});
    //系统配置
    $router->any('setisvconfig','ConfigController@setIsvConfig');
    $router->get('logoindex',"LogoController@logoIndex");
    $router->post('setlogo','LogoController@setLogo');
    $router->any('uploadlogo',"UploadController@uploadLogo")->name('uploadlogo');
    //授权码
    $router->get('code',"OauthController@code");
    //角色权限管理
    $router->any('rolepermission','RolePermissionController@rolePermission');
    $router->post('delrole','RolePermissionController@delRole');
    $router->post('getroles','RolePermissionController@getRoles');
    $router->post('addrole','RolePermissionController@addRole');
    $router->post('getpermission','RolePermissionController@getPermission');
    $router->post('setrolepermission','RolePermissionController@setRolePermission');
    //员工管理
    $router->any('agentsinfo','AgentMangeController@agentsInfo');
    $router->post('addagent','AgentMangeController@addAgent');
    $router->post('delagent','AgentMangeController@delAgent');
    $router->post('getagentinfo','AgentMangeController@getAgentInfo');
    $router->post('editagentinfo','AgentMangeController@editAgentInfo');
    $router->post('checkagent','AgentMangeController@checkAgent');
    $router->post('setagentrole','AgentMangeController@setAgentRole');

    $router->any('me','AgentMangeController@getMe');
    $router->any('test','AgentMangeController@test');
    //上传图片
    $router->any('uploadimg','UploadFileController@uploadImg');
    //物业公司管理
    $router->any('companysinfo','CompanyManageController@companysInfo');
    $router->post('changestatus','CompanyManageController@changeStatus');
    $router->post('delcompany','CompanyManageController@delCompany');

});
//物业公司授权进件
Route::group(['prefix' => 'admin','namespace' => 'Admin','middleware'=>'merchant.oauth:merchantoauth'],function ($router)
{
    $router->get("oauth","OauthController@oauth");
    $router->any('callback',"OauthController@callback");
    $router->get('addcompany','OauthController@addCompany');
    $router->post('createcompany',"OauthController@createcompany");
});
//物业公司端
Route::group(['prefix' => 'admin','namespace' => 'Merchant'],function ($router)
{
    $router->get('oauthlogin', 'OauthLoginController@showLoginForm');
    $router->post('oauthlogin', 'OauthLoginController@login')->name('oauthmerchant.login');
    $router->get('oauthlogout', 'OauthLoginController@logout');
    $router->get("oauthregister","OauthRegisterController@showRegistrationForm")->name("oauthmerchant.register");
    $router->post("oauthregister","OauthRegisterController@register")->name('merchantoauth.register');
    $router->get('oauthhome', 'OauthHomeController@index');
});
//登录注册
Route::group(['prefix' => 'merchant','namespace' => 'Merchant'],function ($router)
{
    $router->get('login', 'LoginController@showLoginForm');
    $router->post('login', 'LoginController@login')->name('merchant.login');
    $router->get('logout', 'LoginController@logout');
    $router->get("register","RegisterController@showRegistrationForm")->name("register");
    $router->post("register","RegisterController@register")->name('merchant.register');
    $router->get('/home', 'MerchantHomeController@index');
});

//物业公司端需要登录
Route::group(['prefix' => 'merchant','namespace' => 'Merchant','middleware'=>'auth.merchant:merchant'],function ($router)
{
    $router->get('merchantindex',"MerchantHomeController@merchantIndex");
    //角色权限管理
    $router->get('rolepermission','RolePermissionController@rolePermission');
    $router->post('addrole','RolePermissionController@addRole');
    $router->post('getpermission','RolePermissionController@getPermission');
    $router->post('setrolepermission','RolePermissionController@setRolePermission');
    $router->post('delrole','RolePermissionController@delRole');
    //物业端员工管理
    $router->get('merchantinfo','MerchantController@merchantInfo');
    $router->post('addmerchant',"MerchantController@addMerchant");
    $router->post('getrole','MerchantController@getRole');
    $router->post('getcommunities','MerchantController@getCommunity');
    $router->post('assigncommunity','MerchantController@assignCommunity');
    $router->post('setrole','MerchantController@setRole');
    $router->post('getmerchantinfo',"MerchantController@getMerchantInfo");
    $router->post('updatemerchantinfo','MerchantController@updateMerchantInfo');
    $router->post('delmerchant','MerchantController@delMerchant');
    //小区管理
    $router->get("communityinfo","CommunityController@index")->name('communityInfo');
    $router->post('createcommunity',"CommunityController@createCommunity");
    $router->post('getcommunity',"CommunityController@getCommunity");
    $router->post("editcommunity","CommunityController@editCommunity");
    $router->post("delcommunity","CommunityController@delCommunity");
    $router->post('getcode',"CommunityController@getCode");
    //同步小区到支付宝
    $router->post('uploadcommunity',"CommunityController@uploadCommunity");
    //初始化小区服务
    $router->post('initializebasicservice','BasicServiceController@initializeBasicService');
    //小区楼宇管理
    $router->any('buildinginfo',"BuildingController@BuildingInfo");
    $router->post('createbuilding',"BuildingController@createBuilding");
    $router->post('getbuilding',"BuildingController@getBuilding");
    $router->post('editbuilding',"BuildingController@editBuilding");
    $router->post('deleteunit',"BuildingController@deleteUnit");
    $router->post('deletebuilding',"BuildingController@deleteBuilding");

    //房屋管理
    $router->get('roominfo',"RoomInfoController@roomInfo");
    $router->post('getroominfo',"RoomInfoController@getRoomInfo");
    $router->post('createroom','RoomInfoController@createRoom');
    //上传文件
    $router->any("upload","PublicController@upload")->name("upload");
    //房屋模板导出
    $router->get("roomExcel","ExcelController@roomExcel")->name("roomExcel");
    //批量导入房屋
    $router->post('createrooms',"RoomInfoController@createRooms");
    //同步房屋到支付宝
    $router->post('uploadroom','RoomInfoController@uploadRoom');
    $router->post('uploadrooms',"RoomInfoController@uploadRooms");
    $router->post('delroom',"RoomInfoController@delRoom");
    $router->post('delrooms',"RoomInfoController@delRooms");
    //住户管理
    $router->any('householdinfo',"HouseholdManageController@houseHoldInfo");
    $router->post('gethousehold',"HouseholdManageController@getHouseHold");
    $router->post('edithousehold',"HouseholdManageController@editHouseHold");
    $router->post('gethouse',"HouseholdManageController@getHouse");
    $router->post('deletehouse',"HouseholdManageController@deleteHouse");
    $router->post('edithouses',"HouseholdManageController@editHouses");
    //账单管理
    $router->get('billinfo',"BillController@billInfo");
    $router->post('addbill','BillController@addBill');
    $router->post('addbills','BillController@addBills');
    $router->get("billExcel","ExcelController@billExcel")->name("billExcel");
    $router->post('uploadbill','BillController@uploadBill');
    $router->post('uploadbills','BillController@uploadBills');
    $router->post("editlinebill","BillController@editLineBill");
    $router->get("linebillinfo","BillController@LineBillInfo");
    $router->get("questionbillinfo","BillController@questionBillInfo");
    $router->post("questionbillsubmit","BillController@questionBillSubmit");
    $router->post('checkbill',"BillController@CheckBill");
    $router->post('delcheckbill',"BillController@delCheckBill");
    $router->get('ceshi',"CommunityController@ceshi");
    //统计管理
    $router->any("billquery","StatisticalManageController@billQuerry");
    //系统设置
    $router->get('companylogo',"LogoController@companyLogo");
    $router->post('setcompanylogo',"LogoController@setCompanyLogo");
    $router->any('uploadlogo',"UploadController@uploadLogo")->name('uploadlogos');

});
//支付异步通知
Route::group(['namespace' =>'Merchant', 'prefix' => "merchant"], function () {
    Route::any("notify","NotifyController@notify")->name("notify");
});
