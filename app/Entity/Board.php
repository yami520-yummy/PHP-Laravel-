<?PHP
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class Board extends Model {
    //資料表名稱
    protected $table = 'board';

    //主鍵名稱
    protected $promaryKey = 'id';

    //可變動欄位
    protected $fillable = [
        'user_id',
        'email',
        'picture',
        'content',
        'enabled',
    ];
}
?>