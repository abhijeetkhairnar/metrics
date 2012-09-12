<?

echo "<pre>";

$oConn_proc = oci_connect('adq2anywhere','adq2anywhere','GADEVDB');
if( ! $oConn_proc )
{
	print_r(oci_error());
}
echo $sqlFetchData = "BEGIN PKG_ADQ_DISPLAY.PRC_DISP_SPLASHMETADATA (:pv_label_name, :pv_filter, :pn_start_row,:pn_end_row, :pc_rc); END;";
$stmt_query = oci_parse($oConn_proc, $sqlFetchData);
if( ! $stmt_query )
{
	print_r(oci_error($stmt_query));
}
$input = 'creative_type';
$filter  = 'splash';
$start_row  = null;
$end_row  = null;


$oci_flag = oci_bind_by_name($stmt_query,":pv_label_name",$input);
if( ! $oci_flag)
{
	print_r(oci_error($stmt_query));
}


$oci_flag = oci_bind_by_name($stmt_query,":pv_filter",$filter);
if( ! $oci_flag)
{
	print_r(oci_error($stmt_query));
}

$oci_flag = oci_bind_by_name($stmt_query,":pn_start_row",$start_row);
if( ! $oci_flag)
{
	print_r(oci_error($stmt_query));
}


$oci_flag = oci_bind_by_name($stmt_query,":pn_end_row",$end_row);
if( ! $oci_flag)
{
	print_r(oci_error($stmt_query));
}
$returnResultLabel = oci_new_cursor($oConn_proc);
if( ! $returnResultLabel)
{
	print_r(oci_error($stmt_query));
}

$oci_flag = oci_bind_by_name($stmt_query,":pc_rc",$returnResultLabel,-1,OCI_B_CURSOR);
if( ! $oci_flag)
{
	print_r(oci_error($stmt_query));
}

$oci_flag = oci_execute($stmt_query,OCI_DEFAULT);
if( ! $oci_flag)
{
	print_r(oci_error($stmt_query));
}
$oci_flag = oci_execute($returnResultLabel,OCI_DEFAULT);
if( ! $oci_flag)
{
	print_r(oci_error($returnResultLabel));
}




while($rowFetchLabels = oci_fetch_array($returnResultLabel))
{
	print_r($rowFetchLabels);
}

exit;


?>