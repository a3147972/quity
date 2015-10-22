<?php
namespace Common\Model;

use Common\Model\BaseModel;
use Common\Tools\ArrayHelper;

class GoldToQuityModel extends BaseModel
{
    protected $tableName = 'gold_to_quity';
    protected $selectFields = 'id,member_id,quity_count,then_balance,ctime';

    protected $_validate = array(
        array('quity_count', 'require', '请输入股权金额', 1),
    );
    protected $_auto = array(
        array('member_id', 'auto_member', 1, 'callback'),
        array('status', 0, 1, 'string'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );

    protected function auto_member()
    {
        return session('user_id');
    }

    public function lists($map = array(), $field = '', $order = '', $page = 0, $page_size = 0)
    {
        $pk = $this->pk; //主键
        $field = empty($field) ? $this->selectFields : $fields;
        $order = empty($order) ? $pk . ' desc' : $order;
        if ($page === 0) {
            $list = $this->where($map)->field($field)->order($order)->select();
        } else {
            $page_index = ($page - 1) * $page_size;
            $list = $this->where($map)->field($field)->order($order)->limit($page_index . ',' . $page_size)->select();
        }

        if (empty($list)) {
            return $list;
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
