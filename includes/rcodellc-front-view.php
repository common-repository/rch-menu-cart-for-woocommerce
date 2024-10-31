<?php
//Fetch product list
add_shortcode('rcodellc_menu_cart', 'rcodellc_product_show');
function rcodellc_product_show($val) {
    if(is_admin()) {
        return false;
    }
    global $woocommerce;
    if (!class_exists('Woocommerce')) {
        $errorTxt = '<div id="message" class="error"><p>Please Activate WordPress WooCommerce Plugin</p></div>';
        return $errorTxt;
    }
    $rcodellc_shop_condition  = get_option('rcodellc_shop_condition');
    if($rcodellc_shop_condition==2){
        $errorTxt = '<div id="message" class="error"><p>'.get_option('rcodellc_shop_msg').'</p></div>';
        return $errorTxt;
    }else if($rcodellc_shop_condition==4){
        $errorTxt = '<div id="message" class="error"><p>'.get_option('rcodellc_shop_msg').'</p></div>';
        return $errorTxt;
    }
    ob_start();
    echo rcodellc_product_list($val);
    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;  
}

function rcodellc_getvarprice(){
    global $woocommerce;
    $var_id        =  sanitize_text_field($_POST['rcodellc_var_id']);
    $product_products       =  wc_get_product($var_id);                
    $product_products_price =  woocommerce_price($product_products->get_price());
    echo $product_products_price;
    exit();  
}
function rcodellc_addtocart() { 
  global $woocommerce;
  //$var_id=$_POST['rcodellc_prod_var_id'];
  $prod_id  = sanitize_text_field($_POST['rcodellc_prod_id']);
  $var_id   = sanitize_text_field($_POST['rcodellc_prod_var_id']);
  $prod_quant_id = sanitize_text_field($_POST['rcodellc_prod_qty']);
 
  if($var_id==0){    
	  $product_products  = wc_get_product($prod_id);
    $bool     =  $product_products->is_sold_individually();
    if($bool==1){
      $chk_cart=rcodellc_check_cart_item_by_id($prod_id);
      if($chk_cart==0){        
        echo __("Already added to cart","rcodellc-lang");
        exit;
      }
    }
  }else{
  	  $product_products = wc_get_product($var_id);
      $bool    = $product_products->is_sold_individually();
      if($bool==1){      
        $chk_cart = rcodellc_check_cart_item_by_id($var_id);
        if($chk_cart==0){        
          echo __("Already added to cart","rcodellc-lang");
          exit;
        }
      }
  }
  $prod_stock   = $product_products->get_stock_quantity();
  $availability = $product_products->get_availability();
  if($availability['class']=='out-of-stock'){    
    echo __("Out of stock","rcodellc-lang");
    exit;
  }
  if($prod_stock!=''){
    	foreach($woocommerce->cart->cart_contents as $cart_item_key => $values ) {
        $var_pro_item_id = '';
        $prod_item_id    = '';
        if($values['variation_id']!=''){
          $var_pro_item_id = $values['variation_id'];
        }else{
          $var_pro_item_id = $values['product_id'];
        }
        $prod_item_id = $values['quantity'] + $prod_quant_id;
        if($var_id==0 && $prod_id==$var_pro_item_id && $prod_item_id>$prod_stock){
          $product_products = wc_get_product($prod_id);		            
          echo __("You have cross the stock limit","rcodellc-lang");
          exit;
        }else if($var_id==$var_pro_item_id && $prod_item_id>$prod_stock){
          $product_products = wc_get_product($var_id);
          echo __("You have cross the stock limit","rcodellc-lang");
          exit;
        }        
	   }    
  }
  
  do_action( 'woocommerce_set_cart_cookies', true );
  if($var_id==0){
    $z = $woocommerce->cart->add_to_cart($prod_id,$prod_quant_id,null, null, null );
  }else{    
    $z = $woocommerce->cart->add_to_cart($prod_id, $prod_quant_id, $var_id, $product_products->get_variation_attributes(),null);
  }  
  echo '1';  
  exit;
}

function rcodellc_check_cart_item_by_id($id) { 
  	global $woocommerce;
  	foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
    $all_product = $values['data'];
    		if($id == $all_product->id) {
    			 return 0;
    		}
  	}	
  	return 1;
}

function rcodellc_cart_amount(){
    global $woocommerce;
    echo $woocommerce->cart->get_cart_total();  
    exit;
}


function rcodellc_product_list($val) {  
  global $woocommerce;
  if(get_option('rcodellc_image_size')){
    $rcodellc_img_size = get_option('rcodellc_image_size');
  }else{
    $rcodellc_img_size = 40;
  }

  $rcodellc_menu_bg_color             = 'FF0000';
  $rcodellc_menu_hover_color          = '222222';
  $rcodellc_menu_text_color           = 'FFFFFF';  
  $rcodellc_submenu_bg_color          = 'FFFFFF';
  $rcodellc_prod_name_color           = '000000';
  $rcodellc_prod_name_hover_color     = 'FFFFFF';
  $rcodellc_prod_des_color            = '000000';
  $rcodellc_searcodellc_bg_color      = 'ffffff';
  $rcodellc_searcodellc_border_color  = 'FF0000';
  $rcodellc_searcodellc_text_color    = 'FF0000';
  $rcodellc_item_bg                   = 'cccccc';
  $rcodellc_border                    = 'f6f6f6';
  $rcodellc_shop_condition            = 1;
  
  if(get_option('rcodellc_searcodellc_bg_color')){
    $rcodellc_searcodellc_bg_color = get_option('rcodellc_searcodellc_bg_color');
  }
  if(get_option('rcodellc_searcodellc_border_color')){
    $rcodellc_searcodellc_border_color = get_option('rcodellc_searcodellc_border_color');
  }
  if(get_option('rcodellc_searcodellc_text_color')){
    $rcodellc_searcodellc_text_color = get_option('rcodellc_searcodellc_text_color');
  }
  if(get_option('rcodellc_menu_bg_color')){
    $rcodellc_menu_bg_color = get_option('rcodellc_menu_bg_color');
  }
  if(get_option('rcodellc_menu_hover_color')){
    $rcodellc_menu_hover_color = get_option('rcodellc_menu_hover_color');
  }
  if(get_option('rcodellc_menu_text_color')){
    $rcodellc_menu_text_color  = get_option('rcodellc_menu_text_color');
  }   
  if(get_option('rcodellc_submenu_bg_color')){
    $rcodellc_submenu_bg_color = get_option('rcodellc_submenu_bg_color');
  }
  if(get_option('rcodellc_prod_name_color')){
    $rcodellc_prod_name_color = get_option('rcodellc_prod_name_color');
  }
  if(get_option('rcodellc_prod_name_hover_color')){
    $rcodellc_prod_name_hover_color = get_option('rcodellc_prod_name_hover_color');
  }
  if(get_option('rcodellc_prod_des_color')){
    $rcodellc_prod_des_color = get_option('rcodellc_prod_des_color');
  }
  if(get_option('rcodellc_item_bg')){
    $rcodellc_item_bg = get_option('rcodellc_item_bg');
  }
  if(get_option('rcodellc_border')){
    $rcodellc_border   = get_option('rcodellc_border');
  }
  if(get_option('rcodellc_shop_condition')){
    $rcodellc_shop_condition   = get_option('rcodellc_shop_condition');
  }
  ?>

  <style>
    .rcodellc_searcodellc{
      <?php 
      echo 'background:#'.$rcodellc_searcodellc_bg_color.'!important;';
      echo 'border:2px solid #'.$rcodellc_searcodellc_border_color.'!important;';
      echo 'color:#'.$rcodellc_searcodellc_text_color.'!important;';
      ?>
    }
    .glossymenu a.menuitem{
      font: bold "Lucida Grande", "Trebuchet MS", Verdana, Helvetica, sans-serif;
      font-size: 16px;
      <?php echo 'background:#'.$rcodellc_menu_bg_color.';';?>
    }
    .glossymenu div.submenu{ /*DIV that contains each sub menu*/
      <?php echo 'background:#'.$rcodellc_submenu_bg_color.';';?>
     }
    .glossymenu a.menuitem:hover{
      <?php echo 'background:#'.$rcodellc_menu_hover_color.';';?>
    }
    .glossymenu a.menuitem{
      <?php echo 'color:#'.$rcodellc_menu_text_color.'!important;';?>
    }  
    .glossymenu div.submenu ul li a{
      <?php echo 'color:#'.$rcodellc_menu_text_color.'!important;';?>
    }
    .rcodellc_name{
      color: black;
      font-size: 13px;
      font-weight: bold;
    }
    .rcodellc_name a{
      <?php echo 'color:#'.$rcodellc_prod_name_color.';';?>
     }
     .rcodellc_name a:hover{
      <?php echo 'color:#'.$rcodellc_prod_name_hover_color.'!important;';?>
     }
    .rcodellc_des{
      <?php echo 'color:#'.$rcodellc_prod_des_color.';';?>
      font-size: 11px;
      line-height: 15px;
    }
    .rcodellc_des a{
      <?php echo 'color:#'.$rcodellc_prod_name_color.';';?>
    }
    .rcodellc_des a:hover{
      <?php echo 'color:#'.$rcodellc_prod_name_color.';';?>
    }
    .alert-info {
        <?php 
         echo 'background-color:#'.$rcodellc_menu_text_color.';';
         echo 'border-color:#'.$rcodellc_menu_bg_color.';';
         echo 'color:#'.$rcodellc_menu_bg_color.';';
         ?>
    }
 

    .rcodellc_table tr{  
      <?php
      echo 'border-bottom:solid 10px #'.$rcodellc_border.'!important;';
      echo 'background:#'.$rcodellc_item_bg.'!important;';
      ?>
    }
    .rwd-table tr {
      border-bottom: unset!important;
    }
    .wrs_cf_table tr{
      border-bottom: unset!important;
    }
  </style>
  <?php
  
  
  if(get_option('rcodellc_display_mini_cart')==1){  
     wp_enqueue_style('template_rcodellc_cart_template',RCODELLC_BASE_URL.'/assets/css/rch_template_'.get_option('rcodellc_cart_template').'.css');
  ?>
<?php
  }
?>
<form method="post" id="rcodellc_options">
  <?php
    if($val){
        echo rcodellc_product_dropdown_categories2($val['category_id'], array(), 1, 0, '' );
    }else{
        echo rcodellc_product_dropdown_categories( array(), 1, 0, '' );
    }
  ?>  
  <input type="hidden" value="1" name="rcodellc_hval" />
  <input type="submit" class="rcodellc_searcodellc" name="rcodellc_btn_searcodellc" value="<?php _e("Search","rcodellc-lang");?>"/>
</form> 
<?php 
if($rcodellc_shop_condition==3){
    $errorTxt = '<div id="message" class="error"><p>'.get_option('rcodellc_shop_msg').'</p></div>';
    echo $errorTxt;
}
?>

<br /> 

<?php  $cart_url = wc_get_cart_url(); ?>

<div class="span4 alertAdd" style="opacity: 1; display: block;">
  <div class="alert alert-info" id="rcodellc_alert_info" style="display: none;"><?php _e("Added to your cart","rcodellc-lang");?></div>
</div>
<?php if(get_option('rcodellc_display_mini_cart')==1){ ?>
<div id="rcodellc_cart_amount" class="rcodellc_cart_amount">
  <a href="<?php echo$cart_url;?>"><div id="rcodellc_cart_price" class="rcodellc_cart_price"><?php echo $woocommerce->cart->get_cart_total(); ?></div></a>  
</div>
<?php }?>


<script>
  jQuery(document).ready(function() {
	jQuery('.simple-ajax-popup-align-top').customPopup({
      type: 'ajax',		
      overflowY: 'scroll'
    });	
  });
  
  //-------------------------------------
  var img_url_plus  = '<?php echo RCODELLC_BASE_URL; ?>/assets/images/plus.png';
  var img_url_minus = '<?php echo RCODELLC_BASE_URL; ?>/assets/images/minus.png';
  
  ddaccordion.init({
      headerclass: "submenuheader", 
      contentclass: "submenu", 
      revealtype: "click",
      mouseoverdelay: 500, 
      collapseprev: false,
      defaultexpanded: [<?php if(get_option('rcodellc_display_type')){echo '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30';} ?>], 
      onemustopen: false,
      animatedefault: true, 
      persiststate: false, 
      toggleclass: ["", ""], 
      togglehtml: ["suffix", img_url_plus, img_url_minus],
      animatespeed: "slow",
      oninit:function(headers, expandedindices){ 
        //do nothing
      },
      onopenclose:function(header, index, state, isuseractivated){ 
      }
  })  
  //------------------------------------  
  //jQuery('#dropdown_product_cat option[value=]').text('All products');
  function rcodellc_getvarprice_ajax(var_id, id){
      var_id = var_id.value;
      jQuery('#rcodellc_var_id_'+id).val(var_id);
      var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
      jQuery.ajax({
        type: "POST",
        url:ajax_url,
        data : {'action': 'rcodellc_getvarprice',
          'rcodellc_var_id':     var_id
        },
        success: function(data){
          jQuery('#rcodellc_price_'+id).html(data);
        }
      });
  }
  
  function rcodellc_add_prod(pid,vid){
    jQuery("#rcodellc_loader"+pid).show();
    var vid = jQuery('#rcodellc_var_id_'+pid).val();
    var qty = jQuery('#product_qty_'+pid).val();
    
    if(qty==0 || qty==''){
      jQuery('#rcodellc_alert_info').text('Quantity can not be less than 1');
      jQuery('#rcodellc_alert_info').show()
      setTimeout(function(){jQuery('#rcodellc_alert_info').hide()}, 1500);      
      return false;
    }
    if(qty>1000){
      jQuery('#rcodellc_alert_info').text('You have cross the quantity limit');
      jQuery('#rcodellc_alert_info').show()
      setTimeout(function(){jQuery('#rcodellc_alert_info').hide()}, 1500);      
      return false;
    }
    if(vid==0){
      qty= jQuery('#product_qty_'+pid).val();
    }
  
    var ajax_url2 = '<?php echo admin_url( 'admin-ajax.php' ); ?>';   
        jQuery.ajax({
          type: "POST",
          url:ajax_url2,
          data : {
                  'action': 'rcodellc_addtocart',
                  'rcodellc_prod_id':     pid,
                  'rcodellc_prod_var_id': vid,
                  'rcodellc_prod_qty':    qty
          },
          success: function(response){          
            if(response==1){              
              jQuery('#rcodellc_alert_info').text('<?php _e("Added to your cart","rcodellc-lang"); ?>');
              rcodellc_updateCartFragment();
            }else{
              jQuery('#rcodellc_alert_info').text(response);
            }            
            jQuery.ajax({
              type: "POST",
              url:ajax_url2,
              data : {'action': 'rcodellc_cart_amount'},
              success: function(data){                
                jQuery('#rcodellc_cart_price').html(data);
              }
            });            
             jQuery('#rcodellc_alert_info').show()
             setTimeout(function(){jQuery('#rcodellc_alert_info').hide()}, 2000);
             jQuery("#rcodellc_loader"+pid).hide();
          }
        });
  }
  
  jQuery(document).ready(function(){
    jQuery(".ajax").colorbox();
  });
  
  function rcodellc_updateCartFragment() {
    $fragment_refresh = {
    url: woocommerce_params.ajax_url,
    type: 'POST',
    data: { action: 'woocommerce_get_refreshed_fragments' },
    success: function( data ) {
      if ( data && data.fragments ) {          
          jQuery.each( data.fragments, function( key, value ) {
              jQuery(key).replaceWith(value);
          });

          //if ( $supports_html5_storage ) {
          if(window.localStorage) {            
              sessionStorage.setItem( "wc_fragments", JSON.stringify( data.fragments ) );
              sessionStorage.setItem( "wc_cart_hash", data.cart_hash );
          }                
          jQuery('body').trigger( 'wc_fragments_refreshed' );
      }
    }
  };
  //Always perform fragment refresh
  jQuery.ajax( $fragment_refresh );  
  }
</script>  
<?php
$s_va=0;
if(isset($_POST['rcodellc_hval']) && isset($_POST['product_cat']) && sanitize_text_field($_POST['product_cat'])!=''){
  $s_va=1;
}  

    if($val && $s_va==0){
      echo '<div class="glossymenu">';
      $id = $val['category_id'];
      $categories_ids = explode(",",$id);
      if(!empty($categories_ids)){
        foreach($categories_ids as $key) { 
          $product_products_category =  get_term_by( 'id', $key, 'product_cat', 'ARRAY_A' );
          if(!empty($product_products_category )){
            echo '<a class="menuitem submenuheader">'.$product_products_category['name'].'</a>';//menu cat
              $args = array(
                'post_type'		=> 'product',
                'post_status' => 'publish',			
                'orderby' 		=> 'title',
                'order' 		  => 'asc',
                'type'        => 'numeric',
                'posts_per_page' 	=> 200,
                'tax_query' 			=> array(
                      array(
                      'taxonomy' 	=> 'product_cat',
                      'terms' 		=> array( esc_attr($product_products_category['slug']) ),
                      'field' 		=> 'slug',
                      'operator' 	=> 'IN'
                    )
                  )
              );
            $loop = new WP_Query( $args );
            rcodellc_show_prod2($loop, $rcodellc_img_size);
          }  
        } 
      }
    }else{
      echo '<div class="glossymenu">';
      if(isset($_POST['rcodellc_hval']) && isset($_POST['product_cat']) && sanitize_text_field($_POST['product_cat'])!=''){
    
         //$exc_cats_slug=  explode(',', get_option('rcodellc_exc_cat'));
         $args = array(
          'post_type'				  => 'product',
          'post_status' 			=> 'publish',			
          'orderby' 				  => 'title',
          'order' 				    => 'asc',
          'type'              => 'numeric',
          'posts_per_page' 		=> 200,
          /*'meta_query' 			=> array(
            array(
              'key' 			=> '_visibility',
              'value' 		=> array('catalog', 'visible'),
              'compare' 	=> 'IN'
            )
          ),*/
          'tax_query' 			=> array(
                array(
                'taxonomy' 	=> 'product_cat',
                'terms' 		=> array( esc_attr(sanitize_text_field($_POST['product_cat'])) ),
                'field' 		=> 'slug',
                'operator' 	=> 'IN'
              )
            )
        );
       
       $cat_data = get_term_by( 'slug', sanitize_text_field($_POST['product_cat']), 'product_cat', 'ARRAY_A' );       
       echo '<a class="menuitem submenuheader">'.$cat_data['name'].'</a>';//menu cat
       $loop = new WP_Query( $args );
       rcodellc_show_prod2($loop, $rcodellc_img_size);
  }else{
    $exc_cats_slug=array();
    if (get_option('rcodellc_exc_cat')){          
          $exc_cat       = str_replace(' ','',get_option('rcodellc_exc_cat'));
          $exc_cats_slug =  explode(',', $exc_cat);
    }
    
    $term 			= get_queried_object();
    $parent_id  = empty( $term->term_id ) ? 0 : $term->term_id;
    $args2      = array(
      'parent'       => $parent_id,
      'child_of'     => $parent_id,
      'menu_order'   => 'ASC',
      'hide_empty'   => 1,
      'hierarcodellcical' => 1,
      'taxonomy'     => 'product_cat',
      'terms' 		   => 'bag',
      'field' 		   => 'slug'
    );
		
		$product_products_categories = get_categories( $args2  );
    $total = sizeof($product_products_categories);
    foreach ($product_products_categories as $cat_data){
      if(!in_array(trim($cat_data->slug), $exc_cats_slug)){
      //if(!in_array($cat_data->slug,$exc_cats_slug)){
        echo '<a class="menuitem submenuheader">'.$cat_data->name.'</a>';//menu cat
          $args = array(
            'post_type'				  => 'product',
            'post_status' 			=> 'publish',			
            'orderby' 				  => 'title',
            'order' 				    => 'asc',
            'type'              => 'numeric',
            'posts_per_page' 		=> 200,
            /*'meta_query' 			=> array(
              array(
                'key' 			=> '_visibility',
                'value' 		=> array('catalog', 'visible'),
                'compare' 	=> 'IN'
              )
            ),*/
            'tax_query' 			=> array(
                  array(
                  'taxonomy' 	=> 'product_cat',
                  'terms' 		=> array( esc_attr($cat_data->slug) ),
                  'field' 		=> 'slug',
                  'operator' 	=> 'IN'
                )
              )
          );
        $loop = new WP_Query( $args );
        rcodellc_show_prod2($loop, $rcodellc_img_size);
      }
    }
  }
}
  echo '</div>';//glossymenu end
}
function rcodellc_show_prod2($loop,$rcodellc_img_size){
      global $woocommerce;   
      if ($loop->have_posts()){        
        echo '<div class="submenu"><ul><table class="rcodellc_table">';
        foreach($loop->posts as $val){         
          $product_products = wc_get_product($val->ID );
          $att_value = '';
          if($product_products->is_type( 'variable')){
            $default_att = $product_products->get_default_attributes();
            if(!empty($default_att)){
              foreach ($default_att as $att_val){
                $att_value = $att_val;
              }
            }
          }
          $is_cat = 0;    
          if($is_cat==0){
            $variation_display = false;
            $variation = false;
            if (get_option('rcodellc_display_variation')=='1'){
              $variation_display = true;
            }            
            
            if ($variation_display == true){
                $variation_query  = new WP_Query();
                $args_variation   = array(
                  'post_status' => 'publish',
                  'post_type' => 'product_variation',
                  'posts_per_page'   => -1,  
                  'post_parent' => $val->ID
                );                
                $variation_query->query($args_variation);
                if ($variation_query->have_posts()){
                  $variation = true;
                }
            }
            
             
            if($variation==true && $product_products->is_type( 'variable' )){  
              $product_products_name_org = '<div class="rcodellc_name"><a href="#">'.$val->post_title.'</a></div>';
              $prod_des = '';
              if($val->post_content){
                $prod_des = $val->post_content;
                if(strlen($prod_des)>=120){
                  $prod_des = substr($prod_des,0,60).'... <a href="#">Read More..</a>';
                }   
              }
                            
              $rch_att_val = '';              
              $var_query   = $variation_query->posts;
              $prod_price  = '';
              $rcodellc_var_id = '';
              foreach($var_query as $var_data){
                $product_products    = wc_get_product($var_data->ID);                
                $attributes = woocommerce_get_formatted_variation($product_products->get_variation_attributes(),true);
                $attributes = explode(':', $attributes); 
                
                $att_value = strtolower($att_value);
                $att_curr  = strtolower($attributes[1]);
                $att_curr  = str_replace(' ', '', $att_curr);
                $select='';
                $product_products_price=woocommerce_price($product_products->get_price());
                if($att_value==''){
                  if(!$prod_price){                    
                    $prod_price      = $product_products_price;
                    $rcodellc_var_id = $var_data->ID;
                  }
                
                }else if($att_value==$att_curr){                    
                  $prod_price      = $product_products_price;
                  $rcodellc_var_id = $var_data->ID;
                  $select          = 'selected="selected"';
                }                  
                //------dropdown product variation
                $rch_att_val.='<option '.$select.' value='.$var_data->ID.'>'.$attributes[1].'</option>';
                
                //-------image
                $img_url = RCODELLC_BASE_URL. '/assets/images/placeholder.png';
                if (has_post_thumbnail($var_data->ID)){
                  $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($var_data->ID) );                    
                  $thumb    = wp_get_attachment_image_src( get_post_thumbnail_id($var_data->ID), 'thumbnail' );
                  $img_url  = $thumb['0'];
                  
                } else if (has_post_thumbnail($val->ID)){
                  $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($val->ID) );                    
                  $thumb    = wp_get_attachment_image_src( get_post_thumbnail_id($val->ID), 'thumbnail' );
                  $img_url  = $thumb['0'];                   
                }
                //--------stock
                $max_stock=1000;
              }//end foreach
              //prod_image
              $img_url = RCODELLC_BASE_URL. '/assets/images/placeholder.png';
              if (has_post_thumbnail($val->ID)){
                    $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($val->ID) );                    
                    $thumb    = wp_get_attachment_image_src( get_post_thumbnail_id($val->ID), 'thumbnail' );
                    $img_url  = $thumb['0'];                   
              }
              $prod_option = '<select onchange="rcodellc_getvarprice_ajax(this,'.$val->ID.');" style="max-width:100px;">'.$rch_att_val.'</select>';
              echo '<tr>';
              if (get_option('rcodellc_display_image')=='1'){
                if (get_option('rcodellc_display_image_preview')=='1'){
                      echo '<td class="rcodellc_td_img"><a href="'.$img_url.'" class="preview"><img src="'.$img_url.'" height="'.$rcodellc_img_size.'" width="'.$rcodellc_img_size.'" /></a></td>';
                    }else{
                      echo '<td class="rcodellc_td_img"><img src="'.$img_url.'" height="'.$rcodellc_img_size.'" width="'.$rcodellc_img_size.'" /></td>';
                }
              }              
              echo '<td class="rcodellc_td_title" colspan="2">'.$product_products_name_org.'<div class="rcodellc_des">'.$prod_des.'</div><br />'.$prod_option.'</td>';
              ?>
                    <td class="rcodellc_td_quantity" valign="top">
                      <input type="hidden" name="rcodellc_var_id" id="rcodellc_var_id_<?php echo $val->ID?>" value="<?php echo $rcodellc_var_id;?>" />
                        <?php
                        if($max_stock!=0){                            
                          ?><input type="number" style="width:60px;" value="1" min="1"  max="<?php echo $max_stock;?>" name="product_qty_<?php echo $val->ID?>" id="product_qty_<?php echo $val->ID?>" /><?php                            
                        }else{                            
                           ?><input type="number" style="width:60px;" value="0" min="0" max="0" name="product_qty_<?php echo $val->ID?>" id="product_qty_<?php echo $val->ID?>" /><?php
                        }
                        ?>
                    </td>  
                  <?php                  
                  if(get_option('rcodellc_cart_image')){
                    $rcodellc_cart_image = get_option('rcodellc_cart_image');
                  }else{
                    $rcodellc_cart_image = 'cart0.png';
                  }
                  $cartImg = '<img src='.RCODELLC_BASE_URL.'/assets/images/cart-images/'.$rcodellc_cart_image.'>';

                  if($product_products->regular_price && $max_stock!=0){  
                  echo '<td class="rcodellc_td_price">
                      <div id="rcodellc_price_'.$val->ID.'">
                          '.$prod_price.'
                      </div>  
                      <div class="rcodellc_add_btn"><a onclick="rcodellc_add_prod('.$val->ID.',1);"><div class="rcodellc_add_cart">'.$cartImg.'</div></a></div>
                      <div class="rcodellc_loading" id="rcodellc_loader'.$val->ID.'" style="display: none;"></div>
                      </td>';
                  }else {
                    echo '<td></td>';
                  }
                  //echo '<td width="30"><div class="rcodellc_loading" id="rcodellc_loader'.$val->ID.'" style="display: none;"></div></td></tr>';
                  echo '</tr>';
            }else{
                rcodellc_show_prod($val->ID,$rcodellc_img_size, $val->post_title);
            }
          }//is cat check end  
        }//end foreach
          echo '</table></ul></div>';
    }//if
}

function rcodellc_show_prod($id, $rcodellc_img_size, $post_title){
    $max_stock  = 500;
    $product_products    = wc_get_product( $id );
    if($product_products->get_stock_quantity()!=''){
      $max_stock = $product_products->get_stock_quantity();
    }
    $availability = $product_products->get_availability();
    if($availability['class']=='out-of-stock'){
      $max_stock = 0;
    }
    $product_products_name = '<div class="rcodellc_name"><a href="#">'.$post_title.'</a>
      </div>';
    $product_products  = wc_get_product($id);
    $prod_des = '';
    if($product_products->post->post_content){
      $prod_des = $product_products->post->post_content;   
      if(strlen($prod_des)>=70){
         $prod_des = substr($prod_des,0,50).'... <a href="#">Read More</a>';
      }
    }

    $product_products_price = $product_products->get_price_html();
    if (has_post_thumbnail($id)){
        $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($id,'thumbnail'));
        $thumb    = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'thumbnail' );
        $img_url  = $thumb['0'];

    } else {
        $img_url  = RCODELLC_BASE_URL. '/assets/images/placeholder.png';
        $img_url2 = $img_url;
    }
    if (has_post_thumbnail($id)){
        $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($id,'thumbnail'));
        $thumb    = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'thumbnail' );
        $img_url  = $thumb['0'];

    } else {
        $img_url  = RCODELLC_BASE_URL. '/assets/images/placeholder.png';
        $img_url2 = $img_url;
    }
    echo '<tr>';
    if (get_option('rcodellc_display_image')=='1'){
      if (get_option('rcodellc_display_image_preview')=='1'){
        echo '<td class="rcodellc_td_img"><a href="'.$img_url2.'" class="preview"><img src="'.$img_url.'" height="'.$rcodellc_img_size.'" width="'.$rcodellc_img_size.'" /></a></td>';
      }else{                
        echo '<td class="rcodellc_td_img"><img src="'.$img_url.'" height="'.$rcodellc_img_size.'" width="'.$rcodellc_img_size.'" /></td>';
      }
    }
    echo '<td class="rcodellc_td_title" colspan="2">'.$product_products_name.'<div class="rcodellc_des">'.$prod_des.'</div></td>';
    ?>

      <td class="rcodellc_td_quantity" valign="top">
          <?php
          if($max_stock!=0){
          //if($product_products->regular_price && $max_stock!=0){
            ?><input type="number" style="width:55px;" value="1" min="0" max="0<?php echo $max_stock;?>" name="product_qty_<?php echo $id;?>" id="product_qty_<?php echo $id;?>" /><?php
          }else{
            ?><input type="number" style="width:55px;" value="0" min="0" max="0" name="product_qty_<?php echo $id;?>" id="product_qty_<?php echo $id;?>" /><?php
          }
          ?>        
      </td>  
    <?php
    
    if(get_option('rcodellc_cart_image')){
      $rcodellc_cart_image = get_option('rcodellc_cart_image');
    }else{
      $rcodellc_cart_image = 'cart0.png';
    }
    $cartImg = '<img src='.RCODELLC_BASE_URL.'/assets/images/cart-images/'.$rcodellc_cart_image.'>';
    if($max_stock!=0){    
      echo '<td class="rcodellc_td_price">
             <div>'.$product_products_price.'</div>
             <div class="rcodellc_add_btn"><a onclick="rcodellc_add_prod('.$id.', 0);"><div class="rcodellc_add_cart">'.$cartImg.'</div></a></div>
             <div class="rcodellc_loading" id="rcodellc_loader'.$id.'" style="display: none;"></div>  
          </td>';
    }else{
      echo '<td><div>'.$product_products_price.'</div></td>';
    }
    //echo '<td><div class="rcodellc_loading" id="rcodellc_loader'.$id.'" style="display: none;"></div></td></tr>';
    echo '</tr>';
    
}
add_action( 'wp_ajax_nopriv_rcodellc_addtocart','rcodellc_addtocart' );
add_action( 'wp_ajax_rcodellc_addtocart', 'rcodellc_addtocart' );

add_action( 'wp_ajax_nopriv_rcodellc_cart_amount','rcodellc_cart_amount' );
add_action( 'wp_ajax_rcodellc_cart_amount', 'rcodellc_cart_amount' );

add_action( 'wp_ajax_nopriv_rcodellc_getvarprice','rcodellc_getvarprice' );
add_action( 'wp_ajax_rcodellc_getvarprice', 'rcodellc_getvarprice' );


function rcodellc_product_dropdown_categories2($cids, $args = array(), $deprecated_hierarcodellcical = 1, $deprecated_show_uncategorized = 1, $deprecated_orderby = '' ) { 
  global $wp_query; 
  $categories_ids = explode(",",$cids);
  $query_args_c   = array(
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => true
  );

  $product_products_categories_custom = get_terms( 'product_cat', $query_args_c );
  $slug_cat  = '';
  $output2   = '';
  if(isset($_POST['product_cat'])){
      $slug_cat = sanitize_text_field($_POST['product_cat']);  
  }
  foreach( $product_products_categories_custom as $cat ) {
    $cselected = '';
    if($slug_cat== $cat->slug){
      $cselected ='selected';
    }

    if (in_array($cat->term_taxonomy_id, $categories_ids)){
      $output2.='<option class="level-0" value="'.$cat->slug.'" '.$cselected.'>'.$cat->name.' ('.$cat->count.')</option>';  
    }

    if ( ! is_array( $args ) ) { 
        wc_deprecated_argument( 'rcodellc_product_dropdown_categories()', '2.1', 'show_counts, hierarcodellcical, show_uncategorized and orderby arguments are invalid - pass a single array of values instead.' ); 
        $args['show_count']         = $args; 
        $args['hierarcodellcical']  = $deprecated_hierarcodellcical; 
        $args['show_uncategorized'] = $deprecated_show_uncategorized; 
        $args['orderby']            = $deprecated_orderby; 
    } 

    $current_product_cat = isset( $wp_query->query_vars['product_cat'] ) ? $wp_query->query_vars['product_cat'] : ''; 
    $defaults = array( 
        'pad_counts'          => 1,   
        'show_count'          => 1,  
        'hierarcodellcical'   => 1,  
        'hide_empty'          => 1,  
        'show_uncategorized'  => 0,  
        'orderby'             => 'name',  
        'selected'            => $current_product_cat,  
        'menu_order'          => false,  
  ); 

    $args = wp_parse_args( $args, $defaults ); 
    if ( 'order' === $args['orderby'] ) { 
        $args['menu_order'] = 'asc'; 
        $args['orderby']    = 'name'; 
    } 

    //Get Category
    $terms = get_terms( 'product_cat', apply_filters( 'rcodellc_product_dropdown_categories_get_terms_args', $args ) ); 
    if ( empty( $terms ) ) { 
        return; 
    } 

    //Show category
    $output = "<select name='product_cat' class='dropdown_product_cat'>"; 
    $output .= '<option value="" ' . selected( $current_product_cat, '', false ) . '>' . esc_html__( 'Select a category', 'woocommerce' ) . '</option>'; 
    $output .=$output2;
    if ( $args['show_uncategorized'] ) { 
        $output .= '<option value="0" ' . selected( $current_product_cat, '0', false ) . '>' . esc_html__( 'Uncategorized', 'woocommerce' ) . '</option>'; 
    } 
    $output .= "</select>"; 
    echo $output; 
  } 
}
?>