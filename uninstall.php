<?php
if ( function_exists('register_uninstall_hook') ){
	register_uninstall_hook(__FILE__, 'svd_uninstall');
}

function svd_uninstall() {
	delete_option('svd_name');
}
?>