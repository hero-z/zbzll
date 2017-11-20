<?php
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller{
    //添加房屋模板导出
    public function roomExcel(Request $request){
        $data[]=["房间号",'面积',"屋主","联系方式"];
        Excel::create(iconv('utf-8','gbk','房屋导入模板'),function($excel) use ($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);

                $sheet->setWidth(array(
                    'A'     =>  40,
                    'B'     =>  40,
                    "C"     =>  40,
                    "D"     =>  60,
                ));
            });

        })->export('xls');
    }
    //添加账单模板导出
    public function billExcel(Request $request){
        $unit_id=$request->unit_id;
        $acct_period=$request->acct_period;
        $time_start=$request->time_start;
        $time_end=$request->time_end;
        $time1 = strtotime($time_start); // 自动为00:00:00 时分秒
        $time2 = strtotime($time_end);

        $monarr = array();
        $monarr[] = $time_start; // 当前月;
        while( ($time1 = strtotime('+1 month', $time1)) <= $time2){
            $monarr[] = date('Y-m',$time1); // 取得递增月;
        }
        $release_day=$request->release_day;
        $deadline=$request->deadline;
        $down_bill_entry_amount=$request->down_bill_entry_amount;
        $info=DB::table("room_infos")
            ->join("units",'room_infos.unit_id',"=","units.id")
            ->where("room_infos.unit_id",$unit_id)
            ->select("room_infos.room","room_infos.area",'room_infos.out_room_id')
            ->get();
        $data[]=['房间',"物业系统编号",'归属账期','出账日期',"房屋面积",'应收金额',"截止日期"];
        foreach($info as $k => $v){
            foreach ($monarr as $key=>$value){
                $data[]=[$info[$k]->room,$info[$k]->out_room_id,$value,$release_day,$info[$k]->area,sprintf("%.2f", number_format($info[$k]->area,2)*number_format($down_bill_entry_amount,2)),$deadline];
            }
        }
        Excel::create(iconv('utf-8','gbk','账单导入模板'),function($excel) use ($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
                $sheet->setWidth(array(
                    'A'     =>  20,
                    'B'     =>  50,
                    "C"     =>  20,
                    "D"     =>  20,
                    "E"     =>  20,
                    "F"     =>  20,
                    "G"     =>  20,
                ));
            });
        })->export('xls');
    }
    //错误信息导出
    public function roomError(Request $request)
    {
        $data=[];
        $data[]=["房间号",'面积',"屋主","联系方式","错误信息",'操作时间'];
        $error=Cache::store('file')->get("errorCheck");
        if($error){
            foreach ($error as $k=>$v){
                $data[]=$v;
            }
        }
        Cache::store('file')->forget('errorCheck');
        Excel::create(iconv('utf-8','gbk','房屋错误信息'),function($excel) use ($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
                $sheet->setWidth(array(
                    'A'     =>  20,
                    'B'     =>  20,
                    "C"     =>  20,
                    "D"     =>  20,
                    "E"     =>  60,
                    'F'     =>  40,
                ));
            });

        })->export('xls');
    }
    //账单错误导出
    public function billError(Request $request)
    {
        $data=[];
        $data[]=['房间',"物业系统编号",'归属账期','出账日期',"房屋面积",'应收金额',"截止日期","错误信息","操作时间"];
        $error=Cache::store('file')->get("billError");
        if($error){
            foreach ($error as $k=>$v){
                $data[]=$v;
            }
        }
        Cache::store('file')->forget('billError');
        Excel::create(iconv('utf-8','gbk','账单错误信息'),function($excel) use ($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
                $sheet->setWidth(array(
                    'A'     =>  20,
                    'B'     =>  30,
                    "C"     =>  20,
                    "D"     =>  20,
                    "E"     =>  10,
                    "F"     =>  10,
                    "G"     =>  20,
                    "H"     =>  50,
                    "I"     =>  20,
                ));
            });

        })->export('xls');
    }
}
?>