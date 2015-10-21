<?php
namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller
{
    public function test()
    {
        $list[0]['title'] = '标题一';
        $list[0]['content'] = '内容';
        $list[1]['title'] = '标题二';
        $list[1]['content'] = '内容11111';

        $title[] = 'title';
        $title[] = 'content';

        explode_excel($list, $title);
    }
    public function login()
    {
        $this->display();
    }
    /**
     * 检测登录
     * @method checkLogin
     */
    public function checkLogin()
    {
        if (!IS_POST) {
            $this->error('非法请求');
        }
        $username = I('post.username');
        $password = I('post.password');

        if (empty($username)) {
            $this->error('请输入用户名');
        }
        if (empty($password)) {
            $this->error('请输入密码');
        }

        $model = D('Member');
        $result = $model->login($username, $password);

        if ($result) {
            session('user_id', $result['id']);
            session('name', $result['name']);
            $this->success('登录成功', U('Index/index'));
        } else {
            $this->error($model->getError());
        }
    }
    /**
     * 退出
     * @method logout
     */
    public function logout()
    {
        session(null);
        session_regenerate_id();
        redirect(U('Login/login'));
    }
}
