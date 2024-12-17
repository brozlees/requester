<?php

namespace app\components\helpers;

use Yii;
use yii\mail\BaseMailer;

class MailerHelper
{
    /**
     * Отправляет email-уведомление
     *
     * @param string $to Email получателя
     * @param string $subject Тема письма
     * @param string $body Текст письма
     */
    public static function sendEmailNotification(string $to, string $subject, string $body)
    {
        /** @var BaseMailer $mailer */
        $mailer = Yii::$app->mailer;
        $mailer->viewPath = '@app/mail'; // Указываем путь к представлениям писем
        $mailer->compose('notification', compact('body'))
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}
