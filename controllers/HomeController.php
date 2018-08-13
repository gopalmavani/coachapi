<?php
/**
 * Created by PhpStorm.
 * User: Deepak
 * Date: 8/13/2018
 * Time: 2:28 PM
 */


namespace app\controllers;
use Yii;
use yii\web\Controller;
use  yii\web\Request;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\web\HeaderCollection ;
use yii\web\Application;
use app\models\Users;
use app\models\UserInfo;
use app\models\DeviceLocation;


class HomeController extends ActiveController
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

    //Registration Api As per Required Parameters.
    public function actionRegister()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());

        $model = new UserInfo();
        $model->attributes = $request;
        $model->password = md5($request['password']);
        $model->date_of_registration = date('Y-m-d H:i:s');
        $model->created_date = date('Y-m-d H:i:s');
        $model->modified_date = date('Y-m-d H:i:s');
//          MD5 hash for admin@123 is : e6e061838856bf47e1de730719fb2609
        $model->user_token = md5(uniqid($model->user_id, true));
        if ($model->save()) {
            $result = [
                "code" => 200,
                "message" => "success",
                "userId" => $model->user_id,
                "userToken" => $model->user_token,
            ];
        } else {
            $result = [
                "code" => 500,
                "message" => [$model->errors],
            ];
        }
        echo JSON::encode($result);
    }

    //Login Api As per Required Parameters.
    public function actionLogin()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        if ((!empty($request['loginType'])) && (!empty($request['socialId'])) && ($request['userType'] == "coach/user") && (!empty($request['deviceId'])) && ($request['deviceType'] == "ios/android")) {
            if (!empty($request['email']) && !empty($request['password'])) {
                $user = UserInfo::findOne(["email" => $request['email'], "password" => md5($request['password'])]);
                if (!empty($user)) {
                    if($user->gender == 1){
                        $user->gender = "Female";
                    }else{
                        $user->gender = "male";
                    }
                    if($user->is_enabled == 1){
                        $user->is_enabled = "yes";
                    }else{
                        $user->is_enabled = "no";
                    }
                    $result = [
                        "code" => 200,
                        "status" => "success",
                        "userDetails" => $user,
                    ];
                } else {
                    $result = [
                        "code" => 500,
                        "message" => "Invalid user/Password",
                    ];
                }
            } else {
                $result = [
                    "code" => 500,
                    "message" => "email and password required",
                ];
            }
        } else {
            $result = [
                "code" => 500,
                "message" => "Invalid user/Password",
            ];
        }
        echo JSON::encode($result);
    }

    //Social Login Api As per Required Parameters.

    public function actionSocialLogin()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        if (($request['method'] == "login") && ($request['loginType'] == "facebook/google/insta/linkedin") && ($request['deviceId'] == "ezjdhhh455hh5jh565") && ($request['deviceType'] == "ios/android")) {
            if (!empty($request['email']) && ($request['socialId'] == "3e44r45r4")) {
                $user = UserInfo::findOne(["email" => $request['email']]);
                if (!empty($user)) {
                    if($user->gender == 1){
                        $user->gender = "Female";
                    }else{
                        $user->gender = "male";
                    }
                    if($user->is_enabled == 1){
                        $user->is_enabled = "yes";
                    }else{
                        $user->is_enabled = "no";
                    }
                    $result = [
                        "code" => 200,
                        "status" => "success",
                        "user Id" => $user['user_id'],
                        "userDetails" => $user,
                    ];
                } else {
                    $result = [
                        "code" => 500,
                        "message" => "Invalid user/Password",
                    ];
                }
            } else {
                $result = [
                    "code" => 500,
                    "message" => "email and password required",
                ];
            }
        } else {
            $result = [
                "code" => 500,
                "message" => "Invalid user/Password",
            ];
        }
        echo JSON::encode($result);
    }

    //get splash Api As per Required Parameters.

    public function actionSplash()
    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
//        if (($request['method'] == "getSplash")) {
            $result = [
                "code" => "200",
                "splash" => "http://localhost/influencer.ae/assets/img/influ.png",
            ];
//        } else {
//            $result = [
//                "code" => 500,
//                "message" => "Error Occured,Please try again later",
//            ];
//        }
        echo JSON::encode($result);
    }

    //register step -2 Api As per Required Parameters.

    public function actionRegisterStep2()

    {
        $result = [];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        if(!empty($request)){
            $id = $request['id'];
            $model = UserInfo::findOne($id);
//        $model->attributes = $request;
            $model->focus_areas = $request['focusArea'];
            $model->about_user = $request['aboutMe'];
            $model->goals = $request['goals'];
            $model->location = $request['location'];
            $model->profession = $request['working'];
            $model->modified_date = date('Y-m-d H:i:s');
            if ($model->save()) {
                $result = [
                    "code" => 200,
                    "message" => "success",
                    "userId" => $model->user_id,
                    "userToken" => $model->user_token,
                ];
            } else {
                $result = [
                    "code" => 500,
                    "message" => [$model->errors],
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => ["No Data Found"],
            ];
        }

        echo JSON::encode($result);
    }

}