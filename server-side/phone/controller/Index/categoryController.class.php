<?php
//category
class CategoryController extends Controller {
    
    //输出所有分类标签
    public function indexAction(){
    	$c = get_categories_tree();
    	
       echo json_encode(array("data"=>$c));
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
        $children = get_children($id);
    	//echo json_encode($_GET);exit;
        function category_get_goods($children, $brand, $min, $max, $ext, $size, $page, $sort, $order)
		{
		    //$display = $GLOBALS['display'];
		    $where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ".
		            "g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';

		    if ($brand > 0)
		    {
		        $where .=  "AND g.brand_id=$brand ";
		    }

		    if ($min > 0)
		    {
		        $where .= " AND g.shop_price >= $min ";
		    }

		    if ($max > 0)
		    {
		        $where .= " AND g.shop_price <= $max ";
		    }

		    /* 获得商品列表 */
		    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
		                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
		                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
		            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
		            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
		                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
		            "WHERE $where $ext ORDER BY $sort $order";
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

		        /* 处理商品水印图片 */
		        $watermark_img = '';

		        if ($promote_price != 0)
		        {
		            $watermark_img = "watermark_promote_small";
		        }
		        elseif ($row['is_new'] != 0)
		        {
		            $watermark_img = "watermark_new_small";
		        }
		        elseif ($row['is_best'] != 0)
		        {
		            $watermark_img = "watermark_best_small";
		        }
		        elseif ($row['is_hot'] != 0)
		        {
		            $watermark_img = 'watermark_hot_small';
		        }

		        if ($watermark_img != '')
		        {
		            $arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
		        }

		        $arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
		       
		            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
		   
		        $arr[$row['goods_id']]['name']             = $row['goods_name'];
		        $arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
		        $arr[$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
		        $arr[$row['goods_id']]['market_price']     = price_format($row['market_price']);
		        $arr[$row['goods_id']]['shop_price']       = price_format($row['shop_price']);
		        $arr[$row['goods_id']]['type']             = $row['goods_type'];
		        $arr[$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
		        $arr[$row['goods_id']]['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
		        $arr[$row['goods_id']]['goods_img']        = get_image_path($row['goods_id'], $row['goods_img']);
		        $arr[$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
		    }

		    return $arr;
		}

		$a = category_get_goods($children, 0, 0, 0, '' ,$size, $p, '', 'goods_id');
		$a = array_values($a);

        if(count($a) == 0){
        	$code = 400;
        	$msg = '没有更多商品';
        }else{
        	$code = 200;
        	$msg = '成功';
        }
		//echo  $msg;
       	echo json_encode(array('code'=>$code,'msg'=>$msg,'data'=>$a));

    }
}