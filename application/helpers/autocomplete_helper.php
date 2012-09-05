<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
| Author: Amin S
--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/
function autocomplete_widget($table_name, $id){
	$id = str_replace('"' , '', $id);
	$html  = '<input type="text" name="'.$id.'" class="'.$table_name.' auto_complete" id="'.$id.'">';
	$html .= '<input type="hidden" name="'.$id.'_val" id = "'.$id.'_val">';
	$html .= '<script language="javascript" type="text/javascript">
				$(function() {
					$("#'.$id.'").autocomplete({
						minLength: 3,
						source:  "'.base_url().'index.php/report/adopsreport/autocomplete?table_name='.$table_name.'"
					})
				});
			</script>';
	return $html;
}

?>