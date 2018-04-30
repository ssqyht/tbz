# 图帮主后台Yii2

目录结构 
-------------------

```
common
    config/              系统公共配置
    models/              系统公共模型
    tests/               公共目录下的测试文件
console
    config/              控制台应用配置
    components           系统公共组件库
        helpers          系统助手类
        traits           traits
        validators       系统验证器
    extension            yii2扩展
    controllers/         控制台命令控制器 
    migrations/          数据库表迁移目录
    models/              控制台模型
    runtime/             contains files generated during runtimea
api
    common               
        controllers/     api公共控制器
        models/          api公共模型
    config/              配置目录
    controllers          默认控制器
    modules
        v1               版本模块
            controllers  控制器目录
            models       模块目录
vendor/                  第三方包
environments/            环境配置
```
