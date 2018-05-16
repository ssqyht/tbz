<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\forms;

use Yii;
use common\components\traits\ModelErrorTrait;
use common\extension\Code;
use common\models\TemplateMember;
use common\models\TemplateOfficial;
use yii\base\Model;
use yii\helpers\Json;

/**
 * 模板保存接口
 * @property TemplateOfficial|TemplateMember $templateModel
 * @package common\models\forms
 * @author thanatos <thanatos915@163.com>
 */
class TemplateForm extends Model
{
    use ModelErrorTrait;

    /** @var string 官方模板保存 */
    const METHOD_SAVE_OFFICIAL = 'official';
    /** @var string 用户模板保存 */
    const METHOD_SAVE_MEMBER = 'member';

    /** @var string 前台保存 */
    const SCENARIO_FRONTEND = 'frontend';
    /** @var string 后台保存 */
    const SCENARIO_BACKEND = 'backend';

    /** @var string 保存方式 (用户还是官方) */
    public $method;
    public $template_id;
    public $product;
    public $title;
    public $price;
    public $virtual_edit;
    public $virtual_view;
    public $virtual_favorite;
    public $sort;
    public $is_diy;
    public $edit_from;
    public $content;

    public $_templateModel;

    public function rules()
    {
        return [
            [['method', 'product', 'title'], 'required'],
            [['virtual_edit', 'virtual_view', 'virtual_favorite', 'sort', 'is_diy', 'edit_from'], 'default', 'value' => 0],
            [['content'], 'default', 'value' => ''],
            ['method', 'default', 'value' => static::METHOD_SAVE_MEMBER],
            [['virtual_edit', 'virtual_view', 'virtual_favorite', 'sort'], 'integer'],
            ['method', 'in', 'range' => [static::METHOD_SAVE_MEMBER, static::METHOD_SAVE_OFFICIAL]],
            ['content', 'validateContent']
        ];
    }

    /**
     * 验证json格式
     * @author thanatos <thanatos915@163.com>
     */
    public function validateContent()
    {
        if (!$this->hasErrors()) {
            if (empty(Json::decode($this->content))) {
                $this->addError('content', Code::TEMPLATE_FORMAT_ERROR);
                return false;
            }
        }
    }

    public function scenarios()
    {
        return [
            static::SCENARIO_FRONTEND => ['title', 'is_diy', 'edit_from', 'content', 'method', 'template_id'],
            static::SCENARIO_BACKEND => ['product', 'title', 'price', 'virtual_edit', 'virtual_view', 'virtual_favorite', 'sort'],
        ];
    }


    public function submit($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }

        // 保存模板数据
        $this->templateModel->load($this->attributes, '');
       if (!$this->templateModel->save()) {
            $this->addErrors($this->templateModel->getErrors());
            return false;
        }

        return $this->templateModel;
    }

    /**
     * @return TemplateMember|TemplateOfficial|string
     * @author thanatos <thanatos915@163.com>
     * @internal
     */
    public function getTemplateModel()
    {
        if ($this->_templateModel === null) {
            switch ($this->method) {
                case static::METHOD_SAVE_OFFICIAL:
                    $model = TemplateOfficial::class;
                    break;
                case static::METHOD_SAVE_MEMBER:
                    $model = TemplateMember::class;
                    break;
                default:
                    $model = '';
            }
            $this->_templateModel = $model ? ($this->template_id ? $model::findById(['template_id' => $this->template_id]) : new $model) : false;
        }
        return $this->_templateModel;
    }


}