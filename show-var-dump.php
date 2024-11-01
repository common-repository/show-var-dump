<?php
/*
Plugin Name: Show var_dump
Plugin URI: http://wordpress.org/plugins/show-var-dump/
Description: "var_dump" method can't show results in functions.php. If You can use original method "var_dump_ex",the results shows in your dashboard after "Show var_dump" plugin install.
Version: 1.0
Author: woodroots
Author URI: http://wood-roots.com
License: GPLv2 or later
Text Domain: showvardump
Domain Path: /languages/
*/

add_action( 'plugin_loaded', function() {
	load_plugin_textdomain( 'showvardump', false, basename( dirname( __FILE__ ) ).'/languages');
});

//option name
define('SVD_NAME','svd_name');


//var_dump results to variables
//ex: var_dumpの出力を変数に渡す http://sakuragaoka.hatenadiary.jp/entry/20100401/p1
function vdump($obj){
	ob_start();
	var_dump($obj);
	$dump = ob_get_contents();
	ob_end_clean();
	//date for title
	return '----'.date(DATE_RFC2822)."-----------------\n".$dump;
}

//add option
function var_dump_ex($dump){
	$cr = get_option(SVD_NAME);
	if($cr){
		update_option(SVD_NAME,vdump($dump) . $cr);
	} else {
		add_option(SVD_NAME,vdump($dump));
	}
}

//show dashboard
function show_dump(){
$dump = get_option(SVD_NAME) ? get_option(SVD_NAME) : __('no dump yet.','showvardump');
$btn = __('Reset Dump Log','showvardump');
$nonce= wp_create_nonce('svd-nonce');

//insert html
$dump_text = <<< EOF
	<div class="postbox">
		<div class="inside">
			<textarea style="width: 100%; height: 100px;" readonly>{$dump}</textarea>
			<form action="" method="post">
				<input type="hidden" name="_wpnonce" value="{$nonce}" />
				<input type="hidden" name="svd_reset" value="1" />
				<input type="submit" value="{$btn}" />
			</form>
		</div>
	</div>
EOF;
echo $dump_text;

}
//add footer
add_action('in_admin_footer','show_dump');

//reset log
function reset_log(){
	$nonce = $_POST['_wpnonce'];

	if(is_user_logged_in() && $_POST['svd_reset'] == 1 && wp_verify_nonce($nonce, 'svd-nonce')){
		delete_option(SVD_NAME);
		add_action('in_admin_footer','reset_log_alert');
	}
}
function reset_log_alert(){
	echo '<div class="updated"><p>'.__('Reset Your Dump log.').'</p></div>';
}
add_action('admin_init','reset_log');

//css fix
function show_dump_css(){
$css = <<< EOF
	<style type="text/css">
		#wpbody-content {
			padding-bottom: 300px !important;
		}
	</style>
EOF;
echo $css;
}
add_action('admin_head','show_dump_css');


?>
