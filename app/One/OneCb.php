<?php

namespace App\One;

use Exception;
use Session;
use Cache;


class OneCb
{
    public static function getAllCBsOptionTags(){
        return [
            'ALLOW-REPORT-ABUSE' => 'security_allow_report_abuses',
            'ALLOW-COMMENTS' => 'topic_comments_allow_comments',
            'CREATE-TOPIC' => 'security_create_topics',
            'CREATE-TOPICS-ANONYMOUS' => 'security_create_topics_anonymous',
            'PUBLIC-ACCESS' => 'security_public_access',
            'COMMENT-NEEDS-AUTHORIZATION' => 'security_comment_authorization',
            'COMMENTS-ANONYMOUS' => 'security_anonymous_comments',
            'ALLOW-CO-OP' => 'topic_options_allow_co_op',
            'ALLOW-FILES' => 'topic_options_allow_files',
            'ALLOW-FOLLOW' => 'topic_options_allow_follow',
            'ALLOW-PICTURES' => 'topic_options_allow_pictures',
            'ALLOW-SHARE' => 'topic_options_allow_share',
            'ALLOW-USER-COUNT' => 'topic_options_allow_user_count',
            'ALLOW-VIDEO-LINK' => 'topic_options_allow_video_link',
            'ALLOW-LIKES' => '',
            'TOPIC-NEED-MODERATION' => 'topic_need_moderation',
            'TOPIC-COMMENTS-NORMAL' => 'topic_comments_normal',
            'TOPIC-COMMENTS-POS-NEG' => 'topic_comments_positive_negative',
            'TOPIC-COMMENTS-ALL' => 'topic_comments_positive_neutral_negative',
            'NOTIFICATION-STATUS-CHANGE' => 'notification_status_change',
            'NOTIFICATION-NEW-COMMENTS' => 'notification_new_comments',
            'NOTIFICATION-CONTENT-CHANGE' => 'notification_content_change'
        ];
    }
    
    public static function checkCBsOption($cbOptions, $optionTAG){

        $allOptions = OneCb::getAllCBsOptionTags();

        if(isset($allOptions[$optionTAG])){
            $tag = $allOptions[$optionTAG];
            if (in_array($tag, $cbOptions,true)){
                return true;
            }
        }
        return false;
    }
}        