<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;

?>

<h2>Transfer</h2>
<? $form = ActiveForm::begin(['id' => 'contact-form']); ?>
    <input type="hidden" name="TransactionForm[currencyFrom]" value="<?=$currency?>">
    <?=$form->field($model, 'userReceiverId')->dropDownList(
        ArrayHelper::map($userList, 'id', 'username'),
        ['options' => [0 => ['Selected'=>'selected']]
        ,'prompt' => ' -- Select Value --'])
     ?>
    <?=$form->field($model, 'currencyTo')->dropDownList(
       $curencyList,
        ['options' => [0 => ['Selected'=>'selected']]
        ,'prompt' => ' -- Select Value --'])
     ?>
     Available:<?=$senderWallet->balance?> <?=$currency?>
    <?= $form->field($model, 'amount') ?>
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    </div>

<?php ActiveForm::end(); ?>