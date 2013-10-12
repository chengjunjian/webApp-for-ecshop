<?php
// 本类由系统自动生成，仅供测试用途
class SearchController extends Controller {
    
    public function indexAction(){
    	
    }

    public function __call($methodName, $args){	
    	if('Action' == substr($methodName, - 6))
    	{	
    		$action = substr($methodName, 0, strlen($methodName) - 6);
    		//如果是合法用户
    		$this->searchAction($action);
    		return;
    	}
    	parent::__call($methodName, $args);
    }


    public function searchAction($name){
       
        $name = urldecode($name);   //先进行转码
        
        $_REQUEST['keywords']   = $name ;
        $_REQUEST['brand']      = !empty($_REQUEST['brand'])      ? intval($_REQUEST['brand'])      : 0;
        $_REQUEST['category']   = !empty($_REQUEST['category'])   ? intval($_REQUEST['category'])   : 0;
        $_REQUEST['min_price']  = !empty($_REQUEST['min_price'])  ? intval($_REQUEST['min_price'])  : 0;
        $_REQUEST['max_price']  = !empty($_REQUEST['max_price'])  ? intval($_REQUEST['max_price'])  : 0;
        $_REQUEST['goods_type'] = !empty($_REQUEST['goods_type']) ? intval($_REQUEST['goods_type']) : 0;
        $_REQUEST['sc_ds']      = !empty($_REQUEST['sc_ds']) ? intval($_REQUEST['sc_ds']) : 0;
        $_REQUEST['outstock']   = !empty($_REQUEST['outstock']) ? 1 : 0;

      

        /* 初始化搜索条件 */
        $keywords  = '';
        $tag_where = '';
        if (!empty($_REQUEST['keywords']))
        {
            $arr = array();
            if (stristr($_REQUEST['keywords'], ' AND ') !== false)
            {
                /* 检查关键字中是否有AND，如果存在就是并 */
                $arr        = explode('AND', $_REQUEST['keywords']);
                $operator   = " AND ";
            }
            elseif (stristr($_REQUEST['keywords'], ' OR ') !== false)
            {
                /* 检查关键字中是否有OR，如果存在就是或 */
                $arr        = explode('OR', $_REQUEST['keywords']);
                $operator   = " OR ";
            }
            elseif (stristr($_REQUEST['keywords'], ' + ') !== false)
            {
                /* 检查关键字中是否有加号，如果存在就是或 */
                $arr        = explode('+', $_REQUEST['keywords']);
                $operator   = " OR ";
            }
            else
            {
                /* 检查关键字中是否有空格，如果存在就是并 */
                $arr        = explode(' ', $_REQUEST['keywords']);
                $operator   = " AND ";
            }

            $keywords = 'AND (';
            $goods_ids = array();
            foreach ($arr AS $key => $val)
            {
                if ($key > 0 && $key < count($arr) && count($arr) > 1)
                {
                    $keywords .= $operator;
                }
                $val        = mysql_like_quote(trim($val));
                $sc_dsad    = $_REQUEST['sc_ds'] ? " OR goods_desc LIKE '%$val%'" : '';
                $keywords  .= "(goods_name LIKE '%$val%' OR goods_sn LIKE '%$val%' OR keywords LIKE '%$val%' $sc_dsad)";

                $sql = 'SELECT DISTINCT goods_id FROM ' . $GLOBALS['ecs']->table('tag') . " WHERE tag_words LIKE '%$val%' ";
                $res = $GLOBALS['db']->query($sql);
                while ($row = $GLOBALS['db']->FetchRow($res))
                {
                    $goods_ids[] = $row['goods_id'];
                }

                $GLOBALS['db']->autoReplace($GLOBALS['ecs']->table('keywords'), array('date' => local_date('Y-m-d'),
                    'searchengine' => 'ecshop', 'keyword' => addslashes(str_replace('%', '', $val)), 'count' => 1), array('count' => 1));
            }
            $keywords .= ')';
            
            $goods_ids = array_unique($goods_ids);
            $tag_where = implode(',', $goods_ids);
            if (!empty($tag_where))
            {
                $tag_where = 'OR g.goods_id ' . db_create_in($tag_where);
            }
        }
  
        $category   = !empty($_REQUEST['category']) ? intval($_REQUEST['category'])        : 0;
        $categories = ($category > 0)               ? ' AND ' . get_children($category)    : '';
        $brand      = $_REQUEST['brand']            ? " AND brand_id = '$_REQUEST[brand]'" : '';
        $outstock   = !empty($_REQUEST['outstock']) ? " AND g.goods_number > 0 "           : '';

        $min_price  = $_REQUEST['min_price'] != 0                               ? " AND g.shop_price >= '$_REQUEST[min_price]'" : '';
        $max_price  = $_REQUEST['max_price'] != 0 || $_REQUEST['min_price'] < 0 ? " AND g.shop_price <= '$_REQUEST[max_price]'" : '';

        
        


        $page       = !empty($_REQUEST['page'])  && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
        $size       = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
       // echo $page;exit;
        $intromode = '';    //方式，用于决定搜索结果页标题图片

        if (!empty($_REQUEST['intro']))
        {
            switch ($_REQUEST['intro'])
            {
                case 'best':
                    $intro   = ' AND g.is_best = 1';
                    $intromode = 'best';
                    $ur_here = $_LANG['best_goods'];
                    break;
                case 'new':
                    $intro   = ' AND g.is_new = 1';
                    $intromode ='new';
                    $ur_here = $_LANG['new_goods'];
                    break;
                case 'hot':
                    $intro   = ' AND g.is_hot = 1';
                    $intromode = 'hot';
                    $ur_here = $_LANG['hot_goods'];
                    break;
                case 'promotion':
                    $time    = gmtime();
                    $intro   = " AND g.promote_price > 0 AND g.promote_start_date <= '$time' AND g.promote_end_date >= '$time'";
                    $intromode = 'promotion';
                    $ur_here = $_LANG['promotion_goods'];
                    break;
                default:
                    $intro   = '';
            }
        }
        else
        {
            $intro = '';
        }

        

        /*------------------------------------------------------ */
        //-- 属性检索
        /*------------------------------------------------------ */
        $attr_in  = '';
        $attr_num = 0;
        $attr_url = '';
        $attr_arg = array();

        if (!empty($_REQUEST['attr']))
        {
            $sql = "SELECT goods_id, COUNT(*) AS num FROM " . $ecs->table("goods_attr") . " WHERE 0 ";
            foreach ($_REQUEST['attr'] AS $key => $val)
            {
                if (is_not_null($val) && is_numeric($key))
                {
                    $attr_num++;
                    $sql .= " OR (1 ";

                    if (is_array($val))
                    {
                        $sql .= " AND attr_id = '$key'";

                        if (!empty($val['from']))
                        {
                            $sql .= is_numeric($val['from']) ? " AND attr_value >= " . floatval($val['from'])  : " AND attr_value >= '$val[from]'";
                            $attr_arg["attr[$key][from]"] = $val['from'];
                            $attr_url .= "&amp;attr[$key][from]=$val[from]";
                        }

                        if (!empty($val['to']))
                        {
                            $sql .= is_numeric($val['to']) ? " AND attr_value <= " . floatval($val['to']) : " AND attr_value <= '$val[to]'";
                            $attr_arg["attr[$key][to]"] = $val['to'];
                            $attr_url .= "&amp;attr[$key][to]=$val[to]";
                        }
                    }
                    else
                    {
                        /* 处理选购中心过来的链接 */
                        $sql .= isset($_REQUEST['pickout']) ? " AND attr_id = '$key' AND attr_value = '" . $val . "' " : " AND attr_id = '$key' AND attr_value LIKE '%" . mysql_like_quote($val) . "%' ";
                        $attr_url .= "&amp;attr[$key]=$val";
                        $attr_arg["attr[$key]"] = $val;
                    }

                    $sql .= ')';
                }
            }

            /* 如果检索条件都是无效的，就不用检索 */
            if ($attr_num > 0)
            {
                $sql .= " GROUP BY goods_id HAVING num = '$attr_num'";

                $row = $db->getCol($sql);
                if (count($row))
                {
                    $attr_in = " AND " . db_create_in($row, 'g.goods_id');
                }
                else
                {
                    $attr_in = " AND 0 ";
                }
            }
        }
        elseif (isset($_REQUEST['pickout']))
        {
            /* 从选购中心进入的链接 */
            $sql = "SELECT DISTINCT(goods_id) FROM " . $ecs->table('goods_attr');
            $col = $db->getCol($sql);
            //如果商店没有设置商品属性,那么此检索条件是无效的
            if (!empty($col))
            {
                $attr_in = " AND " . db_create_in($col, 'g.goods_id');
            }
        }

        /* 获得符合条件的商品总数 */
        $sql   = "SELECT COUNT(*) FROM ecs_goods AS g ".
            "WHERE g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 $attr_in ".
            "AND (( 1 " . $categories . $keywords . $brand . $min_price . $max_price . $intro . $outstock ." ) ".$tag_where." )";
        $count = $GLOBALS['db']->getOne($sql);

        $max_page = ($count> 0) ? ceil($count / $size) : 1;
        if ($page > $max_page)
        {
            $page = $max_page;
        }

        /* 查询商品 */
        $sql = "SELECT g.goods_id, g.goods_name, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ".
                    "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                    "g.promote_price, g.promote_start_date, g.promote_end_date, g.goods_thumb, g.goods_img, g.goods_brief, g.goods_type ".
                "FROM " .$GLOBALS['ecs']->table('goods'). " AS g ".
                "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                        "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
                "WHERE g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 $attr_in ".
                    "AND (( 1 " . $categories . $keywords . $brand . $min_price . $max_price . $intro . $outstock . " ) ".$tag_where." ) " .
                "ORDER BY g.goods_id DESC";
        $res = $GLOBALS['db']->SelectLimit($sql, $size, ($page - 1) * $size);

        $arr = array();
        while ($row = $GLOBALS['db']->FetchRow($res))
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
             $arr[$row['goods_id']]['goods_name'] = $row['goods_name'];
            $arr[$row['goods_id']]['goods_id']      = $row['goods_id'];
           
            $arr[$row['goods_id']]['type']          = $row['goods_type'];
            $arr[$row['goods_id']]['market_price']  = price_format($row['market_price']);
            $arr[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
            $arr[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
            $arr[$row['goods_id']]['goods_brief']   = $row['goods_brief'];
            $arr[$row['goods_id']]['goods_thumb']   = get_image_path($row['goods_id'], $row['goods_thumb'], true);
            $arr[$row['goods_id']]['goods_img']     = get_image_path($row['goods_id'], $row['goods_img']);
            $arr[$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
        }

       //print_r($arr);exit;
        echo json_encode(array('data'=>array_values($arr)));
        
    }
	
}