<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/11/7
 * Time: 17:17
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class StatisticalManageController extends Controller
{
    protected $typeformat=['alipay'=>'物业官方支付宝','money'=>'现金'];
    protected $costtypeformat=['property_fee'=>'物业费','public_property_fee'=>'物业费公摊','rubbish_fee'=>'垃圾费','elevator_fee'=>'电梯费'];
    protected $billstatusformat=['ONLINE'=>'已同步',
        'NONE'=>'未同步',
        'UNDERREVIEW'=>'线下结算审核中',
        'ONLINE_UNDERREVIEW'=>'线下结算审核中',
        'TRADE_SUCCESS'=>'已结算'
    ];
    protected $head=['代理商','物业公司','交易号','金额(元)','支付方式','费用类型'
        ,'账单状态','所属账期','出账日期','备注','更新日期'];
    public function billQuerry(Request $request)
    {
        $line='';
        $error='';
        try{
            $admin_id=$request->admin_id;
            $agent_id=$request->agent_id;
            $company_id=$request->company_id;
            $bill_status=$request->bill_status;
            $bill_type=$request->bill_type;
            $bill_cost_type=$request->bill_cost_type;
            $time=$request->time;
            $time_start=$request->time_start;
            $time_end=$request->time_end;
            $export=$request->export;
            $total_amount=$request->total_amount;
            $searchcid=[];
            $where=[];
            $communityInfo='';
            $wherestatus=[];
            $user=Auth::guard('admin')->user();
            $users=$agents=[];
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($root){
                $users=$this->getAgents();
            }else{
                $users=[$user->id=>$user->name];
                $bill_status=4;
                $agents=$this->getAgents($admin_id?$admin_id:$user->id);
            }
            if($time){
                $where[]=['bills.release_day',$time];
            }
            if($time_start){
                $where[]=['bills.updated_at','>',date('Y-m-d' . ' ' . ' 00:00:00',strtotime($time_start))];
            }
            if($time_end){
                $where[]=['bills.updated_at','<',date('Y-m-d' . ' ' . ' 23:59:59',strtotime($time_end))];
            }
            if($bill_status){
                switch ($bill_status){
                    case 1:
                        $wherestatus=['ONLINE'];
                        break;
                    case 2:
                        $wherestatus=['NONE'];
                        break;
                    case 3:
                        $wherestatus=['UNDERREVIEW','ONLINE_UNDERREVIEW'];
                        break;
                    case 4:
                        $wherestatus=['TRADE_SUCCESS'];
                        break;
                }
            }
            if($bill_cost_type){
                switch ($bill_cost_type){
                    case 1:
                        $where[]=['bills.cost_type','property_fee'];
                        break;
                    case 2:
                        $where[]=['bills.cost_type','public_property_fee'];
                        break;
                    case 3:
                        $where[]=['bills.cost_type','rubbish_fee'];
                        break;
                    case 4:
                        $where[]=['bills.cost_type','elevator_fee'];
                        break;
                }
            }
            if($bill_type){
                switch ($bill_type){
                    case 1:
                        $where[]=['bills.type','alipay'];
                        break;
                    case 2:
                        $where[]=['bills.type','money'];
                        break;
                }
            }
            $collcet=DB::table('bills')
                ->join('company_infos','bills.company_id','company_infos.id')
                ->join('admins','admins.id','bills.admin_id')
                ->when(!empty($wherestatus),function($q)use($wherestatus){
                    return $q->whereIn('bills.bill_status',$wherestatus);
                })
                ->when(1,function($q)use($root,$admin_id,$users,$agent_id,$agents){
                    $keys=[];
                    if(!$admin_id){
                        if(!$agent_id){
                            if($root){
                                return ;
                            }else{
                                $keys=array_keys($users);
                            }
                        }else{
                            if($agent_id==='all'){
                                $keys=array_keys($agents);
                            }else{
                                $keys=[$agent_id];
                            }
                        }
                    }else{
                        if($agent_id){
                            if($agent_id==='all'){
                                $keys=array_keys($agents);
                            }else{
                                $keys=[$agent_id];
                            }
                        }else{
                            $keys=[$admin_id];
                        }
                    }
                    return $q->whereIn('bills.admin_id',$keys);
                })
                ->where($where)
                ->when($total_amount,function($q){
                    return $q->select('bills.bill_entry_amount');
                })
                ->when(!$total_amount,function($q){
                    return $q->select('bills.*','company_infos.company_name','admins.name as admin_name');
                });
            if($export){
                try{
                    $typeformat=$this->typeformat;
                    $costtypeformat=$this->costtypeformat;
                    $billstatusformat=$this->billstatusformat;
                    $head=$this->head;
                    $body=[$head];
                    $lists=$collcet->get();
                    if($lists){
                        foreach($lists as $k=>$v){
                            $typestr=$cost_typestr=$bill_statusstr='';
                            if(array_key_exists($v->type,$typeformat)){
                                $typestr=$typeformat[$v->type];
                            }
                            if(array_key_exists($v->cost_type,$costtypeformat)){
                                $cost_typestr=$costtypeformat[$v->cost_type];
                            }
                            if(array_key_exists($v->bill_status,$billstatusformat)){
                                $bill_statusstr=$billstatusformat[$v->bill_status];
                            }
                            $body[]=[
                                $v->admin_name,
                                $v->company_name,
                                $v->bill_entry_id,
                                $v->bill_entry_amount,
                                $typestr,
                                $cost_typestr,
                                $bill_statusstr,
                                $v->acct_period,
                                $v->release_day,
                                $v->remark_str,
                                $v->updated_at
                            ];
                        }
                    }
                    $cellData = $body;
                    Excel::create(iconv('utf-8','gbk',date('Y-m-d日').'物业费统计'),function($excel) use ($cellData){
                        $excel->sheet('score', function($sheet) use ($cellData){
                            $sheet->rows($cellData);
                        });
                    })->export('xls');
                }catch (\Exception $e){
                    die('导出数据失败');
                }
            }
            if($total_amount==1){
                //统计金额
                $totalje=$collcet->sum('bill_entry_amount');
                return json_encode([
                    'success'=>1,
                    'totalje'=>$totalje,
                ]);
            }
            $count=$collcet->count();
            $lists=$collcet
                ->orderBy('bills.updated_at','DESC')
                ->paginate(8);
            return view('admin.bill.query',compact('lists','users','agents','admin_id','agent_id','company_id','bill_cost_type','bill_status','bill_type','count','time','time_start','time_end'));
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            Log::info($e);
        }
        return view('error',compact('line','error'));
    }

    /**获取代理商信息
     * @param null $adminId
     * @return array
     */
    protected function getAgents($adminId=null)
    {
        $agentsinfo=[];
        if(is_null($adminId)||empty($adminId)){
            $user=Auth::guard('admin')->user();
            if($user->hasRole('root')){
                $agentsinfo=Admin::pluck('name','id')->toArray();
            }else{
                $agentsinfo=Admin::where('pid',$adminId)->pluck('name','id')->toArray();
            }
        }else{
            $agentsinfo=Admin::where('pid',$adminId)->pluck('name','id')->toArray();
        }
        return $agentsinfo;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function agents(Request $request)
    {
        $error='';
        $line='';
        $agents=[];
        try{
            $admin_id=$request->id;
            if($admin_id){
                $agents=$this->getAgents($admin_id);
            }
            return json_encode([
                'success'=>1,
                'data'=>$agents
            ]);

        }catch(\Exception $e) {
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            'success'=>0,
            'msg'=>$error.$line
        ]);

    }
}