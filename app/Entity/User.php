<?PHP
namespace App\Entity;

use PHPUnit\Event\Runtime\PHP;
use Illuminate\Database\Eloquent\Model;
class User extends Model{
    //資料表名稱
    protected $table = 'users';
    //主鍵名稱
    protected $promaryKey = 'id';
    //可變動欄位
    protected $fillable = [
        //前端(sign_up.blade.php)的class_name也要照著資料表中的column_name命名
        'name',
        'account',
        'password',
        'type',
        'sex',
        'height',
        'weight',
        'interest',
        'introduce',
        'picture',
        'enabled',
        //created_at跟updated_at這兩個欄位Laravel會自己處理
    ];

}