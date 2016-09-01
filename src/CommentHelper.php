<?php
namespace SocialFoundation\Testing;

/**
 * Class CommentTestCase
 * @package unit
 */
trait CommentHelper {
    /**
     * @param $values
     * @return array
     */
    public function makeCommentArray($values) {
        $default=  ['id'=>0,'date'=>gmdate('Y-m-d H:i:s'),'parent_id'=>0,'user_id'=>0,'module_id'=>0,'module'=>'', 'content'=>'',
            'status'=>'', 'type'=>''];
        $array = array_merge($default, $values);
        return $array;
    }
}
