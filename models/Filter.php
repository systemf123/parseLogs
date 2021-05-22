<?php
namespace app\models;

use Yii;
use yii\base\Model;

class Filter extends Model {

    public $architecture;
    public $operation;
    public $timestampStart;
    public $timestampEnd;

    public function attributeLabels() {
        return [
            'architecture' => 'По архитектуре',
            'operation' => 'По ОС',
            'timestampStart' => 'С этой даты',
            'timestampEnd' => 'До этой даты',
        ];
    }

    public function rules() {
        return [
            // удалить пробелы для всех трех полей формы
            [['architecture', 'operation', 'timestampStart', 'timestampEnd'], 'trim'],
        ];
    }
}
