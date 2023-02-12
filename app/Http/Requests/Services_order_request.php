<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Services_order_request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'order_date'=>'required',
            'order_type'=>'required',
            'is_account_number'=>'required',
            'account_number'=>'required_if:is_account_number,1',
            'entity_name'=>'required_if:is_account_number,0',
            'pill_type' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'pill_type.required' => 'نوع الفاتورة مطلوب',
            'order_type.required' => 'فئة الفاتورة مطلوب',
            'order_date.required' => 'تاريخ الفاتورة مطلوب',
            'is_account_number.required' => 'هل حساب مالي مطلوب',
            'account_number.required_if'=>'رقم الحساب المالي مطلوب',
            'entity_name.required_if'=>'  اسم الجهة مطلوية',

        ];
    }
}
