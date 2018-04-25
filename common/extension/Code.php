<?php
/**
 * @user: thanatos
 */
namespace common\extension;

class Code
{
    const USER_EXIST = 50001;
    /** @var array common return code */
    public $common = [
        0 => '请求成功',
        -1 => '系统繁忙, 请稍候再试',
    ];

    /** @var array system return code */
    public $system = [

    ];

    /** @var array user return code */
    public $user = [
        50001 => '用户已经存在',
    ];



}