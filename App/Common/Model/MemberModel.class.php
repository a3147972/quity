<?php
namespace Common\Model;

use Common\Model\BaseModel;

class MemberModel extends BaseModel
{
    protected $tableName = 'member';
    protected $selectFields = 'id,name,username,password,second_password,gold,phone,quity,id_number,ctime';

    protected $_validate = array(
        array('username', 'require', '请输入用户名', 1),
        array('username', '', '用户名已存在', 0, 'unique', 1, 1),
        array('password', 'require', '请输入密码', 0, 'regex', 1),
        array('second_password', 'require', '请输入二级密码', 0, 'regex', 1),
        array('phone', 'require', '请输入联系电话', 1),
        array('id_number', 'require', '请输入证件号码', 1),
    );

    protected $_auto = array(
        array('password', 'md5', 1, 'function'),
        array('password', 'auto_password', 2, 'callback'),
        array('password', '', 2, 'ignore'),
        array('second_password', 'md5', 1, 'function'),
        array('second_password', 'auto_password', 2, 'callback'),
        array('second_password', '', 2, 'ignore'),
        array('is_enable', 1, 1, 'string'),
        array('admin_id', 'auto_admin', 3, 'callback'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );
    protected function auto_admin()
    {
        return session('uid');
    }

    protected function auto_password($v)
    {
        if (empty($v)) {
            return '';
        } else {
            return md5($v);
        }
    }
    /**
     * 前台登录方法
     * @method login
     * @param  string $username 用户名
     * @param  string $password 密码
     * @return array|bool       成功返回数据,失败返回false
     */
    public function login($username, $password)
    {
        $map['username'] = $username;

        $info = $this->_get($map);

        if (empty($info)) {
            $this->error = '账号不存在';
            return false;
        }

        if (md5($password) != $info['password']) {
            $this->error = '密码不正确';
            return false;
        }

        return $info;
    }
    /**
     * 增加奖金币
     * @method addGold
     * @param  int  $uid  用户id
     * @param  int  $gold 要增加的奖金币数
     * @return  bool      成功返回true,失败返回false
     */
    public function addGold($uid, $gold)
    {
        $map['id'] = $uid;
        $data['gold'] = array('exp', 'gold + ' . $gold);

        $result = $this->where($map)->save($data);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 减少奖金币
     * @method delGold
     * @param  int  $uid     用户id
     * @param  int  $gold    要减少的奖金币数
     * @return bool          成功返回true,失败返回false
     */
    public function delGold($uid, $gold)
    {
        $map['id'] = $uid;
        $data['gold'] = array('exp', 'gold -' . $gold);

        $result = $this->where($map)->save($data);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function addQuity($uid, $quity)
    {
        $map['id'] = $uid;
        $data['quity'] = array('exp', 'quity +' . $quity);

        $result = $this->where($map)->save($data);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function delQuity($uid, $quity)
    {
        $map['id'] = $uid;
        $data['quity'] = array('exp', 'quity -' . $quity);

        $result = $this->where($map)->save($data);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
