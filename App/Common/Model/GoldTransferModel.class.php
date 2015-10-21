<?php
namespace Common\Model;

use Common\Model\BaseModel;
use Common\Tools\ArrayHelper;

class GoldTransferModel extends BaseModel
{
    protected $tableName = 'gold_transfer';
    protected $selectFields = 'id,member_id,to_member_id,gold,remark,status,ctime';

    protected $_validate = array(
        array('gold', 'require', '请输入要转账金额', 1),
        array('to_member_id', 'require', '请输入要转给的会员昵称', 1),
    );
    protected $_auto = array(
        array('status', 0, 1, 'string'),
        array('member_id', 'auto_member', 1, 'callback'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );

    protected function auto_member()
    {
        return session('uid');
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
        $member_id = array_merge($member_id, array_column($list, 'to_member_id'));
        $member_id = array_unique($member_id);
        $member_map['id'] = array('in', $member_id);
        $member_list = D('Member')->_list($member_map, 'id,username');
        $member_list = ArrayHelper::array_key_replace($member_list, 'id', 'member_id');
        $member_list = array_column($member_list, null, 'member_id');

        foreach ($list as $_k => $_v) {
            $list[$_k]['username'] = $member_list[$_v['member_id']]['username'];
            $list[$_k]['to_username'] = $member_list[$_v['to_member_id']]['username'];
        }

        return $list;
    }
}
