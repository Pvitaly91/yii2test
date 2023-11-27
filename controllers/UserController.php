<?php
namespace app\controllers;
use Yii;
use app\models\User;
use yii\helpers\Url;
use app\models\Wallet;
use yii\web\Controller;
use app\models\Transaction;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\TransactionForm;
use yii\web\NotFoundHttpException;

class UserController extends Controller{

   
   

    function curl(){
       // https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5
       $ch = curl_init();
       $headers = array(
       'Accept: application/json',
       'Content-Type: application/json',
   
       );
       curl_setopt($ch, CURLOPT_URL,"https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5");
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_HEADER, 0);

       curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   
       // Timeout in seconds
       curl_setopt($ch, CURLOPT_TIMEOUT, 30);
   
       $authToken = curl_exec($ch);
       $info = curl_getinfo($ch);
       curl_close($ch);
       if($info["http_code"] == "200"){
            return $authToken;
       }else{
         //error nendler
       }
      
       
    }

    /**
     * 
     * Не нашел нормального сервиса которий напрямую конвертирует USD и EUR и наоборот
     * поетому реализаци конвертации через UAH 
     * 
     */
    function getCurrencyRation($currencyFrom,$currencyTo){
        
        $cuorses = json_decode($this->curl(),true);
        
        $buy = $sell = 0;
        foreach($cuorses as $item){
            if($currencyFrom == $item["ccy"]){
                $sell = $item["sale"];
            }
            if($currencyTo == $item["ccy"]){
                $buy = $item["buy"];
            }
        }
      
        return $sell/$buy;
    }
    function transaction(&$model){
        $transactionModel = new Transaction();
   
        $receiverWallet = Wallet::find()
            ->where(["user_id" => $model->userReceiverId])
            ->andWhere(["currency" => TransactionForm::$curencyList[$model->currencyTo]])
            ->one();
      
        $senderWallet = Wallet::find()
            ->where(["user_id" => Yii::$app->user->getId()])
            ->andWhere(["currency" => $model->currencyFrom])
            ->one();

        $amountFrom = $model->amount;
        
        if($senderWallet->currency == $receiverWallet->currency){
            $amountTo = $amountFrom;
        }else{
            $amountTo = $this->getCurrencyRation($senderWallet->currency,$receiverWallet->currency )*$model->amount;
        }
     
        if($senderWallet->balance < $model->amount){
            $model->addError("amount","Not enough on balance");
            return false;
        }else{
            $senderWallet->balance -=  $model->amount;
            $receiverWallet->balance += $amountTo;
        }

        $transactionModel->sender_wallet_id = $senderWallet->id;
        $transactionModel->receiver_wallet_id =  $receiverWallet->id;
        $transactionModel->currency_from = $senderWallet->currency;
        $transactionModel->amount_from = $amountFrom;
        $transactionModel->currency_to = $receiverWallet->currency;
        $transactionModel->amount_to = $amountTo;
        $transactionModel->timestamp = time();

        $transaction = Yii::$app->db->beginTransaction();

        if($transactionModel->save() && $senderWallet->save() && $receiverWallet->save()){
            $transaction->commit();
            return true;
        }else{
            $transaction->rollback();
            return false;
        }
   
    }
    function actionBalance(){
     
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $currentUser = Yii::$app->user->getIdentity();

        return $this->render("balance", ["wallets" => $currentUser->wallets]);
    }
    function actionTransfer(){
       
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
    
    
        if(($currency = Yii::$app->request->get("currency") ) != true || !in_array($currency, TransactionForm::$curencyList)){
            throw new NotFoundHttpException ();
        }
        $model = new TransactionForm();
        $curencyList =  TransactionForm::$curencyList;
        $userList= User::find()->where(["!=","id", Yii::$app->user->getId()])->all();

        if(Yii::$app->request->isPost && ($post = Yii::$app->request->post("TransactionForm")) == true){
          
          
            $model->setAttributes($post);
            if($model->validate() && $this->transaction($model)){
                $model = new TransactionForm();
                Yii::$app->session->setFlash('success', "The transfer was successful");
               
            }
        }
        $senderWallet = Wallet::find()
            ->where(["user_id" => Yii::$app->user->getId()])
            ->andWhere(["currency" => $currency])
            ->one();
        
        return $this->render('transfer',[ "model" => $model,"currency" => $currency,"userList" => $userList,"curencyList" => $curencyList,"senderWallet" => $senderWallet]);
    }
}