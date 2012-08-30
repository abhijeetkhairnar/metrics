<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
| Author: Amin S
--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/
function autocomplete_widget($table_name, $id){
	$id = str_replace('"' , '', $id);
	$html = '<input type="text" name="'.$id.'" class="advertiser auto_complete" id="'.$id.'">';
	$html .= '<input type="hidden" name="'.$id.'_val" id = "'.$id.'_val">';
	return $html;
}

?>