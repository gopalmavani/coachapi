<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "friends_list".
 *
 * @property int $friend_list_id
 * @property int $user_id
 * @property int $requested_by
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
            [['user_id', 'requested_by'], 'required'],
            [['user_id', 'requested_by', 'status'], 'integer'],
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
            'requested_by' => 'Requested By',
            'status' => 'Status',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }
}
