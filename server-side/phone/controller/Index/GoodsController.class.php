<?php
// 本类由系统自动生成，仅供测试用途
class GoodsController extends Controller {
    
    public function indexAction(){
        echo '访问出错！～';
    }
	
	
	public function __call($methodName, $args)
    {	
    	if('Action' == substr($methodName, - 6))
    	{	
    		$action = substr($methodName, 0, strlen($methodName) - 6);
    		//如果是合法用户
    		$this->goodsAction($action);
    		return;
    	}
    	parent::__call($methodName, $args);
    }
	
    public function goodsAction($id){
        
        $goods = get_goods_info($id);
        
        if($goods){
            $code = 200;
            $msg = '成功';
           
        }else{
            $code = 400;
            $msg = '没有该商品';
        }


        $properties = get_goods_properties($id);  
        $properties['spe'] = array_values($properties['spe']);
         /*
        if(count($properties['spe']) == 0){
            $goods['properties'] = array();
        }else{
            $goods['properties'] = $properties['spe'][0];
        }
        */
        $goods['properties'] = $properties;
        //print_r($goods);exit; 

        echo json_encode(array('code'=>$code,'msg'=>$msg,'data'=>$goods));
    }
}
