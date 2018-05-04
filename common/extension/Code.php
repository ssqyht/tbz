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
    const USER_OAUTH_KEY_NOT_FOUND = 50002;


    /** @var int 文件不存在 */
    const FILE_NOT_EXIST = 30001;
    const FILE_EXTENSION_NOT_ALLOW = 30002;

    const SERVER_FAILED = -1;
    const SERVER_SUCCESS = 0;
    const SERVER_UNAUTHORIZED = 10001;

    /** @var array common return code */
    public $common = [
        self::SERVER_SUCCESS => '请求成功',
        self::USER_EXIST => '系统繁忙, 请稍候再试',
        self::SERVER_UNAUTHORIZED => '验证失败',
    ];

    /** @var array system return code */
    public $system = [
        self::FILE_NOT_EXIST => '文件不存在',
        self::FILE_NOT_EXIST => '不允许的文件类型',
    ];

    /** @var array user return code */
    public $user = [
        self::USER_EXIST => '用户已经存在',
        self::USER_OAUTH_KEY_NOT_FOUND => '用户不存在',
    ];


}