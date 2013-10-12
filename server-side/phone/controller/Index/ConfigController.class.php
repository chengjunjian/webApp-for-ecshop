<?php
// 本类由系统自动生成，仅供测试用途
class ConfigController extends Controller {
    
    public function indexAction(){
        $cfg = load_config();
        echo json_encode($cfg);
    }
}