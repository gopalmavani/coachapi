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
 * @property string $role
 * @property int $gender
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
            [['first_name', 'email', 'password', 'role'], 'required'],
            [['dob', 'last_logged_in', 'date_of_registration', 'created_date', 'modified_date'], 'safe'],
            [['gender', 'is_active', 'is_enabled'], 'integer'],
            [['first_name', 'last_name', 'goals','password'], 'string', 'max' => 50],
            [['email', 'about_user', 'focus_areas', 'location', 'profession'], 'string', 'max' => 100],
            [['role'], 'string', 'max' => 20],
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
            'role' => 'Role',
            'gender' => 'Gender',
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
        ];
    }
}
