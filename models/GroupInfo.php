<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "group_info".
 *
 * @property int $group_id
 * @property int $user_id
 * @property string $group_name
 * @property string $group_description
 * @property string $group_image
 * @property string $group_category
 * @property string $likes_count
 * @property int $group_status
 * @property string $created_date
 * @property string $modified_date
 *
 * @property UserInfo $user
 * @property GroupLikes[] $groupLikes
 * @property GroupMapping[] $groupMappings
 */
class GroupInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'group_name', 'created_date', 'modified_date'], 'required'],
            [['user_id', 'group_status'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['group_name', 'group_category'], 'string', 'max' => 50],
            [['group_description', 'group_image'], 'string', 'max' => 100],
            [['likes_count'], 'string', 'max' => 11],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'group_id' => 'Group ID',
            'user_id' => 'User ID',
            'group_name' => 'Group Name',
            'group_description' => 'Group Description',
            'group_image' => 'Group Image',
            'group_category' => 'Group Category',
            'likes_count' => 'Likes Count',
            'group_status' => 'Group Status',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupLikes()
    {
        return $this->hasMany(GroupLikes::className(), ['group_id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMappings()
    {
        return $this->hasMany(GroupMapping::className(), ['group_id' => 'group_id']);
    }
}
