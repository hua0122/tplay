<?php
/**
 * 顾客管理
 * Created by PhpStorm.
 * User: gh
 * Date: 2018/5/23
 * Time: 21:02
 */

namespace app\admin\controller;

use app\admin\model\UserPatient;
use \think\Db;
use app\admin\controller\Permissions;
use \think\Cookie;
use \think\Session;
use \think\Cache;
use app\admin\model\User;//用户模型

class Customer extends Permissions
{
    /**
     * 用户列表
     * @return mixed
     */
    public function index()
    {

        //实例化用户模型
        $model = new User();
        $where = ['status'=>'A'];
        $where['code'] = ['neq',''];
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['name'] = ['like', '%' . $post['keywords'] . '%'];
        }

        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
        }

        $user = $model->where($where)->order('create_time desc')->paginate(20);

        $this->assign('user',$user);
        return $this->fetch();


    }

    /**
     * 修改
     */
    public function update(){
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;

        if($this->request->isPost()) {
            $post = $this->request->post();

            $user = new User();

            if(false == $user->save($post,['id'=>$id])) {
                return $this->error('修改失败');
            } else {

                return $this->success('修改成功','admin/customer/index');
            }


        }
        else {
            $user = new User();
            $vo = $user->find($id)->toArray();
            $this->assign('vo',$vo);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function delete(){
        if($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            $model = new User();
            $data['status'] = 'D';
            $r = $model ->save($data,['id'=>$id]);
            if(false == $r) {
                return $this->error('删除失败');
            } else {
                return $this->success('删除成功','admin/customer/index');
            }
        }
    }

    /**
     * 问诊信息
     */
    public function detail(){
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        $model = new UserPatient();
        $where['user_id'] = $id;
        $where['status'] = 'A';//正常
        $user = $model->where($where)->select();
        $this->assign('id',$id);
        $this->assign('user',$user);
        return $this->fetch();
    }

    /**
     * 问诊详情
     */
    public function userDetail(){
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        $uid = $this->request->param('uid', 0, 'intval') ;
        if($this->request->isPost()) {
            $post = $this->request->post();

            $user = new UserPatient();

            if(false == $user->save($post,['id'=>$id])) {
                return $this->error('修改失败');
            } else {

                return $this->success('修改成功',"admin/customer/index");
            }


        }
        else {
            $user = new UserPatient();
            $vo = $user->find($id)->toArray();
            $this->assign('vo',$vo);
            $this->assign('id',$id);
            return $this->fetch();
        }

    }
    /**
     * 问诊信息删除
     */
    public function userDelete(){
        if($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            $model = new UserPatient();
            $data['status'] = 'D';
            $r = $model ->save($data,['id'=>$id]);
            if(false == $r) {
                return $this->error('删除失败');
            } else {
                return $this->success('删除成功','admin/customer/index');
            }
        }
    }


}