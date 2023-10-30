<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Jobs\SendEmail;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Repositiries\UserRepositry;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyEmailRequest;

class AuthController extends Controller
{

    protected $userRepository;

    public function __construct(UserRepositry $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
   
    
    public function register(RegisterRequest $request)
    {
        $userData = $request->validated();
    
    
        $user = $this->userRepository->create($userData);
        $otp = generateOTP($user, $length = 6, $expirationMinutes = 3);

        dispatch(new SendEmail($user , $otp));

        return ResponseJson(1 , 'Code Sent successfully');

    }
    
    public function VerifyEmail(VerifyEmailRequest $request)
    {
        $data = $request->validated();
        $user = $this->userRepository->findByEmail($data['email']);

        if ($user) {
            if ($user->otp === $data['otp']) {
                if ($user->expires_at && Carbon::now()->lte($user->expires_at)) {
                    $this->userRepository->verifyEmail($user);
                    return ResponseJson(1 , 'Email verified Successfully'); 

                } else {

                    return ResponseJson(1 , 'OTP has expired'); 

                }
            }
        }

        return ResponseJson(1 , 'Invalid Otp'); 
    }


    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (auth()->attempt($credentials)) {
            $user = $this->userRepository->findByEmail($request->email);
    
            if ($user->is_verified === 1) {
                $tokenResult = $user->createToken('AuthToken');
                $token = $tokenResult->plainTextToken; // Extract the plain text token
    
                return response()->json(['token' => $token , 'user' => $user]);

                return ResponseJson(1 , 'Login successfully' , ['token' => $token , 'user' => $user]); 

            } else {
                return ResponseJson(0 , 'User is not verified' , ['is_verified'=>$user->is_verified]); 

            }
        }
    
        return ResponseJson(0 , 'Invalid credentials'); 

    }
    
    

   

}