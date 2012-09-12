<? 
echo "<pre>";
$oConn_proc = oci_connect('adq2anywhere','adq2anywhere','GADEVDB');
$sqlFetchResultData = "BEGIN PKG_ADQ_DISPLAY.PRC_SPLASH_REPSEARCH(
							 :pv_adv_name,
							 :pv_order_name,
							 :pv_ad_name,
							 :pv_crtv_name,
							 :pv_ad_size,
							 :pn_crtv_format,
							 :pn_adv_id,
							 :pn_order_id,
							 :pn_ad_id,
							 :pn_crtv_id,
							 :pc_rc,:pv_sql); END;";

$stmt_query = oci_parse($oConn_proc, $sqlFetchResultData);
$adv_name = null; $order_name = null;
$ad_name = null; $crtv_name = null;
$ad_size = null; $crtv_format = null;
$adv_id = null; $order_id = null;
$ad_id = null; $crtv_id = null;
$pv_sql = null;
oci_bind_by_name($stmt_query,":pv_adv_name",$adv_name);
oci_bind_by_name($stmt_query,":pv_order_name",$order_name);
oci_bind_by_name($stmt_query,":pv_ad_name",$ad_name);
oci_bind_by_name($stmt_query,":pv_crtv_name",$crtv_name);
oci_bind_by_name($stmt_query,":pv_ad_size",$ad_size);
oci_bind_by_name($stmt_query,":pn_crtv_format",$crtv_format);
oci_bind_by_name($stmt_query,":pn_adv_id",$adv_id);
oci_bind_by_name($stmt_query,":pn_order_id",$order_id);
oci_bind_by_name($stmt_query,":pn_ad_id",$ad_id);
oci_bind_by_name($stmt_query,":pn_crtv_id",$crtv_id);
oci_bind_by_name($stmt_query,":pn_crtv_id",$crtv_id);
oci_bind_by_name($stmt_query,":pv_sql",$pv_sql,4000);
$returnResultLabel = oci_new_cursor($oConn_proc);
oci_bind_by_name($stmt_query,":pc_rc",$returnResultLabel,-1,OCI_B_CURSOR);
oci_execute($stmt_query,OCI_DEFAULT);
oci_execute($returnResultLabel,OCI_DEFAULT);
while($rowFetchData = oci_fetch_array($returnResultLabel))
{
	print_r($rowFetchData);
}
exit;
?>