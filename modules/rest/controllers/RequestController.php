<?php

namespace app\modules\rest\controllers;

use app\components\filters\AuthTokenFilter;
use app\components\helpers\MailerHelper;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Response;
use app\models\Request;
use yii;

class RequestController extends ActiveController
{
    public $modelClass = 'app\models\Request';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // Добавляем фильтр контента для установки формата JSON, - он определен в родительском классе
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];

        $behaviors['authenticator'] = [
            'class' => AuthTokenFilter::class,
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // Отключаем стандартные действия
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['index']);

        return $actions;
    }

    // Метод для получения списка заявок
    public function actionIndex()
    {
        $query = Request::find()->orderBy(['created_at' => SORT_DESC]);

        // Фильтрация по статусу
        if (!empty($_GET['status'])) {
            $query->andWhere(['status' => $_GET['status']]);
        }

        // Фильтрация по дате
        if (!empty($_GET['date_from']) && !empty($_GET['date_to'])) {
            $query->andFilterWhere(['between', 'updated_at', $_GET['date_from'], $_GET['date_to']]);
        }

        return $query->all();
    }

    // Создание заявки
    public function actionCreate()
    {
        $model = new Request();
        $model->load(Yii::$app->request->post(), '');
        $model->status = $model::STATUS_NEW;
        if ($model->save()) {
            // Отправляем уведомление пользователю
            $this->sendEmailNotification($model, 'Спасибо за обращение. Ваш запрос принят в работу');
            return ['message' => 'Заявка успешно создана'];
        } else {
            return ['error' => $model->getErrors()];
        }
    }

    // Метод для обновления статуса и ответа заявки
    public function actionUpdate($id)
    {

        $model = Request::findOne($id);

        if (!$model) {
            return ['error' => 'Заявка не найдена'];
        }

        $model->load(Yii::$app->request->getBodyParams(), '');
        $model->status = $model::STATUS_REVIEWED;

        if ($model->save()) {
            // Отправляем уведомление пользователю
            $this->sendEmailNotification($model, 'Ваш запрос обработан');
            return ['message' => 'Заявка обновлена'];
        } else {
            return ['error' => $model->getErrors()];
        }
    }

    // Отправка уведомления
    protected function sendEmailNotification($model, $text)
    {
        MailerHelper::sendEmailNotification(
            $model->email,
            $text,
            "Ответ: {$model::statusList()[$model->status]}"
        );
    }
}