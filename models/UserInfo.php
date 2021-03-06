<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_info".
 *
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $dob
 * @property string $user_type
 * @property int $gender
 * @property string $house_number
 * @property string $region
 * @property string $city
 * @property string $country
 * @property string $pincode
 * @property string $phone
 * @property string $about_user
 * @property string $goals
 * @property string $focus_areas
 * @property string $location
 * @property string $profession
 * @property int $is_active
 * @property int $is_enabled
 * @property string $last_logged_in
 * @property string $date_of_registration
 * @property string $created_date
 * @property string $modified_date
 * @property string $user_token
 * @property string $social_id
 * @property string $image
 * @property int $likes_count
 *
 * @property DeviceLocation[] $deviceLocations
 * @property FriendsList[] $friendsLists
 * @property FriendsList[] $friendsLists0
 * @property GroupInfo[] $groupInfos
 * @property GroupLikes[] $groupLikes
 * @property GroupMapping[] $groupMappings
 * @property PostComments[] $postComments
 * @property PostLikes[] $postLikes
 * @property Posts[] $posts
 */
class UserInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'email', 'password', 'user_type'], 'required'],
            [['dob', 'last_logged_in', 'date_of_registration', 'created_date', 'modified_date'], 'safe'],
            [['gender', 'is_active', 'is_enabled', 'likes_count'], 'integer'],
            [['first_name', 'last_name', 'password', 'region', 'city', 'country', 'goals', 'user_token', 'social_id'], 'string', 'max' => 50],
            [['email', 'about_user', 'focus_areas', 'location', 'profession', 'image'], 'string', 'max' => 100],
            [['user_type', 'house_number'], 'string', 'max' => 20],
            [['pincode'], 'string', 'max' => 10],
            [['phone'], 'string', 'max' => 12],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'password' => 'Password',
            'dob' => 'Dob',
            'user_type' => 'User Type',
            'gender' => 'Gender',
            'house_number' => 'House Number',
            'region' => 'Region',
            'city' => 'City',
            'country' => 'Country',
            'pincode' => 'Pincode',
            'phone' => 'Phone',
            'about_user' => 'About User',
            'goals' => 'Goals',
            'focus_areas' => 'Focus Areas',
            'location' => 'Location',
            'profession' => 'Profession',
            'is_active' => 'Is Active',
            'is_enabled' => 'Is Enabled',
            'last_logged_in' => 'Last Logged In',
            'date_of_registration' => 'Date Of Registration',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
            'user_token' => 'User Token',
            'social_id' => 'Social ID',
            'image' => 'Image',
            'likes_count' => 'Likes Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceLocations()
    {
        return $this->hasMany(DeviceLocation::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFriendsLists()
    {
        return $this->hasMany(FriendsList::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFriendsLists0()
    {
        return $this->hasMany(FriendsList::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupInfos()
    {
        return $this->hasMany(GroupInfo::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupLikes()
    {
        return $this->hasMany(GroupLikes::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMappings()
    {
        return $this->hasMany(GroupMapping::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostComments()
    {
        return $this->hasMany(PostComments::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostLikes()
    {
        return $this->hasMany(PostLikes::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Posts::className(), ['user_id' => 'user_id']);
    }
}
