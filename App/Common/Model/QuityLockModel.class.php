<?php
namespace Common\Model;

use Common\Model\BaseModel;
use Common\Tools\ArrayHelper;

class QuityLockModel extends BaseModel
{
    protected $tableName = 'quity_lock';
    protected $selectFields = 'id,member_id,quity_count,then_gold,unlock_time,status,ctime';

    protected $_validate = array(
        array('quity_count', 'require', '请输入要锁定的股权数量', 1),
        array('unlock_time', 'require', '请选择要锁定时间', 1),
    );
    protected $_auto = array(
        array('status', 0, 1, 'string'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );

    public function lists($map = array(), $field = '', $order = '', $page = 1, $page_size = 10)
    {
        $list = $this->_list($map, $field, $order, $page, $page_size);

        if (empty($list)) {
            return array();
        }

        //查询会员表
        $member_id = array_column($list, 'member_id');
        $member_id = array_unique($member_id);
        $member_map['id'] = array('in', $member_id);
        $member_list = D('Member')->_list($member_map, 'id,username');
        $member_list = ArrayHelper::array_key_replace($member_list, 'id', 'member_id');
        $member_list = array_column($member_list, null, 'member_id');

        foreach ($list as $_k => $_v) {
            $list[$_k] = array_merge($_v, $member_list[$_v['member_id']]);
        }

        return $list;
    }
}
