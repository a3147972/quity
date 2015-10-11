<?php
namespace Common\Model;

use Common\Model\BaseModel;
use Common\Tools\ArrayHelper;

class RechargeLogModel extends BaseModel
{
    protected $tableName = 'recharge_log';
    protected $selectFields = 'id,member_id,admin_id,gold,remark,ctime';

    protected $_validate = array(
        array('gold', 'require', '请输入要充值金额', 1),
        array('member_id', 'require', '请选择要操作的会员', 1),
    );
    protected $_auto = array(
        array('ctime', 'now', 1, 'function'),
        array('admin_id', 'auto_admin', 1, 'function'),
    );

    protected function auto_admin()
    {
        return session('uid');
    }

    /**
     * 写入充值记录
     * @method write
     * @param  int $gold      充值金额
     * @param  int $member_id 要充值给的会员
     * @return bool            成功返回true,失败返回false
     */
    public function write($gold, $member_id)
    {
        $data['gold'] = $gold;
        $data['member_id'] = $member_id;

        $this->create($data);

        $result = $this->add();

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
