<?PHP
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Module\ShareData;
// 新增這一行：引入 Laravel 的 Validator Facade 
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Hash;
//透過Laravel的Model將資料新增到資料庫中,
use App\Entity\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserAuthController extends Controller
{
    public $page = "";
    public function signUpPage()
    {   
        $name = 'sign_up';
        $User = $this->GetUserData();
        $binding = [
            'title' => ShareData::TITLE,
            'page' => $this->page,
            'User' => $User,
            'name' => $name,
        ];
        return view('user.sign_up', $binding);
    }

    //處理註冊資料
    public function signUpProcess()
    {
        //接收輸入資料
        $input = request()->all();
        //驗證規則
        $rule = [
            //暱稱
            'name' => [
                'required',
                'max:10',
            ],
            //帳號(E-mail)
            'account' => [
                'required',
                'max:50',
                'email',
                'unique:users,account', // 新增這條規則
            ],
            //密碼
            'password' => [
                'required',
                'min:5',
                //'regex:/[A-Z]/',    // 至少包含一個大寫字母
                //'regex:/[^A-Za-z0-9]/', // 至少包含一個特殊字元
                // **將兩個獨立的 regex 規則替換為一個更全面的 regex**
                // 這個 regex 意味著：
                // 1. 至少包含一個大寫字母 (?=.*[A-Z])
                // 2. 至少包含一個特殊字元 (?=.*[^A-Za-z0-9])
                // 3. 總長度至少為 5 個字元 .{5,}
                'regex:/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{5,}$/', 
            ],
            //密碼驗證
            'password_confirm' => [
                'required',
                'same:password',
            ],
        ];
        
        //驗證資料
        $validator = Validator::make($input, $rule);
        if($validator->fails()){
            //資料驗證錯誤 // 回傳到原頁面並攜帶錯誤訊息
            // 建議加上 withInput() 讓舊輸入資料保留 //搭配前端old()函式來取得之前輸入的資料 ex:value="{{ old('name') }}"
            return redirect('/user/auth/sign_up') -> withErrors($validator)->withInput(); 
            
        }
        // 通常，你會將 $input 陣列中的明文密碼替換為雜湊後的密碼
        // 並將 $input['password_confirm'] 移除，因為它不再需要儲存
        $input['password'] = Hash::make($input['password']);
        unset($input['password_confirm']); 

        //exit;// 在實際應用中，通常不會使用 exit; 而是重定向或渲染另一個視圖

        Log::notice(print_r($input, true));
        //啟用紀錄SQL語法
        DB::enableQueryLog();
        //在資料庫裡新增你的輸入內容 使用到Laravel的Eloquent ORM // 確保你的 User Model 在文件頂部正確引入：use App\Entity\User;
        User::create($input);
        //取得目前使用過的SQL語法
        Log::notice(print_r(DB::getQueryLog(), true));
        return redirect('/user/auth/sign_in')->with('success', '註冊成功！請登入。');


    }//signUpProcess結束

    //使用者登入畫面
    public function signInPage()
    {
        
        $name = 'sign_in';
        $User = $this->GetUserData();
        $binding = [
            'title' => ShareData::TITLE,
            'page' => $this->page,
            'User' => $User,
            'name' => $name,
        ];
        return view('user.sign_in', $binding);
    }
    public function signInProcess(){
        //接收輸入資料
        $input = request() -> all();
        //驗證規則
        $rules = [
            //帳號(E-mail)
            'account' => [
                'required',
                'max: 50',
                'email',
            ],

            //密碼
            'password' => [
                'required',
                'min : 5',
            ],
        ];

        //驗證資料
        $validator = Validator::make($input, $rules);
        if($validator->fails())
        {
            //資料驗證錯誤
            return redirect('/user/auth/sign_in')-> withErrors($validator)-> withInput();
        }

        //資料庫 取得資料表(users)的資料
        //取得使用者資料 //使用到Laravel的Eloquent ORM 語法
        $User = User::where('account', $input['account'])->first();
        if(!$User){

            //帳號錯誤回傳錯誤訊息
            $error_message = [
                'msg' => [
                    '帳號輸入錯誤',
                ],
            ];

            return redirect('/user/auth/sign_in')
                ->withErrors($error_message)
                ->withInput();
        }

        //檢查密碼是否正確
        $is_password_correct = Hash::check($input['password'], $User->password);
        if(!$is_password_correct)
        {
            //密碼錯誤回傳錯誤訊息
            $error_message = [
                'msg' => [
                    '密碼輸入錯誤',
                ],
            ];

            return redirect('/user/auth/sign_in')
                ->withErrors($error_message)
                ->withInput();
        }

    //當驗證通過之後,就可以正常登入,在登入的時候,我們會透過session(全域變數)來記錄會員的編號,來作為會員已登入的驗證.
    //session紀錄會員編號
    session()->put('user_id', $User->id);
    //重新導向到原先使用者造訪頁面，沒有嘗試造訪頁則重新導向回"自我介紹"頁面
    return redirect()->intended('/admin/user');

    }


    //登出
    public function signOut()
    {
        //清除Session
        session()->forget('user_id');
        //重新導向回首頁
        return redirect('/');
    }

}
?>