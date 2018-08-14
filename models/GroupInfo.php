<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "group_info".
 *
 * @property int $group_id
 * @property string $group_name
 * @property string $group_description
 * @property string $group_author
 * @property string $group_image
 * @property string $group_category
 * @property int $group_status
 * @property string $created_date
 * @property string $modified_date
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
            [['group_name', 'created_date', 'modified_date'], 'required'],
            [['group_status'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['group_name', 'group_author', 'group_category'], 'string', 'max' => 50],
            [['group_description', 'group_image'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'group_id' => 'Group ID',
            'group_name' => 'Group Name',
            'group_description' => 'Group Description',
            'group_author' => 'Group Author',
            'group_image' => 'Group Image',
            'group_category' => 'Group Category',
            'group_status' => 'Group Status',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }
}
