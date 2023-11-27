<?php

namespace app\controllers;

use app\models\Transfer;
use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\ContactForm;
use app\models\Currency;
use app\models\Wallet;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    
  
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
     //  $user = User::find()->where(["id"=> Yii::$app->user->id])->one();
     //  dd($user->wallets[0]);
     //  $model = Wallet::find()->one();
     //   dd($model->user);
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
    /**
     * Create Default Wallets
     *
     * @param [type] $userId
     * @return void
     */
    public function createWallets($userId){
        $curency = ["EUR","USD"];

        foreach($curency as $code ){
            $wallet = new Wallet();
            $wallet->user_id = $userId;
            $wallet->currency = $code;
            $wallet->balance = "300";
            $wallet->save();
        }
    }
    /**
     * Register new user
     *
     * @return void
     */
    public function actionSignup()
    {
     
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                $this->createWallets($user->id);
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }
 
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }


}
