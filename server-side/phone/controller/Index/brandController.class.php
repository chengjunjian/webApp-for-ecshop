<?php
//brand
class BrandController extends Controller {
    
    //输出所有分类标签
    public function indexAction(){
    	$brand = get_brands();
    	//print_r($brand);
       	echo json_encode(array('code'=>200,"data"=>$brand));
    }

    public function __call($methodName, $args)
    {	
    	if('Action' == substr($methodName, - 6))
    	{	
    		$action = substr($methodName, 0, strlen($methodName) - 6);
    		//如果是合法用户
    		$this->categoryAction($action);
    		return;
    	}
    	parent::__call($methodName, $args);
    }
	
    //输出该id下的分类
    public function categoryAction($id){
        
        $p = isset($_GET['p']) ? $_GET['p'] : 1;
        $size = 10;
        $cate = 0;
    	/**
		 * 获得品牌下的商品
		 *
		 * @access  private
		 * @param   integer  $brand_id
		 * @return  array
		 */
		function brand_get_goods($brand_id, $cate, $size, $page, $sort, $order)
		{
		    $cate_where = ($cate > 0) ? 'AND ' . get_children($cate) : '';

		    /* 获得商品列表 */
		    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
		                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, " .
		                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
		            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
		            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
		                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
		            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand_id' $cate_where".
		            "ORDER BY $sort $order";

		    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);

		    $arr = array();
		    while ($row = $GLOBALS['db']->fetchRow($res))
		    {
		        if ($row['promote_price'] > 0)
		        {
		            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
		        }
		        else
		        {
		            $promote_price = 0;
		        }

		        $arr[$row['goods_id']]['goods_id']      = $row['goods_id'];
		        
		            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
		    
		        $arr[$row['goods_id']]['market_price']  = price_format($row['market_price']);
		        $arr[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
		        $arr[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
		        $arr[$row['goods_id']]['goods_brief']   = $row['goods_brief'];
		        $arr[$row['goods_id']]['goods_thumb']   = get_image_path($row['goods_id'], $row['goods_thumb'], true);
		        $arr[$row['goods_id']]['goods_img']     = get_image_path($row['goods_id'], $row['goods_img']);
		        $arr[$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
		    }
		    return $arr;
		}

		$brands = brand_get_goods($id, $cate, $size, $p, '', 'goods_id');
		$brands  = array_values($brands);
		//print_r($brands);exit;

        if(count($brands ) == 0){
        	$code = 400;
        	$msg = '没有更多商品';
        }else{
        	$code = 200;
        	$msg = '成功';
        }
		//echo  $msg;
       	echo json_encode(array('code'=>$code,'msg'=>$msg,'data'=>$brands ));

    }
}