<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Url;

class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionShorten()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $originalUrl = Yii::$app->request->post('url');

        if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            return ['error' => 'Некорректный URL'];
        }

        $existing = Url::findOne(['original_url' => $originalUrl]);
        if ($existing) {
            return ['short' => Yii::$app->request->hostInfo . '/r/' . $existing->short_code];
        }

        $model = new Url();
        $model->original_url = $originalUrl;
        $model->short_code = self::generateShortCode();
        if ($model->save()) {
            return ['short' => Yii::$app->request->hostInfo . '/r/' . $model->short_code];
        }

        return ['error' => 'Ошибка сохранения'];
    }

    private static function generateShortCode($length = 5)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($characters) - 1;
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $max)];
            }
        } while (Url::findOne(['short_code' => $code]));

        return $code;
    }

    public $enableCsrfValidation = false;

    public function actionRedirect($code)
    {
        $url = Url::findOne(['short_code' => $code]);
        if (!$url) {
            throw new \yii\web\NotFoundHttpException('Ссылка не найдена.');
        }

        // Проверка на бота
        $userAgent = Yii::$app->request->userAgent;
        $checkUrl = "http://qnits.net/api/checkUserAgent?userAgent=" . urlencode($userAgent);
        $isBot = false;

        try {
            $response = file_get_contents($checkUrl);
            $json = json_decode($response, true);
            $isBot = $json['isBot'] ?? false;
        } catch (\Exception $e) {
            // fallback: считаем не ботом
        }

        if (!$isBot) {
            Yii::$app->db->createCommand()->insert('click_log', [
                'url_id' => $url->id,
                'clicked_at' => new \yii\db\Expression('NOW()'),
            ])->execute();
        }

        return $this->redirect($url->original_url);
    }

}
