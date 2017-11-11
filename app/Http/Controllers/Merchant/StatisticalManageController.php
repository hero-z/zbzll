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

class StatisticalManageController  extends Controller
{
    //统计管理
    public function billQuerry(Request $request)
    {
        $line='';
        $error='';
        try{
            $mid=$request->mid;
            $out_community_id=$request->out_community_id;
            $room=$request->room;
            $costtype=$request->costtype;
            $type=$request->type;
            $bill_status=$request->bill_status;
            $time=$request->time;
            $time_start=$request->time_start;
            $time_end=$request->time_end;
            $export=$request->export;
            $total_amount=$request->total_amount;
            $searchcid=[];
            $where=[];
            $communityInfo='';
            if(!$mid){
                $mid=$this->getMids(Auth::guard('merchant')->user()->id);
            }
            list($searchcid,$communityInfo)=$this->getCommunites($mid);
            if($out_community_id){
                $searchcid=[$out_community_id];
            }
            if($room){
                $where[]=['room_infos.room','like','%'.$room.'%'];
            }
            $merchants=Merchant::whereIn('id',$this->getMids(Auth::guard('merchant')->user()->id))->select('name','id')->get();
            $lists=DB::table('bills')
                ->join('communities','bills.out_community_id','communities.out_community_id')
                ->join('room_infos','bills.out_room_id','room_infos.out_room_id')
                ->where($where)
                ->whereIn('communities.merchant_id',$mid)
                ->whereIn('bills.out_community_id',$searchcid)
                ->select('bills.*','communities.community_name','room_infos.room')
                ->orderBy('bills.updated_at','DESC')
                ->paginate(8);
            return view('merchant.bill.billquery',compact('lists','communityInfo','out_community_id','merchants','room','mid'));
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
        $company=Community::whereIn('merchant_id',$mids)->select('out_community_id','community_name')->get();
        foreach ($company as $v) {
            $companyids[]=$v->out_community_id;
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