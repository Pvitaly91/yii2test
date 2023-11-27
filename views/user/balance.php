<? 
use yii\helpers\Url;
?>
<h2>Balance:</h2>
<ul>
    <? foreach($wallets as $wallet):?>
        <li><?=$wallet->currency ?>: <?=$wallet->balance ?> <a href="<?=Url::to(['user/transfer', 'currency' => $wallet->currency])?>">Сreate a transfer</a></li>
    <? endforeach;?>   
</ul> 