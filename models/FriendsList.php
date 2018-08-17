<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "friends_list".
 *
 * @property int $friend_list_id
 * @property int $user_id
 * @property int $friend_user_id
 * @property int $status
 * @property string $created_date
 * @property string $modified_date
 */
class FriendsList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'friends_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'friend_user_id'], 'required'],
            [['user_id', 'friend_user_id', 'status'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'friend_list_id' => 'Friend List ID',
            'user_id' => 'User ID',
            'friend_user_id' => 'Friend User ID',
            'status' => 'Status',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }
}
