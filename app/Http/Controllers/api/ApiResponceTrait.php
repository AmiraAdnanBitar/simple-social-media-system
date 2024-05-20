<?php
namespace App\Http\Controllers\api;
trait ApiResponceTrait
{
    public function ApiResponse($data=null,$msg=null,$status=null)
    {
        $array=[
            'data'=>$data,
            'meesage'=>$msg,
            'status'=>$status
        ];
        return response($array);
    }
}