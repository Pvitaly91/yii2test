<?php 
namespace app\models;
 
use Yii;
use yii\base\Model;
class TransactionForm extends Model{
    public $userReceiverId;
    public $amount;

    public $currencyTo;
    public $currencyFrom;
    public static $curencyList = ["1" => "EUR","2" => "USD"];
    function rules(){
        return [
            ['userReceiverId', 'required'],
            ['userReceiverId', 'checkUserToReceive'],
            ['amount', 'required'],
            ['amount', 'number'],
            ['currencyTo', 'required'],
            ['currencyTo', 'checkCurrencyTo'],
            ['currencyFrom', 'required'],
            ['currencyFrom', 'checkCurrencyFrom'],
        ];
    }
    function checkUserToReceive($attribute, $params){
        if(Yii::$app->user->getId() == $this->attributes[$attribute] || !User::findOne($this->attributes[$attribute])){
            $this->addError($attribute,"Incorrect");
        }
   
    }
    function checkCurrencyTo($attribute, $params){

        if(!Wallet::findOne($this->attributes[$attribute])){
            $this->addError($attribute,"Invalid currency");
        }
    }
    function checkCurrencyFrom($attribute, $params){
  
       if(!in_array($this->attributes[$attribute],self::$curencyList)){
            $this->addError($attribute,"Invalid currency");
       }
    }
    public function attributeLabels(){
        return [
            "userReceiverId" => "User to receive",
            "currencyTo" => "Recipient's currency",
            "amount" => "Amount"
        ];
     }

}