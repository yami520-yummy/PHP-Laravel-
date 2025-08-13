<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //預設不允許存取
        $is_allow = false;
        //取得會員編號
        $user_id = session()->get('user_id');

        if(!is_null($user_id))
        {
            //已登入，允許存取
            $is_allow = true;
        }

        if(!$is_allow)
        {
            //若不允許存取，重新導向至首頁
            return redirect()->to('/');
        }

        return $next($request);
    }
}

