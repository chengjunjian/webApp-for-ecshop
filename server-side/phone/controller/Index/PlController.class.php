<?php

class PlController extends Controller {
    
    public function plAction(){
        $pid = $this->getParam("pid");
        if($pid == 1){
        	$arr = array('data'=>array(
                            array(
                                'name'=>'tern',
                                'content'=>'很asdfasdf～',
                                'time'=>'2013-06-08 17:50:36'
                                ),
                            array(
                                'name'=>'xxxx',
                                'content'=>'难asdfasdfasd了',
                                'time'=>'2013-06-08 16:35:20'
                                )
                        )
        		);
        	echo json_encode($arr);

        }else{
        	$arr = array('data'=>array(
                            array(
                                'name'=>'tern',
                                'content'=>'uuuuuuuuuuu～',
                                'time'=>'2013-06-08 17:50:36'
                                ),
                            array(
                                'name'=>'xxxx',
                                'content'=>'ggggggggg了',
                                'time'=>'2013-06-08 16:35:20'
                                )
                        )
        		);
        	echo json_encode($arr);
        }
    }

    public function __call($methodName, $args)
    {	
    	if('Action' == substr($methodName, - 6))
    	{	
    		$action = substr($methodName, 0, strlen($methodName) - 6);
    		//如果是合法用户
    		$this->plAction($action);
    		return;
    	}
    	parent::__call($methodName, $args);
    }
	
	
}