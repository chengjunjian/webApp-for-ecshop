<?php
// 本类由系统自动生成，仅供测试用途
class IndexController extends Controller {
    
    public function bestAction(){
        $best = get_recommend_goods("new");
       // print_r($best);exit;
        $i=1;
        $temp = array();
        foreach ($best as $key => $value) {
        	$temp[] = $value;
        	if($i == 5) break;
        }
        $best = $temp;
        echo json_encode(array('code'=>200,'data'=>$best));
    }
	
}