<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ConsumableNet;
 
class ConsumableNetsExport implements FromCollection, WithHeadings
{
    use Exportable;
 
    public function collection()
    {
        return ConsumableNet::all();
    }
 
    public function headings(): array
    {
        return [
            '挂网结果id',
            '一级目录',
            '二级目录',
            '产品id',
            '产品名称',
            '注册证号',
            '注册证名称',
            '注册证有效期',
            '国家27位编码',
            '规格',
            '型号',
            '单位',
            '生产企业',
            '投标企业',
            '投标企业社会信用编码',
            '中选价',
            '限价',
            '来源名称',
            '产品备注',
            '挂网时间',
            '采购类别',
            '挂网状态',
            '撤废时间',
        ];
    }
}