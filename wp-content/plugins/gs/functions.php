<?php
function gs_settings_form() {
	
	$gs_url = get_option('siteurl')."/wp-content/plugins/gs/gelSheet/index.php?bookId=1&TB_iframe=true";
	 
	
	$out = <<<EOF
		<a  title="Add Spreadshhet" class="thickbox" id="add_spreadsheet" href="$gs_url"><img alt="Add an Image" src="../wp-content/plugins/gs/img/calendar_16px.gif"/></a>
		<script>
		

		</script>
		
EOF;
	return $out ;
}


function gs_init(){
 add_shortcode('gs_book','gs_shortcode_handler');
}

/*
 * Admin editor buttons
 */
function gs_buttons() {
	echo gs_settings_form() ;

}

function gs_activate(){
	return gs_install() ; 
}




/**
 * pase:  [gs_book id="1"] 
 */
function gs_shortcode_handler($atts, $content=null, $code="") {
	global $wpdb;	

	extract(shortcode_atts(array(
		'id' => null,
		'style' => ""
		
	), $atts));
	
	$sql = "SELECT html from ".$wpdb->prefix."gs_books WHERE id=$id" ;
	$html = stripslashes($wpdb->get_var( $sql ) );	
	$out = <<<EOF
		<div id="gs_book_$id" style="$style">		
		$html	
		</div>
EOF;
	return $out ; 	

}