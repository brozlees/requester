<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/*
 * Заявки
 * */

class Request extends ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_REVIEWED = 2;
    public static function tableName()
    {
        return 'requests';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['comment', 'created_at', 'updated_at'], 'safe'],
            ['comment', 'required', 'when' => function ($model) {
                return $model->status == self::STATUS_REVIEWED;
            }],
            [['status'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'email' => 'Email',
            'message' => 'Сообщение',
            'comment' => 'Комментарий ответственного',
            'created_at' => 'Дата создания заявки',
            'updated_at' => 'Дата ответа на заявку'
        ];
    }

    public static function statusList() {
        return [
            self::STATUS_NEW => 'Новая заявка',
            self::STATUS_REVIEWED => 'Заявка принята и обработана',
        ];
    }


}