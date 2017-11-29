<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Bill;
use App\Models\Community;
use App\Models\Company_info;
use App\Models\Logo;
use App\Models\Merchant;
use App\Models\MerchantRole;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MerchantHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.merchant:merchant');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
        $merchantLogo=Logo::where("merchant_id",$merchant_id)->first();
        return view("merchant.merchanthome",compact('merchantLogo'));
    }
    public function merchantIndex(){
        $req=New Request(['total_amount'=>'TRADE_SUCCESS']);
        $total_amount= round($this->count($req),2);
        $total_amount_rate=round($total_amount/($this->count(New Request(['total_amount'=>'']))==0?1:$this->count(New Request(['total_amount'=>''])))*100,2)."%";
        //本月
        $time_start= date("Y-m-01" . ' ' . ' 00:00:00',time());
        $time_end= date("Y-m-d H:i:s",strtotime("$time_start +1 month"));
         $req=New Request(['total_amount'=>'TRADE_SUCCESS','time_start'=>$time_start,"time_end"=>$time_end]);
         $month_total_amount=round($this->count($req),2);
         $month_total_amount_count=$this->count(New Request(['count'=>'count','total_amount'=>'TRADE_SUCCESS','time_start'=>$time_start,"time_end"=>$time_end]));
         $begin=date("Y-m-d" . ' ' . ' 00:00:00',time());
         $end=date("Y-m-d" . ' ' . ' 23:59:59',time());
         $req=New Request(['total_amount'=>'TRADE_SUCCESS','begin'=>$begin,"end"=>$end]);
         $today_total_amount=round($this->count($req),2);
         $today_total_amount_count=$this->count(New Request(['count'=>'count','total_amount'=>'TRADE_SUCCESS','begin'=>$begin,"end"=>$end]));
        //累计线上缴费用户数
        $total_user=$this->count(New Request(['total_amount'=>'TRADE_SUCCESS',"count"=>"count","type"=>'ONLINE']));
        //累计线下缴费用户数
        $off_total_user=$this->count(New Request(['total_amount'=>'TRADE_SUCCESS',"count"=>"count","type"=>'OFFLINE']));
        //线上累计金额
        $online_total_amount=round($this->count(New Request(['total_amount'=>'TRADE_SUCCESS',"sum"=>"sum","type"=>'ONLINE'])),2);
        //线下累计金额
        $offline_total_amount=round($this->count(New Request(['total_amount'=>'TRADE_SUCCESS',"sum"=>"sum","type"=>'OFFLINE'])),2);
        //线上缴费比例
        $online_total_amount_rate=round( $online_total_amount/ ($total_amount==0?1:$total_amount) * 100 , 2) ;
        $offline_total_amount_rate=100-$online_total_amount_rate;
        //逾期未缴金额
        $overdue_total_amount=round($this->count(New Request(['overdue'=>'overdue'])),2);
        //未缴率
        $overdue_total_amount_rate=round( $overdue_total_amount/($this->count(New Request(['total_amount'=>'']))==0?1:$this->count(New Request(['total_amount'=>''])))*100,2)."%";
        //每个小区已缴金额
        $community_amount=$this->community_amount(New Request(['bill_status'=>"TRADE_SUCCESS"]));
    return view("merchant.index",compact('total_amount','total_amount_rate',"month_total_amount",'month_total_amount_count',
        'today_total_amount',"today_total_amount_count","total_user","off_total_user",'online_total_amount',"offline_total_amount",
        'online_total_amount_rate','offline_total_amount_rate','overdue_total_amount','overdue_total_amount_rate','community_amount'));
    }
    //首页计算
    public function count(Request $request){
        $total_amount=$request->total_amount;
        $time_start=$request->time_start;
        $time_end=$request->time_end;
        $begin=$request->begin;
        $end=$request->end;
        $count=$request->count;
        $type=$request->type;
        $sum=$request->sum;
        $overdue=$request->overdue;
        $typewhere=[];
        $where=[];
        $time_starts=[];
        $time_ends=[];
        if($total_amount){
            $where[]=['bill_status',$total_amount];
        }
        if($type=="ONLINE"){
            $typewhere=['alipay'];
        }
        if($type=="OFFLINE"){
            $typewhere=['money'];
        }
        if(!$type){
            $typewhere=['money',"alipay"];
        }
        if($time_start){
            $time_starts[]=['updated_at',">=",$time_start];
            $time_ends[]=['updated_at',"<=",$time_end];
        }
        if($begin){
            $time_starts[]=['updated_at',">=",$begin];
            $time_ends[]=['updated_at',"<=",$end];
        }
        $overduewhere=[];
        if($overdue){
            $overduewhere[]=['deadline',"<",date('Y-m-d')];
        }
        //获取所有merchant_id
        $merchant_id=$this->getmerchant(Auth::guard('merchant')->user()->id);
        //获取所有的out_community_id
        $out_community_id=$this->getCommunity($merchant_id);
        $bill=DB::table('bills')
            ->where($where)
            ->where($time_starts)
            ->where($time_ends)
            ->where($overduewhere)
            ->whereIn("type",$typewhere)
            ->whereIn('out_community_id',$out_community_id);
        if($count){
            if($type){
                if($sum){
                    $bill=$bill  ->select("bill_entry_amount")->sum('bill_entry_amount');
                }else{
                    $bill=$bill  ->select("out_room_id")->distinct()->count('out_room_id');
                }
            }else{
                $bill=$bill  ->select("bill_entry_amount")->count();
            }
        }else{
                $bill=$bill  ->select("bill_entry_amount")->sum('bill_entry_amount');

        }
        return $bill;
    }
    //计算每个小区当月金额并排序
    public function community_amount(Request $request){
        $bill_status=$request->bill_status;
        $where=[];
        if($bill_status){
            $where[]=['bill_status',$bill_status];
        }
        //获取所有merchant_id
        $merchant_id=$this->getmerchant(Auth::guard('merchant')->user()->id);
        //获取所有的out_community_id
        $out_community_id=$this->getCommunity($merchant_id);
        $bill=Bill::whereIn("out_community_id",$out_community_id)
            ->where('acct_period',date('Y-m'))
            ->where($where);
        if($bill_status){
            $bill=$bill->groupBy('out_community_id')
                ->select('out_community_id',DB::raw('SUM(bill_entry_amount) as total'))
                ->pluck('total','out_community_id')->toArray();
            if($out_community_id){
                foreach ($out_community_id as $k=>$v){
                    if(!array_key_exists($v,$bill)){
                        $bill[$v]=0;
                    }
                }
            }
            $communityInfo=Community::whereIn('out_community_id',$out_community_id)
                ->select('out_community_id','community_name')
                ->pluck('community_name',"out_community_id")->toArray();
            $billInfo=Bill::whereIn("out_community_id",$out_community_id)
                ->where('acct_period',date('Y-m'))
                ->groupBy('out_community_id')
                ->select('out_community_id',DB::raw('SUM(bill_entry_amount) as total'))
                ->pluck('total','out_community_id')->toArray();
            if($out_community_id){
                foreach ($out_community_id as $k=>$v){
                    if(!array_key_exists($v,$billInfo)){
                        $billInfo[$v]=0;
                    }
                }
            }
            $community_amount=[];
           foreach ($communityInfo as $k=>$v){
               foreach ($bill as $key=>$value){
                   foreach ($billInfo as $ke=>$ve){
                       if($k==$key&&$ke==$key){
                           $community_amount[]=['out_community_id'=>$k,'amount'=>$value,'name'=>$v,'rate'=>round( ($value/($ve==0?1:$ve))*100,2)];
                       }
                   }

               }
           }

        }
        foreach ($community_amount as $key=>$value){
            $age[$key] = $value['rate'];
        }
        array_multisort($age,SORT_DESC,$community_amount);
        return $community_amount;
    }
    //获取名下所有的小区
    public function getCommunity($mids){
        $companyids=[];
        $company=Community::whereIn('merchant_id',is_array($mids)?$mids:[$mids])->select('out_community_id','community_name')->get();
        if($company){
            foreach ($company as $v) {
                $companyids[]=$v->out_community_id;
            }
        }
        return $companyids;
    }
    //获取所有么merchant_id
    public function getmerchant($mId){
        $mids=[];
        $m=Merchant::find($mId);
        if($m){
            if($m->pid){
                $permission=$m->can('billglobalview');
                if($permission){
                    $mId=CheckMerchantController::selectMerchant($m->pid);
                }
            }
        }
        $mids=CheckMerchantController::CheckMerchant($mId);
        return $mids;
    }

    public function bill_month()
    {
        $time1 = strtotime(date('Y-m')); // 自动为00:00:00 时分秒
        $time1=strtotime('+1 month', $time1);
        $time2=strtotime('-12 month', $time1);
        $monarr = []; // 当前月;
        while (($time1 = strtotime('-1 month', $time1))>=$time2 ) {
            $monarr[] = date('Y-m', $time1); // 取得递增月;
        }
        //获取所有merchant_id
        $merchant_id=$this->getmerchant(Auth::guard('merchant')->user()->id);
        $out_community_id=$this->getCommunity($merchant_id);
        //获取所有的out_community_id
        $month_amount=Bill::whereIn('acct_period',$monarr)
            ->whereIn('out_community_id',$out_community_id)
            ->select( 'acct_period',DB::raw('SUM(bill_entry_amount) as total'))
            ->groupBy('acct_period')->pluck('total',"acct_period")->toArray();
        foreach ($monarr as $k=>$v){
            if(!array_key_exists($v,$month_amount)){
                $month_amount[$v]=0;
            }
        }
        krsort($month_amount);
        //月账单集合
       $month_amount= array_values($month_amount);
       //已缴月账单查询
        $month_amount_success=Bill::whereIn('out_community_id',$out_community_id)
            ->whereIn('acct_period',$monarr)
            ->where('bill_status',"TRADE_SUCCESS")
            ->select( 'acct_period',DB::raw('SUM(bill_entry_amount) as total'))
            ->groupBy('acct_period')->pluck('total',"acct_period")->toArray();
        foreach ($monarr as $k=>$v){
            if(!array_key_exists($v,$month_amount_success)){
                $month_amount_success[$v]=0;
            }
        }
        krsort($month_amount_success);
        //月账单集合
        $month_amount_success= array_values($month_amount_success);
        //获取最大越账单
        $max_month_amount=Bill::whereIn('acct_period',$monarr)
            ->whereIn('out_community_id',$out_community_id)
            ->groupBy('acct_period')
            ->select(DB::raw('SUM(bill_entry_amount) as total'))
            ->get()
            ->max();
        if($max_month_amount){
            $max_month_amount=$max_month_amount->total;
        }else{
            $max_month_amount=0;
        }
        return json_encode([
           'success'=>1,
           "data1"=>$month_amount,
            "data2"=>$month_amount_success,
            'data3'=>$max_month_amount
        ]);
    }
}