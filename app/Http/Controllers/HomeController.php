<?PHP
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Module\ShareData;
use App\Entity\User;
use App\Entity\Mind;

class HomeController extends Controller
{

    public $page = ""; //$page = ""表示在部落格(首頁)，page="admin"表示在後台，page="user"在部落格(首頁)中點下某位user連結

    public function indexPage()
    {   
        $name = 'home';
        $userList = User::all(); //在資料庫的User資料表內的所有users
        
        $binding = [
            'title' => ShareData::TITLE,
            'page' => $this -> page,    
            'User' => $this->GetUserData(), //登入的該筆使用者
            'name' => $name,
            'userList' => $userList,//所有users, 會在home.blade.php內使用userList列出所有在DB中的使用者
        ];
        return view('home', $binding);
    }

    //自我介紹 當在部落格(首頁)按下某一個使用者的連結 -> web.php的路由 -> HomeController.php的userPage(使用者id) 到DB中尋找該位使用者 -> view
    public function userPage($user_id)
    {
        $this->page = 'user';
        $name = 'user';

        $userData = User::where('id', $user_id)->first();

        if(!$userData)
            return redirect('/');

        //$userData->sex = ShareData::GetSex($userData->sex);

        $binding = [
            'title' => ShareData::TITLE,
            'page' => $this->page,
            'name' => $name,
            'User' => $this->GetUserData(), //當前登入的該位user (user_id從session中獲取 再去查詢DB內是否有該資料 內容在Controller
            'userData' => $userData, //在DB中where找到的那位user
        ];
        return view('blog.user', $binding);
    }

    //心情隨筆
    public function mindPage($user_id)
    {
        $this->page = 'user';
        $name = 'mind';

        $userData = User::where('id', $user_id)->first();

        if(!$userData)
            return redirect('/');

        $mindList = Mind::where('user_id', $user_id)->orderby('created_at', 'desc')->get();

        $binding = [
            'title' => ShareData::TITLE,
            'page' => $this->page,
            'name' => $name,
            'User' => $this->GetUserData(),
            'userData' => $userData,
            'mindList' => $mindList,
        ];
        return view('blog.mind', $binding);
    }

}
?>