<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use  yii\web\Request;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\web\HeaderCollection ;
use yii\web\Application;
use app\models\GroupInfo;
use app\models\UserInfo;
use app\models\GroupMapping;
use app\models\DeviceLocation;
use yii\web\UploadedFile;


class FriendController extends ActiveController
{
    public $modelClass = 'app\models\Employee';
    // public $token = 'abc@1234'; //decrypted token
    public $token = '31528198109743225ff9d0cf04d1fdd1'; //md5 token encrypted

    public function beforeAction($action)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        if (isset(getallheaders()['Token']) && $this->token === getallheaders()['Token']) {
            return parent::beforeAction($action); // TODO: Change the autogenerated stub
        } else {
            http_response_code(401);
            echo Json::encode(array(
                "result" => false,
                "error" => "Unauthorized",
            ));
        }
    }


    public function actionSuggestedFriendList()
    {
        $result = [];
        $model = UserInfo::find()->select(['first_name','email','image','about_user'])->All();
        if(!empty($model)) {
            $result = [
                "code" => 200,
                "message" => "success",
                "userData" => [$model],
            ];
        }else{
            $result = [
                "code" => 500,
                "message" => "failed",
            ];
        }
        echo JSON::encode($result);
    }
}