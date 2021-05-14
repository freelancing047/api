<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRegisterRequest extends FormRequest
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
            'name' => 'required',
            'address1' => 'required',
            'address2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zipCode' => 'required',
            'phoneNo1' => 'required',
            'user.firstName' => 'required',
            'user.lastName' => 'required',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|min:6',
            'user.passwordConfirmation' => 'required|same:user.password',
            'user.phone' => 'required'
        ];
    }


    public function transform()
    {
        $requestData = request()->all();

        return [
            'client' => [
                'client_name' => $requestData['name'],
                'address1' => $requestData['address1'],
                'address2' => $requestData['address2'],
                'city' => $requestData['city'],
                'state' => $requestData['state'],
                'country' => $requestData['country'],
                'zip' => $requestData['zipCode'],
                'phone_no1' => $requestData['phoneNo1'],
                'start_validity' => date("Y-m-d"),
                'end_validity' => date("Y-m-d", strtotime("+15 days"))
            ],
            'user' => [
                'first_name' => $requestData['user']['firstName'],
                'last_name' => $requestData['user']['lastName'],
                'email' => $requestData['user']['email'],
                'password' => $requestData['user']['password'],
                'last_password_reset' => date("Y-m-d H:i:s")
            ]
        ];
    }
}
