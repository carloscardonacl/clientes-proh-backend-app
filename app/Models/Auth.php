<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Firebase\JWT\JWT;

class Auth extends Model
{
    use HasFactory;

    private static $key = "CorvusLab";
    private static $algorithms = ['HS256'];
    private static $user;


    public static function encode($data)
    {

        $time = time();
        $token = array(
            'iat' => $time, // Tiempo que inició el token
            'data' => [$data] // información del usuario        
        );

        $jwt = JWT::encode($token, self::$key);
        return $jwt;
    }
    public static function check($token)
    {

        try {
            $data = JWT::decode($token, self::$key,  self::$algorithms);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public static function decode($token)
    {
        try {
            $data = JWT::decode($token, self::$key,  self::$algorithms);
            return $data->data[0];
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getUser()
    {
        return self::$user;
    }

    public  static function setUser($user)
    {
        self::$user = $user;
    }
}
