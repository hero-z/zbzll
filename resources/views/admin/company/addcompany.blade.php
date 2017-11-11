<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>业务入驻</title>
    <link href="{{asset('adminui/js/jquery-weui.min.css')}}" rel="stylesheet">

    <style>
        html,body{
            width: 100%;
            height: 100%;
        }
        body{
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        *{
            box-sizing: border-box;
        }
        a{
            list-style-type: none;
        }
        .center_style{
            height: 100px;
            line-height: 100px;
            color: #333;
            font-size: 30px;
            text-align: left;

        }
        .main_box{
            margin: 25px;
        }
        .main_title{
            width: 100%;
            height: 90px;
            line-height: 90px;
            text-align: center;
            color: #333333;
            font-size: 34px;
            background-color: #fff;

        }
        .main_center{
            width: 100%;
            height: 300px;
            margin: 25px 0;
            padding-left: 25px;
            background-color: #fff;

        }
        .center_box{

            border-bottom: 1px solid #e7eaec;
        }
        .main_bottom{
            width: 100%;
            height: 250px;
            padding-left: 25px;
            background-color: #fff;
            margin-bottom: 150px;
        }
        .btn{
            display: inline-block;
            width: 100%;
            height: 86px;
            line-height: 86px;
            border-radius: 4px;
            text-align: center;
            font-size: 34px;
            color: #f5f5f5;
            background-color: #00aaef;

        }
        .input{
            height: 75%;
            line-height: 100px;
            margin-top: 20px;
            float: right;
            border: none;
            padding-right: 25px;
            width: 70%;
            text-align: right;
            outline:medium;
            font-size: 28px;
            color: #333333;

        }
        ::-webkit-input-placeholder {
            color:#999999;
            font-size: 28px;
        }

        .address_input{
            width: 100%;
            height: 70px;
            text-align: left;
            padding-right: 25px;
            outline:medium;
            border: none;
            font-size: 28px;
            color: #333333;
        }
        .arrow_right{
            background: url("{{asset('adminui/img/zhishijiantou.png')}}");
            width: 13px;
            height: 24px;
            display: inline-block;
            float: right;
            margin-top: 45px;
            margin-right: 25px;
        }
        .toolbar .picker-button{
            color: #1886fe;
            text-decoration: none;
        }
        .weui-picker-modal.weui-picker-modal-visible{
            height: 34rem;
        }
        .weui-picker-modal .picker-modal-inner{
            height: 35.8rem;
        }
        .weui-picker-modal .picker-items-col-wrapper{
            position: absolute;
            top: -14rem;
        }
        .city-picker .picker-items-col{
            max-width:11rem;
            padding-left: 60px;
        }


        .weui-picker-modal .picker-items{
            text-align: left;
            font-size: 1.7rem;
        }
        .weui-picker-modal .picker-item{
            height: 60px;
            line-height: 60px;
        }
        .toolbar .toolbar-inner{
            height: 3.5rem;
        }
        .toolbar, .toolbar .title{
            font-size: 1.5rem;

        }
        .weui-picker-modal .picker-center-highlight{
            height: 60px;
            top: 9.9%;
            border-bottom: 1px solid #e7eaec;
        }



        p{
            margin: 0;
        }
        .clock_box{
            width: 100%;
            height: 400px;
            line-height: 2.5;
            background-color: #fff;
            display: none;
        }
        .clock_center{
            width: 80%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 6%;
        }
        .await{
            font-size: 34px;
            font-weight:400;
            color: #000;
            border-radius: 50%;
        }
        .dispose{
            font-size: 30px;
            color:#999999;
        }
        .clock{
            width: 120px;
            height: 120px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
{{--YI--}}
<div class="main_box" id="main_box">
    <div class="main_title">物业公司入驻资料</div>
    <div class="main_center">
        <div class="center_box center_style">
            公司名称：
            <input id="company_name" class="input blur" type="text" placeholder="请输入物业公司名称">
        </div>
        <div class="center_box center_style">
            负责人：
            <input id="name" class="input blur" type="text" placeholder="请输入负责人姓名">

        </div>
        <div class=" center_style">
            手机号：
            <input id="phone" class="input blur" type="text" placeholder="请输入负责人手机号">

        </div>
    </div>
    <input type="hidden" value="{{$user_id}}" name="user_id" id="user_id">
    <div class="main_bottom">
        <div class="center_box center_style">
            所在地区：
            <i class="arrow_right"></i>
            <input onclick="hideKeyboard()" id="choose_address" class="input" type="text" placeholder="请选择">

        </div>
        <div class="address">
            <input style="" id="address" class="address_input" type="text" placeholder="请填写详细地址">

        </div>
    </div>

    <span class="btn" id="sure_save">确定保存</span>
</div>
{{--ER--}}
<div class="clock_box" id="clock_box">
    <div class="clock_center">
        <img class="clock" src="{{asset('/adminui/img/clock.jpg')}}" alt="">
        <p class="await">等待</p>
        <p class="dispose">已提交成功，等待支付宝处理</p>
    </div>
</div>
<script src="{{asset('adminui/js/jquery.min.js?v=2.1.4')}}"></script>
<script src="{{asset('adminui/js/jquery-weui.min.js')}}"></script>
<script src="{{asset('adminui/js/city-picker.min.js')}}"></script>
<script src="{{asset('adminui/js/address.js')}}"></script>

<script type="text/javascript">

    var hideKeyboard = function() {
        document.activeElement.blur();
        $(".blur").blur();
    };

    $("#choose_address").cityPicker({
        onChange: function (picker, values, displayValues) {
            province_code=values[0];
            city_code=values[1];
            district_code=values[2];
        }
    });
    //获取省市区信息
    function FindCityByCode(mapObj, code) {
        for (var i = 0; i < mapObj.length; i++) {
            if (mapObj[i].item_code == code) {
                return mapObj[i].item_name;
            }
        }
    }
    $(document).ready(function () {
        $('#sure_save').click(function () {
            var province=FindCityByCode(cityJson,province_code);
            var city=FindCityByCode(cityJson,city_code);
            var district=FindCityByCode(cityJson,district_code);
            $.ajax({
                type: "post",
                url: " {{url('admin/createcompany')}}",
                async: true,
                dataType: 'json',
                data: {
                    'company_name':$('#company_name').val(),
                    'name':$('#name').val(),
                    "user_id":$("#user_id").val(),
                    "address":$("#address").val(),
                    'phone':$('#phone').val(),
                    "_token": '{{csrf_token()}}',
                    'province_code':province_code ,
                    'province':province,
                    'city':city,
                    'district':district,
                    'city_code': city_code,
                    'district_code': district_code
                },
                success: function (res) {
                    if(res.status_code==1){
                        $('#main_box').hide();
                        $('#clock_box').show()
                    }
                },
                error: function (err) {
                    console.log(err)
                }
            });

        });

//        })

    });
</script>
</body>
</html>






