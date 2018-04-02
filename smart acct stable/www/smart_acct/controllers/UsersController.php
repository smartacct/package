<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    public function actionProfile()
    {
		$model = $this->findModel(Yii::$app->user->id);
        
        if ($model->load(Yii::$app->request->post())) {
			
			if($model->activation_status!=1 && $model->act_key!="" && $model->id==2)
			{
				$model->activation_status = 1; 
				$url = 'http://smartacct.co.in/myadmin/activation.php';
					$fields = array(
						
						'date' => urlencode($model->act_date),
						'activation_key' => urlencode($model->act_key),
						'company_name' => urlencode($model->company_name),
						'phone' => urlencode($model->phone),
						'email' => urlencode($model->email),
						'address' => urlencode($model->address),
						'activation_status' => urlencode($model->activation_status)
											
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
					
				if($response->status!="success")
				{
					$model->act_key = "";
					$model->activation_status = 0;
					
				}
				
			}
			
			$model->save(false);
            return $this->redirect(['site/index']);
           
        }

        return $this->render('update', [
            'model' => $model,
        ]);
	}

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
