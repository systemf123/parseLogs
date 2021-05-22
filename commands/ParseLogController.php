<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use Yii;
use app\components\Helper;
use app\models\Logs;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ParseLogController extends Controller
{
    public function actionIndex()
    {

        if(!file_exists(Yii::getAlias('@app')."/logs/modimio.access.log.1")) {
            return false;
        }
        $logs = file(Yii::getAlias('@app')."/logs/modimio.access.log.1");

        $i = 0;
        // подключение к базе данных
        $connection = \Yii::$app->db;
        // Составляем SQL запрос
        $sql = "INSERT INTO logs (`ip`,`date_time`,`timestamp`,`url`,`user_agent`,`operation`,`architecture`,`browser`) VALUE";
        foreach ($logs as $log) {
            $i++;
            //if ($i == 25000) break;
            $ip = Helper::parsePregMatch($log, '/([0-9]{1,3}[\.]){3}[0-9]{1,3}/');
            $date_time = Helper::parsePregMatch($log, '/[0-9]{1,2}\/[A-Z][a-z]{2,3}\/[0-9]{4}:[0-9]{2}:[0-9]{2}:[0-9]{2}/');
            $date_time = Helper::parseDateTime($date_time);
            $timestamp = Helper::parseForTimeStamp($date_time);
            $url = Helper::parsePregMatch($log, '/(GET|POST)(.*)HTTP/', 'url');
            $user_agent = Helper::parsePregMatch($log, '/((Opera|Mozilla)(.*))"/', 'user_agent');
            $operation = Helper::parseOperation($user_agent);
            $architecture = Helper::parseArchitecture($user_agent);
            $browser = Helper::parseBrowser($user_agent);

            if (!$ip) continue;

            $sql .= "('$ip', '$date_time', $timestamp, '$url', '$user_agent', '$operation', '$architecture', '$browser'),";
        }
        $sql = rtrim($sql, ',');
        $command = $connection->createCommand($sql);
        $command->execute();
        return ExitCode::OK;
    }

}
