<?php

namespace app\components\behaviors;

use yii\web\HttpException;
use Yii;


class AuthTokenBehavior extends \yii\base\Behavior
{
    public function events()
    {
        return [
            \yii\web\Application::EVENT_BEFORE_REQUEST => 'checkAuthToken',
        ];
    }


    // Проверка токена
    public function checkAuthToken()
    {
        $token = Yii::$app->request->headers->get('X-Auth-Token');

        if ($token !== Yii::$app->params['token']) {
            throw new HttpException(401, 'Unauthorized');
        }
    }
}
