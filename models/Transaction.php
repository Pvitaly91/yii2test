<?php 

namespace app\models;
 
use Yii;
use app\models\User;
use yii\db\ActiveRecord;
class Transaction extends ActiveRecord{
    public static function tableName()
    {
        return 'transaction';
    }
   /* public function rules()
    {
        return [
            ['sender_wallet_id', 'required'],
            ['receiver_wallet_id', 'required'],
            ['amount_from', 'required'],
            ['currency_to', 'required'],
            ['currency_from', 'required'],

        ];
    }*/
    function getSender(){
        return $this->hasOne(User::class,["id" =>"user_id"]);
    }
    function getReceiver(){
        return $this->hasOne(User::class,["id" =>"user_id"]);
    }
}