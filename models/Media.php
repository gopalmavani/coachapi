<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "media".
 *
 * @property int $id
 * @property int $post_id
 * @property string $url
 * @property string $type
 * @property string $created_date
 * @property string $modified_date
 */
class Media extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id'], 'required'],
            [['post_id'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['url'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'url' => 'Url',
            'type' => 'Type',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
        ];
    }
}
