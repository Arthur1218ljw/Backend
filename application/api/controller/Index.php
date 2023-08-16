<?php

namespace app\api\controller;

use app\admin\model\Auction;
use app\admin\model\Bidlog;
use app\admin\model\User;
use app\common\controller\Api;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    protected function _initialize()
    {
        //跨域请求检测
//        check_cors_request();

        //配置跨域请求
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS, DELETE'); //请求方法
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Control-Type, Content-Type, token, Accept, x-access-sign, x-access-time');
        if (request()->isOptions()) {
            exit();
        }
    }
    /**
     * 首页
     *
     */
    public function index()
    {
        $this->success('请求成功');
    }

    public function login(){
        $param = $this->request->param();
        $address = $param['address'];
        $user = User::get(['address'=>$address]);
        if ($user){
            $this->success('',$user);
        }else{
            User::create([
                'address'=>$address
            ]);
            $user = User::get(['address'=>$address]);
            $this->success('',$user);
        }
    }

    public function upload()
    {
        return json([
            "name"=> "xxx.png",
            "status"=> "done",
            "url"=> "https://zos.alipayobjects.com/rmsportal/jkjgkEfvpUPVyRjUImniVslZfWPnJuuZ.png",
            "thumbUrl"=> "https://zos.alipayobjects.com/rmsportal/jkjgkEfvpUPVyRjUImniVslZfWPnJuuZ.png"
        ]);
    }

    public function info(){
        $param = $this->request->param();
        $address = $param['address'];
        $user = User::get(['address'=>$address]);
        if ($user){
            $this->success('',$user);
        }else{
            $this->success('',[
                'address'=>$address
            ]);
        }
    }

    public function updateinfo(){
        $param = $this->request->param();
        $address = $param['address'];
        $user = User::get(['address'=>$address]);
        if (!$user){
            User::create([
                'address'=>$address
            ]);
        }
        $user = User::get(['address'=>$address]);

        $user->save([
            'image' => $param['image'],
            'nickname' => $param['nickname']
        ]);
        $this->success();
    }


    public function createauction(){
        $param = $this->request->param();
        $address = $param['address'];
        $title = $param['title'];
        $deadline = $param['deadline'];
        $image = $param['image'];
        if (!$title){
            $this->error('param error');
        }
        if (!$image){
            $this->error('param error');
        }
        if (!$deadline){
            $this->error('param error');
        }
        $user = User::get(['address'=>$address]);
        if (!$user){
            $this->error('address not exist');
        }
        $deadtime = strtotime($deadline);
        $sn = md5(uniqid(mt_rand(), true));
        $auction = Auction::create([
            'address'=>$address,
            'image'=>$image,
            'title'=>$title,
            'deadline'=>$deadtime,
            'sn'=>$sn
        ]);
        $this->success('');
    }

    public function markets(){
        $auctions = Auction::where('status','online')->select();
        $this->success('',$auctions);
    }

    public function myauctions(){
        $param = $this->request->param();
        $address = $param['address'];
        $auctions = Auction::where('address',$address)->select();
        $this->success('',$auctions);
    }

    public function mybid(){
        $param = $this->request->param();
        $address = $param['address'];
        $auctions = Auction::where('bid_address',$address)->select();
        $this->success('',$auctions);
    }

    public function bid(){
        $param = $this->request->param();
        $address = $param['address'];
        $id = $param['id'];
        $amount = $param['amount'];
        $txid = $param['txid'];
        $user = User::get(['address'=>$address]);
        $auction = Auction::get($id);
        $auction->save([
            'amount' => $param['amount'],
            'bid_address'=>$address
        ]);
        BidLog::create([
            'auction_id'=>$auction['id'],
            'user_id'=>$user['id'],
            'address'=>$address,
            'txid'=>$txid,
            'amount'=>$amount
        ]);
        $this->success('');
    }
}
