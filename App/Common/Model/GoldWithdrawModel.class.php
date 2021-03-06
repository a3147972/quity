<?php
namespace Common\Model;

use Common\Model\BaseModel;
use Common\Tools\ArrayHelper;

class GoldWithdrawModel extends BaseModel
{
    protected $tableName = 'gold_withdraw';
    protected $selectFields = 'id,member_id,gold,bank_id,pic,remark,status,ctime';

    protected $_validate = array(
        array('gold', 'require', '请输入要提现金额', 1),
        array('bank_id', 'require', '请选择要提现到的银行卡', 1),
    );
    protected $_auto = array(
        array('status', 0, 1, 'string'),
        array('admin_id', 'auto_admin', 2, 'callback'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );

    protected function auto_admin()
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
        $member_id = array_unique($member_id);
        $member_map['id'] = array('in', $member_id);
        $member_list = D('Member')->_list($member_map, 'id,username,name');
        $member_list = ArrayHelper::array_key_replace($member_list, 'id', 'member_id');
        $member_list = array_column($member_list, null, 'member_id');

        //查询银行表
        $bank_id = array_column($list, 'bank_id');
        $bank_id = array_unique($bank_id);
        $bank_map['id'] = array('in', $bank_id);
        $bank_list = D('Bank')->_list($bank_map, 'id,deposit_bank,deposit_name,deposit_code_number,deposit_address');
        $bank_list = ArrayHelper::array_key_replace($bank_list, 'id', 'bank_id');
        $bank_list = array_column($bank_list, null, 'bank_id');
        foreach ($list as $_k => $_v) {
            if (empty($bank_list[$_v['bank_id']])) {
                $bank_list[$_v['bank_id']] = array();
            }
            $list[$_k] = array_merge($_v, $member_list[$_v['member_id']], $bank_list[$_v['bank_id']]);
        }

        return $list;
    }
}
