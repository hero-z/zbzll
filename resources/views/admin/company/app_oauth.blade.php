@extends('layouts.admincontentpublic')
@section('content')
    {{--<div style="text-align: center">--}}
        {{--<div class="ibox-title">--}}
            {{--<h5>第三方应用授权说明</h5>--}}
        {{--</div>--}}
      {{----}}
    {{--</div>--}}
    <div class="col-sm-6">
            {{--<div class="ibox-title">--}}
                {{--<h5>授权给支付宝</h5>--}}
            {{--</div>--}}
                <div class="well col-sm-12">
                    <div style="text-align: center" >
                        <div class="well">
                            <h3>第三方应用授权</h3>
                        </div>
                        <img id="img" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate($code_url)) !!} ">

                    </div>
                {{--<div class="well well-lg">--}}
                {{--<h3>--}}
                {{--开口碑店流程--}}
                {{--</h3> 1.商户扫描上面这个二维码授权 2.在口碑开店列表提交资料 3.口碑开店成功自动签约当面付 4.在门店列表生成收款码--}}
                {{--</div>--}}
                <div class="well">
                    <h4>
                        第三方应用授权说明:
                    </h4>
                    <h4>1.该二维码可用于代理商或员工发展物业公司,物业公司扫码授权后,该公司将自动归属于推广人员</h4>
                    <h4>2.物业公司账号需为企业账号,授权完成后,会有注册的操作,请物业公司务必填写真实信息.</h4>
                    <h4>3.物业公司如未签约支付宝相关业务,请扫右侧二维码,或点击右侧二维码下链接,进行签约授权</h4>
                </div>
                </div>
    </div>
    <div class="col-sm-6">
        {{--<div class="ibox-title">--}}
        {{--<h5>授权给支付宝</h5>--}}
        {{--</div>--}}
        <div class="well col-sm-12">
            <div style="text-align: center" >
                <div class="well">
                    <h3>物业公司授权支付宝</h3>
                </div>
                <img id="img" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate('https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id=2016062101539321&redirect_uri=https%3a%2f%2falivemng.alipay-eco.com%2fcpmerchantmng-web-home%2fsecondauth%2fauthcode')) !!} ">

            </div>
            {{--<div class="well well-lg">--}}
            {{--<h3>--}}
            {{--开口碑店流程--}}
            {{--</h3> 1.商户扫描上面这个二维码授权 2.在口碑开店列表提交资料 3.口碑开店成功自动签约当面付 4.在门店列表生成收款码--}}
            {{--</div>--}}
            <div class="well">
                <h4>
                  说明:
                </h4>
                <h4>1.第三方授权完成后,物业公司需要点击如下链接完成授权给支付宝下单等权限</h4>
                <h4>2.你可以选择点击如下链接完成授权,如果已经签约,可以直接登陆授权,如未签约,点击完成签约（<a href="https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id=2016062101539321&redirect_uri=https%3a%2f%2falivemng.alipay-eco.com%2fcpmerchantmng-web-home%2fsecondauth%2fauthcode"
                                                                     target="_blank">https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id=2016062101539321&redirect_uri=https%3a%2f%2falivemng.alipay-eco.com%2fcpmerchantmng-web-home%2fsecondauth%2fauthcode</a>）</h4>

            </div>
        </div>
    </div>

@endsection