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
use app\models\FriendsList;
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

    //Suggested friend list display

    public function actionSuggestedFriendList()
    {
        $result = [];
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $users =  UserInfo::findOne($user_id);
            if($users) {
                $request = JSON::decode(Yii::$app->request->getRawBody());
                if(!empty($request['latitude']) && !empty($request['longitude'])){
                    $latitude = $request['latitude'];
                    $longitude = $request['longitude'];
                    $connection = Yii::$app->getDb();
                    //example sql
                    // SELECT device_id,user_id, ( 3959 * acos( cos( radians(23.0168) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(72.5003) ) + sin( radians(23.0168) ) * sin( radians( latitude ) ) ) ) AS distance FROM device_location HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;
                    // there are distance in miles so .
                    // 1 kilometer is equal to 0.62137119 miles .
                    // so  5 kilometer is equal to 3.1068559612 miles .
                    $command = $connection->createCommand("SELECT user_id, ( 3959 * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ) ) ) AS distance FROM device_location HAVING distance < 3.1068559612 ORDER BY distance");
                    $result = $command->queryAll();
                    if(!empty($result)){
                        $SuggestedFriends = [];
                        foreach ($result as $userSuggest){
                            $model = UserInfo::findOne($userSuggest['user_id']);
                            if($model['user_id'] == $user_id){
                            }else{
                                array_push($SuggestedFriends,array(
                                    "user_id "=> $model['user_id'],
                                    "fullname" => $model['first_name'],
                                    "email" => $model['email'],
                                    "location" => $model['location'],
                                    "aboutme" => $model['about_user'],
                                    "user_profile_image" => $model['image']
                                ));
                            }
                        }
                        $result = [
                            "code" => 200,
                            "message" => "success",
                            "userData" => $SuggestedFriends,
                        ];
                    }else{
                        $result = [
                            "code" => 200,
                            "message" => "success",
                            "userData" => [],
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
//                        "message" => "failed",
                        "message"=> "latitude or longtitude can not blank"
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
//                    "message" => "failed",
                    "message"=> "user not found"
                ];
            }
        }else{
            $result = [
                "code" => 500,
//                "message" => "failed",
                "message"=> "user id can not blank"
            ];
        }
        echo JSON::encode($result);
    }

    //add friend Request

    public function actionAddFriendRequest()
    {
        $result = [];
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $users =  UserInfo::findOne($user_id);
            if($users){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                if(!empty($request)){
                    $request_id = $request['friend_user_id'];
                    if(!empty($request_id)){
                        $friend = FriendsList::findOne(["friend_user_id"=>$request_id,"user_id"=>$user_id]);
                        $user = UserInfo::findOne(["user_id"=>$request_id]);
                        if(!empty($user)){
                            if(empty($friend)){
                                $model = new FriendsList();
                                $model->friend_user_id = $request_id;
                                $model->user_id = $user_id;
                                if($model->save()){
                                    $result = [
                                        "code" => 200,
                                        "status" => "success",
                                        "friend_request_id" => $model->friend_list_id,
                                    ];
                                }else{
                                    $result = [
                                        "code" => 500,
                                        "message" => "failed",
                                        "error"=> $model->errors,
                                    ];
                                }
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "request alreaady sent",
                                ];
                            }
                        }else{
                            $result = [
                                "code" => 500,
                                "message" => "requested friend is not found",
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "request id not found",
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "friend_user_id can not blank",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "user id can not blank",
            ];
        }
        echo JSON::encode($result);
    }

    //accept friend Request

    public function actionAcceptFriendRequest()
    {
        $result = [];
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $users =  UserInfo::findOne($user_id);
            if($users){
                $request = JSON::decode(Yii::$app->request->getRawBody());
                if(isset($request['friend_request_id'])){
                    if(isset($request['status'])){
                        $response_id = $request['friend_request_id'];
                        $friend = FriendsList::findOne(["friend_list_id"=>$response_id,"friend_user_id"=>$user_id]);
                        if(!empty($friend)){
                            $friend->status = $request['status'];
                            if($friend->save()){
                                $result = [
                                    "code" => 200,
                                    "status" => "success",
                                ];
                            }else{
                                $result = [
                                    "code" => 500,
                                    "message" => "failed",
                                    "error"=>$friend->errors,
                                ];
                            }
                        }else{
                            $result = [
                                "code" => 500,
                                "message" => "request not found",
                            ];
                        }
                    }else{
                        $result = [
                            "code" => 500,
                            "message" => "status can not blank",
                        ];
                    }
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "friend_request_id can not blank",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "user not found",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "user id can not blank",
            ];
        }
        echo JSON::encode($result);
    }
}