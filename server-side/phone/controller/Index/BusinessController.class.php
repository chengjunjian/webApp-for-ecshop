<?php
// 本类由系统自动生成，仅供测试用途
class BusinessController extends Controller {
    
    public function indexAction(){
    	$arr = array(
    		'data'=>array(
	    				array(
	    					'business_id'=>898,
		    				'business_name'=>'深真市宝安区新安及美容馆',
		    				'business_addr'=>'新安街道新安大道东南侧深业新岸线1栋（20）',
		    				'business_tel'=>'0755-29786887',
		    				'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_1.jpg',
                            'business_coordinate'=>array('longitude'=>116.404,'latitude'=>39.915)

	    				),
	    				array(
	    					'business_id'=>894,
		    				'business_name'=>'7777f售部',
		    				'business_addr'=>'安徽sdfsd省合肥市蜀山区山水名城6栋107,108,110',
		    				'business_tel'=>'13965128671',
		    				'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_2.jpg',
                            'business_coordinate'=>array('longitude'=>126.404,'latitude'=>39.915)
	    				),
                        array(
                            'business_id'=>895,
                            'business_name'=>'合6666胎销售部',
                            'business_addr'=>'安徽省合肥sdf名城6栋107,108,110',
                            'business_tel'=>'13965128671',
                            'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_2.jpg',
                            'business_coordinate'=>array('longitude'=>125.404,'latitude'=>39.915)
                        ),
                        array(
                            'business_id'=>896,
                            'business_name'=>'5555胎销售部',
                            'business_addr'=>'安徽省合肥市df7,108,110',
                            'business_tel'=>'13965128671',
                            'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_2.jpg',
                            'business_coordinate'=>array('longitude'=>124.404,'latitude'=>39.915)
                        ),
                        array(
                            'business_id'=>897,
                            'business_name'=>'4444汽车轮胎销售部',
                            'business_addr'=>'安徽省dddd合肥市蜀山区山水名城6栋107,108,110',
                            'business_tel'=>'13965128671',
                            'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_2.jpg',
                            'business_coordinate'=>array('longitude'=>123.404,'latitude'=>39.915)
                        ),
                        array(
                            'business_id'=>898,
                            'business_name'=>'3333容馆',
                            'business_addr'=>'新安街道新安大道东南侧深业新岸线1栋（20）',
                            'business_tel'=>'0755-29786887',
                            'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_1.jpg',
                            'business_coordinate'=>array('longitude'=>122.404,'latitude'=>39.915)

                        ),
                        array(
                            'business_id'=>894,
                            'business_name'=>'合2222df售部',
                            'business_addr'=>'安徽sdfsd省合肥市蜀山区山水名城6栋107,108,110',
                            'business_tel'=>'13965128671',
                            'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_2.jpg',
                            'business_coordinate'=>array('longitude'=>121.404,'latitude'=>39.915)
                        ),
                        array(
                            'business_id'=>895,
                            'business_name'=>'1111胎销售部',
                            'business_addr'=>'安徽省合肥sdf名城6栋107,108,110',
                            'business_tel'=>'13965128671',
                            'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_2.jpg',
                            'business_coordinate'=>array('longitude'=>120.404,'latitude'=>39.915)
                        ),
                        array(
                            'business_id'=>896,
                            'business_name'=>'合000售部',
                            'business_addr'=>'安徽省合肥市df7,108,110',
                            'business_tel'=>'13965128671',
                            'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_2.jpg',
                            'business_coordinate'=>array('longitude'=>118.404,'latitude'=>39.915)
                        ),
                        array(
                            'business_id'=>897,
                            'business_name'=>'10101010胎销售部',
                            'business_addr'=>'安徽省dddd合肥市蜀山区山水名城6栋107,108,110',
                            'business_tel'=>'13965128671',
                            'business_thumb'=>'http://192.168.1.222/ecshop/images/zb/bus_2.jpg',
                            'business_coordinate'=>array('longitude'=>119.404,'latitude'=>39.915)
                        )
    			)
    	);
    	echo json_encode($arr);exit;
    }

    public function __call($methodName, $args){	
    	if('Action' == substr($methodName, - 6))
    	{	
    		$action = substr($methodName, 0, strlen($methodName) - 6);
    		//如果是合法用户
    		$this->businessAction($action);
    		return;
    	}
    	parent::__call($methodName, $args);
    }


    public function businessAction($id){
        $arr = array(
    		'data'=>array(
    				'business_id'=>$id,
    				'business_name'=>'深真市宝安区新安及美容馆',
    				'business_addr'=>'新安街道新安大道东南侧深业新岸线1栋（20）',
    				'business_tel'=>'0755-29786887',
    				'business_thumb'=>'http://192.168.1.222/ecshop/images/bus_1.jpg',
    				'business_info'=>'新安肌恩黛颜美容馆以最專業細膩的技術、 最貼心的服務態度， 讓顧客達到全程的舒緩療程。为都市女性提供的不仅是美容护肤服务，更是为她们在狭小、繁杂的都市生活中提供一片宁静、安详和身心放松解脱的私人空间和时间。',
    				'business_coordinate'=>array('longitude'=>116.404,'latitude'=>39.915),
    				'business_img'=>array(
    					'http://192.168.1.222/ecshop/images/zb/201307271038522394_350-250.jpg',
    					'http://192.168.1.222/ecshop/images/zb/201307301050496011_350-250.jpg',
    					'http://192.168.1.222/ecshop/images/zb/201307271038526852_350-250.jpg',
    					'http://192.168.1.222/ecshop/images/zb/201307301050499595_350-250.jpg'
    					),
                    'follow'=>200,
                    'look'=>300,
                    'comment'=>array(
                            array(
                                'name'=>'zhangsan',
                                'content'=>'很好吃！～～～～～',
                                'time'=>'2013-06-08 17:50:36'
                                ),
                            array(
                                'name'=>'lisi',
                                'content'=>'难吃死了',
                                'time'=>'2013-06-08 16:35:20'
                                )
                        )
    			)
    	);
        echo json_encode($arr);
        
    }
	
}