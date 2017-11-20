<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/11/3
 * Time: 16:04
 */

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Company_info;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class StatisticalManageController  extends Controller
{
    protected $typeformat=['alipay'=>'物业官方支付宝','money'=>'现金'];
    protected $costtypeformat=['property_fee'=>'物业费','public_property_fee'=>'物业费公摊','rubbish_fee'=>'垃圾费','elevator_fee'=>'电梯费'];
    protected $billstatusformat=['ONLINE'=>'已同步',
        'NONE'=>'未同步',
        'UNDERREVIEW'=>'线下结算审核中',
        'ONLINE_UNDERREVIEW'=>'线下结算审核中',
        'TRADE_SUCCESS'=>'已结算'
    ];
    protected $head=['所属员工','所属小区','房间号','金额(元)','支付方式','费用类型'
        ,'账单状态','所属账期','截止日期','备注','更新日期'];

    //统计管理
    public function billQuerry(Request $request)
    {
        $line='';
        $error='';
        try{
            $mid=$merchant_id=$request->merchant_id;
            $out_community_id=$request->out_community_id;
            $room=$request->room;
            $bill_cost_type=$request->bill_cost_type;
            $bill_type=$request->bill_type;
            $bill_status=$request->bill_status;
            $time=$request->time;
            $time_start=$request->time_start;
            $time_end=$request->time_end;
            $export=$request->export;
            $total_amount=$request->total_amount;
            $searchcid=[];
            $where=[];
            $wherestatus=[];
            $communityInfo='';
            if(empty($mid)){
                $mid=$this->getMids(Auth::guard('merchant')->user()->id);
            }
            list($searchcid,$communityInfo)=$this->getCommunites($mid);
            if($out_community_id){
                $searchcid=[$out_community_id];
            }
            if($room){
                $where[]=['room_infos.room','like','%'.$room.'%'];
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
            if($total_amount){
                if($bill_status&&$bill_status!=4){
                    return json_encode([
                        'success'=>1,
                        'totalje'=>0,
                    ]);
                }
                $wherestatus=['TRADE_SUCCESS'];
            }else{
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
            }
            $merchants=Merchant::whereIn('id',$this->getMids(Auth::guard('merchant')->user()->id))->select('name','id')->get();
            $collcet=DB::table('bills')
                ->join('communities','bills.out_community_id','communities.out_community_id')
                ->join('room_infos','bills.out_room_id','room_infos.out_room_id')
                ->join('buildings','room_infos.building_id','buildings.id')
                ->join('units','room_infos.unit_id','units.id')
                ->join('merchants','merchants.id','communities.merchant_id')
                ->when(!empty($wherestatus),function($q)use($wherestatus){
                    return $q->whereIn('bills.bill_status',$wherestatus);
                })
                ->whereIn('communities.merchant_id',is_array($mid)?$mid:[$mid])
                ->whereIn('bills.out_community_id',$searchcid)
                ->where($where)
                ->when($total_amount,function($q){
                    return $q->select('bills.bill_entry_amount');
                })
                ->when(!$total_amount,function($q){
                    return $q->select('bills.*','buildings.building_name','units.unit_name','merchants.name as merchant_name','communities.community_name','room_infos.room');
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
                                $v->merchant_name,
                                $v->community_name,
                                $v->room,
                                $v->bill_entry_amount,
                                $typestr,
                                $cost_typestr,
                                $bill_statusstr,
                                $v->acct_period,
                                $v->deadline,
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
                ->orderBy('room_infos.room')
                ->paginate(8);
            return view('merchant.bill.billquery',compact('lists','communityInfo','out_community_id','merchants','room','merchant_id','bill_cost_type','bill_status','bill_type','count','time','time_start','time_end'));
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            Log::info($e);
        }
        return view('error',compact('line','error'));
    }

    /**
     * @param $mids
     * @return array
     */
    protected function getCommunites($mids)
    {
        $companyids=[];
        $company=Community::whereIn('merchant_id',is_array($mids)?$mids:[$mids])->select('out_community_id','community_name')->get();
        if($company){
            foreach ($company as $v) {
                $companyids[]=$v->out_community_id;
            }
        }
        return [$companyids, $company];
    }

    /**
     * @param $mId
     * 获取下属id
     * @return array
     */
    protected function getMids($mId)
    {
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
}