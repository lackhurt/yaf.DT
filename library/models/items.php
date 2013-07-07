<?php
/**
 * 图片
 * @author ciogao@gmail.com
 *
 */
class models_items extends Models
{
    private static $_instance = NULL;

    /**
     * @return models_items
     */
    final public static function getInstance()
    {
        if (!isset(self::$_instance) || !self::$_instance instanceof self) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    function __construct()
    {
        parent::__construct();
        $this->_table   = 'avi_items';
        $this->_primary = 'items_id';
    }

    /**
     * 首页获取图片
     */
    function getIndexItems()
    {
        $this->db->cache_on();
        return $this->getAll(array('items_id', 'items_name', 'items_pic', 'reply_hits', 'like_hits', 'remark'),
            array('flag' => contast_items::ITEMS_FLAG_YES),
            array('items_id' => 'asc')
        );
    }

    /**
     * 浏览hist+1
     *
     * @param $items_id
     *
     * @return bool
     */
    function viewItems($items_id)
    {
        return $this->update(array('hits' => 'hits + 1'), $items_id);
    }

    function mkdata($v)
    {
        return $data = array(
            'items_name'       => $v['items_name'],
            'items_pic'        => $v['items_pic'],
            'items_pic_thumbs' => $v['items_pic_thumbs'],
            'user_id'          => (int)$v['user_id'],
            'album_id'         => (int)$v['album_id'],
            'tag_ids'          => $v['tag_ids'],
            'reply_hits'       => (int)$v['reply_hits'],
            'collect_hits'     => (int)$v['collect_hits'],
            'like_hits'        => (int)$v['like_hits'],
            'created_time'     => time(),
            'update_time'      => time(),
            'remark'           => $v['remark'],
            'txt_area'         => (int)$v['txt_area'],
            'pic_area'         => (int)$v['pic_area'],
            'is_cover'         => (int)$v['is_cover'],
        );
    }
}