<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Products;
use app\models\Customers;
use app\models\Suppliers;
use app\models\Invoices;
use app\models\Purchases;
use app\models\Estimations;
use app\models\Users;
use mdm\admin\models\User;
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','index'],
                'rules' => [
                    [
                        'actions' => ['logout','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
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
        $user = Users::findOne(2);
				
        $products = Products::find()->count();
        $customers = Customers::find()->count();
        $invoices = Invoices::find()->count();
        $suppliers = Suppliers::find()->count();
        $purchases = Purchases::find()->count();
        $estimations = Estimations::find()->count();
                       
        return $this->render('index',[
        
			'products' => $products,
			'customers' => $customers,
			'invoices' => $invoices,
			'suppliers' => $suppliers,
			'purchases' => $purchases,
			'estimations' => $estimations,
			'activation_status' => $user->activation_status,
			        
        ]);
    }
	
		
	public function actionActivation()
	{
		$user = Users::findOne(2);
		$activation_status = 0;	
			$error_message = "";
		if(Yii::$app->getRequest()->post())
		{	
			$real_user = User::findOne(2);
			$company_name = $_POST['Users']['company_name'];
			$phone = $_POST['Users']['phone'];
			$email = $_POST['Users']['email'];
			$address = $_POST['Users']['address'];
			$invoice_start_no = $_POST['Users']['invoice_start_no'];
			$username = $_POST['Users']['username'];
			$password = $_POST['Users']['password'];
			$act_key = $_POST['Users']['act_key'];
					
			if(empty($company_name))
				$error_message = "Company Name is required.";
			elseif(empty($phone))
				$error_message = "Phone Number is required.";
			elseif(empty($email))
				$error_message = "Email is required.";
			elseif(empty($address))
				$error_message = "Address is required.";
			elseif(empty($invoice_start_no))
				$error_message = "Invoice Start No is required.";
			elseif(empty($username))
				$error_message = "Username is required.";
			elseif(empty($password))
				$error_message = "Password is required.";
			
			
			if(isset($_POST["activate-button"]))
			{	
				if(empty($act_key))
					$error_message = "Activation key is required.";
			}
			
			if($error_message=="")
			{
				$real_user->company_name = $_POST['Users']['company_name'];
				$real_user->phone = $_POST['Users']['phone'];
				$real_user->email = $_POST['Users']['email'];
				$real_user->address = $_POST['Users']['address'];
				$real_user->invoice_start_no = $_POST['Users']['invoice_start_no'];
				$real_user->username = $_POST['Users']['username'];
				if(isset($_POST["activate-button"]))
				{
					$real_user->act_key = $_POST['Users']['act_key'];
					$activation_status = 1;
				}
				elseif(isset($_POST["request-activate-button"]))
					$activation_status = 2;
				$real_user->activation_status =	$activation_status;
				$real_user->setPassword($_POST['Users']['password']);
				$real_user->generateAuthKey();
				
					$url = 'http://smartacct.co.in/myadmin/activation.php';
					$fields = array(
						
						'date' => urlencode($real_user->act_date),
						'activation_key' => urlencode($real_user->act_key),
						'company_name' => urlencode($real_user->company_name),
						'phone' => urlencode($real_user->phone),
						'email' => urlencode($real_user->email),
						'address' => urlencode($real_user->address),
						'activation_status' => urlencode($activation_status)
											
					);
					$fields_string = "";
					//url-ify the data for the POST
					foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
					rtrim($fields_string, '&');

					//open connection
					$ch = curl_init();

					//set the url, number of POST vars, POST data
					curl_setopt($ch,CURLOPT_URL, $url);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch,CURLOPT_POST, count($fields));
					curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
					
					//execute post
					$result = curl_exec($ch);
					$response = json_decode($result);

					//close connection
					curl_close($ch);
													
				if($response->status=="success")
				{
					$real_user->save(false);
					return $this->redirect(['/admin/user/login']);
				}
				else
				{
					$real_user->act_key = "";
					$real_user->activation_status = 0;
					$error_message = "Activation Failure. Please verify your Activation Key or Internet Connection.";
					Yii::$app->session->setFlash('activationFormSubmitted');
					$real_user->save(false);
					$user = Users::findOne(2);
				}
			}
			else
			{
				$user->load(Yii::$app->request->post());
				Yii::$app->session->setFlash('activationFormSubmitted');
				
			}
		}
		$this->layout = "main-login";
		return $this->render('activation', [
            'model' => $user,
            'error_message' => $error_message,
        ]);
		
	}

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
		
		 
		return $this->redirect(['/admin/user/login']);
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
	public function actionCalculator()
    {
       shell_exec("calc");
		 
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

         return $this->redirect(['admin/user/login']);
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
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
	public function actionShortcuts()
    {
        return $this->render('shortcuts');
    }
}