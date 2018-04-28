<?php
/**
 * @user: thanatos
 */

namespace common\components\validators;


use yii\validators\Validator;

class MobileValidator extends Validator
{
    public $message;
    public $pattern = "/^1[34578]{1}\d{9}$/";

    public function init()
    {
        if ($this->message === null) {
            $this->message = '手机号格式不正确';
        }
        parent::init(); // TODO: Change the autogenerated stub
    }

    protected function validateValue($value)
    {
        $valid = preg_match($this->pattern, $value);
        return $valid ? null : [$this->message, []];
    }

}