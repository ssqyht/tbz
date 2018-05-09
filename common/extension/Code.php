<?php
/**
 * @user: thanatos
 */
namespace common\extension;

class Code
{
    /** @var int 用户已经存在 */
    const USER_EXIST = 50001;
    /** @var int 用户不存在 */
    const USER_NOT_FOUND = 50002;
    const USER_WRONG_PASSWORD = 50003;
    const USER_TOKEN_FAILED = '50004';


    /** @var int 文件不存在 */
    const FILE_NOT_EXIST = 30001;
    /** @var int 不允许上传 */
    const FILE_EXTENSION_NOT_ALLOW = 30002;
    /** @var int 目录不存在 */
    const DIR_NOT_EXIST = 30003;

    const SERVER_FAILED = -1;
    const SERVER_SUCCESS = 0;
    const SERVER_UNAUTHORIZED = 10001;
    const SERVER_NOT_PERMISSION = 10002;

    /** @var array common return code */
    public $common = [
        self::SERVER_SUCCESS => '请求成功',
        self::USER_EXIST => '系统繁忙, 请稍候再试',
        self::SERVER_UNAUTHORIZED => '验证失败',
    ];

    /** @var array system return code */
    public $system = [
        self::FILE_NOT_EXIST => '文件不存在',
        self::FILE_EXTENSION_NOT_ALLOW => '不允许的文件类型',
        self::DIR_NOT_EXIST => '目录不存在',
        self::SERVER_NOT_PERMISSION => '没有权限'
    ];

    /** @var array user return code */
    public $user = [
        self::USER_EXIST => '用户已经存在',
        self::USER_NOT_FOUND => '用户不存在',
        self::USER_WRONG_PASSWORD => '密码不正确',
        self::USER_TOKEN_FAILED => 'refreshToken不正确'
    ];


}