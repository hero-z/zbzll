<?php
namespace App\Http\Controllers\Merchant;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifyController extends BaseController{
    public function Notify(Request $request){

        //支付异步通知
        try{
            $aop = $this->AopClientNotify();
            $wzl = $aop->rsaCheckV2($request->all(), $aop->alipayrsaPublicKey,'RSA2');
            if($wzl){
                $data = $request->all();
                $bill=Bill::where("bill_entry_id",$data['det_list'])->first();
                if($data['trade_status']!=$bill['bill_status']){
                    Bill::where('bill_entry_id', $data['det_list'])->update([
                        'bill_status' => $data['trade_status'],
                        'bill_entry_amount' => $data['receipt_amount'],
                        "trade_no"=>$data['trade_no'],
                        "buyer_user_id"=>$data['buyer_user_id']
                    ]);
                    //如果状态不相同修改数据库状态

                }
            }
        }catch(\Exception $e){
            Log::info($e);
            return json_encode([
                "status_code"=>$e->getCode(),
                "message"=>$e->getMessage()
            ]);
        }

    }
}
?>