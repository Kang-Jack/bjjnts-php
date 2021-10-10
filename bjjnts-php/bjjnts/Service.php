<?php
namespace bjjnts;

use think\facade\Cache;
use Yurun\Util\HttpRequest;

/**
 *
 */
class Service
{
    public $http;
    public $username;
    public $password;

    public function __construct($username, $password)
    {
        // 缓存配置
        Cache::config([
            'default' => 'file',
            'stores'  => [
                'file'  => [
                    'type'   => 'File',
                    // 缓存保存目录
                    'path'   => './cache/',
                    // 缓存前缀
                    'prefix' => '',
                    // 缓存有效期 0表示永久缓存
                    'expire' => 0,
                ],
                'redis' => [
                    'type'     => 'redis',
                    'host'     => '127.0.0.1',
                    'port'     => 6379,
                    'password' => '',
                    'prefix'   => '',
                    'expire'   => 0,
                ],
            ],
        ]);
        $this->http     = new HttpRequest;
        $this->username = $username;
        $this->password = $password;

        // 初始化
        $this->init();
    }

    public function init()
    {
        echo  'call init'  . "\n";
        $this->login($this->username, $this->password);
        if (!$this->getInfo()) {
            $this->login($this->username, $this->password);
        }

    }

    /**
     * 登陆
     * @param  [type] $username [description]
     * @param  [type] $password [description]
     * @return [type]           [description]
     */
    public function login($username, $password)
    {
        echo  'call login'  . "\n";
        $res = $this->http->rawHeaders([
            'Connection: keep-alive',
            'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
            'Accept: application/json, text/plain, */*',
            'X-Client-Type: pc',
            'sec-ch-ua-mobile: ?0',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36',
            'Content-Type: application/json',
            'Origin: https://www.bjjnts.cn',
            'Sec-Fetch-Site: same-site',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Dest: empty',
            'Referer: https://www.bjjnts.cn/user/login',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
        ])->post('https://apif.bjjnts.cn/account/login', '{"username":"' . $username . '","password":"' . $password . '","type":1}', 'json');

        $data = $res->json(true);
        echo  '{"username":"' . $username . '","password":"' . $password . '","type":1}'  . "\n";
        $this->log(json_encode($data, JSON_UNESCAPED_UNICODE));

        if (!empty($data['access_token'])) {
            Cache::set($username, $data, 172800);
            return $data;
        }

        return false;
    }

    /**
     * 获取课程列表
     * @return [type] [description]
     */
    public function getCourseList()
    {
        echo  'getCourseList' . "\n";
        $res = $this->http->rawHeaders([
            'Connection: keep-alive',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
            'Accept: application/json, text/plain, */*',
            'Authorization: Bearer ' . $this->getToken(),
            'X-Client-Type: pc',
            'sec-ch-ua-mobile: ?0',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36',
            'Content-Type: application/json',
            'Origin: https://www.bjjnts.cn',
            'Sec-Fetch-Site: same-site',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Dest: empty',
            'Referer: https://www.bjjnts.cn/mine/student/study',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
        ])->get('https://apif.bjjnts.cn/student-center/study');

        $data = $res->json(true);

        return $data[0]['courseMap'];
    }

    /**
     * 获取章节列表
     * @param  [type] $classId   [description]
     * @param  [type] $courseId  [description]
     * @return [type]            [description]
     */
    public function getUnitList($classId, $courseId)
    {
        echo  'getUnitList' . "\n";
        $res = $this->http->rawHeaders([
            'Connection: keep-alive',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
            'Accept: application/json, text/plain, */*',
            'Authorization: Bearer ' . $this->getToken(),
            'X-Client-Type: pc',
            'sec-ch-ua-mobile: ?0',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36',
            'Content-Type: application/json',
            'Origin: https://www.bjjnts.cn',
            'Sec-Fetch-Site: same-site',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Dest: empty',
            'Referer: https://www.bjjnts.cn/study?course_id=' . $courseId . '&class_id=' . $classId,
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
        ])->get('https://apif.bjjnts.cn/courses/test-preview?course_id=' . $courseId . '&class_id=' . $classId);

        $data = $res->json(true);

        return $data['course'];
    }

    /**
     * 获取章节详情
     * @param  [type] $classId  [description]
     * @param  [type] $courseId [description]
     * @param  [type] $unitId   [description]
     * @return [type]           [description]
     */
    public function getUnitInfo($classId, $courseId, $unitId)
    {
        echo  'getUnitInfo' . "\n";
        $res = $this->http->rawHeaders([
            'Connection: keep-alive',
            'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
            'Accept: application/json, text/plain, */*',
            'Authorization: Bearer ' . $this->getToken(),
            'X-Client-Type: pc',
            'sec-ch-ua-mobile: ?0',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36',
            'Content-Type: application/json',
            'Origin: https://www.bjjnts.cn',
            'Sec-Fetch-Site: same-site',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Dest: empty',
            'Referer: https://www.bjjnts.cn/study/video?class_id=' . $classId . '&course_id=' . $courseId . '&unit_id=' . $unitId,
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
        ])->get('https://apif.bjjnts.cn/course-units/' . $unitId . '?class_id=' . $classId);

        $data = $res->json(true);

        return $data;
    }

    /**
     * 上报学习记录
     * @param  [type] $classId  [description]
     * @param  [type] $courseId [description]
     * @param  [type] $unitId   [description]
     * @param  [type] $videoId  [description]
     * @param  [type] $time     [description]
     * @return [type]           [description]
     */
    public function studies($classId, $courseId, $unitId, $videoId, $time, $isEnd = false)
    {
        echo  'studies' . "\n";
        $this->log(json_encode([$classId, $courseId, $unitId, $videoId, $time, $isEnd], JSON_UNESCAPED_UNICODE));

        $url = 'https://apif.bjjnts.cn/studies?video_id=' . $videoId . '&u=' . $this->getInfo()['id'] . '&time=' . $time . '&unit_id=' . $unitId . '&class_id=' . $classId;

        // 初次学习
        if ($time == 0) {
            $url .= '&start=1';
        }

        // 结束学习
        if ($isEnd) {
            $url .= '&end=1';
        }

        $res = $this->http->rawHeaders([
            'Connection: keep-alive',
            'Content-Length: 0',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
            'Accept: application/json, text/plain, */*',
            'Authorization: Bearer ' . $this->getToken(),
            'X-Client-Type: pc',
            'sec-ch-ua-mobile: ?0',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36',
            'Content-Type: application/json',
            'Origin: https://www.bjjnts.cn',
            'Sec-Fetch-Site: same-site',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Dest: empty',
            'Referer: https://www.bjjnts.cn/study/video?class_id=' . $classId . '&course_id=' . $courseId . '&unit_id=' . $unitId,
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
        ])->post($url);

        $data = $res->json(true);
        $this->log(json_encode($data, JSON_UNESCAPED_UNICODE));
        //var_dump($data);
        if (!empty($data['status'])) {
            switch ($data['status']) {
                case 401:
                    echo  'status' . "\n";
                    echo  $data['status'] . "\n";
                    echo  '401 login' . "\n";
                    $this->init();
                    break;
            }
        }  
        if (!empty($data['code'])) {
            switch ($data['code']) {
                // 人机验证
                case 2002:
                    echo  'smart' . "\n";
                    echo  $this->username . "\n";
                    $this->smart($classId, $courseId, $unitId);
                    $data['code'] = 4001;
                    break;
                // 人脸识别
                case 2003:
                    echo  'face' . "\n";
                    echo  $this->username . "\n";
                    $this->face($classId, $courseId, $unitId);
                    $data['code'] = 4001;
                    break;
            }
        }

        return $data;
    }

    /**
     * 人机检测
     * @param  [type] $classId  [description]
     * @param  [type] $courseId [description]
     * @param  [type] $unitId   [description]
     * @return [type]           [description]
     */
    public function smart($classId, $courseId, $unitId, $code = 1)
    {
        echo  'smart' . "\n";
        // 过验证需要重新实例化http
        $http = new HttpRequest;
        $res  = $http->rawHeaders([
            'Connection: keep-alive',
            'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
            'Accept: application/json, text/plain, */*',
            'Authorization: Bearer ' . $this->getToken(),
            'X-Client-Type: pc',
            'sec-ch-ua-mobile: ?0',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36',
            'Content-Type: application/json',
            'Origin: https://www.bjjnts.cn',
            'Sec-Fetch-Site: same-site',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Dest: empty',
            'Referer: https://www.bjjnts.cn/study/video?class_id=' . $classId . '&course_id=' . $courseId . '&unit_id=' . $unitId,
            'Accept-Language: zh-CN,zh;q=0.9',
        ])->post('https://apif.bjjnts.cn/supervises/smart-new', '{"code":"' . $code . '","course_id":"' . $courseId . '","unit_id":"' . $unitId . '"}', 'json');
        //])->post('https://apif.bjjnts.cn/supervises/smart-new', '{"course_id":"' . $courseId . '","unit_id":"' . $unitId . '","class_id":"' . $classId . '"}', 'json');
   
        $data = $res->json(true);
        echo '{"code":"' . $code . '","course_id":"' . $courseId . '","unit_id":"' . $unitId . '"}'  . "\n";
        $this->log(json_encode($data, JSON_UNESCAPED_UNICODE));
        
        if (!empty($data['status'])) {
            switch ($data['status']) {
                case 400:
                    echo  'smart code tring' . "\n";
                    $code = $code + 1;
                    
                    echo  $code . "\n";
                    // sleep(2);
                    $data = $this-> smart($classId, $courseId, $unitId,$code);
                   //var_dump($data);
                    break;
                default :
                    break;
            }
        }  
        return $data;
    }

    /**
     * 人脸识别
     * @param  [type] $classId  [description]
     * @param  [type] $courseId [description]
     * @param  [type] $unitId   [description]
     * @return [type]           [description]
     */
    public function face ($classId, $courseId, $unitId)
    {
        echo  'face' . "\n";
        // 过验证需要重新实例化http
        $http = new HttpRequest;
        $res  = $http->rawHeaders([
            'Connection: keep-alive',
            'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
            'Accept: application/json, text/plain, */*',
            'Authorization: Bearer ' . $this->getToken(),
            'X-Client-Type: pc',
            'sec-ch-ua-mobile: ?0',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36',
            'Content-Type: application/json',
            'Origin: https://www.bjjnts.cn',
            'Sec-Fetch-Site: same-site',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Dest: empty',
            'Referer: https://www.bjjnts.cn/study/video?class_id=' . $classId . '&course_id=' . $courseId . '&unit_id=' . $unitId,
            'Accept-Language: zh-CN,zh;q=0.9',
        ])->post('https://apif.bjjnts.cn/supervises', '{"baseImage":"' . $this->getFace() . '","course_id":"' . $courseId . '","unit_id":"' . $unitId . '","class_id":"' . $classId . '"}', 'json');

        $data = $res->json(true);

        $this->log(json_encode($data, JSON_UNESCAPED_UNICODE));

        return $data;
    }

    public function getTaskList()
    {
        echo  'getTaskList' . "\n";
        $cacheName = $this->username . '_List';
        $list      = Cache::get($cacheName);

        if (!$list) {
            // 重置数组
            $list = [];
            // 获取课程
            $courseList = $this->getCourseList();
            if ($courseList) {
                $courseIds = array_column($courseList, 'class_id', 'course_id');
                // 循环课程
                foreach ($courseIds as $courseId => $classId) {
                    // 获取章节
                    $unitList = $this->getUnitList($classId, $courseId);
                    if ($unitList) {
                        // 循环章节
                        foreach ($unitList as $key => $value) {
                             // echo  '循环章节' . "\n";
                            if (!empty($value['units'])) {
                                foreach ($value['units'] as $k => $v) {
                                    // 获取章节详情
                                    //echo  '获取章节详情' . "\n";
                                    $unitInfo = $this->getUnitInfo($classId, $courseId, $v['id']);
                                    $list[]   = [
                                        'class_id'      => $classId,
                                        'course_id'     => $unitInfo['video']['course_id'],
                                        'unit_id'       => $unitInfo['video']['unit_id'],
                                        'video_id'      => $unitInfo['video']['id'],
                                        'time'          => $unitInfo['video']['time'],
                                        'progress_time' => $unitInfo['progress_time'],
                                    ];
                                }
                            }
                            else{
                                if (!empty($value['sections'])) {
                                    foreach ($value['sections'] as $sec => $s) {
                                        if (!empty($s['units'])) {
                                            foreach ($s['units'] as $k => $v) {
                                                // 获取章节详情
                                               //echo  '获取章节详情' . "\n";
                                                $unitInfo = $this->getUnitInfo($classId, $courseId, $v['id']);
                                                if (!empty($unitInfo['video'])){
                                                    $list[]   = [
                                                        'class_id'      => $classId,
                                                        'course_id'     => $unitInfo['video']['course_id'],
                                                        'unit_id'       => $unitInfo['video']['unit_id'],
                                                        'video_id'      => $unitInfo['video']['id'],
                                                        'time'          => $unitInfo['video']['time'],
                                                        'progress_time' => $unitInfo['progress_time'],
                                                    ];
                                                }
                                                else
                                                {
                                                    echo  'unitInfo' . "\n";
                                                    var_dump($unitInfo);
                                                }
                                            }
                                        }

                                    }
                                }
                            };
                        }
                    }
                }
                echo  'cacheName' . "\n";
                echo  $cacheName . "\n";
                Cache::set($cacheName, $list);
            }
        }

        return $list;
    }

    public function setTaskList($taskList)
    {
        echo  'setTaskList' . "\n";
        $cacheName = $this->username . '_List';
        return Cache::set($cacheName, $taskList);
    }

    public function clearTaskList()
    {
        echo  'clearTaskList' . "\n";
        $cacheName = $this->username . '_List';
        return Cache::delete($cacheName);
    }

    public function getErrorCount()
    {
        echo  'getErrorCount' . "\n";
        $cacheName = $this->username . '_Error';
        return Cache::get($cacheName);
    }

    public function setErrorCount()
    {
        echo  'setErrorCount' . "\n";
        $cacheName = $this->username . '_Error';
        return Cache::inc($cacheName);
    }

    public function clearErrorCount()
    {
        echo  'clearErrorCount' . "\n";
        $cacheName = $this->username . '_Error';
        return Cache::delete($cacheName);
    }

    public function getFinish()
    {
        echo  'getFinish' . "\n";
        $cacheName = $this->username . '_Finish';
        $date      = Cache::get($cacheName);

        return $date && $date >= date('Ymd') ? true : false;
    }

    public function setFinish()
    {
        echo  'setFinish' . "\n";
        $cacheName = $this->username . '_Finish';
        return Cache::set($cacheName, date('Ymd'));
    }

    public function getInfo()
    {
        echo  'getInfo' . "\n";
        return Cache::get($this->username);
    }

    public function getToken()
    {
        //echo  'call getToken'  . "\n";
        $user = $this->getInfo();
        return $user['access_token'];
    }

    public function getFace()
    {
        $file = __DIR__ . "/../face/{$this->username}.jpg";
        //动态的把图片导入内存中
        $image = imagecreatefromjpeg($file);
        //指定字体颜色
        $col = imagecolorallocatealpha($image, 255, 255, 255, 100);
        //随机文字内容
        $content = rand(0, 9);
        //随机位置
        $x = $y = rand(0, 287);
        //给图片添加文字
        imagestring($image, 5, $x, $y, $content, $col);
        //开启缓冲区
        ob_start();
        imagejpeg($image);
        $image_data = ob_get_contents();
        ob_end_clean();
        //转为base64
        $base64 = base64_encode($image_data);
        return $base64;
    }

    public function log($log, $file = 'log.log')
    {
        file_put_contents(__DIR__ . '/../log/' . $this->username . $file, '[' . date('Y-m-d H:i:s') . ']' . $log . PHP_EOL, FILE_APPEND);
    }
}