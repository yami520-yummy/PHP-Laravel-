<?PHP
//登入後的(使用者)後台
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; 
use Intervention\Image\ImageManager; // **新增這行：引入 ImageManager 類別**
use Intervention\Image\Drivers\Gd\Driver; // **新增這行：引入你希望使用的 Driver**
                                          // 通常會是 Gd\Driver 或 Imagick\Driver，取決於你的 PHP 環境

use App\Http\Controllers\Controller;
use App\Module\ShareData;
use App\Enum\ESexType;
use App\Entity\User;
use App\Entity\Mind;

class AdminController extends Controller{

    public $page = "admin"; //判別是否在登入後的後台，AdminController下表示在後台

    //自我介紹頁面
    public function editUserPage(){
        $User = $this->GetUserData(); //從父class:Controller取得GetUserData()函式，來取得user data
        if(!$User)
        {
            //如果找不到使用者，就回到首頁
            return redirect('/');
        }
        
        $name = 'user';

        $binding = [
            'title' => ShareData::TITLE,
            'page' => $this->page, //是否在後台
            'name' => $name,//要導向的前端名稱
            'User' => $User,//已登入的使用者資料
            'result' => '',
        ];
        return view('admin.edituser', $binding);//導向後台
    }

    //處理自我介紹資料
    public function editUserProcess(){

        $User = $this->GetUserData();

        if(!$User){
            //如果找不到使用者，就回到首頁
            return redirect('/');
        }

        $name = 'user';

        //接收輸入資料
        $input = request()->all();

        //驗證規則
        $rules = [
            //性別
            'sex' => [
                'required',
                'integer',
                'in:'.ESexType::MALE.','.ESexType::FEMALE, // 男或女 'in:1,2'
            ],
            //身高
            'height' => [
                'required',
                'numeric',
                'min:100',
            ],
            //體重
            'weight' => [
                'required',
                'numeric',
                'min:30',
            ],
            //興趣
            'interest' => [
                'required',
                'max:50',
            ],
            //自我介紹
            'introduce' => [
                'required',
                'max:500',
            ],
            //圖片
            'file' => [
                'image',
                'max:10240', //10 MB             
            ],   
        ];

        // **定義自定義訊息 (Custom Messages)**
        $messages = [
            'height.min' => ':attribute 不小於 :min 公分。', // 針對 height 欄位的 min 規則
            'weight.min' => ':attribute 不小於 :min 公斤。',
            'file.max' => '圖片大小不能超過 :max MB喔!',

        ];

        // **定義自定義屬性名稱 (Custom Attributes)**
        $customAttributes = [
            'height' => '身高',
            'weight' => '體重',
            'interest' => '興趣',
            'introduce' => '自我介紹',
            'sex' => '性別', // 也建議將性別加上
            'file' => '圖片', // 如果圖片驗證有錯誤，也顯示友善名稱
        ];

        //驗證資料
        $validator = Validator::make($input, $rules, $messages, $customAttributes);

        if($validator->fails()){   
            // 驗證失敗時仍然重定向並閃存錯誤和輸入
            return redirect('/admin/user') ->  withErrors($validator)->withInput(); // <-- 加上這行
        }

        //驗證通過 把輸入資料存放到該位登入使用者的變數($User)中，之後還要使用"$User->save();"才能將$User的內容存至DB中
        $User->sex = $input['sex'];
        $User->height = $input['height'];
        $User->weight = $input['weight'];
        $User->interest = $input['interest'];
        $User->introduce = $input['introduce'];

        
        //圖片處理
        // **處理圖片上傳和使用 Intervention Image**
        if (request()->hasFile('file') && request()->file('file')->isValid()) {
            $file = request()->file('file');

            // **1. 建立 ImageManager 實例 (選擇 Driver)**
            // 這裡使用 GD Driver，如果你的 PHP 環境支援 Imagick，也可以改用 new ImageManager(new Imagick\Driver())
            $manager = new ImageManager(new Driver());

            // **2. 讀取圖片檔案**
            $image = $manager->read($file->getRealPath()); // 從暫存路徑讀取圖片

            // **3. 圖片處理 (例如：調整大小)**
            // 將圖片縮放到最大寬度 120 像素，最大高度 120 像素，並保持比例：
            $image->resize(120, 120, function ($constraint) {
                $constraint->aspectRatio(); // 保持長寬比
                $constraint->upsize();      // 不放大圖片（如果原始圖片小於目標尺寸）
            }); 

             // **4. 定義儲存路徑和檔案名稱**
            // 獲取 public 目錄的絕對路徑
            $publicPath = public_path();
            $targetDirectory = $publicPath . '/images/user/';

            // 確保目標資料夾存在，如果不存在則創建它
            if (!file_exists($targetDirectory)) {
                mkdir($targetDirectory, 0775, true); // 遞迴創建，並設定權限
            }

            // **5. 定義儲存路徑和檔案名稱**
            // 建議使用更獨特的檔案名稱，例如結合時間戳和原始副檔名
            $extension = $file->getClientOriginalExtension();
            $fileName = 'user_picture_' . time() . '.' . $extension;
            $fullPath = $targetDirectory . $fileName;

            // **6. 儲存處理後的圖片到 public/images/user**
            $image->save($fullPath);

            // **7. 更新資料庫路徑**
            // 儲存在資料庫中的路徑應該是公開可訪問的 URL
            $User->picture = '/images/user/' . $fileName;
        }

        //將修改後的資料存入資料庫 //同SQL的update
        $User->save();
        
        $binding = [
            'title' => ShareData::TITLE,
            'page' => $this->page,
            'name' => $name,
            'User' => $User,
            'result' => 'success',//傳給前端 javascript來處理
        ];
         return view('admin.edituser', $binding);
    }


    //心情隨筆列表頁面 //在後台(master.php)按下"心情隨筆"超連結 -> router/web.php -> 找到AdminController.php的mindListPage() -> view('admin.mindlist') 相對路徑為admin/mind 所有心情隨筆的列表畫面
    public function mindListPage(){
        Log::notice('取得心情隨筆列表');
        //先取得自己的資料
        $User = $this->GetUserData();
        //取得心情隨筆列表from Mind table
        //在這裡我們有做分頁的動作,paginate(5)表示每頁5筆資料,如果超過就會顯示頁碼,換頁的動作Laravel會自己處理,
        $mindPaginate = Mind::where('user_id', $User->id)-> paginate(5);
        $name = 'mind';

        //接收輸入資料 
        //$input = request()->all(); //心情隨筆列表頁面 根本沒輸入資料阿!?

        // **核心修改：讀取 URL 中的 'result' 參數**
        // 使用 request()->query('result') 來獲取 URL 中的 ?result=success 參數
        $result = request()->query('result');

        //if(isset($input['result']))
        //    $result = $input['result'];

        $binding = [
            'title' => ShareData::TITLE,
            'page' => $this->page, //page的宣告為全域變數 = 'admin'
            'name' => $name,
            'User' => $User,
            'mindPaginate' =>  $mindPaginate,
            'result' => $result,//紀錄有無跳出成功訊息 if在URL中抓到?result=success則 $result = success
        ];
        //所有心情隨筆的列表畫面
        return view('admin.mindlist', $binding);
    }

    //新增心情隨筆資料 
    // 在所有心情隨筆的列表畫面 即路徑http://127.0.0.1:8000/admin/mind/中，按下"新增"按鈕後-> 依超連結之相對路徑為/admin/mind/add至router/web.php 找到對應的AdminController.php的addMindPage() 
    // ->view('admin.mind')  為心情隨筆之含取消和新增按鈕的畫面
    function addMindPage(){
        Log::notice('新增心情隨筆資料');
        //先取得自己的資料
        $User = $this->GetUserData();
        //取得心情隨筆列表
        $Mind = new Mind; //創建物件(class)
        $name = 'mind';
        $action = '新增';

         $binding = [
        'title' => ShareData::TITLE,
        'page' => $this->page,
        'name' => $name,
        'User' => $User,
        'Mind' => $Mind,
        'action' => $action,
        'result' => '',
        ];
        return view('admin.mind', $binding);
    }

    //編輯(取消或新增 / 修改)心情隨筆的動作 
    //(當在admin.mind.blade.php也就是在瀏覽器路徑/admin/mind/add中，當按下"新增 /修幹"按鈕的處理動作，使用到表格method=post和action="路徑"之兩參數)
    // 藉由相對路徑和post尋找對應的路由 AdminController.php@editMindProcess
    function editMindProcess(){
        Log::notice('處理心情隨筆資料');
        $User = $this->GetUserData();
        $mindPaginate = Mind::where('user_id', $User->id)-> paginate(5);
        if(!$User)
        {
            Log::notice('找不到使用者');
            //如果找不到使用者，就回到首頁
            return redirect('/');
        }
        $name = 'mind';
        //接收輸入資料
        $input = request()->all();
         //驗證規則
        $rules = [
            //內容
            'content' => [
                'required',
                'min:2'
            ],
        ];

        /**定義自定義訊息 (Custom Messages)**/
        $messages = [
            'content.min' => ':attribute 不能小於 :min 字!',
            'content.required' => ':attribute 不能為空',
        ];

        // **定義自定義屬性名稱 (Custom Attributes)**
        $customAttributes = [
            'content' => '內容',
        ];
        
        //驗證資料
         $validator = Validator::make($input, $rules, $messages, $customAttributes);
         
        if($validator->fails())
        {
            return redirect('/admin/mind/add') -> withErrors($validator)->withInput();
        }

        if($input['id'] == '')//新增
        {
            //新增
            $action = '新增';
            $Mind = new Mind;
            $Mind->content = $input['content'];

            //見App\Entity\Mind的資料表 input["column_name"]與資料表Mind中的column_name一致 方便使用$input(含所有輸入的資料)來建立資料
            $input["user_id"] = $User->id;
            $input["enabled"] = 1;
            //Laravel的Eloquent ORM的create()  同SQL的insert(新增資料)
            Mind::create($input);
        }
        else//修改，在/admin/mind(mindlist.php)按下"修改"按鈕，路由過程中會夾帶一個參數(資料表Mind中的id 見mindlist的JS函數)，之後要由該參數判別是屬於"新增"還是"修改"
        {
            //修改
            $action = '修改';
            //取得心情隨筆列表
            //在Mind中驗證有這筆資料(id)存在,再驗證這筆資料是屬於這個使用者的(user_id),
            $Mind = Mind::where('id', $input['id'])->where('user_id', $User->id)->first();

            if(!$Mind)
            {
                //如果找不到資料就回列表頁
                return redirect('/admin/mind');
            }
            $Mind->content = $input['content'];
            //修改 存入資料庫
            $Mind->save();
        }

        //成功就轉回列表頁
        /*$binding = [
            'result' => 'success',//傳給前端 javascript來處理
            'title' => ShareData::TITLE,
            'page' => $this->page,
            'name' => $name,
            'User' => $User,
            'mindPaginate' =>  $mindPaginate,
        ];*/
        //return view('admin.mindlist', $binding);//此時雖然頁面為心情隨筆列表，但路徑為填完表格後的action="/admin/mind/edit"，代表一頁面可以有好幾個路徑
        return redirect('/admin/mind/?result=success');   
    }

    function editMindPage($mind_id){
        Log::notice('修改心情隨筆資料');
        //先取得自己的資料
        $User = $this->GetUserData();
        //取得心情隨筆列表
        $Mind = Mind::where('id', $mind_id)->where('user_id', $User->id)->first();
        if(!$Mind)
        {
            //如果找不到資料就回列表頁
            return redirect('/admin/mind');
        }
        $name = 'mind';
        $action = '修改';

        $binding = [
            'title' => ShareData::TITLE,
            'page' => $this->page,
            'name' => $name,
            'User' => $User,
            'Mind' => $Mind,
            'action' => $action,
            'result' => '',
        ];
        return view('admin.mind', $binding);
    }


}
?>