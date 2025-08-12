<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsumableStoreRequest extends FormRequest
{
    /**
     * 确定用户是否有权限进行此请求
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 获取适用于请求的验证规则
     */
    public function rules(): array
    {
        return [
            'platform_id' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'consumable' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'manufacturer' => 'required|string|max:255',
            'in_drugstore' => 'required|boolean',
            'apply_type' => 'required|in:bid_product,sunshine_purchase,self_purchase,offline_purchase,volume_purchase',
            'apply_date' =>'required|date',
            'count_year' =>'required|integer|min:0',
            'need_selection' => 'required|boolean',
            'registration_num' => 'required|string|max:255',
            'medical_approval_file' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'category_zj' => 'required|string|max:255',
            'parent_directory' => 'required|string|max:255',
            'child_directory' => 'required|string|max:255',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
            'platform_id.required' => '平台ID不能为空',
            'department.required' => '科室不能为空',
            'consumable.required' => '耗材名称不能为空',
            'registration_num.required' => '注册证号不能为空',
            'model.required' => '型号不能为空',
            'price.required' => '价格不能为空',
            'price.numeric' => '价格必须是数字',
            'price.min' => '价格不能小于0',
            'manufacturer.required' => '生产厂家不能为空',
            'start_date.required' => '开始日期不能为空',
            'start_date.date' => '开始日期格式不正确',
            'exp_date.required' => '结束日期不能为空',
            'exp_date.date' => '结束日期格式不正确',
            'exp_date.after' => '结束日期必须晚于开始日期',
            'in_drugstore.required' => '是否在药房必须选择',
            'apply_type.required' => '申请类型不能为空',
            'apply_type.in' => '申请类型不正确',
            'apply_date.required' => '申请日期不能为空',
            'apply_date.date' => '申请日期格式不正确',
            'count_year.required' => '年度用量不能为空',
            'count_year.integer' => '年度用量必须是整数',
            'count_year.min' => '年度用量不能小于0',
            'need_selection.required' => '是否需要遴选必须选择',
            'medical_approval_file.required' => '医疗申请审批单不能为空',
            'medical_approval_file.max' => '医疗申请审批单路径不能超过255个字符',
            'company.required' => '生产企业名称不能为空',
            'company.max' => '生产企业名称不能超过255个字符',
            'category_zj.required' => '浙江分类目录不能为空',
            'category_zj.max' => '浙江分类目录不能超过255个字符',
            'parent_directory.required' => '父级目录不能为空',
            'parent_directory.max' => '父级目录不能超过255个字符',
            'child_directory.required' => '子级目录不能为空',
            'child_directory.max' => '子级目录不能超过255个字符',
        ];
    }
}