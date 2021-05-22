<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;
class Logs extends ActiveRecord
{
    public $data = [];
    public $dataFilter = [];
    public static function tableName()
    {
        return '{{%logs}}';
    }

    public function dataLogs($where = [])
    {
        $whereSql = '';
        $whereSqlDataTime = '';
        if ($where) {
            $countValidDateTime = 0;
            $keyPrevDateTime = '';
            foreach ($where as $key=>$val) {
                if (!$val) continue;
                if ('timestampStart' == $key || 'timestampEnd' == $key) {
                    $keyPrevDateTime = $key;

                    $splitDateTime = explode('T', $val);
                    $splitDate = explode('-', $splitDateTime[0]);
                    $splitTime = explode(':', $splitDateTime[1]);
                    $where[$key] = mktime($splitTime[0], 0, 0, $splitDate[1], $splitDate[2], $splitDate[0]);

                    if ('timestampStart' == $key) {
                        $countValidDateTime++;
                        $whereSqlDataTime .= ' AND timestamp BETWEEN :' . $key . ' AND ';
                    } else if ('timestampEnd' == $key) {
                        $countValidDateTime++;
                        $whereSqlDataTime .= ':' . $key;
                    }
                } else {
                    $whereSql .= ' AND ' . $key . '=' . ':' . $key;
                }
            }
            if ($countValidDateTime !== 2) {
                unset($where[$keyPrevDateTime]);
            } else {
                $whereSql .= $whereSqlDataTime;
            }
            $whereSql = rtrim($whereSql, ' AND ');
        }
        Yii::debug($where);
        Yii::debug($whereSql);
        // подключение к базе данных
        $connection = \Yii::$app->db;
        // Составляем SQL запрос
        $sql = "
            SELECT YEAR(FROM_UNIXTIME(`timestamp`)) as 'Year', MONTH(FROM_UNIXTIME(`timestamp`)) as 'Month', DAY(FROM_UNIXTIME(`timestamp`)) as 'Day', HOUR(FROM_UNIXTIME(`timestamp`)) as 'Hour', Count(id) as 'CountRequest', Count(case when browser='Safari' then 1 end) as 'Safari', Count(case when browser='Chrome' then 1 end) as 'Chrome', Count(case when browser='Opera' then 1 end) as 'Opera', Count(case when browser='Firefox' then 1 end) as 'Firefox', Count(case when browser='IE11' then 1 end) as 'IE11',
    
            (SELECT url from logs WHERE YEAR(FROM_UNIXTIME(`timestamp`)) = Year AND 
                                             MONTH(FROM_UNIXTIME(`timestamp`)) = Month AND 
                                             DAY(FROM_UNIXTIME(`timestamp`)) = Day AND 
                                             HOUR(FROM_UNIXTIME(`timestamp`)) = Hour 
            GROUP BY url
             ORDER BY Count(id) DESC LIMIT 1
            ) as urlLeader
           
            FROM logs 
            WHERE timestamp <> 1621601059 $whereSql
            GROUP BY YEAR(FROM_UNIXTIME(`timestamp`)), MONTH(FROM_UNIXTIME(`timestamp`)), DAY(FROM_UNIXTIME(`timestamp`)), HOUR(FROM_UNIXTIME(`timestamp`)) 
        ";
        if (!$where) {
            $model = $connection->createCommand($sql);
        } else {
            $model = $connection->createCommand($sql)
                ->bindValues($where);
        }

        //Осуществляем запрос к базе данных, переменная $model содержит ассоциативный массив с данными
        $model = $model->queryAll();
        // вывод данных
        $this->data = $model;

        return $this->data;
    }

    public function getFilterData()
    {
        // подключение к базе данных
        $connection = \Yii::$app->db;
        // Get OS
        $model = $connection->createCommand("SELECT distinct `operation` FROM `logs` WHERE operation <> '' AND operation IS NOT NULL");
        $this->dataFilter['modelOS'] = $model->queryAll();
        // Get architecture
        $model = $connection->createCommand("SELECT distinct `architecture` FROM `logs` WHERE architecture <> '' AND architecture IS NOT NULL");
        $this->dataFilter['modelArchitecture'] = $model->queryAll();

        return $this->dataFilter;
    }
}