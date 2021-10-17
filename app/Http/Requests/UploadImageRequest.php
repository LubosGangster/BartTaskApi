<?php


namespace App\Http\Requests;


class UploadImageRequest
{
    public function rules()
    {
        return array(
            'image' => array(
                'required',
                'mimes:jpeg,jpg,png'
            )
        );
    }
}
