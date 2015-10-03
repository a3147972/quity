<?php
namespace Common\Model;

use Common\Model\BaseModel;

class AdminModel extends BaseModel
{
    protected $tableName = 'admin';
    protected $selectFields = 'id,username,password,nickname,is_enable,ctime';
    protected $_validate = array(
        array('username', 'require', '请输入用户名'),
        array('password', 'require', '请输入密码'),
        array('nickname', 'require', '请输入昵称'),
    );
    protected $_auto = array(
        array('password', 'md5', 3, 'function'),
        array('password', '', 2, 'ignore'),
        array('is_enable', 1, 1, 'string'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );
    /**
     * 管理员登录
     * @method login
     * @param  string $username 用户名
     * @param  string $password 密码
     * @return bool|array           成功返回数据，失败返回false
     */
    public function login($username, $password)
    {
        $map['username'] = $username;

        $info = D('Admin')->_get($map);

        if (empty($info)) {
            $this->error = '账户不存在';
            return false;
        }

        if (md5($password) != $info['password']) {
            $this->error = '用户名不正确';
            return false;
        }
        return $info;
    }
}
