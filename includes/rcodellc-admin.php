<?php
function rcodellc_setting_reset() {
  update_option('rcodellc_shop_condition', 1);
  update_option('rcodellc_shop_msg', 1);
  update_option('rcodellc_display_variation', 1);
  update_option('rcodellc_image_size', 40);
  update_option('rcodellc_display_mini_cart', 1);
  update_option('rcodellc_display_type', 0);
  update_option('rcodellc_display_image_preview', 1);
  update_option('rcodellc_display_image', 1);
  update_option('rcodellc_cart_template', 'red');
  update_option('rcodellc_exc_cat', '');
  update_option('rcodellc_menu_bg_color', 'F52727');
  update_option('rcodellc_menu_hover_color', '222222');
  update_option('rcodellc_menu_text_color', 'FFFFFF');
  update_option('rcodellc_submenu_bg_color', 'FFFFFF');
  update_option('rcodellc_prod_name_color', '000000');
  update_option('rcodellc_prod_name_hover_color', '292929');
  update_option('rcodellc_prod_des_color', '000000');
  update_option('rcodellc_searcodellc_bg_color', 'ffffff');
  update_option('rcodellc_searcodellc_border_color', 'F52727');
  update_option('rcodellc_searcodellc_text_color', 'F52727');
  update_option('rcodellc_item_bg', 'cccccc');
  update_option('rcodellc_border', 'f6f6f6');
  update_option('rcodellc_cart_image', 'cart0.png');
}

function rcodellc_product_dropdown_categories($args = array(), $deprecated_hierarcodellcical = 1, $deprecated_show_uncategorized = 1, $deprecated_orderby = '') {
  global $wp_query;
  global $woocommerce;
  if (!is_array($args)) {
    _deprecated_argument('rcodellc_product_dropdown_categories()', '2.1', 'show_counts, hierarcodellcical, show_uncategorized and orderby arguments are invalid - pass a single array of values instead.');
    $args['show_counts']        = $args;
    $args['hierarcodellcical']  = $deprecated_hierarcodellcical;
    $args['show_uncategorized'] = $deprecated_show_uncategorized;
    $args['orderby']            = $deprecated_orderby;
  }
  $current_product_cat = isset($wp_query->query['product_cat']) ? $wp_query->query['product_cat'] : '';
  $defaults = array(
      'pad_counts'          => 1,
      'show_counts'         => 1,
      'hierarcodellcical'   => 1,
      'hide_empty'          => 1,
      'show_uncategorized'  => 0,
      'orderby'             => 'name',
      'selected'            => $current_product_cat,
      'menu_order'          => false
  );
  $args = wp_parse_args($args, $defaults);
  if ($args['orderby'] == 'order') {
    $args['menu_order'] = 'asc';
    $args['orderby']    = 'name';
  }
  $terms = get_terms('product_cat', apply_filters('rcodellc_product_dropdown_categories_get_terms_args', $args));
  if (get_option('rcodellc_exc_cat')) {
    $exc_cats_slug = explode(',', get_option('rcodellc_exc_cat'));
    foreach ($terms as $key => $val) {
      if (in_array($val->slug, $exc_cats_slug)) {
        unset($terms[$key]);
      }
    }
  }
  if (!$terms) {
    return;
  }

  $output = "<select name='product_cat' class='dropdown_product_cat'>";
  $output .= '<option value="" ' . selected($current_product_cat, '', false) . '>' . __('Select a category', 'woocommerce') . '</option>';
  $output .= wc_walk_category_dropdown_tree($terms, 0, $args);
  if ($args['show_uncategorized']) {
    $output .= '<option value="0" ' . selected($current_product_cat, '0', false) . '>' . __('Uncategorized', 'woocommerce') . '</option>';
  }
  $output .= "</select>";
  echo $output;
}

function rcodellc_setting() {
  if (!class_exists('Woocommerce')) {
    echo '<div id="message" class="error"><p>Please Activate WordPress WooCommerce Plugin</p></div>';
    return false;
  }
  if (isset($_POST['rcodellc_status_submit']) && sanitize_text_field($_POST['rcodellc_status_submit']) == 1) {
    update_option('rcodellc_shop_condition', sanitize_text_field($_POST['rcodellc_shop_condition']));
    update_option('rcodellc_shop_msg', sanitize_text_field($_POST['rcodellc_shop_msg']));
    update_option('rcodellc_display_variation', sanitize_text_field($_POST['rcodellc_display_variation']));
    update_option('rcodellc_image_size', sanitize_text_field($_POST['rcodellc_image_size']));
    update_option('rcodellc_display_mini_cart', sanitize_text_field($_POST['rcodellc_display_mini_cart']));
    update_option('rcodellc_display_type', sanitize_text_field($_POST['rcodellc_display_type']));
    update_option('rcodellc_display_image_preview', sanitize_text_field($_POST['rcodellc_display_image_preview']));
    update_option('rcodellc_display_image', sanitize_text_field($_POST['rcodellc_display_image']));
    update_option('rcodellc_cart_template', sanitize_text_field($_POST['rcodellc_cart_template']));
    update_option('rcodellc_exc_cat', sanitize_text_field($_POST['rcodellc_exc_cat']));
    update_option('rcodellc_menu_bg_color', sanitize_text_field($_POST['rcodellc_menu_bg_color']));
    update_option('rcodellc_menu_hover_color', sanitize_text_field($_POST['rcodellc_menu_hover_color']));
    update_option('rcodellc_menu_text_color', sanitize_text_field($_POST['rcodellc_menu_text_color']));
    update_option('rcodellc_submenu_bg_color', sanitize_text_field($_POST['rcodellc_submenu_bg_color']));
    update_option('rcodellc_prod_name_color', sanitize_text_field($_POST['rcodellc_prod_name_color']));
    update_option('rcodellc_prod_name_hover_color', sanitize_text_field($_POST['rcodellc_prod_name_hover_color']));
    update_option('rcodellc_prod_des_color', sanitize_text_field($_POST['rcodellc_prod_des_color']));
    update_option('rcodellc_searcodellc_bg_color', sanitize_text_field($_POST['rcodellc_searcodellc_bg_color']));
    update_option('rcodellc_searcodellc_border_color', sanitize_text_field($_POST['rcodellc_searcodellc_border_color']));
    update_option('rcodellc_searcodellc_text_color', sanitize_text_field($_POST['rcodellc_searcodellc_text_color']));
    update_option('rcodellc_item_bg', sanitize_text_field($_POST['rcodellc_item_bg']));
    update_option('rcodellc_border', sanitize_text_field($_POST['rcodellc_border']));
    update_option('rcodellc_cart_image', sanitize_text_field($_POST['rcodellc_cart_image']));
  }

  if (isset($_POST['rcodellc_status_submit']) && sanitize_text_field($_POST['rcodellc_status_submit']) == 2) {
    rcodellc_setting_reset();
  }

  $cartImg = RCODELLC_BASE_URL.'/assets/images/cart-images/'.$rcodellc_cart_image;
  ?>
  <h2>Settings</h2>
  <form method="post" id="rcodellc_options">	
    <input type="hidden" name="rcodellc_status_submit" id="rcodellc_status_submit" value="2"  />
    <table width="100%" cellspacing="2" cellpadding="5" class="widefat">
      <tr style="background: #eee;">
        <td scope="row" width="10%" colspan="3"><strong><?php _e("Shop Stock Condition"); ?>:</strong>
          <select name="rcodellc_shop_condition" style="width: 180px;">
            <option value="1" <?php if (get_option('rcodellc_shop_condition') == 1): ?> selected="selected"<?php endif; ?>>
            Open
            </option>
            <option value="2" <?php if (get_option('rcodellc_shop_condition') == 2): ?> selected="selected"<?php endif; ?>>
            Lunch Time
            </option>
            <option value="3" <?php if (get_option('rcodellc_shop_condition') == 3): ?> selected="selected"<?php endif; ?>>
            Home Delivery Only 
            </option>
            <option value="4" <?php if (get_option('rcodellc_shop_condition') == 4): ?> selected="selected"<?php endif; ?>>
            Closed
            </option>
          </select>
          <input size="28" placeholder="Custom Msg Like Closed..." type="text" name="rcodellc_shop_msg" id="rcodellc_shop_msg" value="<?php if (get_option('rcodellc_shop_msg')) { echo get_option('rcodellc_shop_msg');  } ?>"  />
        </td>

        <td width="150" scope="row" colspan="3"><?php _e("Exclude Category If Any"); ?>:
          <input size="40" placeholder="Comma seperate category slug i.e. slu_1,slu_2" type="text" name="rcodellc_exc_cat" id="rcodellc_exc_cat" value="<?php if (get_option('rcodellc_exc_cat')) { echo get_option('rcodellc_exc_cat');  } ?>"  />
        </td>
      </tr>

      <tr valign="top"> 
        <td scope="row" width="10%"><?php _e("Product image size"); ?>:</td>
        <td  width="20%">
          <select name="rcodellc_image_size" style="width: 125px;">
            <option value="16" <?php if (get_option('rcodellc_image_size') == 16): ?> selected="selected"<?php endif; ?>>16x16</option>
            <option value="32" <?php if (get_option('rcodellc_image_size') == 32): ?> selected="selected"<?php endif; ?>>32x32</option>
            <option value="40" <?php if (get_option('rcodellc_image_size') == 40): ?> selected="selected"<?php endif; ?>>40x40</option>
            <option value="48" <?php if (get_option('rcodellc_image_size') == 48): ?> selected="selected"<?php endif; ?>>48x48</option>
            <option value="64" <?php if (get_option('rcodellc_image_size') == 64): ?> selected="selected"<?php endif; ?>>64x64</option>
          </select>
        </td>
        <td scope="row"  width="10%"><?php _e("Display Mini Cart"); ?>:</td>
        <td  width="20%">
          <select name="rcodellc_display_mini_cart" style="width: 125px;">
            <option value="1"<?php if (get_option('rcodellc_display_mini_cart') == '1'): ?> selected="selected"<?php endif; ?>><?php _e("Yes"); ?></option>
            <option value="0"<?php if (get_option('rcodellc_display_mini_cart') == '0'): ?> selected="selected"<?php endif; ?>><?php _e("No"); ?></option>                
          </select>
        </td>
     
        <td width="150" scope="row"><?php _e("Display Type"); ?>:</td>
        <td>
          <select name="rcodellc_display_type" style="width: 125px;">
            <option value="1"<?php if (get_option('rcodellc_display_type') == '1'): ?> selected="selected"<?php endif; ?>><?php _e("Show content"); ?></option>
            <option value="0"<?php if (get_option('rcodellc_display_type') == '0'): ?> selected="selected"<?php endif; ?>><?php _e("Hide content"); ?></option>                
          </select>
        </td>

         </tr>

      <tr valign="top" style="background: #eee;"> 
        <td width="150" scope="row"><?php _e("Display Image"); ?>:</td>
        <td>
          <select name="rcodellc_display_image" style="width: 125px;">
            <option value="1"<?php if (get_option('rcodellc_display_image') == '1'): ?> selected="selected"<?php endif; ?>><?php _e("Yes"); ?></option>
            <option value="0"<?php if (get_option('rcodellc_display_image') == '0'): ?> selected="selected"<?php endif; ?>><?php _e("No"); ?></option>                
          </select>
        </td>
     
        <td width="150" scope="row"><?php _e("Display Image Preview"); ?>:</td>
        <td>
          <select name="rcodellc_display_image_preview" style="width: 125px;">
            <option value="1"<?php if (get_option('rcodellc_display_image_preview') == '1'): ?> selected="selected"<?php endif; ?>><?php _e("Yes"); ?></option>
            <option value="0"<?php if (get_option('rcodellc_display_image_preview') == '0'): ?> selected="selected"<?php endif; ?>><?php _e("No"); ?></option>                
          </select>
        </td>
        <td width="150" scope="row"><?php _e("Mini Cart Template"); ?>:</td>
        <td>
          <select name="rcodellc_cart_template" style="width: 125px;">
            <option value="red"<?php if (get_option('rcodellc_cart_template') == 'red'): ?> selected="selected"<?php endif; ?>><?php _e("Red"); ?></option>
            <option value="blue"<?php if (get_option('rcodellc_cart_template') == 'blue'): ?> selected="selected"<?php endif; ?>><?php _e("blue"); ?></option>
            <option value="green"<?php if (get_option('rcodellc_cart_template') == 'green'): ?> selected="selected"<?php endif; ?>><?php _e("Green"); ?></option>
            <option value="sky"<?php if (get_option('rcodellc_cart_template') == 'sky'): ?> selected="selected"<?php endif; ?>><?php _e("Sky"); ?></option>
            <option value="pink"<?php if (get_option('rcodellc_cart_template') == 'pink'): ?> selected="selected"<?php endif; ?>><?php _e("Pink"); ?></option>
            <option value="black"<?php if (get_option('rcodellc_cart_template') == 'black'): ?> selected="selected"<?php endif; ?>><?php _e("Black"); ?></option>
            <option value="grey"<?php if (get_option('rcodellc_cart_template') == 'grey'): ?> selected="selected"<?php endif; ?>><?php _e("Grey"); ?></option>
            <option value="yellow"<?php if (get_option('rcodellc_cart_template') == 'yellow'): ?> selected="selected"<?php endif; ?>><?php _e("Yellow"); ?></option>
          </select>
        </td>
      </tr>

      <tr valign="top"> 
        <td><?php _e("Border color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_border" size="10" id="rcodellc_border" class="color" value="<?php echo get_option('rcodellc_border') ?>" /> 
        </td>
        <td><?php _e("Search Button Background Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_searcodellc_bg_color" size="10" id="rcodellc_searcodellc_bg_color" class="color" value="<?php echo get_option('rcodellc_searcodellc_bg_color') ?>" /> 
        </td>
  
        <td><?php _e("Search button Border Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_searcodellc_border_color" size="10" id="rcodellc_searcodellc_border_color" class="color" value="<?php echo get_option('rcodellc_searcodellc_border_color') ?>" /> 
        </td>
        </tr>

      <tr style="background: #eee;">
        <td><?php _e("Search button Text Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_searcodellc_text_color" size="10" id="rcodellc_searcodellc_text_color" class="color" value="<?php echo get_option('rcodellc_searcodellc_text_color') ?>" /> 
        </td>
     
        <td><?php _e("Menu Background Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_menu_bg_color" size="10" id="rcodellc_menu_bg_color" class="color" value="<?php echo get_option('rcodellc_menu_bg_color') ?>" /> 
        </td>
         <td><?php _e("Menu Hover Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_menu_hover_color" size="10" id="rcodellc_menu_hover_color" class="color" value="<?php echo get_option('rcodellc_menu_hover_color') ?>" /> 
        </td>
      </tr>

      <tr>
        <td><?php _e("Menu Text Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_menu_text_color" size="10" id="rcodellc_menu_text_color" class="color" value="<?php echo get_option('rcodellc_menu_text_color') ?>" /> 
        </td>
        <td><?php _e("Sub Menu Background Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_submenu_bg_color" size="10" id="rcodellc_submenu_bg_color" class="color" value="<?php echo get_option('rcodellc_submenu_bg_color') ?>" /> 
        </td>
     
        <td><?php _e("Item Name Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_prod_name_color" size="10" id="rcodellc_prod_name_color" class="color" value="<?php echo get_option('rcodellc_prod_name_color') ?>" /> 
        </td>
         </tr>

      <tr style="background: #eee;">
        <td><?php _e("Item Name Hover Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_prod_name_hover_color" size="10" id="rcodellc_prod_name_hover_color" class="color" value="<?php echo get_option('rcodellc_prod_name_hover_color') ?>" /> 
        </td>
   
        <td><?php _e("Item Description Color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_prod_des_color" size="10" id="rcodellc_prod_des_color" class="color" value="<?php echo get_option('rcodellc_prod_des_color') ?>" /> 
        </td>
        <td><?php _e("Item background color"); ?>:</td>
        <td>
          <input type="text" name="rcodellc_item_bg" size="10" id="rcodellc_item_bg" class="color" value="<?php echo get_option('rcodellc_item_bg') ?>" /> 
        </td>
      </tr>     

      <tr>
        <td><?php _e("Cart Image"); ?>:</td>
        <td colspan="6">
          <table>
            <tr>
              <?php for ($i = 0; $i <= 14; $i++) { 
                $imgName = 'cart'.$i.'.png';
                ?>
              <td>
                <input type="radio"  name="rcodellc_cart_image" value="<?php echo $imgName; ?>" <?php if(get_option('rcodellc_cart_image')==$imgName) { echo 'checked'; } ?>>
                <label for="male"><img src="<?php echo $cartImg.'/'.$imgName; ?>"></label>
              </td>
              <?php } ?>
            </tr>
            <tr>
              <?php for ($i = 16; $i <= 30; $i++) { 
                $imgName = 'cart'.$i.'.png';
                ?>
              <td>
                <input type="radio"  name="rcodellc_cart_image" value="<?php echo $imgName; ?>" <?php if(get_option('rcodellc_cart_image')==$imgName) { echo 'checked'; } ?>>
                <label for="male"><img src="<?php echo $cartImg.'/'.$imgName; ?>"></label>
              </td>
              <?php } ?>
            </tr>
          </table>
        </td>
      </tr>

      <tr valign="top">
        <td colspan="4" scope="row">			
          <input type="button" name="save" onclick="document.getElementById('rcodellc_status_submit').value = '1'; document.getElementById('rcodellc_options').submit();" value="<?php _e("Save"); ?>" class="button-primary" />
          <input type="button" name="reset" onclick="document.getElementById('rcodellc_status_submit').value = '2'; document.getElementById('rcodellc_options').submit();" value="<?php _e("Reset All"); ?>" class="button-primary" />
        </td> 
      </tr>
      </td>
      </tr>      
    </table>
  </form>   
<?php
}
?> 