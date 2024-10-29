<?php
/*
Plugin Name: Admin Bar as Menu
Plugin URI: http://wordpressable.me/plugins/admin-bar-as-menu
Description: Displays menu items on admin bar when user isn't logged in. 
Version: 0.1
Author: Amit Verma
Author URI: http://amit.me
*/

//constants
$kCabam_display_menu='abam_display_menu'; //menu to display

add_action('admin_bar_init', 'abam_init');

function abam_init(){
	if ( !is_user_logged_in() )
		add_action('add_admin_bar_menus', 'add_non_logged_in_menu');
	
}

add_filter('show_admin_bar', 'enable_admin_bar');
function enable_admin_bar(){
	//if( !is_user_logged_in())
		return true;
}

function add_non_logged_in_menu(){
	add_action( 'admin_bar_menu', 'add_site_menu');
}

function add_site_menu(){
	global $wp_admin_bar, $kCabam_display_menu;
	
	$menu_to_display=get_option($kCabam_display_menu);
	if(!$menu_to_display){
		$menus=wp_get_nav_menus();
		$menu_to_display=$menus[1]->name;
	}
	
	$items = wp_get_nav_menu_items($menu_to_display);
	
	foreach($items as $item){

		$args = array();
		$args['id'] = $item->ID;
		$args['title'] = $item->title;
		$args['href'] = $item->url;
		if($item->menu_item_parent)
			$args['parent'] = $item->menu_item_parent;
		
		$meta=array();
		if($item->classes)
			$meta['class']=implode(' ', $item->classes);
			
		if($item->target)
			$meta['target']=$item->target;
			
		if($item->attr_title)
			$meta['title']=$item->attr_title;
			
		if( !empty($meta) )
			$args['meta'] = $meta;
		$wp_admin_bar->add_menu($args);
		
	}

		
}

if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'add_abam_option_page');
}


function add_abam_option_page() {
    // Add a new submenu under Options:
    add_options_page('Admin bar as Menu Options', 'Admin bar as Menu', 'administrator', 'abamoptions', 'abam_option_page');

}

function abam_option_page() {
global $kCabam_display_menu;
?>
<div class="wrap">
<h2>Admin bar as Menu</h2>

<form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <table class="form-table">
        <tr valign="top">
            <td style="width:200px;"><b>Select menu</b></td>
            <td>
            	<select name="<?php echo $kCabam_display_menu;  ?>">
                	<?php
						$old_value=get_option($kCabam_display_menu);
						foreach(wp_get_nav_menus() as $menu){
							?>
                            <option value="<?php echo $menu->name; ?>" <?php if($old_value==$menu->name) echo 'selected="selected"';  ?>><?php echo $menu->name; ?></option>
							<?php
						}
					?>
                </select>
            </td>
        </tr>
    </table>
    <input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="<?php echo $kCabam_display_menu;  ?>" />

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
</div>
<?php } 


?>