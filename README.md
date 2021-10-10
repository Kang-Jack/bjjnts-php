
modifed and based on `https://gitee.com/qeq66/bjjnts.git`

京训钉自动学习
===============
> 因为平台只允许单设备登陆，脚本执行期间不要登陆平台，否则会导致学习失败！

### 环境需求
php+git+composer

### 使用方式
1.克隆本项目 


2.进入项目目录
`cd bjjnts`

3.安装依赖
`composer install`

4.编辑 **index.php** 填写账号密码

```
$userList = [
    ['username' => '账号1', 'password' => '密码1'],
    ['username' => '账号2', 'password' => '密码2'],
    ['username' => '账号3', 'password' => '密码3'],
];
```

5.以账号命名的287x287像素的jpg自拍照放入face文件夹

6.运行

```
php index.php
```



### 方法列表
```
// 登陆
$login = $service->login($username, $password);
// 课程列表
$courseList = $service->getCourseList();
// 章节列表
$unitList = $service->getUnitList(12473, 733);
// 章节详情
$unit = $service->getUnitInfo(12473, 733, 12493);
// 上传学习记录
$res = $service->studies(12473, 733, 12493, 12444, 0);
// 人机验证
$res = $service->smart(12473, 732, 12492);
// 人脸识别
$res = $service->face(12473, 731, 12486);

// 获取全部学习任务
$taskList = $service->getTaskList();
// 执行学习
run($taskList, $service);

```