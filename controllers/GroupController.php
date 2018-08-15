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


class GroupController extends ActiveController
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

    public function actionCreateGroup()
    {
        $result = [];
        $data = $_POST;
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        if(!empty($user_id)){
            $model = new GroupInfo();
            $model->attributes = Yii::$app->request->post();
            $image = UploadedFile::getInstancesByName('group_image');
            $model->created_date = date('Y-m-d H:i:s');
            $model->modified_date = date('Y-m-d H:i:s');
            if(!empty($image)){
                foreach ($image as $file){
                    $path = Yii::getAlias('@webroot').'/uploads/group/'.$file->name; //Generate your save file path here;
                    $file->saveAs($path); //Your uploaded file is saved, you can process it further from here
                    $model->group_image = $file->name;
                }
            }
            if($model->save()){
                if(!empty($data['memberList'])){
                    $datamember = explode(",",$data['memberList']);
                    foreach ($datamember as $user){
                        $GroupMapping = new GroupMapping();
                        $GroupMapping->user_id = $user;
                        $GroupMapping->added_by_user_id = $user_id;
                        $GroupMapping->group_id = $model->group_id;
                        $GroupMapping->created_date = date('Y-m-d H:i:s');
                        $GroupMapping->modified_date = date('Y-m-d H:i:s');
                        $GroupMapping->save();
                    }
                    $result = [
                        "code" => 200,
                        "message" => "success",
                    ];
                }else{
                    $result = [
                        "code" => 200,
                        "message" => "success",
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                    "errors" => [$model->errors],
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "user id not available",
            ];
        }
    echo JSON::encode($result);
    }

    //group list display

    public function actionGroupList()
    {
        $model = GroupInfo::find()->select(['group_name','group_id','group_image'])->All();
        $data = [];
        if(!empty($model)){
            foreach ($model as $user){
                $countuser = GroupMapping::findAll(['group_id'=>$user->group_id]);
                $countuser = count($countuser);
                array_push($data,["group_id"=>$user->group_id,"group_name"=>$user->group_name,"no_of_user"=>$countuser,"group_image"=>$user->group_image]);
            };
            $result = [
                "code" => 200,
                "message" => "success",
                "groupData" => $data,
            ];
        }else{
            $result = [
                "code" => 500,
                "message" => "no data",
            ];
        }
        echo JSON::encode($result);
    }

    //get Group Details

    public function actionGroupDetail()
    {
//        $model = GroupInfo::find()->select(['group_name','group_id','group_image'])->All();
//        $data = [];
//        if(!empty($model)){
//            foreach ($model as $user){
//                $countuser = GroupMapping::findAll(['group_id'=>$user->group_id]);
//                $countuser = count($countuser);
//                array_push($data,["group_id"=>$user->group_id,"group_name"=>$user->group_name,"no_of_user"=>$countuser,"group_image"=>$user->group_image]);
//            };
//            $result = [
//                "code" => 200,
//                "message" => $data,
//
//            ];
//        }else{
//            $result = [
//                "code" => 500,
//                "message" => "no data",
//            ];
//        }
//        echo JSON::encode($result);
    }

    //add member to group

    public function actionAddMemberGroup()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        $group_id = $request['group_id'];
        if(!empty($user_id && $group_id)){
            $grouped = GroupMapping::findOne(["user_id"=>$user_id,"group_id"=>$group_id]);
            if(empty($grouped)){
                $GroupMapping = new GroupMapping();
                $GroupMapping->group_id = $group_id;
                $GroupMapping->user_id = $user_id;
                $GroupMapping->added_by_user_id = $user_id;
                $GroupMapping->created_date = date('Y-m-d H:i:s');
                $GroupMapping->modified_date = date('Y-m-d H:i:s');
                if($GroupMapping->save()){
                    $result = [
                        "code" => 200,
                        "message" => "success",
                    ];
                }else{
                    $result = [
                        "code" => 500,
                        "message" => "failed",
                        "errors" => [$GroupMapping->errors],
                    ];
                }
            }else{
                $result = [
                    "code" => 500,
                    "message" => "Already Added Member",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "no data",
            ];
        }
        echo JSON::encode($result);
    }

    //Leave member to group

    public function actionLeaveMemberGroup()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        $group_id = $request['group_id'];
        if(!empty($user_id && $group_id)){
            $grouped = GroupMapping::findOne(["user_id"=>$user_id,"group_id"=>$group_id]);
            if($grouped->delete()){
                $result = [
                "code" => 200,
                "message" => "success",
            ];
            }else{
                $result = [
                    "code" => 500,
                    "message" => "failed",
                ];
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "no data",
            ];
        }
        echo JSON::encode($result);
    }

    //add group by

    public function actionAddGroup()
    {
        $headers = Yii::$app->request->headers;
        $user_id = $headers['user_id'];
        $request = JSON::decode(Yii::$app->request->getRawBody());
        $group_id = $request['group_id'];
        if(!empty($user_id && $group_id && $request['members_id'])){
            $datamember = explode(",",$request['members_id']);
            foreach ($datamember as $user){
                $grouped = GroupMapping::findOne(["user_id"=>$user,"group_id"=>$group_id]);
                if(empty($grouped)){
                    $GroupMapping = new GroupMapping();
                    $GroupMapping->user_id = $user;
                    $GroupMapping->added_by_user_id = $user_id;
                    $GroupMapping->group_id = $group_id;
                    $GroupMapping->created_date = date('Y-m-d H:i:s');
                    $GroupMapping->modified_date = date('Y-m-d H:i:s');
                    $GroupMapping->save();
                    $result = [
                        "code" => 200,
                        "message" => "success",
                    ];
                }
            }
        }else{
            $result = [
                "code" => 500,
                "message" => "no data",
            ];
        }
        echo JSON::encode($result);
    }
}