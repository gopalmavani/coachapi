<?php

namespace app\controllers;

use app\models\UserInfo;
use Yii;
use app\models\Users;
use app\models\UsersSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use yii\filters\VerbFilter;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{
    /**
     * {@inheritdoc}
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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
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

    public function actionResetpassword($id)
    {
        $this->layout = 'forget_password';
        return $this->render('resetpassword', [
            'id'=>$id,
        ]);
    }

    public function actionChangepassword($id)
    {
        if($id){
            $users = UserInfo::findOne($id);
            if($users){
                if($_POST['password']){
                    $password = md5($_POST['password']);
                    if($users->password == $password){
                        $result = [
                            "code" => 500,
                            "message" => "failed",
                            "token"=> "2",
                            "error"=> "password same as privious one",
                        ];
                    }else{
                        $users->password = $password;
                        if($users->save()){
                            $result = [
                                "code" => 200,
                                "message" => "success",
                                "token"=> "1",
                            ];
                        }else{
                            $result = [
                                "code" => 200,
                                "message" => "failed",
                                "error"=> $users->errors,
                            ];
                        }
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "error"=> "password blank"
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                    "error"=> "user not found"
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
                "error"=> "user id not found"
            ];
        }
        echo JSON::encode($result);
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
