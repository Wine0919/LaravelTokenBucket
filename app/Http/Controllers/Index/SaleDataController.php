<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use App\Models\Index\SaleData;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class SaleDataController extends Controller
{
    public function excel()
    {
        $saleData = new SaleData();
        $data = $saleData->getData();
        $cellData = [
            ['学号','姓名','年龄','成绩','名次'],
            ['10001','林',19,100,1],
            ['10001','林',19,100,1],
            ['10001','林',19,100,1],
            ['10001','林',19,100,1],
            ['10001','林',19,100,1],
        ];
        Excel::create("测试数据",function ($excel) use ($cellData){
            $excel->sheet('score',function ($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
}
