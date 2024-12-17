<?php

namespace app\components\filters;

use Yii;
use yii\base\ActionFilter;
use yii\web\HttpException;

class AuthTokenFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        $token = Yii::$app->request->headers->get('X-Auth-Token');

        if ($token !== Yii::$app->params['token']) {
            throw new HttpException(401, 'Unauthorized');
        }

        return true;
    }
}