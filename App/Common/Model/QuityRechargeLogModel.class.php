<?php
namespace Common\Model;

use Common\Model\BaseModel;
use Common\Tools\ArrayHelper;

class QuityRechargeLogModel extends BaseModel
{
    protected $tableName = 'quity_recharge_log';
    protected $selectFields = 'id,member_id,admin_id,quity_count,remark,ctime';

    /**
     * 股权充值记录表
     * @method write
     * @param  int $member_id   会员id
     * @param  int $quity_count 股权数
     * @param  string $remark      备注
     * @return array              操作结果，成功返回true
     */
    public function write($member_id, $quity_count, $remark = '')
    {
        $data['member_id'] = $member_id;
        $data['quity_count'] = $quity_count;
        $data['admin_id'] = session('uid');
        $data['remark'] = $remark;
        $data['ctime'] = now();

        $result = $this->add($data);

        if ($result) {
            return true;
        } else {
            return false;
        }
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
