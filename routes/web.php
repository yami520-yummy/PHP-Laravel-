<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AdminController; 




Route::group(['prefix' => 'user'], function(){
     //使用者驗證
    Route::group(['prefix' => 'auth'], function(){
    //使用者註冊畫面
    Route::get('/sign_up', [UserAuthController::class, 'signUpPage']); //當在左側列表中按下"註冊"時，會有一個超連結
    //處理註冊資料
    Route::post('/sign_up', [UserAuthController::class, 'signUpProcess']); //當按下按鈕"註冊"時 form會傳遞POST請求，並把表單中輸入的資料一起發送
    //使用者登入畫面
    Route::get('/sign_in', [UserAuthController::class, 'signInPage']);
    //處理登入資料
    Route::post('/sign_in', [UserAuthController::class, 'signInProcess']); //當按下按鈕"註冊"時
    //處理登出資料
    Route::get('/sign_out', [UserAuthController::class, 'signOut']);
    });

});

Route::group(['middleware'=> ['web']], function(){
        Route::group(['prefix' => 'admin'], function(){
        //自我介紹相關 路徑:admin/user
        Route::group(['prefix' => 'user'], function(){
            //自我介紹頁面
            Route::get('/', [AdminController::class, 'editUserPage']);
            //處理自我介紹資料
            Route::post('/', [AdminController::class, 'editUserProcess']); 
        });

        //心情隨筆相關 路徑:admin/mind
        Route::group(['prefix' => 'mind'], function(){
            //心情隨筆列表頁面
            Route::get('/', [AdminController::class, 'mindListPage']);
            //新增心情隨筆資料
            Route::get('/add', [AdminController::class, 'addMindPage']);//新增
            //處理心情隨筆資料 表單中有參數action指定路徑"/admin/mind/edit" 
            Route::post('/edit', [AdminController::class, 'editMindProcess']);//在mind.blade.php中，action="/admin/mind/edit" 是至關重要的。它統一了表單提交的處理端點。無論用戶是在新增頁面或修改頁面，所有提交的資料都會被導向到同一個控制器方法 (AdminController@editMindProcess)，然後由該方法透過隱藏的 id 欄位來判斷具體的處理邏輯（新增或修改）。
            //單一資料 admin/mind/mind_id
            Route::group(['prefix' => '{mind_id}'], function(){
                //編輯心情隨筆資料
                Route::get('/edit', [AdminController::class, 'editMindPage']);//"修改" 與"新增"是路由到同一個contoller底下的同func
                //刪除心情隨筆資料
                Route::get('/delete', [AdminController::class, 'deleteMindProcess']);
            });
        });
    });
});

Route::group(['prefix' => '/'], function(){
    //首頁
    Route::get('/', [HomeController::class, 'indexPage']);
    //單一使用者資料
    Route::group(['prefix' => '{user_id}'], function(){
        //自我介紹
        Route::get('/user', [HomeController::class, 'userPage']);
        //心情隨筆
        Route::get('/mind', [HomeController::class, 'mindPage']);
        //留言板
        Route::get('/board', [HomeController::class, 'boardPage']);

    });
});


?>