<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\App;
use App\Models\Bill;
use App\Models\Company_info;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.admin:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin=Auth::guard('admin')->user();
        if($admin->status!=1){
            return view('admin.auth_force',compact('admin'));
        }
        return view('admin.adminhome');
    }
    public function adminIndex(){
        $data = App::where('id', 1)->first();
        $total_amount=$this->Count(New Request(['bill_status'=>'TRADE_SUCCESS','sum'=>'sum']));
        $total_amount_rate=round($total_amount/($this->count(New Request(['bill_status'=>'','sum'=>'sum']))==0?1:$this->count(New Request(['total_amount'=>'','sum'=>'sum'])))*100,2)."%";
        //本月
        $time_start= date("Y-m-01" . ' ' . ' 00:00:00',time());
        $time_end= date("Y-m-d H:i:s",strtotime("$time_start +1 month"));
        $req=New Request(['bill_status'=>'TRADE_SUCCESS','time_start'=>$time_start,"time_end"=>$time_end,'sum'=>'sum']);
        $month_total_amount=round($this->count($req),2);
        $month_total_amount_count=$this->count(New Request(['count'=>'count','bill_status'=>'TRADE_SUCCESS','time_start'=>$time_start,"time_end"=>$time_end]));
        $begin=date("Y-m-d" . ' ' . ' 00:00:00',time());
        $end=date("Y-m-d" . ' ' . ' 23:59:59',time());
        $req=New Request(['bill_status'=>'TRADE_SUCCESS','begin'=>$begin,"end"=>$end,'sum'=>'sum']);
        $today_total_amount=round($this->count($req),2);
        $today_total_amount_count=$this->count(New Request(['count'=>'count','bill_status'=>'TRADE_SUCCESS','begin'=>$begin,"end"=>$end]));;
        //线上累计金额
        $online_total_amount=round($this->count(New Request(['bill_status'=>'TRADE_SUCCESS',"sum"=>"sum","type"=>'ONLINE'])),2);
        //线下累计金额
        $offline_total_amount=round($this->count(New Request(['bill_status'=>'TRADE_SUCCESS',"sum"=>"sum","type"=>'OFFLINE'])),2);
        //线上缴费比例
        $online_total_amount_rate=round( $online_total_amount/ ($total_amount==0?1:$total_amount) * 100 , 2) ;
        $offline_total_amount_rate=100-$online_total_amount_rate;
        //逾期未缴金额
        $overdue_total_amount=round($this->count(New Request(['overdue'=>'overdue',"sum"=>"sum",])),2);
        //未缴率
        $overdue_total_amount_rate=round( $overdue_total_amount/($this->count(New Request(['bill_status'=>'','sum'=>'sum']))==0?1:$this->count(New Request(['total_amount'=>'','sum'=>'sum'])))*100,2)."%";
        //每个物业公司已缴金额
        $company_amount=$this->company_amount(New Request(['bill_status'=>"TRADE_SUCCESS"]));
        return view("admin.index",compact('data','total_amount','total_amount_rate','month_total_amount',
            'month_total_amount_count','today_total_amount_count','today_total_amount','online_total_amount_rate','online_total_amount',
            'offline_total_amount','offline_total_amount_rate','overdue_total_amount','overdue_total_amount_rate','company_amount'));
    }
    public function forceOauth(Request $request){
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        return redirect()->guest('checkoauth');
    }

    public function getCompanyid()
    {
        $company_ids=[];
        if (Auth::guard('admin')->user()->hasRole('root')) {
            $company_id=Company_info::get();
        }else{
            $company_id=Company_info::where('admin_id',Auth::guard('admin')->user()->id)->get();
        }
        if($company_id){
            foreach ($company_id as $k=>$v){
                $company_ids[]=$v->id;
            }
        }
        return $company_ids;
    }
    //计算每个物业公司当月金额并排序
    public function company_amount(Request $request)
    {
        $bill_status = $request->bill_status;
        $where = [];
        if ($bill_status) {
            $where[] = ['bill_status', $bill_status];
        }
        //获取所有的company_id
            $company_id =$this->getCompanyid();
            $bill = Bill::whereIn("company_id", $company_id)
                ->where('acct_period', date('Y-m'))
                ->where($where);
            if ($bill_status) {
                $bill = $bill->groupBy('company_id')
                    ->select('company_id', DB::raw('SUM(bill_entry_amount) as total'))
                    ->pluck('total', 'company_id')->toArray();
                if ($company_id) {
                    foreach ($company_id as $k => $v) {
                        if (!array_key_exists($v, $bill)||!$bill) {
                            $bill[$v] = 0;
                        }
                    }
                }
                $companyInfo = Company_info::whereIn('id', $company_id)
                    ->select('id', 'company_name')
                    ->pluck('company_name', "id")->toArray();
                $billInfo = Bill::whereIn("company_id", $company_id)
                    ->where('acct_period', date('Y-m'))
                    ->groupBy('company_id')
                    ->select('company_id', DB::raw('SUM(bill_entry_amount) as total'))
                    ->pluck('total', 'company_id')->toArray();
                if ($company_id) {
                    foreach ($company_id as $k => $v) {
                        if (!array_key_exists($v, $billInfo)||!$billInfo) {
                            $billInfo[$v] = 0;
                        }
                    }
                }
                $company_amount = [];
                foreach ($companyInfo as $k => $v) {
                    foreach ($bill as $key => $value) {
                        foreach ($billInfo as $ke => $ve) {
                            if ($k == $key && $ke == $key) {
                                $company_amount[] = ['out_community_id' => $k, 'amount' => $value, 'name' => $v, 'rate' => round(($value / ($ve == 0 ? 1 : $ve)) * 100, 2)];
                            }
                        }

                    }
                }

            }
            $age=[];
            foreach ($company_amount as $key => $value) {
                $age[$key] = $value['rate'];
            }
            array_multisort($age, SORT_DESC, $company_amount);
            return $company_amount;
        }
    //首页计算
    public function Count(Request $request){
        $bill_status=$request->bill_status;
        $where=[];
        $sum=$request->sum;
        $count=$request->count;
        $time_start=$request->time_start;
        $time_end=$request->time_end;
        $time_starts=[];
        $time_ends=[];
        $type=$request->type;
        $begin=$request->begin;
        $end=$request->end;
        $typewhere=[];
        $overdue=$request->overdue;
        $overduewhere=[];
        if($overdue){
            $overduewhere[]=['deadline',"<",date('Y-m-d')];
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
        if($bill_status){
            $where[]=['bill_status',$bill_status];
        }
        if($begin){
            $time_starts[]=['updated_at',">=",$begin];
            $time_ends[]=['updated_at',"<=",$end];
        }
        if(Auth::guard('admin')->user()->hasRole('root')){
            $bill=Bill::where($where)
                ->where($time_starts)
                ->where($time_ends)
                ->whereIn("type",$typewhere)
                ->where($overduewhere);
        }else{
            $bill=Bill::where('id',Auth::guard('admin')->user()->id)
                ->where($where)
                ->where($time_starts)
                ->where($time_ends)
                ->whereIn("type",$typewhere)
                ->where($overduewhere);
        }
        if($sum){
            $bill=$bill->select('bill_entry_amount')->sum('bill_entry_amount');
        }
        if($count){
            $bill=$bill->select('bill_entry_amount')->count();
        }
        return $bill;
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
        //获取所有的out_community_id
        if(Auth::guard('admin')->user()->hasRole('root')){
            $month_amount=Bill::whereIn('acct_period',$monarr)
                ->select( 'acct_period',DB::raw('SUM(bill_entry_amount) as total'))
                ->groupBy('acct_period')->pluck('total',"acct_period")->toArray();
        }else{
            $month_amount=Bill::whereIn('acct_period',$monarr)
                ->where('admin_id',Auth::guard('admin')->user()->id)
                ->select( 'acct_period',DB::raw('SUM(bill_entry_amount) as total'))
                ->groupBy('acct_period')->pluck('total',"acct_period")->toArray();
        }

        foreach ($monarr as $k=>$v){
            if(!array_key_exists($v,$month_amount)){
                $month_amount[$v]=0;
            }
        }
        krsort($month_amount);
        //月账单集合
        $month_amount= array_values($month_amount);
        //已缴月账单查询
        if(Auth::guard('admin')->user()->hasRole('root')){
            $month_amount_success=Bill::whereIn('acct_period',$monarr)
                ->where('bill_status',"TRADE_SUCCESS")
                ->select( 'acct_period',DB::raw('SUM(bill_entry_amount) as total'))
                ->groupBy('acct_period')->pluck('total',"acct_period")->toArray();
        }else{
            $month_amount_success=Bill::whereIn('acct_period',$monarr)
                ->where('bill_status',"TRADE_SUCCESS")
                ->where('admin_id',Auth::guard('admin')->user()->id)
                ->select( 'acct_period',DB::raw('SUM(bill_entry_amount) as total'))
                ->groupBy('acct_period')->pluck('total',"acct_period")->toArray();
        }

        foreach ($monarr as $k=>$v){
            if(!array_key_exists($v,$month_amount_success)){
                $month_amount_success[$v]=0;
            }
        }
        krsort($month_amount_success);
        //月账单集合
        $month_amount_success= array_values($month_amount_success);
        //获取最大越账单
        if(Auth::guard('admin')->user()->hasRole('root')){
            $max_month_amount=Bill::whereIn('acct_period',$monarr)
                ->groupBy('acct_period')
                ->select(DB::raw('SUM(bill_entry_amount) as total'))
                ->get()
                ->max();
        }else{
            $max_month_amount=Bill::whereIn('acct_period',$monarr)
                ->where('admin_id',Auth::guard('admin')->user()->id)
                ->groupBy('acct_period')
                ->select(DB::raw('SUM(bill_entry_amount) as total'))
                ->get()
                ->max();
        }

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