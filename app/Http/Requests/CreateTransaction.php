<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

/**
 * @property int venezuelan_operator_account_id
 */
class CreateTransaction extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'client_id'                             => 'required|exists:users,id',
            'foreign_account_id'                    => 'required|exists:accounts,id',
            'received_transaction_attachment_ids.*' => 'numeric|exists:attachments,id',
            'receiver_account_id'                   => 'required|exists:accounts,id',
            'venezuelan_operator_account_id'        => 'required|exists:accounts,id',
            'venezuelan_operator_id'                => 'required|exists:users,id',
            'transaction_number'                    => 'required',
            'receiver_user_id'                      => 'required|exists:users,id',
            'rate'                                  => 'nullable|numeric',
            'amount'                                => 'required|numeric',
        ];
    }
}
