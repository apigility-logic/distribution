<?php

class ConfigAction extends BaseAction
{
    /**
    *
    *   获取app版本号
    *   @param int system
    */
    public function getAppVersion($system = 0){
        if($system == 0){
            $this->responseError( L('_PARAM_ERROR_'));
        }
        if($system == 1){
            $this->responseSuccess(M('siteconfig')->where('id=1')->field('apkversion,apkaddress')->find());
        }else if($system == 2){
            $this->responseSuccess(M('siteconfig')->where('id=1')->field('ipaversion')->find());
        }else{
            $this->responseError(L('_PARAM_ERROR_'));
        }

    }

    /**
    *   获取ios商品id
    *
    */
    public function getIosGoodsId(){
        // $data = M('siteconfig')->where('id = 1')->getField('ios_goods');
        // if(empty($data)){
        //     $this->responseError(L('_CONFIG_ERROR_'));
        // }else{
        //     $ios_goods = explode('@',$data);
        // }
        // $this->responseSuccess($ios_goods);


        //苹果充值数据
        //goods_id 苹果商品id .  pay 支付金额. get 获取虚拟币总数 gift 赠送的虚拟币数量
        $items = array();

        $items[] = array('goods_id'=>'com.meilibo.mars1','pay'=>'6','get'=>'42','gift'=>'0');
        $items[] = array('goods_id'=>'com.meilibo.mars2','pay'=>'30','get'=>'210','gift'=>'15');
        $items[] = array('goods_id'=>'com.meilibo.mars21','pay'=>'98','get'=>'686','gift'=>'45');
        // $items[] = array('goods_id'=>'com.53live.goods.1490','pay'=>'1490','get'=>'3129','gift'=>'149');
        // $items[] = array('goods_id'=>'com.53live.goods.2490','pay'=>'2490','get'=>'5229','gift'=>'249');
        // $items[] = array('goods_id'=>'com.53live.goods.2990','pay'=>'2990','get'=>'6279','gift'=>'299');

        $this->responseSuccess($items);
    }
    /*
    获取定时器信息
     */
    public function getTimer(){
        $this->responseSuccess( M('timer')->select() );
    }

}
