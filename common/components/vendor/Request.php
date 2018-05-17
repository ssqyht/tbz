<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\components\vendor;

use common\extension\Code;
use Yii;
use common\models\OauthPublicKeys;
use yii\web\ForbiddenHttpException;


/**
 * Class Request
 * @property OauthPublicKeys|false|null $client
 * @package common\components\vendor
 * @author thanatos <thanatos915@163.com>
 */
class Request extends \yii\web\Request
{
    /** @var OauthPublicKeys */
    public $_client;

    private $_handle;

    public function getClient()
    {
        // 处理客户端Client信息
        if ($this->_client === null) {
            $client_id = $this->headers->get('Client');
            $client = OauthPublicKeys::getClientById($client_id);
            $this->_client = empty($client) ? false : $client;
        }
        return $this->_client;
    }

    public function setClient($value)
    {
        $this->_client = OauthPublicKeys::getClientById($value);
    }

    /**
     * 判断当前请求是不是前端请求
     * @return bool
     * @author thanatos <thanatos915@163.com>
     */
    public function isFrontend()
    {
        if ($this->_handle === null) {
            $this->setHandle();
        }
        return $this->_handle == 'frontend';
    }

    /**
     * @param $value
     * @author thanatos <thanatos915@163.com>
     */
    public function setHandle($value = '')
    {
        $handle = $value ?: $this->headers->get('Handle');
        $this->_handle = $handle;
    }

}