<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed received_transaction_attachment_ids
 * @property mixed client_id
 * @property mixed venezuelan_operator_id
 * @property mixed venezuelan_operator_account_id
 * @property mixed receiver_account_id
 * @property mixed transaction_number
 * @property mixed amount
 * @property mixed receiver_id
 * @property mixed operator_account_id
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
            'operator_id'                             => 'required|exists:users,id',
            'operator_account_id'                    => 'required|exists:accounts,id',
            'received_transaction_attachment_ids.*' => 'numeric|exists:attachments,id',
            'receiver_account_id'                   => 'required|exists:accounts,id',
            'venezuelan_operator_account_id'        => 'required|exists:accounts,id',
            'venezuelan_operator_id'                => 'required|exists:users,id',
            'transaction_number'                    => 'required',
            'receiver_id'                      => 'required|exists:users,id',
            'rate'                                  => 'nullable|numeric',
            'amount'                                => 'required|numeric',
        ];
    }
}
