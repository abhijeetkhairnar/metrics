<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
| Author: Amin S
--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/
function autocomplete_widget($id){
	$id = str_replace('"' , '', $id);
	$html = '<input type="text" class="auto_complete" id="'.$id.'">';
	return $html;
}

?>