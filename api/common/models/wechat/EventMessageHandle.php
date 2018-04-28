<?php

namespace api\common\models\wechat;

use common\models\MemberOauth;
use common\models\forms\RegisterForm;

class EventMessageHandle extends BaseMessageHandle
{

    /**
     * 关注事件
     */
    public function eventSubscribe()
    {
        $subscribe_string = 'hi,等你好久啦！'. "\n".'认识帮主，设计不用愁。'. "\r\n" .'为您提供名片、宣传单、PPT、新媒体、电商等5大类40多个应用场景的精美模板。'. "\r\n" .'拖拉拽，秒出图。'. "\n" .'5分钟搞定设计'. "\r\n" .'登录图帮主电脑网页版极速体验吧~'."\r\n".'欢迎加入图帮主服务群：188123416，一起来玩转设计吧~'. "\r\n\r\n". '点击→“<a href="http://mp.weixin.qq.com/s/4uSPVB6vVwotywkpzQuBNA">这里</a>”挑选符合自己的女神定义，昭告世界你的“女神态度”';
        // 扫描带参数二维码关注
        if ($this->eventKey) {
            // 用户注册
            $model = new RegisterForm();
            $model->load([
                'username' => $this->wechatInfo->nickname,
                'sex' => (int)$this->wechatInfo->sex,
                'oauth_name' => MemberOauth::OAUTH_WECHAT,
                'oauth_key' => $this->wechatInfo->unionid,
                'headimgurl' => $this->wechatInfo->headimgurl,
            ], '');
            if ($member = $model->register()) {

            }
        } else {
            return $subscribe_string;
        }

    }


}