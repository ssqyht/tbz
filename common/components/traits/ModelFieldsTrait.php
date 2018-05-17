<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\components\traits;

use Yii;
use yii\base\Arrayable;

trait ModelFieldsTrait
{

    /**
     * 规范每个模型类返回值处理
     * @return mixed
     * @author thanatos <thanatos915@163.com>
     */
    public function fields()
    {
        $request = Yii::$app->request;
        $fields = parent::fields();

        if ($request->isFrontend())
            $fields = self::$frontendFields;

        // 整合其它值
        foreach ($this->extraFields() as $field => $definition) {
            $fields[$field] = $definition;
        }
        return $this->filterName($fields);
    }

    /**
     * 把带下划线的值改成大写
     * @param $fields
     * @return array
     * @author thanatos <thanatos915@163.com>
     */
    protected function filterName($fields)
    {
        $newFields = [];
        foreach ($fields as $k => $item) {
            if (is_string($item) && strpos($item, '_') !== false) {
                $newK = preg_replace_callback('%_([a-z0-9_])%i', function ($matches) {
                    return ucfirst($matches[1]);
                }, $item);
                $newFields[$newK] = function() use($item){
                    return $this->$item;
                };
            } else {
                $newFields[$k] = $item;
            }
        }

        return $newFields;
    }

}