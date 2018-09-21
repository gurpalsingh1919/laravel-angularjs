<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use JWTAuth;
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    public static function getId($token)
    {
    	$user = JWTAuth::toUser($token);
    	return $user->id;
    }
    public static function getStatus()
    {
    	$user = JWTAuth::toUser($token);
    	return $user->status;
    }
   /* public static function getOldPassword($user_id) {
    	return $this->belongs_to('User')->select(array('password'));
    }*/
    
     public static function authenticateUser($email,$password)
    {
    	$credentials=array("email"=>$email,"password"=>$password);
    	$user = User::where('email', '=', $credentials['email'])->first();
    	$customClaims = ['role' => $user->role];
    	if($customClaims['role']=='2')
    	{
    	try {
    		// verify the credentials and create a token for the user
    		if (! $token = JWTAuth::attempt($credentials)) {
    			return response()->json(['error' => 'Invalid Credentials'], 401);
    		}
    	} catch (JWTException $e) {
    		// something went wrong
    		return response()->json(['error' => 'could_not_create_token'], 500);
    	}
    		
    	// if no errors are encountered we can return a JWT
    	$token= response()->json(compact('token'));
    		
    	return $token;
    	}
    	else
    	{
    			return response()->json(['error' => 'Unauthorised Admin'], 401);
    	}
    }
}
