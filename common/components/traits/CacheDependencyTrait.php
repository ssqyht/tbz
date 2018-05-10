<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\components\traits;

use common\models\CacheDependency;
use Yii;
use yii\caching\DbDependency;

trait CacheDependencyTrait
{
    /**
     * @param string $cacheName
     * @param null $db
     * @return DbDependency
     * @author thanatos <thanatos915@163.com>
     */
    public static function getCacheDependency($cacheName, $db = null)
    {
        return new DbDependency([
            'db' => $db ?: Yii::$app->db,
            'sql' => CacheDependency::getDependencyCacheName($cacheName)
        ]);
    }
}