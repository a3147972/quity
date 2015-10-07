<?php
namespace Common\Model;

use Common\Model\BaseModel;

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
}
