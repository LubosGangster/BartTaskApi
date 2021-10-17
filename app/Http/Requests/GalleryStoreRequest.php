<?php


namespace App\Http\Requests;


class GalleryStoreRequest
{
    public function rules()
    {
        return array(
            'name' => array(
                'required',
                'string',
                'regex:/^[^\/]*$/u',
                'min:1'
            )
        );
    }
}
