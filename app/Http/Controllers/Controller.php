<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Entity\User;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function GetUserData(){
        //取得會員編號 //session為全域變數， 因為在不同Controller都能取用的變數
        $user_id = session()->get('user_id');
        if(is_null($user_id))
        {
            return null;
        }
        //資料庫裡找尋session內的'user_id'
        $User = User::where('id', $user_id)->first();

        return $User;
    }
}

?>