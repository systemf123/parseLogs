<?php


namespace app\controllers;

use app\models\Logs;
use app\models\Filter;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class LogController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'log';

        $model = new Logs();

        $data = $model->dataLogs();
        $dataFilter = $model->getFilterData();

        $formFilter = new Filter();

        return $this->render('index', compact('data', 'formFilter', 'dataFilter'));
    }

    public function actionFilter() {

        $this->layout = 'filter';

        $formFilter = new Filter();
        $modelLogs = new Logs();
        $dataFilter = $modelLogs->getFilterData();

        if ($formFilter->load(Yii::$app->request->post()) && $formFilter->validate()) {

            $post = Yii::$app->request->post();

            if ($post['Filter']) {
                $filterArray = array_filter($post['Filter'], function($element) {
                    return !empty($element);
                });
                $data = $modelLogs->dataLogs($filterArray);
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = [
                'success' => true,
                'responseHTML' => $this->render('filter', compact('data', 'formFilter', 'dataFilter'))
            ];
            return $response;
        } else {
            $data = $modelLogs->dataLogs();
            // либо страница отображается первый раз, либо есть ошибка в данных
            return $this->render('index', compact('data', 'formFilter', 'dataFilter'));
        }
    }
}