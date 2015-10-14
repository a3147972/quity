<?php
namespace Common\Model;

use Common\Model\BaseModel;
use Common\Tools\ArrayHelper;

class QuityDealModel extends BaseModel
{
    protected $tableName = 'quity_deal';
    protected $selectFields = 'id,member_id,quity_count,then_balance,option,to_member_id,type,overdue_time,status,ctime';

    protected $_validate = array(
        array('quity_count', 'require', '请输入要交易的股权数量', 1),
        array('option', 'require', '请选择要进行的操作', 1),
        array('to_member_id', 'validate_to_member', '请填写要交易会员编号', 1, 'callback'),
    );
    protected $_auto = array(
        array('status', 0, 1, 'string'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );

    protected function validate_to_member()
    {
        $option = I('post.option');
        $to_member_id = I('post.to_member_id');
        if (in_array($option, array(3, 4)) && empty($to_member_id)) {
            return false;
        }
        return true;
    }

    public function lists($map = array(), $field = '', $order = '', $page = 1, $page_size = 10)
    {
        $pk = $this->pk; //主键
        $field = empty($field) ? $this->selectFields : $field;
        $order = empty($order) ? $pk . ' desc' : $order;
        if ($page === 0) {
            $list = $this->where($map)->field($field)->order($order)->select();
        } else {
            $page_index = ($page - 1) * $page_size;
            $list = $this->where($map)->field($field)->order($order)->limit($page_index . ',' . $page_size)->select();
        }

        if (empty($list)) {
            return array();
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
