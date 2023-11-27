<?
namespace app\models;
 
use Yii;
use app\models\User;
use yii\db\ActiveRecord;


class Wallet extends ActiveRecord{
    public static function tableName()
    {
        return 'wallet';

    }
    function getUser(){
        return $this->hasOne(User::class,["id" =>"user_id"]);
    }

   
}