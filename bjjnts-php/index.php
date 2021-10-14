<?php
require __DIR__ . '/vendor/autoload.php';
use bjjnts\Service;
/*
// 多用户学习
$userList = [
    ['username' => 'name1', 'password' => 'pass1'],
    ['username' => 'name2', 'password' => 'pass2'],
    //['username' => 'name3', 'password' => 'pass3'],
];

// 循环实例化
foreach ($userList as $user) {
    $service[$user['username']] = new Service($user['username'], $user['password']);
}

// 循环执行
while (true) {
    foreach ($userList as $user) {
        $taskList[$user['username']] = $service[$user['username']]->getTaskList();
        run($taskList[$user['username']], $service[$user['username']]);
    }
    sleep(61);
}*/
 


// 单用户学习
$username = 'name';
$password = 'password';

// 实例化
$service = new Service($username, $password);

// 循环执行
while (true) {
$taskList = $service->getTaskList();
echo  'taskList' . "\n";

run($taskList, $service);
sleep(61);
}
 

function run($taskList, $service)
{
    if (!$taskList || $service->getFinish()) {
        echo  'return 1' . "\n";
        return;
    }

    if ($taskList[0]['time'] == $taskList[0]['progress_time']) {
        echo  'run tasklist' . "\n";
        echo  'taskList-time' . "\n";
        echo  $taskList[0]['time'] . "\n";
        echo  'taskList-progress_time' . "\n";
        echo  $taskList[0]['progress_time']. "\n";
        array_shift($taskList);
        echo  'array_shift time' . "\n";
        echo  $taskList[0]['time'] . "\n";
        echo  'array_shift progress_time' . "\n";
        echo  $taskList[0]['progress_time'] . "\n";
        run($taskList, $service);
        //echo  'run tasklist' . "\n";
    } else {
        $time = 60;
        if ($taskList[0]['progress_time'] == 0) {
            $time = 0;
        }
        if ($taskList[0]['progress_time'] == 1) {
            $time = 59;
        }
        $isEnd = false;
        if ($taskList[0]['time'] - $taskList[0]['progress_time'] < 60) {
            $isEnd = true;
            $time  = $taskList[0]['time'] - $taskList[0]['progress_time'];
        }
        echo  'studies 1' . "\n";
        $res = $service->studies($taskList[0]['class_id'], $taskList[0]['course_id'], $taskList[0]['unit_id'], $taskList[0]['video_id'], $taskList[0]['progress_time'] + $time, $isEnd);

        // 正常返回
        if ( $res == null || !empty($res['id'])) {
            $taskList[0]['progress_time'] += $time ?: 1;
            $service->setTaskList($taskList);
            echo  'studies progress_time ok' . "\n";
            echo  '{"class_id":"' . $taskList[0]['class_id'] . '","course_id":"' . $taskList[0]['course_id'] . '","unit_id":"'. $taskList[0]['course_id'] . '}' . "\n";
            echo  '{"video_id":"' . $taskList[0]['video_id'] . '}' . "\n";
            echo  'studies progress_time (seconds)' . "\n";
            echo  $taskList[0]['progress_time'] . "\n";
        }
        // 学习进度错误
        elseif (!empty($res['code']) && $res['code'] == 3001) {
            // 错误3次重置学习列表
            if ($service->getErrorCount() >= 3) {
                $service->clearTaskList();
            } else {
                $service->setErrorCount();
            }
            echo  'studies progress_time Error' . "\n";
        }

        elseif (!empty($res['code']) && $res['code'] == 4001) {
            // pass validation 重置学习列表
            $service->clearTaskList();
            echo  'pass validation reset tasklist' . "\n";
        }
        // 学习时间限制
        elseif (!empty($res['code']) && $res['code'] == 3003) {
            $service->setFinish();
            echo  'studies progress_time timelimt' . "\n";
        }

        return $res;
    }
}
