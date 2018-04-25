<?php

namespace api\common\models\wechat;

use EasyWeChat\Kernel\Messages\Text;
use thanatos\wechat\MessageHandler;

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

        } else {
            return $subscribe_string;
        }

    }


}