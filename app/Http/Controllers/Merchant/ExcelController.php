<?php
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            $data[]=[$info[$k]->room,$info[$k]->out_room_id,$acct_period,$release_day,$info[$k]->area,number_format($info[$k]->area,2)*number_format($down_bill_entry_amount,2),$deadline];
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
}
?>