<?php
namespace App\Http;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
class Kernel extends HttpKernel{


    //應用程式中介層
    protected $middlewareGroups = [
        'web'  => [
            \App\Http\Middleware\AdminMiddleware::class, 
        ], 
        //以下省略
    ];
}
?>