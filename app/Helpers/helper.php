
<?php

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

function generateOTP($user, $length = 6, $expirationMinutes = 3) {
    $characters = '0123456789';
    $otp = '';

    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }

    $expirationTime = Carbon::now()->addMinutes($expirationMinutes);

    $user->update([
        'otp' => $otp,
        'expires_at' => $expirationTime,
    ]);

    return $otp;
}


function ResponseJson($status,$message,$data=null):JsonResponse{
    if($data != null){
        $response=[
            'status'=>$status,
            'message'=>$message,
            'data'=>$data
    
           ];
    }else{
        $response=[
            'status'=>$status,
            'message'=>$message,
    
           ];
    }
 
   
       return response()->json($response);
}


