<?php
class Reportutilsclass
{
    protected static $aReportSrchTblLabels;
    public static $aReportStartEndDt = array();
    public static $aGeoFields = array("Country"=>"Country", "State/Region"=>"State/Region", "DMA"=>"DMA", "Zip"=>"Zip", "City"=>"City");
	private $dbType;
	private $filterSql;
	public static function getReportParentChildResultSet($iFieldType = null,$ireportType = null)
	{
		
		$aResult = array();
		$aToReturn = array();
		$oReport = new ReportDAO();
		$aResult = $oReport->getReportParentChildResultSet($iFieldType,$ireportType);
		unset($oReport);
		if( count($aResult) )
		{
			
			foreach($aResult as $aResultDtls)
			{
				if( $aResultDtls["PARENT_FIELD_TYPE"] == 1) //1 means filter field 
				{
						if($aResultDtls["PARENT_DISPLAY_NAME"]=='Atako Filters')
						{
							$aToReturn["filterresultset"][ $aResultDtls["CHILD_DISPLAY_NAME"] ] [0] = $aResultDtls["CHILD_DISPLAY_NAME"];
							
						}
						else
						{
					
							$aToReturn["filterresultset"][ $aResultDtls["PARENT_DISPLAY_NAME"] ] [0] = $aResultDtls["PARENT_DISPLAY_NAME"];
							
							if($aResultDtls["CHILD_TABLE_FIELD_NAME"])
							{
		
							  		$aToReturn["filterresultset"][ $aResultDtls["PARENT_DISPLAY_NAME"] ][ $aResultDtls["CHILD_TABLE_FIELD_NAME"] ] = $aResultDtls["CHILD_DISPLAY_NAME"];
							  
							}
							else if(!empty($aResultDtls["CHILD_DISPLAY_NAME"]))
							{
							  $aToReturn["filterresultset"][ $aResultDtls["PARENT_DISPLAY_NAME"] ][ $aResultDtls["CHILD_DISPLAY_NAME"] ] = $aResultDtls["CHILD_DISPLAY_NAME"];
							}
						}
					
				}
				else //0 means display field
				{
					
					
					if( $aResultDtls["CHILD_TABLE_FIELD_NAME"] == "" && $aResultDtls["PARENT_TABLE_FIELD_NAME"] != "")
				 	{
				 		// this condition is used for metric only 
				 		if($iFieldType == '2')
				 		{
							$aToReturn["displayresultset"][$aResultDtls["PARENT_TABLE_FIELD_NAME"]]=$aResultDtls["PARENT_DISPLAY_NAME"];
				 		}
				 		else
				 		{
				 		// this patch is used becoz js is giving error for regexp as it includes double quotes
				 			if($aResultDtls["PARENT_TABLE_FIELD_NAME"]=="TO_CHAR(TO_DATE(DATE_ID,''YYYYMMDD''), ''MM-YYYY'') AS DATE_ID")
				 			{
				 				$aResultDtls["PARENT_TABLE_FIELD_NAME"] = 'Daily';
				 			}
				 				
				 			if($aResultDtls["PARENT_TABLE_FIELD_NAME"]=="TO_CHAR(TO_DATE(DATE_ID,''YYYYMMDD''), ''MM-DD-YYYY'') AS DATE_ID")
				 			{
				 				$aResultDtls["PARENT_TABLE_FIELD_NAME"] = 'Monthly';
				 			}
				 		}
				 		
				 		$aToReturn["displayresultset"][ $aResultDtls["PARENT_TABLE_FIELD_NAME"] ][0] = $aResultDtls["PARENT_DISPLAY_NAME"];	
				 	}
				 	else
				 	{
				 		if($iFieldType != '2')
				 		{
				 			$aToReturn["displayresultset"][ $aResultDtls["PARENT_DISPLAY_NAME"] ][0] = $aResultDtls["PARENT_DISPLAY_NAME"];
				 		}
				 	}
				 	
				 	if($aResultDtls["CHILD_TABLE_FIELD_NAME"])
				 	{

				 		if(($ireportType== '2' && $iFieldType=='2') || ($ireportType== '3' && $iFieldType=='2') || ($ireportType== '4' && $iFieldType=='2'))
				 		{
				 			$aToReturn["displayresultset"][$aResultDtls["CHILD_TABLE_FIELD_NAME"]] = $aResultDtls["CHILD_DISPLAY_NAME"];
				 		}
				 		else
				 		{
				 	  		$aToReturn["displayresultset"][ $aResultDtls["PARENT_DISPLAY_NAME"] ][ $aResultDtls["CHILD_TABLE_FIELD_NAME"] ] = $aResultDtls["CHILD_DISPLAY_NAME"];
				 		}
				 	}
				 	else if(!empty($aResultDtls["CHILD_DISPLAY_NAME"]))
				 	{
				 	  $aToReturn["displayresultset"][ $aResultDtls["PARENT_DISPLAY_NAME"] ][ $aResultDtls["PARENT_DISPLAY_NAME"].".".str_replace(" ","_",$aResultDtls["CHILD_DISPLAY_NAME"]) ] = $aResultDtls["CHILD_DISPLAY_NAME"];
				 	}
				 	
				}
			}
		}
		
		
		
		return $aToReturn;
	}
	/**/
	public static function saveDD($request, $postfix, $iIndex)
	{
	  //Start: Save drag and drop list when submit action is called after on handleError validation.
        $gid=0;
        $aSelectedList = $request->getParameter('hdnrightcontainer');
		$aOrgList = array_keys(sfContext::getInstance()->getUser()->getAttribute("aOrgList$postfix", null, "adSession"));
		$orgDDList = sfContext::getInstance()->getUser()->getAttribute("orgDDList$postfix", null, "adSession");
		$aFinalResult = self::customizeDDUpdate($gid, $aSelectedList[$iIndex], $aOrgList, $orgDDList);
	   	return array(urldecode(	implode(",",$aFinalResult['aRecordsToBeAdded'])), $aFinalResult['sNewList']);
	}
	
	/*new function */
  public static function prepareAssignedList($sCsvString)
  {
		$aNewList = array();
		if(!empty($sCsvString))
		{
			$aTemp = explode(",", $sCsvString);
			foreach($aTemp as $iRow)
			{
				array_push($aNewList, array('ID' => $iRow));
			}
		}
		return $aNewList;
   }
	
/*trupti*/
 public static function generateConfigForJSON($presetFlag =0)
 {
   return array("mode"=>"children", "divId"=>"divDim", "leftList"=>"leftListDIM", "rightList"=>"rightListDIM", "dragClass"=>"drag_dim", "dropClass"=>"drop_dim");
 }
 
 
 public static function generateConfigForJSONMetric($presetFlag =0)
 {
   return array("mode"=>"children", "divId"=>"divMetric", "leftList"=>"leftListMetric", "rightList"=>"rightListMetric", "dragClass"=>"drag_isp", "dropClass"=>"drop_isp");
 }

 	public static function getFilterElementHtml($sType = null,$oPost=null,$requestId='')
	{
		$reportType = sfContext::getInstance()->getUser()->getAttribute("ireportType");

		if(!$reportType){$reportType=1;}
		$aResultSet = array();
		$oReport = new ReportDAO();
		list($sChar, $sType) = explode("_", $sType);
		$aResultSet = $oReport->getFilterElementDtls($sType,0, $reportType);
		
		if(count($aResultSet)<1)
		{
			$aResultSet = $oReport->getFilterElementDtls($sType,1,$reportType);
		}
		
		unset($oReport);
		
		//parent not having child need to handle them explicitly.  
		if((count($aResultSet) == 1 && $aResultSet[0]["CHILD_FILTER_FIELD_TYPE"] == ""))
		{
			$aResultSet[0]["CHILD_FILTER_FIELD_TYPE"] = $aResultSet[0]["PARENT_FILTER_FIELD_TYPE"];
			if( $aResultSet[0]["PARENT_DISPLAY_NAME"] == "Salesperson" )
			{
				//$aResultSet[0]["PARENT_DISPLAY_NAME"] = "Salespeople";
				$aResultSet[0]["CHILD_DISPLAY_NAME"] = $aResultSet[0]["PARENT_DISPLAY_NAME"];
			}
			else
			{
				$aResultSet[0]["CHILD_DISPLAY_NAME"] = $aResultSet[0]["PARENT_DISPLAY_NAME"];
			}	 
			
			$aResultSet[0]["CHILD_TABLE_FIELD_NAME"] = $aResultSet[0]["PARENT_TABLE_FIELD_NAME"];
			$aResultSet[0]["CHILD_LOOKUP_DATA"] = $aResultSet[0]["PARENT_LOOKUP_DATA"];
		}
		
		
		return self::hydrateFilterElementResultSet($aResultSet, $sType,$oPost,$requestId);
	}
	
	public static function hydrateFilterElementResultSet($aInput = array(), $sType,$oPost=null,$requestId='')
	{

			if($requestId == '')
			{
			  $requestId = sfContext::getInstance()->getRequest()->getParameter('requestId');
			}
		if( count($aInput) )
		{
			$sFilterHtml = null;
			$sFilterHtml .= "<div class=\"filter_hdr\">$sType</div>";
			$iIsGeoSelected = 0;
			$aGeoInfo = array();
		
		$iGeoDDIndex = 3;
		$iAffDDIndex = 3;	
		//for getting geo and affiliate DD position and index from post array to access values, starts	
		if(count($oPost))
		{
			
			$aSelectedFltrs = self::getDDPostClumnList($oPost['hdnrightcontainer'][2],10,'');

			if(in_array("Affiliate", $aSelectedFltrs) && in_array("Geo", $aSelectedFltrs))
			{
				//$aPostVarKeys = array_keys($oPost);
				$iAffIndex = array_search("Affiliate", $aSelectedFltrs);
				$iGeoIndex = array_search("Geo", $aSelectedFltrs);
				if($iAffIndex > $iGeoIndex)	{
					$iGeoDDIndex = 3;
					$iAffDDIndex = 6;
				}
				else{
					$iAffDDIndex = 3;
					$iGeoDDIndex = 4;
				}
			}
			
			$sAffiliateClmNm = trim($oPost['affiliateclm']);
			if($sAffiliateClmNm)
			{
				$aAffLeftSideItmList  = array(); 
			    $aAffRightSideItmList = array();
				$sAffiliate_left  = $oPost['hdnleftcontainer'][$iAffDDIndex];
				$sAffiliate_right = $oPost['hdnrightcontainer'][$iAffDDIndex];
				
				$aAffiliate_left =  ($sAffiliate_left ) ? self::getDDPostClumnList($sAffiliate_left,10,'') : array(); 
				$sAffiliateIDs_left = implode(',',$aAffiliate_left);
				$aAffLeftSideItmList = Utils::getAffliateDisplayByIds($sAffiliateIDs_left);
				
				$aAffiliate_right = ($sAffiliate_right ) ? self::getDDPostClumnList($sAffiliate_right,10,'') : array(); 
				$sAffiliateIDs_right = implode(',',$aAffiliate_right);
				$aAffRightSideItmList = Utils::getAffliateDisplayByIds($sAffiliateIDs_right);
			}
			
		}
		//getting geo and affiliate DD position and index from post array to access values, ends
				
				// this code block is used to check index 
				if($sType=='Geo - Country' || $sType=='Geo - State/Region' || $sType=='Affiliate')
				{

					$key_country = array_search('Geo - Country', $aSelectedFltrs);
					if(isset($key_country))
					{
						$arrsortindex[] = $key_country;
					}
					
					$key_state = array_search('Geo - State/Region', $aSelectedFltrs);
					if(isset($key_state))
					{
						
						$arrsortindex[] = $key_state;
					}
					$key_affliate = array_search('Affiliate', $aSelectedFltrs);
					
					if(isset($key_affliate))
					{
						$arrsortindex[] = $key_affliate;
					}
						
					sort($arrsortindex);
					$keyindex =3;
					$final_index_arr = array();
					
					foreach($aSelectedFltrs as $filterKey=>$filterVal)
					{
						foreach($arrsortindex as $keys=>$vals)
						{
							if($filterKey === $vals )
							{
								$final_index_arr[$aSelectedFltrs[$vals]]=$keyindex;
								$keyindex++;
							}
						}
					}
				}

				// end of block this code block is used to check index
				
			
						
			foreach($aInput as $aInputDtls)
			{
			
				$sInputType = $aInputDtls["CHILD_FILTER_FIELD_TYPE"];
				$sLabel     = $aInputDtls["CHILD_DISPLAY_NAME"];
				$sTableFieldName = $aInputDtls["CHILD_TABLE_FIELD_NAME"];
				
				if($sLabel=='Include Sub Category(s)' || $sLabel=='Exclude Sub Category(s)')
				{
					$sType = 'Ad Sub Category';
				}
				
				$sPostValue = null;
				if(isset($oPost['reportfilter'][$sType]))
				{
					if($oPost['reportfilter'][$sType][$sTableFieldName]!='')
					{
						$sPostValue = $oPost['reportfilter'][$sType][$sTableFieldName];
					}
				}
					
								
				//echo $sInputType;
				//exit;

				$report_type = sfContext::getInstance()->getUser()->getAttribute("ireportType");
				switch($sInputType)
				{
					case "TextBox":
					
						if(strtoupper($sLabel) == "LAST MODIFIED")
						{
						sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset', 'Tag', 'Form' ));
				
							$sFilterHtml .= "<label class=\"form_label\">$sLabel</label>";
							$sFilterHtml .=  "<label class='form_field'>";
							$sFilterHtml .= input_date_tag("reportfilter[$sType][$sTableFieldName]", $sPostValue, 'rich=true class=form_calendar withtime=true calendar_button_img=calendar.png format = Y-MM-dd');
							 $sFilterHtml .= "</label><label class=\"form_label\"></label><label class=\"form_label\"></label>";
							//$sFilterHtml .=  "<label class=\"form_label\"></label>";
						}
						else
						{
							if(strtoupper($sLabel) == "TARGETED KEY VALUES")	
							{
								$sFilterHtml .= "<label class=\"form_label\"><input type=\"radio\" checked=\"\" name=\"reportfilter[$sType][$sTableFieldName]\" value=\"0\" id=\"\" class=\"radio_button\"/>Exact Match</label>
												<label class=\"form_label\"><input type=\"radio\" name=\"reportfilter[$sType][$sTableFieldName]\" value=\"0\" id=\"\" class=\"radio_button\"/>Contains Match</label><label class=\"form_label\"> </label><label class=\"form_label\"> </label>";
							}
							$sjsString = ($aInputDtls["CHILD_LOOKUP_DATA"])	? "onBlur=\"javascript:fnCheckInputExist(this, '".$aInputDtls["CHILD_LOOKUP_DATA"]."');\"" : "";
							$sFilterHtml .= "<label class=\"form_label\">$sLabel</label><input type=\"text\" name=\"reportfilter[$sType][$sTableFieldName]\" id=\"txtid_{$sTableFieldName}\" value=\"$sPostValue\" class=\"text_field_long\" $sjsString />\n";
							

							if($sType=='Ad ID' && $sTableFieldName=='ANALYTICS_DATA.Ad_ID')
							{
								$report_type = sfContext::getInstance()->getUser()->getAttribute("ireportType");
								$oReportDao =  new ReportDAO();
								$aResult = $oReportDao->findByKeyName('Report Type');
								$message_display='';
								foreach($aResult AS $keyReport => $valReport )
								{
									if(($valReport['DISPLAY']=='Unique To-Date Report') && ($valReport['STR_VALUE_ID']==$report_type))
									{
										$message_display = "Uniques To-date data available from Sep 15, 2011 onward";
									}
								}
								
								if($message_display!='')
								{
									$sFilterHtml .= "<span id=\"txtid_{$sTableFieldName}_err\">
									<div class=\"field_error\" style=\"width:500px\"> $message_display</div>
											<label class=\"form_label\">&nbsp;</label>
											<label class=\"form_label\">&nbsp;</label></span>";


								}
							}
							else
							{
								$sFilterHtml .= "<span id=\"txtid_{$sTableFieldName}_err\"></span>";
							}

						}	//$sFilterHtml .= "<a href=\"\">Validate</a>";
					break;
					
					case "AutoComplete":
						list($sDisplayColumn, $sValueColumn) =  explode("||", $aInputDtls["CHILD_LOOKUP_DATA"]);
						list($sTblToUseForSql, $sTemp) = explode(".", $sValueColumn);
						
						list($sTblNm, $sColmnNm) = explode(".", $sTableFieldName);
						$sTblNm = str_replace("UPPER(", "", $sTblNm);
						$sColmnNm = str_replace(")", "", $sColmnNm);
						$sName = $sTblNm."_".$sColmnNm;
						
						$oAdvtNames = new Autocomplete( array( "multiplebyseparator" => ",", "name" => $sName, "id" => "$sName","isall" => true, "table" => $sTblToUseForSql, "listvalueclmn" => $sValueColumn, "listdisplayclmn" => $sDisplayColumn, "likeclmns" => $sDisplayColumn,  "errmsg" => "No result found.","textboxstyle" => "class=\"text_field_long\"","textboxdefaultvalue" =>$sPostValue,"hiddenboxdefaultvalue" =>"",      
						"customqueryparams" => array(
						      "custom1" => "AND KEY_NAME=\'Creative Vendor\'", 
						      "parameters" => array("inclausefield" => "STR_VALUE_ID",
						      						"inclauseon" => "Creative Vendor")),) );
						
						$sFilterHtml .= "<label class=\"form_label\">$sLabel</label>".$oAdvtNames->getAutoCompleteHtml();
						//$sFilterHtml = str_replace("name=\"hid_$sName\"", "name=\"reportfilter[$sType][$sValueColumn]\"", $sFilterHtml);
						$sFilterHtml = str_replace("which_combo/$sName", "which_combo/reportfilter[$sType][$sValueColumn]", $sFilterHtml);
						$sFilterHtml = str_replace("name=\"$sName\"", "name=\"reportfilter[$sType][$sTableFieldName]\"", $sFilterHtml);
					break;
					
					case "DropDown":
						
						if( strtoupper($sLabel) == "ACTIVE")
						{
							if($sPostValue=='')
							{
								$allSelected = 'selected=selected';
							}
							elseif($sPostValue=='9')
							{
								$archivedSelected = 'selected=selected';
							}
							elseif($sPostValue=='1' || $sPostValue=='' )
							{
								$activeSelected = 'selected=selected';
							}
							elseif($sPostValue=='0')
							{
								$inactiveSelected = 'selected=selected';	
							}
							
							$sFilterHtml .= "<label class=\"form_label\">$sLabel</label><SELECT name=\"reportfilter[$sType][$sTableFieldName]\" id=\"\" class=\"form_dropdown\" >
											<option value=\"\" $allSelected >All</option>
											<option value=\"9\" $archivedSelected >Archived</option>
											<option value=\"1\" $activeSelected >Active</option>
											<option value=\"0\" $inactiveSelected>Inactive</option>
											</SELECT><label class=\"form_label\"></label><label class=\"form_label\"></label>";
						}
						else
						{

							switch($sLabel)
							{
								case "Tag Types":
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionList('Tag Type','form_dropdown', $sPostValue, false);
									$sFilterHtml .=  "<label class=\"form_label\"></label><label class=\"form_label\"></label>";
								break;

								case "ATFs":
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionList('ATF Value','form_dropdown', $sPostValue, false);
									$sFilterHtml .=  "<label class=\"form_label\"></label><label class=\"form_label\"></label>";
								break;
								/*
								case "Impression Flags":
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionList('Bit Flags','form_dropdown', $sPostValue, false);
									$sFilterHtml = str_replace("name = 'BitFlags'", "name=\"reportfilter[$sType][$sTableFieldName]\"", $sFilterHtml);
								break;
								*/
								

								case "Creative Type":
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionList('Creative Type','form_dropdown', $sPostValue, true);
									$sFilterHtml = str_replace("name = 'CreativeType'", "name=\"reportfilter[$sType][$sTableFieldName]\"", $sFilterHtml);
									$sFilterHtml .=  "<label class=\"form_label\"></label><label class=\"form_label\"></label>";
								break;
								
								case "Source Id":
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionList('Source Id','form_dropdown', $sPostValue, true);
									$sFilterHtml = str_replace("name = 'SourceId'", "name=\"reportfilter[$sType][$sTableFieldName]\"", $sFilterHtml);
								break;
								
								case "Creative Vendor":
									//$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".CreativeUtils::getOptionListForVendor('Creative Vendor','form_dropdown',$sPostValue);
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionList('Creative Vendor','form_dropdown',$sPostValue,true);
									$sFilterHtml = str_replace("name = 'CreativeVendor'", "name=\"reportfilter[$sType][$sTableFieldName]\"", $sFilterHtml);
									$sFilterHtml .=  "<label class=\"form_label\"></label><label class=\"form_label\"></label>";
								break;
								
								case "Creative Status Active":
								
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionListBoolean('CreativeStatusActive','form_dropdown', $sPostValue, $sType,$sTableFieldName,true);
									//exit;
									$sFilterHtml .=  "<label class=\"form_label\"></label><label class=\"form_label\"></label>";
								break;

								case "Ad Group Name":
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionList('Ad Group Name','form_dropdown', $sPostValue, true);
									$sFilterHtml = str_replace("name = 'AdGroupName'", "name=\"reportfilter[$sType][$sTableFieldName]\"", $sFilterHtml);
								break;
								
								/*case "Survey Present":
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionListBoolean('SurveyPresent','form_dropdown', $sPostValue, $sType,$sTableFieldName,true);
									$sFilterHtml .=  "<label class=\"form_label\"></label><label class=\"form_label\"></label>";
								break;
								*/
									case "Creative Format Type":
									$sFilterHtml .=  "<label class=\"form_label\">$sLabel</label>".ReportUtils::getOptionList('Creative Format Type','form_dropdown', $sPostValue, false);
									$sFilterHtml = str_replace("name = 'CreativeFormatType'", "name=\"reportfilter[$sType][$sTableFieldName]\"", $sFilterHtml);
									$sFilterHtml .=  "<label class=\"form_label\"></label><label class=\"form_label\"></label>";
								break;
							}
						}
					break;

					case "DargDrop":
						
						
						if($sType == "Geo")
						{
							$iIsGeoSelected = 1;
							if( strtoupper($sLabel) == "COUNTRY") { $sFilterHtml .= "<label class=\"form_label\">$sLabel</label><div id=\"divCountry\"></div> <input type='hidden' name=\"hid_geo_columns[country]\" value=\"$sTableFieldName\" >"; }
							if( strtoupper($sLabel) == "STATE/REGION") { $sFilterHtml .= "<label class=\"form_label\">$sLabel</label><div id=\"divStateRegion\"></div><input type='hidden' name=\"hid_geo_columns[state]\" value=\"$sTableFieldName\" >"; }
					
							if( strtoupper($sLabel) == "CITY") {
							 $sFilterHtml .= '
							<label class="form_label">'.$sLabel.'</label>
							<input type="text" name="reportfilter['.$sType.']['.$sTableFieldName.']" id="reportfilter['.$sType.']['.$sTableFieldName.']"	class="text_field_med" value="'.$sPostValue.'" />
							<input type="hidden" name="'.$sType.'" id="'.$sType.'"	class="text_field" value='.$sType.'"/> ';
							$sAjax = "removeSpace(); new Ajax.Request('".Utils::getApplicationPath('report')."/adopsreport/ajax',{synchronous:true, evalScripts:false, parameters: { name:document.getElementById('reportfilter[$sType][$sTableFieldName]').value ,type:'$sType',searchField:'reportfilter[$sType][$sTableFieldName]',searchType:'$sType'},onComplete:function(request, json){populateTable(request,json, 'divList', 'overlayTable','reportfilter[$sType][$sTableFieldName]');}} ); return false;";

		                     $sFilterHtml .= '<label class="form_label"><input style="margin:5px 0 5px 7px;" type="button" name="search_'.$sType.'" id="search_'.$sType.'" value="Search" class="formbutton_small"  onclick="'.$sAjax.'" /></label>';
							}


							if( strtoupper($sLabel) == "DMA") { $sFilterHtml .= "<label class=\"form_label\">$sLabel</label><div id=\"divDMA\"></div><input type='hidden' name=\"hid_geo_columns[dma]\" value=\"$sTableFieldName\" >"; }
							if( strtoupper($sLabel) == "AREA CODE") { $sFilterHtml .= "<label class=\"form_label\">$sLabel</label><div id=\"divAC\"></div><input type='hidden' name=\"hid_geo_columns[ac]\" value=\"$sTableFieldName\" >"; }
							if( strtoupper($sLabel) == "ZIP") { $sFilterHtml .= "<label class=\"form_label\">$sLabel</label><input class=\"text_field_med\" type=\"text\" id=\"txtZipCodes\" name=\"txtZipCodes\" value=\"$sPostValue\"> <input type='hidden' name=\"hid_geo_columns[zipcode]\" value=\"$sTableFieldName\" >"; }
						}
						else if($sType == "Geo - Country")
						{
							//echo "<pre>";
							//print_r($oPost['hdnrightcontainer']);
							//echo "</pre>";



							$sCountries_right = $oPost['hdnrightcontainer'][$final_index_arr['Geo - Country']];
							if($sCountries_right ) 
							{ 
								$aCountries_right = self::getDDPostClumnList($sCountries_right,10,''); 
							}
								
							$populateArr['CN_right'] = implode(',',$aCountries_right);
							
							$sFilterHtml .= "<label class=\"form_label\">$sLabel</label>
							<div id=\"divCountry\"></div>
							<input type='hidden' name=\"hid_geo_columns[country]\" value=\"$sTableFieldName\" >";
							
							list($aCNLeftSideItmList, $aCNRightSideItmList) = Utils::getDDForDisplay('Country',$populateArr['CN_right'],'edit',true);
							

							$aListCountry = array();
							$aListCountry = Utils::generateDDData($aCNLeftSideItmList, $aCNRightSideItmList, 'Country');
							$aConfigCountry = array();
							$aConfigCountry = array("divId"=>"divCountry", "leftList"=>"leftListCN", "rightList"=>"rightListCN", "dragClass"=>"drag_cn", "dropClass"=>"drop_cn");
							$sListCountry = json_encode($aListCountry);
							$sConfigCountry = json_encode($aConfigCountry);
							$sCountryData = "var listCountry = ".$sListCountry."; var configCountry = ".$sConfigCountry."; ";

							Utils::writeDataToJsFile($sCountryData, sfConfig::get('app_userjs_include_path').'geoData_'.session_id().'.js');


							$sUserJsWritePath = sfConfig::get("app_userjs_read_path");
							$sDDJsUrl = sfConfig::get("app_js_include_path");
							$sFilterHtml .= "<script type=\"text/javascript\">
										populateWidgets('".$sUserJsWritePath."geoData_".session_id().".js', '".$sDDJsUrl."countryMasterData.js','Geo','getGeoTargeting','','');
											</script>";
							
							
						}
						else if($sType == "Geo - State/Region")
						{
							
							$sFilterHtml .= "<label class=\"form_label\">$sLabel</label><div id=\"divStateRegion\"></div><input type='hidden' name=\"hid_geo_columns[state]\" value=\"$sTableFieldName\" >";
							
							// write on state -region code
							$aStateRegion = DragDropUtils::getAllStateRegion();
							$aLeftListSR = DragDropUtils::getStateTree($aStateRegion);
							
							
							
							$sstates_right = $oPost['hdnrightcontainer'][$final_index_arr['Geo - State/Region']];
							if($sstates_right )
							{
								$aStates_right = self::getDDPostClumnList($sstates_right,10,'');
							}
								
							$populateArr['SR_right'] = implode(',',$aStates_right);
							
							$aRightListSR  = $populateArr['SR_right'];
							$aConfig = array("mode"=>"children", "divId"=>"divStateRegion", "leftList"=>"leftListSR", "rightList"=>"rightListSR","dragClass"=>"drag_sr", "dropClass"=>"drop_sr");
					  		list($masterDataList,$listStateMaster,$masterConfigList, $masterParentList) = Utils::getTreeData($aLeftListSR, $aRightListSR, $aConfig, 'SR', 'edit', 'all');
					  		

					  		
					  		$sTreeData = "var masterConfigList_SR = ".$masterConfigList."; var listStateMaster_SR = ".$listStateMaster.";";
					  		
					  			
					  		Utils::writeDataToJsFile($sTreeData, sfConfig::get('app_userjs_include_path').'stateregion_'.session_id().'_'.$requestId.'.js');

					  		
					  		
							$sUserJsWritePath = sfConfig::get("app_userjs_read_path");
							$sDDJsUrl = sfConfig::get("app_js_include_path");
							$sFilterHtml .= "<script type=\"text/javascript\">
												populateWidgets('".$sUserJsWritePath."stateregion_".session_id().'_'.$requestId.".js', '".$sDDJsUrl."stateregionMasterData.js','State','getStateTargeting','','');
											</script>";
							
							

						}
					break;
					
					case "AffDragDrop":
						
						
						//$sLbl =  sfContext::getInstance()->getI18N()->__('Sites',null,'labels');
						$oAffDragDrop1 = new DragDrop('leftListAff','rightListAff', $aAffLeftSideItmList, $aAffRightSideItmList, false,'','');
		  				$oAffDragDrop1->GetHtml();
						$sPath = Utils::getApplicationPath('preset')."/addedit/getaffiliate";
						$sFilterHtml .= "<label class=\"form_label\">Affiliates</label><div>
							<input type=\"text\" name=\"txtafflist\" id=\"idafflist\" class=\"text_field_med\" />
							<input type=\"hidden\" name=\"affiliateclm\" value=\"$sTableFieldName\" /> 
							<div style=\"float:left;padding-left:5px;\">	
								<input class=\"formbutton_small\" style=\"margin: 5px 0pt 5px 7px;\" type=\"button\" name=\"btnsearchaff\" value=\"Search\" id=\"affSearch\" onclick=\"javascript:fnGetAffList('idafflist', 'leftListAff', '$sPath');\" />
							</div>
						</div>
						<div style=\"clear:both;\"></div>
						<div style=\"padding-left:215px;padding-top:-3px;font-size:11px;padding-bottom:5px;\">(Please enter comma separated list of Affiliate names or ids to search.)</div>
						<div style=\"padding-left:210px;\">".$oAffDragDrop1->GetHtml()."</div>";
					break;	
					
					case "Search":

					if($sLabel=='Exclude Category(s)')
               		 {
            			if(strtoupper($oPost['reportfilter']['Ad Category']['ADM_ADS.EXCLUDE_CATEGORY'])=='INTERNAL')
						{
							$chkExclude='checked=checked';
							$sPostValue='';
						}
						else
						{
							$chkExclude='';
							if($oPost['chkExclude']==1)
							{
								$chkExclude = 'checked';
							}
						}
					 }

						if($sLabel=='Include Sub Category(s)')
						{
							if($oPost['reportfilter']['Ad Sub Category']['ADM_ADS.INCLUDE_SUB_CATEGORY']!='')
							{
								$sPostValue = $oPost['reportfilter']['Ad Sub Category']['ADM_ADS.INCLUDE_SUB_CATEGORY'];
							}
							else
							{
								$sPostValue = $oPost['reportfilter']['Ad Category']['ADM_ADS.INCLUDE_SUB_CATEGORY'];
							}
						}
						elseif($sLabel=='Exclude Sub Category(s)')
						{
							if($oPost['reportfilter']['Ad Sub Category']['ADM_ADS.EXCLUDE_SUB_CATEGORY']!='')
 							{
								$sPostValue = $oPost['reportfilter']['Ad Sub Category']['ADM_ADS.EXCLUDE_SUB_CATEGORY'];
							}
							else
							{
								$sPostValue = $oPost['reportfilter']['Ad Category']['ADM_ADS.EXCLUDE_SUB_CATEGORY'];
							}
	
						} 

					$sLabel_str	 = '<label class="form_label" >'.$sLabel.'</label>';
					$readonly = '';
					if($sLabel == 'Product Lists' || $sLabel == 'Verticals' || $sLabel == 'Channels')
					{
						$allchecked = '';
						$anychecked = '';

						if($oPost['reportfilter']['Affiliate List']['OPERATOR_VAL'][$sTableFieldName]=='1')
						{
							$anychecked = 'checked = checked';
						}
						else
						{
							$allchecked = 'checked = checked';
						}
						$sLabel_str = '<label class="form_label" style="width:190">'."Include Impressions belonging to </label>
						<span style='width:230px;float:left;padding-top:7px;'>
						<input type='radio' $allchecked name='".$sTableFieldName."_checked' value='0' class='radio_button'> All <input type='radio' $anychecked name='".$sTableFieldName."_checked' value='1' class='radio_button'> Any selected ".$sLabel."</span>";
						$style_width_text ='style="width:240"';
						$readonly = 'readonly';
					}

					if($sLabel=='Exclusion List')
					{	
						
						$sLabel_str = '<label class="form_label" style="width:420">Exclude Impression belonging to any selected Exclusion Lists</label>';
						$style_width = ' style="width:190"';
						$style_width_text ='style="width:240"';

						$readonly = 'readonly';
					}

					

					 $sFilterHtml .= $sLabel_str.'				
							<input type="text" name="reportfilter['.$sType.']['.$sTableFieldName.']" id="reportfilter['.$sType.']['.$sTableFieldName.']"	class="text_field_med" '.$style_width_text.' value="'.$sPostValue.'" '.$readonly.' />
							<input type="hidden" name="'.$sType.'" id="'.$sType.'"	class="text_field" value='.$sType.'"/> ';
							$sAjax = "removeSpace(); new Ajax.Request('".Utils::getApplicationPath('report')."/adopsreport/ajax',{synchronous:true, evalScripts:false, parameters: { name:document.getElementById('reportfilter[$sType][$sTableFieldName]').value ,type:'$sType',searchField:'reportfilter[$sType][$sTableFieldName]',searchType:'$sType'},onComplete:function(request, json){populateTable(request,json, 'divList', 'overlayTable','reportfilter[$sType][$sTableFieldName]');}} ); return false;";

                     $sFilterHtml .= '<label class="form_label"><input style="margin:5px 0 5px 7px;" type="button" name="search_'.$sType.'" id="search_'.$sType.'" value="Search" class="formbutton_small"  onclick="'.$sAjax.'" /></label>
                     
                    ';
                     
                     if($sLabel=='Exclude Category(s)')
                     {
						 if(count($oPost)<1)
						{
							$chkExclude='checked=checked';
						}
						 $sFilterHtml.= '<div class="form_field">
										  Exclude Ad Category = Internal &nbsp;&nbsp;&nbsp;
										  <input id="chkExclude" class="active_checkbox" type="checkbox" value="1" name="chkExclude" '.$chkExclude.'/>
										  </div>';
						 $sFilterHtml.= '<div class="filter_hdr"></div>';
                     }

					if($sLabel=='Order Name')
					{	
							$reportTypeArr = array_flip(self::getReportType());
							if($report_type==$reportTypeArr['Campaign Summary Report'])
							{
								 $sFilterHtml.= '<div class="form_field"></div><div class="form_field"><font color="red">[ Select single Order Name]</font></div><div class="form_field">  </div><div class="form_field"></div>';
							}

					}
				}
			}
			
			// if post data exist . rewrite geo data : function  
			$sCountries_left = $oPost['hdnleftcontainer'][$iGeoDDIndex];
			
			if($sCountries_left ) { $aCountries_left = self::getDDPostClumnList($sCountries_left); } 
			$populateArr['CN_left'] = implode(',',$aCountries_left);
			
			 $sCountries_right = $oPost['hdnrightcontainer'][$iGeoDDIndex];
			if($sCountries_right ) { $aCountries_right = self::getDDPostClumnList($sCountries_right,10,''); } 
			$populateArr['CN_right'] = implode(',',$aCountries_right);
			
			++$iGeoDDIndex;
			$sSR_left = $oPost['hdnleftcontainer'][$iGeoDDIndex];
			if($sSR_left ) { $aSR_left = self::getDDPostClumnList($sSR_left,10,''); } 
			$populateArr['SR_left'] = implode(',',$aSR_left);
			
			$sSR_right = $oPost['hdnrightcontainer'][$iGeoDDIndex];
			if($sSR_right ) { $aSR_right = self::getDDPostClumnList($sSR_right,10,''); }
			$populateArr['SR_right'] = implode(',',$aSR_right);
			
			++$iGeoDDIndex;
			$sDMA_left = $oPost['hdnleftcontainer'][$iGeoDDIndex];
			if($sDMA_left ) { $aDMA_left = self::getDDPostClumnList($sDMA_left,10,''); } 
			$populateArr['DMA_left'] = implode(',',$aDMA_left);
			
			$sDMA_right = $oPost['hdnrightcontainer'][$iGeoDDIndex];
			if($sDMA_right ) { $aDMA_right = self::getDDPostClumnList($sDMA_right,10,''); } 
			$populateArr['DMA_right'] = implode(',',$aDMA_right);
			
			++$iGeoDDIndex;
			$sAC_left = $oPost['hdnleftcontainer'][$iGeoDDIndex];
			if($sAC_left ) { $aAC_left = self::getDDPostClumnList($sAC_left,10,''); } 
			$populateArr['AC_left'] = implode(',',$aAC_left);
			
			$sAC_right = $oPost['hdnrightcontainer'][$iGeoDDIndex];
			if($sAC_right ) { $aAC_right = self::getDDPostClumnList($sAC_right,10,''); } 
			$populateArr['AC_right'] = implode(',',$aAC_right);
						
			// write data into JS
			if($requestId == '')
			{
			  $requestId = sfContext::getInstance()->getRequest()->getParameter('requestId');
			}
			
			

			if($iIsGeoSelected)
			{
				self::populateGEOData($populateArr,$requestId);
				$sUserJsWritePath = sfConfig::get("app_userjs_read_path");
				$sDDJsUrl = sfConfig::get("app_js_include_path");
				$sFilterHtml .= "<script type=\"text/javascript\">
									changeheight('idgreydiv');
									populateWidgets('".$sUserJsWritePath."geoData_".session_id().'_'.$requestId.".js', '".$sDDJsUrl."geoMasterData.js','Geo','getGeoTargeting','','');
									
								</script>";
			}
			
			return $sFilterHtml;
		}//end of if count
	}	
	
	// this function is used to show generated reports
	public static function getSavedReport($sUserName,$schedule=null, $iSelectedId = null)
	{
	
		$ul_html = '<ul class="droptreedp" style="height:200px;">';
		if($schedule)
		{
			
					$ul_html .= '<li onclick="treerecord( this ); getReportListName(0);" class="hide" id="root_My Scheduled Reports"><img width="9" height="9" src="http://files-adapt.glam.com/images/plus.png" class="hide" id="img_My Scheduled Reports" > 
						My Scheduled Reports <div id="divList_0"></div>
					</li>';
		}
		$ul_html .= '<li onclick="treerecord( this ); getReportListName(1);" class="hide" id="root_My Reports">
					<img width="9" height="9" src="http://files-adapt.glam.com/images/plus.png" class="hide" id="img_My Reports"> 
						My Reports <div id="divList_1"></div>
					</li>';
		
		$ul_html .= '<li onclick="treerecord( this );  getReportListName(2);" class="hide" id="root_Public Reports">
						<img width="9" height="9" src="http://files-adapt.glam.com/images/plus.png" class="hide" id="img_Public Reports"> 
						Public Reports <div id="divList_2"></div>
						</li></ul>';
		
		$action_path = Utils::getApplicationPath('report');
		
		$ul_html .= '<input type="hidden" name="action_path" id="action_path" value="'.$action_path.'">';
		$ul_html .= '<input type="hidden" name="reportTypeSelected" id="reportTypeSelected" value="'.$action_path.'">';
		
		return $ul_html;
	}

	public static function extractGenerateReportPostVars($oRequest,$action='')
	{
		$aDateRange = array();
		$aReportType = array(0 => "", 1 => "_HOURLY", 2 => "DAILY", 3 => "MONTHLY", 4 => "ALL");
		/**/
		$aAdType= array("1" =>"Exclusive","2"=>"Standard 1","3"=>"Standard 2","4"=>"Standard 3","5"=>"Standard 4","6"=>"Bulk");
		/**/
		$iReportType = (int) trim($oRequest->getParameter('report_type'));
		$date_range = $oRequest->getParameter('date_range');
		$rptName = $oRequest->getParameter('txtReportName');
		
		if($date_range==1)
		{
			$PreDefineDropDown = $oRequest->getParameter('PreDefineDropDown');

			$date_range_desc = $PreDefineDropDown;
			switch($PreDefineDropDown)
			{
				case 'Today':
					$sStartDt = date('m/d/Y', strtotime('now'));
					$sEndDt = '';
					$sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE),'YYYYMMDD'))";
					$sSqlEndDt = "";
				break;
				
				case 'Yesterday':
					$sStartDt = date('m/d/Y', strtotime('-1 day'));
					$sEndDt = '';
					$sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),'YYYYMMDD'))";
					$sSqlEndDt = "";
				break;
				
				case 'Week to date':
					$sStartDt = date('m/d/Y',strtotime('Last Sunday'));
					$sEndDt = date('m/d/Y',strtotime('now'));
					$sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(NEXT_DAY(SYSDATE - 7, 'Sunday')),'YYYYMMDD'))";
					$sSqlEndDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE),'YYYYMMDD'))";
					
				break;
				
				case 'Past 7 days':
					$sStartDt = date('m/d/Y',strtotime('-7 days'));
					$sEndDt = date('m/d/Y', strtotime('-1 day'));
					$sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 7),'YYYYMMDD'))";
					$sSqlEndDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),'YYYYMMDD'))";
				break;
				
				case 'Last week':
					$sStartDt = date('m/d/Y',strtotime('Last Sunday',strtotime('Last week')));
					$sEndDt = date('m/d/Y',strtotime('-1 week saturday'));
					$sSqlStartDt = "TO_NUMBER(TO_CHAR(NEXT_DAY(SYSDATE - 14, 'Sunday'),'YYYYMMDD'))";
					$sSqlEndDt = "TO_NUMBER(TO_CHAR(NEXT_DAY(SYSDATE - 14, 'Sunday') +  6,'YYYYMMDD'))";
				break;
				
				case 'Month to date':
					$sStartDt = date('m/01/Y');
					$sEndDt = date('m/d/Y',strtotime('now'));
					$sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE, 'MM'),'YYYYMMDD'))";
					$sSqlEndDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE),'YYYYMMDD'))";
				break;
				
				case 'Past 30 days':
					$sStartDt = date('m/d/Y',strtotime('-30 days'));
					$sEndDt = date('m/d/Y', strtotime('-1 day'));
					$sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 30),'YYYYMMDD'))";
					$sSqlEndDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),'YYYYMMDD'))";
				break;
				
				case 'Last month':
					$sStartDt = date('m/01/Y',strtotime('last month'));
					$sEndDt = date('m/d/Y',(strtotime('this month',strtotime(date('m/01/y'))) - 1));
					$sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(TRUNC(SYSDATE, 'MM') - 1, 'MM'),'YYYYMMDD'))";
					$sSqlEndDt = "TO_NUMBER(TO_CHAR(LAST_DAY(TRUNC(SYSDATE, 'MM') - 1),'YYYYMMDD'))";
				break;

				case 'Year to date':
					   $sStartDt = date('01/01/Y');
					   $sEndDt = date('m/d/Y',strtotime('now'));
					   $sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE, 'YY'),'YYYYMMDD'))";
					   $sSqlEndDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE),'YYYYMMDD'))";
				break;
				
				default:
					$sStartDt=null;
					$sEndDt=null;
					$sSqlStartDt = null;
					$sSqlEndDt = null;
				break;
			}
		}
		elseif($date_range==2)
		{
			$txtName = $oRequest->getParameter('txtName');
			$iDays = (int)$txtName;
			
			$LastDropDown = $oRequest->getParameter('LastDropDown');
			$date_range_desc = $LastDropDown;
			
			if($LastDropDown=='Days')
			{
				$sStartDt = date('m/d/Y',strtotime('-'.$txtName.'days'));
				$sEndDt = date('m/d/Y',strtotime('now'));
				$sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - $iDays),'YYYYMMDD'))";
				$sSqlEndDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),'YYYYMMDD'))";
			}
			elseif($LastDropDown=='Weeks')
			{	
				$sStartDt = date('m/d/Y',strtotime('Last Sunday',strtotime('-'.$txtName.'weeks')));
				$sEndDt = date('m/d/Y',strtotime('-1 week saturday'));
				$sSqlStartDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - $iDays * 7),'YYYYMMDD'))";
				$sSqlEndDt = "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),'YYYYMMDD'))";
			}
			else
			{
				$sStartDt=null;
				$sEndDt=null;
				$sSqlStartDt = null;
				$sSqlEndDt = null;
			}
		}
		elseif($date_range==3)
		{
			$date_range_desc = 'CUSTOM';
			$sStartDt = trim($oRequest->getParameter('txtStartDt'));
			( !preg_match('/^(0[1-9]|1[0-2])[\/](0[1-9]|[1-2][0-9]|3[0-1])[\/][0-9]{4}$/', $sStartDt) ) ? $sStartDt = null : array_push($aDateRange, $sStartDt);
			
			$sEndDt = trim($oRequest->getParameter('txtEndDt'));
			( !preg_match('/^(0[1-9]|1[0-2])[\/](0[1-9]|[1-2][0-9]|3[0-1])[\/][0-9]{4}$/', $sEndDt) ) ? $sEndDt = null : array_push($aDateRange, $sEndDt);
			
			if($sStartDt!='' && $sEndDt=='')
			{
				$sSqlStartDt = $sStartDt;
			}
			elseif($sStartDt!='' && $sEndDt!='')
			{
				$sSqlStartDt = $sStartDt;
				$sSqlEndDt = $sEndDt;
			}
			
			// this code block is used to change the format of date range for ADQ result query 
			$sSqlStartDtArr = explode('/',$sSqlStartDt);
			$sSqlStartDt =$sSqlStartDtArr[2].$sSqlStartDtArr[0].$sSqlStartDtArr[1]; 
			if($sSqlEndDt!='')
			{
				$sSqlEndDtArr = explode('/',$sSqlEndDt);
				$sSqlEndDt =$sSqlEndDtArr[2].$sSqlEndDtArr[0].$sSqlEndDtArr[1];
			}
		}
		
		if($sStartDt!='' && $sEndDt=='')
		{
			$aDateRange = array($sSqlStartDt);
			$aDisplayDateRange = array($sStartDt);
		}
		elseif($sStartDt!='' && $sEndDt!='')
		{
			$aDateRange = array($sSqlStartDt, $sSqlEndDt);
			$aDisplayDateRange = array($sStartDt, $sEndDt);
		}
		
		//for showing report start,end date on generate report page 
		self::$aReportStartEndDt = $aDisplayDateRange;
		$aDragDropPostVar = $oRequest->getParameter('hdnrightcontainer');
		
		$sDataDimensions   = $aDragDropPostVar[0];
		$aDataDimensions = self::getDDPostClumnList($sDataDimensions,0,$iReportType);
		
		if(in_array('Day',$aDataDimensions) && in_array('Month',$aDataDimensions))
		{
			foreach($aDataDimensions as	$keys=>$vals)
			{
				if($vals=='Month')
				{
					unset($aDataDimensions[$keys]);
				}	
			}
		}
		
		     
        foreach($aDataDimensions as $keys=>$Vals)
        {
        	self::$aReportSrchTblLabels[$keys] = $Vals;
        }


		// end of block of code // added by Abhijeet K 
		$sMtricsDimensions = $aDragDropPostVar[1]; 
		$aMtricsDimensions = self::getDDPostClumnList($sMtricsDimensions,2,$iReportType);
		
	
		
		if($aDataDimensions)
		{
			sfContext::getInstance()->getUser()->setAttribute("reportdatadimension", $aDataDimensions);
			sfContext::getInstance()->getUser()->setAttribute("reportName", $rptName);
		}
		
		// end of code // added by abhijeetk

		
		
		if( count($aMtricsDimensions) && count(self::$aReportSrchTblLabels) )
		{
			foreach($aMtricsDimensions as $iKey => $sValu)
			{
				self::$aReportSrchTblLabels[$iKey] = $sValu;
			}
		}
		
		// format array ad per DAY / MONTH
		$label_arr = array();
		if(self::$aReportSrchTblLabels['DATE_ID AS DAILY'])
		{
			self::$aReportSrchTblLabels['DATE_ID AS DAILY'] = 'Daily';
		}
		elseif(self::$aReportSrchTblLabels['DATE_ID AS MONTHLY'])
		{
			self::$aReportSrchTblLabels['DATE_ID AS MONTHLY'] = 'Monthly';
		}
		elseif(self::$aReportSrchTblLabels['DATE_ID AS WEEKLY'])
		{
			self::$aReportSrchTblLabels['DATE_ID AS WEEKLY'] = 'Weekly';
		}

		// end of code
		
		//getting geo and affiliate DD position and index from post array to access values, starts
		$aSelectedFltrs = self::getDDPostClumnList( $aDragDropPostVar[2],1,$iReportType);
		// this code patch is used 
		foreach($aDragDropPostVar as $keyContainer=>$valContainer)
		{
			if(strstr($valContainer,'rightListCN[]='))
			{
				$icountryIndex = $iGeoDDIndex = $keyContainer;
			}

			if(strstr($valContainer,'rightListAff[]='))
			{
				$iAffDDIndex = $keyContainer;
			}

			if(strstr($valContainer,'rightListSR[]='))
			{
				$istateIndex = $keyContainer;
			}

			if(strstr($valContainer,'rightListDMA[]='))
			{
				$idmaIndex = $keyContainer;
			}

		}
		// end of code patch



		//getting geo and affiliate DD position and index from post array to access values, ends
		$aGeo = array();
		$aGeoColumnNms = $oRequest->getParameter('hid_geo_columns');

	
		if(in_array("Geo", $aSelectedFltrs) || in_array("Geo - Country", $aSelectedFltrs))
		{
			$sCountries = $aDragDropPostVar[$iGeoDDIndex];
			if($sCountries ) 
			{ 
				$aCountries = self::getDDPostClumnList($sCountries,10,'');
				$aGeo[] = self::prepareGEOArray($aCountries, $aGeoColumnNms["country"]); 
			} 
			
			$sStates = $aDragDropPostVar[$istateIndex]; 
			if($sStates) { $aStates = self::getDDPostClumnList($sStates,10,''); $aGeo[] =  self::prepareGEOArray($aStates,  $aGeoColumnNms["state"]);}
			
			$sDMA = $aDragDropPostVar[$idmaIndex]; 
			if($sDMA) { $aDMA = self::getDDPostClumnList($sDMA,10,''); $aGeo[]  = self::prepareGEOArray($aDMA,  $aGeoColumnNms["dma"]);  }
			
			$sAC = $aDragDropPostVar[++$idmaIndex]; 
			if($sAC) {  $aAC = self::getDDPostClumnList($sAC,10,''); $aGeo[] = self::prepareGEOArray($aAC,  $aGeoColumnNms["ac"]); }
		}	



		if(in_array("Geo - State/Region", $aSelectedFltrs) && in_array("Geo - Country", $aSelectedFltrs))
		{
			$sCountries = $aDragDropPostVar[$icountryIndex];
			if($sCountries ) 
			{ 
				$aCountries = self::getDDPostClumnList($sCountries,10,'');
				$aGeo[] = self::prepareGEOArray($aCountries, $aGeoColumnNms["country"]); 
			} 

			$sStates = $aDragDropPostVar[$istateIndex]; 
			if($sStates) { $aStates = self::getDDPostClumnList($sStates,10,'');
			$aGeo[] =  self::prepareGEOArray($aStates,  $aGeoColumnNms["state"]);}

		}
		elseif(in_array("Geo - State/Region", $aSelectedFltrs))
		{
			$sStates = $aDragDropPostVar[$istateIndex]; 
			if($sStates) { $aStates = self::getDDPostClumnList($sStates,10,'');
			$aGeo[] =  self::prepareGEOArray($aStates,  $aGeoColumnNms["state"]);}
		}
		elseif(in_array("Geo - Country", $aSelectedFltrs))
		{
			$sCountries = $aDragDropPostVar[$icountryIndex];
			if($sCountries ) 
			{ 
				$aCountries = self::getDDPostClumnList($sCountries,10,'');
				$aGeo[] = self::prepareGEOArray($aCountries, $aGeoColumnNms["country"]); 
			} 
		}

		$sZipCodes = $oRequest->getParameter('txtZipCodes');
		if($sZipCodes) { $aGeo[] = array($aGeoColumnNms["zipcode"] => $sZipCodes);}
		
		$aDDFilters = array();
		$aFilterPost = $oRequest->getParameter('reportfilter');
		

		

		$sAffiliateClmNm = trim($oRequest->getParameter('affiliateclm'));
		if($sAffiliateClmNm)
		{
			$aAffValues = self::prepareGEOArray(self::getDDPostClumnList($aDragDropPostVar[$iAffDDIndex],10,''), $sAffiliateClmNm);
			$aFilterPost["Affiliate"] = $aAffValues;
		}
		
		if($oRequest->getParameter('chkExclude') == "1")
		{
			if($aFilterPost['Ad Category']['ADM_ADS.EXCLUDE_CATEGORY'])
			{
				$aFilterPost['Ad Category']['ADM_ADS.EXCLUDE_CATEGORY']  = $aFilterPost['Ad Category']['ADM_ADS.EXCLUDE_CATEGORY'].',INTERNAL';
			}
			else
			{
				$aFilterPost['Ad Category']['ADM_ADS.EXCLUDE_CATEGORY'] = 'INTERNAL';
			}
			
		}
		
		if(count($aFilterPost))
		{
			$iKeyIndex = 0;
			$aTempA = array();
			foreach($aFilterPost as $sArrayIndex => $aFilterPostDtls)
			{
				
			    foreach($aFilterPostDtls as $sIndex => $sValue)
				{
					 
					$sValue = trim($sValue);
					if($sValue != "")
					{
						$aDDFilters[$iKeyIndex][$sIndex] =  $sValue;
						if($sArrayIndex=='Affiliate List')
						{	
							$aDDFilters[$iKeyIndex]['OPERATOR_VAL'][$sIndex] =  $oRequest->getParameter($sIndex."_checked");
						}
						

        				if($sArrayIndex == "Ad Type")
        				{
        				    $aDDFilters[$iKeyIndex][$sIndex] = $aAdType[$sValue];
    				    }
					}
				}
				++$iKeyIndex;
			}
		}
		

		$aGeoInfo = array();
		$iIndex = count($aDDFilters);
		

		
		if(count($aGeo))
		{
			foreach($aGeo as $aGeoDtls)
			{
				foreach($aGeoDtls as $sColumnNm => $sPostValue)
				{
					if($sColumnNm && $sPostValue)
					{
						$aGeoInfo[$iIndex][$sColumnNm] = $sPostValue;
					}	
				}
			}
		}
		
		if(count($aGeoInfo))
		{
			$aDDFilters = array_merge($aDDFilters, $aGeoInfo);
		}
		
		// time zone is added 
        $timezone = $oRequest->getParameter('LaunchTimezone');
		
		if(count($aDataDimensions))
		{
			$aReturnVals = array();
			$aReturnVals['D'] = $aDataDimensions;
			$aReturnVals['DATE_RANGE_LIST'] = $aDateRange;
			$aReturnVals['REPORT_TYPE_VAL'] = $iReportType;
			
			$aReturnVals['REPORT_DESC'] = $oRequest->getParameter('txtReportDesc');
			$aReturnVals['F'] =  $aDDFilters;
			$aReturnVals['M'] = $aMtricsDimensions;
			$aReturnVals['RUN_TYPE'] = $iReportType;
			$aReturnVals['TIME_ZONE'] = $timezone;
			$aReturnVals['DATE_RANGE_DESC'] = $date_range_desc;
			$aReturnVals['DATE_RANGE_NUM'] = $iDays;
		}
		
		return $aReturnVals;
	}
	
	/**
	 * getDDPostClumnList
	 * purpose:  after search form submit getting column list from the right side list of drag drop to make select statement of search query .
	 * @param string $sPostDDRightSideList
	 * @return array() $aToReturn
	 */
	public static function getDDPostClumnList($sPostDDRightSideList = null,$ifieldType=null,$ireportType=null)
	{
		$aToReturn = array();
		if($sPostDDRightSideList)
		{
			if($ifieldType!='10')
			{
				$aToReturn = self::getExtractDDClumnList($sPostDDRightSideList,$ifieldType,$ireportType);
			}
			else
			{
				$keys = $ireportType;
				$aToReturn = self::getExtractDDClumnList_others($sPostDDRightSideList,$keys);
			}
			
		}
		return $aToReturn;
	}
	
 /**
         * getExtractDDClumnList
         * purpose:  Drag drop post var is a array of values. so extacting actual values from POST var of drag drop.
         * @param string $sValue
         * @return array() $aDrgDrpSelectClmns
         */
        public static function getExtractDDClumnList_others($sValue = null,$keys)
        {
                if($sValue)
                {
                        $aExplodedOnAnd = array();
                        $aExplodedOnAnd = explode("&", $sValue);
                        if(count($aExplodedOnAnd))
                        {
                                $aDrgDrpSelectClmns = array();
                                foreach($aExplodedOnAnd as $aExplodedOnAndDtls)
                                {
                                        list($sStr, $sTblClmnName) = explode("=", $aExplodedOnAndDtls);
                                        
                                        $sTblClmnName = urldecode($sTblClmnName);
                                        if($keys)
                                        {
                                                $aDrgDrpSelectClmns[$sTblClmnName] = $sTblClmnName;
                                        }
                                        else
                                        {
                                                $aDrgDrpSelectClmns[] = $sTblClmnName;
                                        }
                                }
                        }
                        
                        return $aDrgDrpSelectClmns;
                }
        }
	
	/**
	 * getExtractDDClumnList
	 * purpose:  Drag drop post var is a array of values. so extacting actual values from POST var of drag drop.
	 * @param string $sValue
	 * @return array() $aDrgDrpSelectClmns
	 */
	public static function getExtractDDClumnList($sValue = null,$ifieldType,$ireportType)
	{
		
		if($sValue)
		{
			$aExplodedOnAnd = array();
			$aExplodedOnAnd = explode("&", $sValue);
			if(count($aExplodedOnAnd))
			{
				$aDrgDrpSelectClmns = array();
				$aMapArr = array();
				$aMapArr = ReportUtils::getReportParentChildResultSet($ifieldType,$ireportType);

				
				
				foreach($aExplodedOnAnd as $aExplodedOnAndDtls)
				{
					list($sStr, $sTblClmnName) = explode("=", $aExplodedOnAndDtls);

					$sTblClmnName = stripslashes(urldecode($sTblClmnName));
					if($ifieldType=='0')
					{
						if(array_key_exists($sTblClmnName, $aMapArr['displayresultset']))
						{
							if(count($aMapArr['displayresultset'][$sTblClmnName])==1)
							{
								foreach ($aMapArr['displayresultset'][$sTblClmnName] as $valArr)
								{
									$aDrgDrpSelectClmns[$sTblClmnName] = $valArr;
								}
							}
						}
						else
						{
							foreach ($aMapArr['displayresultset'] as $keyArr=>$valArr)
							{
								foreach($valArr as $keyValarr=> $valueValArr)
								{
									if($keyValarr !='0' && $sTblClmnName == $keyValarr)
									{
										$aDrgDrpSelectClmns[$sTblClmnName] = $valueValArr;
									}
								}
							}
						}
					}
					else if($ifieldType=='2')
					{
						//echo $aExplodedOnAndDtls;

						list($sStr, $sTblClmnName) = explode("rightListISPNC[]=", $aExplodedOnAndDtls);
						$sTblClmnName = stripslashes(urldecode($sTblClmnName));
						
						if(array_key_exists($sTblClmnName, $aMapArr['displayresultset']))
						{
							$aDrgDrpSelectClmns[$sTblClmnName] = $aMapArr['displayresultset'][$sTblClmnName];
						}
						elseif(in_array($sTblClmnName, $aMapArr['displayresultset']))
						{
							$keyval = array_search($sTblClmnName, $aMapArr['displayresultset']);
							$aDrgDrpSelectClmns[$keyval] = $sTblClmnName;
						}
						
					}
					elseif($ifieldType=='1')
					{
						if(array_key_exists($sTblClmnName, $aMapArr['filterresultset']))
						{
							$aDrgDrpSelectClmns[$sTblClmnName] = $sTblClmnName;
						}		
						
						
					}
					
				}
			}

			//print_r($aDrgDrpSelectClmns);
			return $aDrgDrpSelectClmns;
		}
	}

	
	public static function getReportDBCredentials()
	{
		$aToReturn["report_db_username"] = sfConfig::get("app_report_db_username");
		$aToReturn["report_db_password"] = sfConfig::get("app_report_db_password");
		$aToReturn["report_db_name"] = sfConfig::get("app_report_db_name");
		return $aToReturn;
	}

	
	public static function saveReport($aParam, $sAction)
	{
		$oReportDao =  new ReportDAO();
		if($sAction == 'insert')
		{
			$returnvalue = $oReportDao->insertReportRecord($aParam);
		}
		else
		{
			$returnvalue = $oReportDao->updateReportRecord($aParam);
		}

		unset($oReportDao);
		return $returnvalue;
	}

	/**
	 * getAdvertiser
	 * purpose:  To save advertiser data into the database
	 * @param integer $iId
	 * @return array
	 */
	public static function getSavedReportData($iId = null)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getSavedReportRecord($iId);
		unset($oReportDao);
		return $returnvalue;

	}

	public static function getSavedReportDataOld($iId = null)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getSavedReportRecordOld($iId);
		unset($oReportDao);
		return $returnvalue;

	}
	
	public static function validateFilterInput($sInputStr = null, $sKey = null)
	{
		$aToPass = array();
		$aIsKeysExists = array();
		$aSplited = explode(",", $sInputStr);
		if(count($aSplited))
		{
			foreach($aSplited as $iKey => $sValue)
			{
				$aToPass[$sKey."_".trim($sValue)] = 0;
			}	
		}
		
		//$sMemcachedHost =  sfConfig::get("app_memcacheseverip");

		$sMemcachedHost ='10.1.1.108';
		$aNotExistData = array();
		$oMemCache = new MemcacheAdq();
		$oMemCache->setMemcached($sMemcachedHost);
		$aIsKeysExists = $oMemCache->isKeyInMemcache($aToPass);
		if(count($aIsKeysExists))
		{
			foreach($aIsKeysExists as $sKeyName => $iIsKeyExist)
			{
				if(!$iIsKeyExist)
				{
					$aSplitted = explode("{$sKey}_", $sKeyName);
					array_push($aNotExistData, $aSplitted[1]); 
				}
			}
		}
		return (count($aNotExistData))? implode(",", $aNotExistData)." is invalid data." : " ";
	}
	
	public static function prepareGEOArray($aInput = array(), $sColumnName)
	{
		$aToReturn = array();
		
		if(count($aInput) && $sColumnName)
		{
			$aToReturn[$sColumnName] = implode(",", $aInput);
			return $aToReturn;
		}
	}
	

	public static function populateGEOData($populateArr,$requestId)
	{
		list($aCNLeftSideItmList, $aCNRightSideItmList) = Utils::getDDForDisplay('Country',$populateArr['CN_right'],'edit',true);
		list($aDMALeftSideItmList, $aDMARightSideItmList) = Utils::getDDForDisplay('DMA',$populateArr['DMA_right'],'edit',true);
		list($aACLeftSideItmList, $aACRightSideItmList) = Utils::getDDForDisplay('Areacode',$populateArr['AC_right'],'edit',true);
	
		// write on state -region code
		$aStateRegion = DragDropUtils::getAllStateRegion();
  		$aLeftListSR = DragDropUtils::getStateTree($aStateRegion);
		$aRightListSR  = $populateArr['SR_right'];
		$aConfig = array("mode"=>"children", "divId"=>"divStateRegion", "leftList"=>"leftListSR", "rightList"=>"rightListSR","dragClass"=>"drag_sr", "dropClass"=>"drop_sr");
  		list($masterDataList,$listStateMaster,$masterConfigList, $masterParentList) = Utils::getTreeData($aLeftListSR, $aRightListSR, $aConfig, 'SR', 'edit', 'all');
  		$sTreeData = "var masterConfigList_SR = ".$masterConfigList."; var listStateMaster_SR = ".$listStateMaster.";";
		Utils::writeDataToJsFile($sTreeData, sfConfig::get('app_userjs_include_path').'stateregion_'.session_id().'_'.$requestId.'.js');
				
		/*********************************Start: Geo Targeting Data***************************************************************/
		$aListCountry = array();
		$aListCountry = Utils::generateDDData($aCNLeftSideItmList, $aCNRightSideItmList, 'Country');
		$aConfigCountry = array();
		$aConfigCountry = array("divId"=>"divCountry", "leftList"=>"leftListCN", "rightList"=>"rightListCN", "dragClass"=>"drag_cn", "dropClass"=>"drop_cn");
		$sListCountry = json_encode($aListCountry);
		$sConfigCountry = json_encode($aConfigCountry);
		$sCountryData = "var listCountry = ".$sListCountry."; var configCountry = ".$sConfigCountry."; ";

		$aListDMA = array();
		$aListDMA = Utils::generateDDData($aDMALeftSideItmList, $aDMARightSideItmList, 'DMA');
		$aConfigDMA = array();
		$aConfigDMA = array("divId"=>"divDMA", "leftList"=>"leftListDMA", "rightList"=>"rightListDMA", "dragClass"=>"drag_dma", "dropClass"=>"drop_dma");
		$sListDMA = json_encode($aListDMA);
		$sConfigDMA = json_encode($aConfigDMA);
		$sDMAData = "var listDMA = ".$sListDMA."; var configDMA = ".$sConfigDMA."; ";

		$aListAC = array();
		$aListAC = Utils::generateDDData($aACLeftSideItmList, $aACRightSideItmList, 'AC');
		$aConfigAC = array();
		$aConfigAC = array("divId"=>"divAC", "leftList"=>"leftListAC", "rightList"=>"rightListAC", "dragClass"=>"drag_ac", "dropClass"=>"drop_ac");
		$sListAC = json_encode($aListAC);
		$sConfigAC = json_encode($aConfigAC);
		$sACData = "var listAC = ".$sListAC."; var configAC = ".$sConfigAC."; ";
		$sGeoData = $sCountryData . $sDMAData . $sACData;
		Utils::writeDataToJsFile($sGeoData, sfConfig::get('app_userjs_include_path')."geoData_".session_id().'_'.$requestId.".js");
	/*********************************End: Geo Targeting Data***************************************************************/
	}
	

	/*trupti*/
	public static function getSearchResult($field,$pager,$type)
	{

		//echo $field."#####".$pager."#####".$type;
		//exit;


		
		$schema_name= sfConfig::get("app_schema_name");

		if($_POST['searchField']=='reportfilter[Affiliate List][INCLUDE_AFFILIATE_PRODUCT_LIST]')
		{
			$type = 'INCLUDE_AFFILIATE_PRODUCT_LIST';
		}
		elseif($_POST['searchField']=='reportfilter[Affiliate List][INCLUDE_AFFILIATE_VERTICALS]')
		{
			$type = 'INCLUDE_AFFILIATE_VERTICALS';
		}
		elseif($_POST['searchField']=='reportfilter[Affiliate List][INCLUDE_AFFILIATE_CHANNELS]')
		{
			$type = 'INCLUDE_AFFILIATE_CHANNELS';
		}
		elseif($_POST['searchField']=='reportfilter[Affiliate List][INCLUDE_AFFILIATE_EXCLUSION_LIST]')
		{
			$type = 'INCLUDE_AFFILIATE_EXCLUSION_LIST';
		}
		elseif($_POST['searchField']=='reportfilter[Affiliate List][EXCLUDE_AFFILIATE_EXCLUSION_LIST]')
		{
			$type = 'INCLUDE_AFFILIATE_EXCLUSION_LIST';
		}
		
	  
	  if(trim($field)!='')
	  {
		$aField = explode(",",$field);
		if($type == "Geo")
		{
			$city_new_arr = array();
			foreach( $aField as $keyC=>$valueC)
			{
				$valueC_arr = explode('(#',$valueC);
				$city_new_arr[] = $valueC_arr[0];
			}
			$aField = array();
			$aField = $city_new_arr;
		}
	  }

	  if($type == "Salesperson" || $type == "Campaign Manager" ||$type == "Secondary Campaign Manager" || $type == "Creative Vendor" || $type == "Creative Type" || $type == "Ad Type" || $type == "Sales Planner"  || $type == "Impression Flag" || 	 $type == "Tag Type" || $type == "ATF" || $type=="Browser"):
	    $matchVar = "DISPLAY";
	  elseif($type == "Affiliate"):  
	    $matchVar = "TITLE";
	   elseif($type == "Creative Type"):  
	    $matchVar = "KEY_NAME";
	   elseif($type == "Ad Size"):  
	    $matchVar = "SIZE_NAME";
	   elseif($type == "Ad Category"):  
	    $matchVar = "CATEGORY";
	   elseif($type == "Ad Sub Category"):  
	    $matchVar = "SUB_CATEGORY";
	   elseif($type == "GROUP_ID"):  
	    $matchVar = "AD_GROUP_ID";
	   elseif($type == "GROUP_NAME"):  
	    $matchVar = "AD_GROUP_NAME";
	  elseif($type == "Network"):  
	    $matchVar = "NETWORK";
	  elseif($type == "CREATIVE_SIZE"):  
	    $matchVar = "SIZE_NAME";
	  elseif($type == "Geo"):  
	    $matchVar = "ID";
	  elseif($type=='Affiliate List'):
		 $matchVar = "AFF_LIST_DISP_COL";
	  elseif($type=='Country of Contract'):
		 $matchVar = "country_contract_name";
	  elseif($type=='Affiliate List Vertical'):
		 $matchVar = "NAME";
	  elseif($type=='Affiliate List Channel'):
		 $matchVar = "NAME";
	  elseif($type=='INCLUDE_AFFILIATE_PRODUCT_LIST'):
		 $matchVar = "ADQ_LIST_NAME";
		 $condAff = 'PRODUCT LISTS';
	  elseif($type=='INCLUDE_AFFILIATE_VERTICALS'):
		 $matchVar = "ADQ_LIST_NAME";
		 $condAff = 'VERTICALS';
	  elseif($type=='INCLUDE_AFFILIATE_CHANNELS'):
		 $matchVar = "ADQ_LIST_NAME";
		 $condAff = 'CHANNELS';
      elseif($type=='INCLUDE_AFFILIATE_EXCLUSION_LIST'):
		 $matchVar = "ADQ_LIST_NAME";
		 $condAff = 'EXCLUSION LISTS';
	 elseif($type=='Country of IO Origin'):
		 $matchVar = "META_COUNTRY";
	  else:
	    $matchVar = "NAME";
	  endif;  

	  
	if(trim($field)!='')
	  {
		  foreach( $aField as $key=>$value)
		  {
			  
			$value = utf8_decode($value);
			$sStr .= "UPPER($matchVar) LIKE UPPER('%".strtoupper(str_replace("'","''",trim($value)))."%')";
			if($key != count($aField)-1)
			{
			  $sStr .= " OR ";
			}
			
		  }
	  } 
	  else
	  {
		$sStr = ' 1=1 ';
	  }


	  $campaign_db = 0;
	  switch($type)
	  {
		
		case 'Country of IO Origin':
			$sSql = "SELECT  PT.ID, PT.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GPT.ID, GPT.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('META COUNTRY') AND ACTIVE=1 ORDER BY UPPER(trim(DISPLAY))
                              ) GPT
                              WHERE ROWNUM <= $pager[1]
                            ) PT      
                            WHERE RN >= $pager[0]";
	      	  $campaign_db = 1;


		$campaign_db = 1;

		break;

		 case 'INCLUDE_AFFILIATE_PRODUCT_LIST':	
		 case 'INCLUDE_AFFILIATE_VERTICALS':	
		 case 'INCLUDE_AFFILIATE_CHANNELS':	
		 case 'INCLUDE_AFFILIATE_EXCLUSION_LIST':
		 $sSql = "SELECT  PT.ID, PT.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GPT.ID, GPT.NAME
                              FROM 
                              (
                                SELECT ROWNUM as ID, ADQ_LIST_NAME AS NAME
                                FROM ADQ_AFF_LIST_CATEGORY
                                where $sStr AND UPPER(LIST_CATEGORY)='".$condAff."'  ORDER BY ADQ_LIST_NAME
                              ) GPT
                              WHERE ROWNUM <= $pager[1]
                            ) PT      
                            WHERE RN >= $pager[0]";
	      	  $campaign_db = 1;
        	  break;


		  case 'Browser':	
	      	$sSql = "SELECT  PT.ID, PT.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GPT.ID, GPT.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('BROWSER') AND ACTIVE=1 ORDER BY UPPER(trim(DISPLAY))
                              ) GPT
                              WHERE ROWNUM <= $pager[1]
                            ) PT      
                            WHERE RN >= $pager[0]";
	      	  $campaign_db = 1;
        	  break;

		  case 'Tag Type':	
	      	$sSql = "SELECT  PT.ID, PT.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GPT.ID, GPT.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('TAG TYPE') AND ACTIVE=1 ORDER BY UPPER(trim(DISPLAY))
                              ) GPT
                              WHERE ROWNUM <= $pager[1]
                            ) PT      
                            WHERE RN >= $pager[0]";
	      	  $campaign_db = 1;
        	  break;


			case 'ATF':	
	      	$sSql = "SELECT  PT.ID, PT.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GPT.ID, GPT.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('ATF VALUE') AND ACTIVE=1 ORDER BY UPPER(trim(DISPLAY))
                              ) GPT
                              WHERE ROWNUM <= $pager[1]
                            ) PT      
                            WHERE RN >= $pager[0]";
	      	  $campaign_db = 1;
        	  break;

		 case 'Country of Contract':
		 $sSql = " SELECT 
					cha.ID,
					cha.NAME
				FROM
				  (SELECT ROWNUM AS rn,
					gcha.ID,
					gcha.NAME
				  FROM
					(SELECT DISTINCT country_contract as ID,
					 country_contract_name         AS NAME
					FROM adq_affiliate
					WHERE $sStr AND country_contract IS NOT NULL
					ORDER BY UPPER (TRIM (NAME))
					) gcha
				  WHERE ROWNUM <= $pager[1]
				  ) cha
				WHERE rn >= $pager[0]";

				$campaign_db = 1;
		break;
        case 'Impression Flag':	
	      	$sSql = "SELECT  PT.ID, PT.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GPT.ID, GPT.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('BIT FLAG') AND ACTIVE=1 ORDER BY UPPER(trim(DISPLAY))
                              ) GPT
                              WHERE ROWNUM <= $pager[1]
                            ) PT      
                            WHERE RN >= $pager[0]";
	      	  $campaign_db = 1;
        	  break;

		  	case 'Zip Code':
		 $sSql = " SELECT 
					cha.ID,
					cha.NAME
				FROM
				  (SELECT ROWNUM AS rn,
					gcha.ID,
					gcha.NAME
				  FROM
					(SELECT 'Country_'||ID AS ID,
					  NAME          AS NAME
					FROM gad_country
					WHERE $sStr
					ORDER BY UPPER (TRIM (NAME))
					) gcha
				  WHERE ROWNUM <= $pager[1]
				  ) cha
				WHERE rn >= $pager[0]";

				$campaign_db = 1;
		break;
		 
		 case 'Affiliate List Vertical':
		 $sSql = " SELECT 
					cha.ID,
					cha.NAME
				FROM
				  (SELECT ROWNUM AS rn,
					gcha.ID,
					gcha.NAME
				  FROM
					(SELECT 'Country_'||ID AS ID,
					  NAME          AS NAME
					FROM gad_country
					WHERE $sStr
					ORDER BY UPPER (TRIM (NAME))
					) gcha
				  WHERE ROWNUM <= $pager[1]
				  ) cha
				WHERE rn >= $pager[0]";

				$campaign_db = 1;
		break;

		case 'Affiliate List Channel':
		 $sSql = " SELECT 
					cha.ID,
					cha.NAME
				FROM
				  (SELECT ROWNUM AS rn,
					gcha.ID,
					gcha.NAME
				  FROM
					(SELECT 'Country_'||ID AS ID,
					  NAME          AS NAME
					FROM gad_country
					WHERE $sStr
					ORDER BY UPPER (TRIM (NAME))
					) gcha
				  WHERE ROWNUM <= $pager[1]
				  ) cha
				WHERE rn >= $pager[0]";

				$campaign_db = 1;
		break;

		

		 case 'Affiliate List':
		 $sSql = " SELECT 
					cha.ID,
					cha.NAME
				FROM
				  (SELECT ROWNUM AS rn,
					gcha.ID,
					gcha.NAME
				  FROM
					(SELECT AFF_LIST_COL_INDEX AS ID,
					  AFF_LIST_DISP_COL          AS NAME
					FROM lm_affiliate_map
					WHERE $sStr
					ORDER BY UPPER (TRIM (NAME))
					) gcha
				  WHERE ROWNUM <= $pager[1]
				  ) cha
				WHERE rn >= $pager[0]";

				$campaign_db = 1;
		break;

		case 'Geo':
		 $sSql = " SELECT 
					cha.ID,
					cha.NAME
				FROM
				  (SELECT ROWNUM AS rn,
					gcha.ID,
					gcha.NAME
				  FROM
					(SELECT ID ,
							Name || '(#' || ID || ')' as NAME 
					FROM gad_city
					WHERE $sStr
					ORDER BY UPPER (TRIM (NAME))
					) gcha
				  WHERE ROWNUM <= $pager[1]
				  ) cha
				WHERE rn >= $pager[0]";

				$campaign_db = 1;
		break;

		case 'Network Name':
            	 $sSql = "SELECT CHA.ID, CHA.NAME FROM
                        (
                          SELECT 
                                ROWNUM AS RN, GCHA.ID, GCHA.NAME
                          FROM 
                          (
                            SELECT ID as ID, NAME
                            FROM ADQ_NETWORK
                            where $sStr ORDER BY UPPER(trim(NAME))
                          ) GCHA
                          WHERE ROWNUM <= $pager[1]
                        ) CHA      
                        WHERE RN >= $pager[0]";
						$campaign_db = 1;
        	  break;

			case 'Unduplicated Channel':
            	 $sSql = "SELECT CHA.ID, CHA.NAME FROM
                        (
                          SELECT 
                                ROWNUM AS RN, GCHA.ID, GCHA.NAME
                          FROM 
                          (
                            SELECT ID as ID, NAME
                            FROM ADQ_CHANNEL
                            where $sStr ORDER BY UPPER(trim(NAME))
                          ) GCHA
                          WHERE ROWNUM <= $pager[1]
                        ) CHA      
                        WHERE RN >= $pager[0]";
						$campaign_db = 1;
        	  break;

			  case 'Sub Channel Main Name':
            	 $sSql = "SELECT CHA.ID, CHA.NAME FROM
                        (
                          SELECT 
                                ROWNUM AS RN, GCHA.ID, GCHA.NAME
                          FROM 
                          (
                            SELECT ID as ID, NAME
                            FROM ADQ_SUB_CHANNEL_M
                            where $sStr ORDER BY UPPER(trim(NAME))
                          ) GCHA
                          WHERE ROWNUM <= $pager[1]
                        ) CHA      
                        WHERE RN >= $pager[0]";
						$campaign_db = 1;
        	  break;

			  case 'Sub Channel Sec Name':
            	 $sSql = "SELECT CHA.ID, CHA.NAME FROM
                        (
                          SELECT 
                                ROWNUM AS RN, GCHA.ID, GCHA.NAME
                          FROM 
                          (
                            SELECT ID as ID, NAME
                            FROM ADQ_SUB_CHANNEL_S
                            where $sStr ORDER BY UPPER(trim(NAME))
                          ) GCHA
                          WHERE ROWNUM <= $pager[1]
                        ) CHA      
                        WHERE RN >= $pager[0]";
						$campaign_db = 1;
        	  break;

			  case 'Vertical Primary Name':
            	 $sSql = "SELECT CHA.ID, CHA.NAME FROM
                        (
                          SELECT 
                                ROWNUM AS RN, GCHA.ID, GCHA.NAME
                          FROM 
                          (
                            SELECT ID as ID, NAME
                            FROM ADQ_VERTICAL_P
                            where $sStr ORDER BY UPPER(trim(NAME))
                          ) GCHA
                          WHERE ROWNUM <= $pager[1]
                        ) CHA      
                        WHERE RN >= $pager[0]";
						$campaign_db = 1;
        	  break;

			  case 'Unduplicated Vertical':
            	$sSql = "SELECT CHA.ID, CHA.NAME FROM
                        (
                          SELECT 
                                ROWNUM AS RN, GCHA.ID, GCHA.NAME
                          FROM 
                          (
                            SELECT ID as ID, NAME
                            FROM ADQ_VERTICAL_U
                            where $sStr ORDER BY UPPER(trim(NAME))
                          ) GCHA
                          WHERE ROWNUM <= $pager[1]
                        ) CHA      
                        WHERE RN >= $pager[0]";
						$campaign_db = 1;
        	  break;

						  

		  case 'Network':
		 $sSql = "SELECT CHA.ID, CHA.NAME FROM
                        (
                          SELECT 
                                ROWNUM AS RN, GCHA.ID, GCHA.NAME
                          FROM 
                          (
                            SELECT ID as ID, NAME
                            FROM ADQ_NETWORK
                            where $sStr ORDER BY UPPER(trim(NAME))
                          ) GCHA
                          WHERE ROWNUM <= $pager[1]
                        ) CHA      
                        WHERE RN >= $pager[0]";
						
		  $campaign_db = 1;
		  break;


    	case 'GROUP_ID':
        	 $sSql = "SELECT GROUPAD.ID, GROUPAD.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GAD_GROUP.ID, GAD_GROUP.NAME
                              FROM 
                              (
                                SELECT AD_GROUP_ID as ID, AD_GROUP_ID AS NAME
                                FROM ADQ_AD_GROUP
                                where $sStr ORDER BY UPPER(trim(AD_GROUP_ID))
                              ) GAD_GROUP
                              WHERE ROWNUM <= $pager[1]
                            ) GROUPAD      
                            WHERE RN >= $pager[0]";
			  $campaign_db = 1;
        	  break;
			  	    /*Advertiser*/
	    case 'GROUP_NAME':
        	  $sSql = "SELECT GROUPAD.ID, GROUPAD.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GAD_GROUP.ID, GAD_GROUP.NAME
                              FROM 
                              (
                                SELECT AD_GROUP_ID as ID, AD_GROUP_NAME AS NAME
                                FROM ADQ_AD_GROUP
                                where $sStr ORDER BY UPPER(trim(AD_GROUP_ID))
                              ) GAD_GROUP
                              WHERE ROWNUM <= $pager[1]
                            ) GROUPAD      
                            WHERE RN >= $pager[0]";
			  $campaign_db = 1;
        	 
        	  break;
	    /*Advertiser*/
	    case 'Advertiser':
		case 'Advertiser Name':
        	 $sSql = "SELECT ADV.ID, ADV.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GADV.ID, GADV.NAME
                              FROM 
                              (
                                SELECT ID as ID, NAME
                                FROM ADQ_ADVERTISER
                                where $sStr ORDER BY UPPER(trim(NAME))
                              ) GADV
                              WHERE ROWNUM <= $pager[1]
                            ) ADV      
                            WHERE RN >= $pager[0]";
			  $campaign_db = 1;
        	  break;
        /*Order*/
	    case 'Order':
		case 'Order Name':
	      $sSql = "SELECT  ORD.ID, ORD.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GORD.ID, GORD.NAME
                              FROM 
                              (
                                SELECT ID as ID, NAME
                                FROM  ADQ_ORDER
                                where $sStr ORDER BY UPPER(trim(NAME))
                              ) GORD
                              WHERE ROWNUM <= $pager[1]
                            ) ORD      
                            WHERE RN >= $pager[0]";
			  $campaign_db = 1;
        	  break;
        case 'Creative':	
	      $sSql = "SELECT  CRT.ID, CRT.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GCRT.ID, GCRT.NAME
                              FROM 
                              (
                                SELECT ID as ID, NAME
                                FROM  ADQ_CREATIVE
                                where $sStr ORDER BY UPPER(trim(NAME))
                              ) GCRT
                              WHERE ROWNUM <= $pager[1]
                            ) CRT      
                            WHERE RN >= $pager[0]";
			  $campaign_db = 1;
        	  break;	

       case 'Ad':
	   case 'Ad Name':
		   
	      $sSql = "SELECT  AD.ID, AD.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GAD.ID, GAD.NAME
                              FROM 
                              (
                                SELECT ID as ID, NAME
                                FROM  ADQ_AD
                                where $sStr ORDER BY UPPER(trim(NAME))
                              ) GAD
                              WHERE ROWNUM <= $pager[1]
                            ) AD      
                            WHERE RN >= $pager[0]";
			  $campaign_db = 1;
        	  break;

        case 'Salesperson':
          $sSql = "SELECT  SP.ID, SP.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GSP.ID, GSP.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('Salesperson') ORDER BY UPPER(trim(DISPLAY))
                              ) GSP
                              WHERE ROWNUM <= $pager[1]
                            ) SP      
                            WHERE RN >= $pager[0]"; 
			$campaign_db = 1;
        break;
        	  
        case 'Campaign Manager':	
	      	$sSql = "SELECT  PT.ID, PT.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GPT.ID, GPT.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('Campaign Manager') ORDER BY UPPER(trim(DISPLAY))
                              ) GPT
                              WHERE ROWNUM <= $pager[1]
                            ) PT      
                            WHERE RN >= $pager[0]";
	      	  $campaign_db = 1;
        	  break;

        case 'Secondary Campaign Manager':
        	$sSql = "SELECT  ST.ID, ST.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GST.ID, GST.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM  ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('Campaign Manager') ORDER BY UPPER(trim(DISPLAY))
                              ) GST
                              WHERE ROWNUM <= $pager[1]
                            ) ST      
                            WHERE RN >= $pager[0]";
							$campaign_db = 1;
        break;

        case 'Creative Vendor':
        	$sSql = "SELECT  CV.ID, CV.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GCV.ID, GCV.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM  ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('Creative Vendor') ORDER BY UPPER(trim(DISPLAY))
                              ) GCV
                              WHERE ROWNUM <= $pager[1]
                            ) CV      
                            WHERE RN >= $pager[0]";
							$campaign_db = 1;
        break;
        
        case 'Channel':
    	 $sSql = "SELECT CHA.ID, CHA.NAME FROM
                        (
                          SELECT 
                                ROWNUM AS RN, GCHA.ID, GCHA.NAME
                          FROM 
                          (
                            SELECT ID as ID, NAME
                            FROM ADQ_CHANNEL
                            where $sStr ORDER BY UPPER(trim(NAME))
                          ) GCHA
                          WHERE ROWNUM <= $pager[1]
                        ) CHA      
                        WHERE RN >= $pager[0]";
						$campaign_db = 1;
    	  break;
	      
    	case 'Sub-channel':
      	 $sSql = "SELECT CHA.ID, CHA.NAME FROM
                          (
                            SELECT 
                                  ROWNUM AS RN, GCHA.ID, GCHA.NAME
                            FROM 
                            (
                              SELECT ID as ID, NAME
                              FROM ADQ_SUB_CHANNEL
                              where $sStr ORDER BY UPPER(trim(NAME))
                            ) GCHA
                            WHERE ROWNUM <= $pager[1]
                          ) CHA      
                          WHERE RN >= $pager[0]";
						  $campaign_db = 1;
    	  break;
    	  
    	 case 'Affiliate':
    	   $sSql = "SELECT AFF.ID, AFF.NAME FROM
                          (
                            SELECT 
                                  ROWNUM AS RN, GAFF.ID, GAFF.NAME
                            FROM 
                            (
                              SELECT  AFFILIATE_ID AS ID,TITLE as NAME
                              FROM GAD_AFFILIATE 
                              where $sStr ORDER BY UPPER(trim(TITLE))
                            ) GAFF
                            WHERE ROWNUM <= $pager[1]
                          ) AFF      
                          WHERE RN >= $pager[0]";
    	  break;
    	  
    	 case 'Creative Type':
    	 	$sAndChar = ($sStr) ? " AND " : "";
    	 	$sSql = "SELECT OPTIONTBL1.ID , OPTIONTBL1.NAME FROM
    	 			(
    	 				SELECT 
                                  ROWNUM AS RN, OPTIONTBL2.ID , OPTIONTBL2.NAME
                            FROM 
                            (
                            	SELECT DISPLAY as NAME, STR_VALUE_ID as ID FROM ADM_OPTION_LOOKUP 
                            	WHERE ACTIVE=1 AND UPPER(KEY_NAME)=UPPER ('Creative Type') $sAndChar 
    	 						$sStr 
                            ) OPTIONTBL2
                            WHERE ROWNUM <= $pager[1]
                     )  OPTIONTBL1
    	 			WHERE RN >= $pager[0]";
					$campaign_db = 1;
    	 	
    	 break;

    	 case 'Ad Type':
    	 	$sAndChar = ($sStr) ? " AND " : "";
    	 	$sSql = "SELECT OPTIONTBL1.ID , OPTIONTBL1.NAME FROM
    	 			(
    	 				SELECT 
                                  ROWNUM AS RN, OPTIONTBL2.ID , OPTIONTBL2.NAME
                            FROM 
                            (
                            	SELECT DISPLAY as NAME, STR_VALUE_ID as ID FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)=UPPER ('Ad Type') $sAndChar $sStr 
                            ) OPTIONTBL2
                            WHERE ROWNUM <= $pager[1]
                     )  OPTIONTBL1
    	 			WHERE RN >= $pager[0]";
					$campaign_db = 1;
    	 break;
		
    	 case 'Ad Size':
    	 case 'CREATIVE_SIZE':
    	 	$sAndChar = ($sStr) ? " AND " : "";
    	 	$sSql = "SELECT AD1.ID , AD1.NAME FROM
    	 			(
    	 				SELECT 
                                  ROWNUM AS RN, AD2.ID , AD2.NAME
                            FROM 
                            (
                            	SELECT GAD_AD_SIZE_ID AS ID , SIZE_NAME AS NAME FROM GAD_AD_SIZE WHERE ACTIVE = 1 $sAndChar $sStr
                            ) AD2
                            WHERE ROWNUM <= $pager[1]
                     )  AD1
    	 			WHERE RN >= $pager[0]";
    	 	$campaign_db = 1;
    	 break;
    	 
    	  case 'Ad Category':
			  //ORDER BY CATEGORY
    	  	$sAndChar = ($sStr) ? " AND " : "";
    	  	$sSql = "SELECT 
    	  					CAT_TBL.ID,
    	  				 	CAT_TBL.NAME 
						FROM
						(
						    SELECT ROWNUM RN, CAT.ID , CAT.NAME FROM
						    (
						        SELECT DISTINCT CATEGORY_CODE AS ID , UPPER(CATEGORY) AS NAME FROM GAD_CATEGORY 
						        WHERE  ACTIVE = 1  $sAndChar $sStr 
						        AND CATEGORY IS NOT NULL 
						        AND UPPER(CATEGORY) !='INTERNAL'
						        
						    ) CAT
						    WHERE ROWNUM <= $pager[1]
						)CAT_TBL
						WHERE CAT_TBL.RN >= $pager[0]";
    	  	$campaign_db = 1;
        	 
        	  break;
        	  case 'Ad Sub Category':
    	  	$sAndChar = ($sStr) ? " AND " : "";
    	  	$sSql = "SELECT 
    	  					CAT_TBL.ID,
    	  				 	CAT_TBL.NAME 
						FROM
						(
						    SELECT ROWNUM RN, CAT.ID , CAT.NAME FROM
						    (
						        SELECT DISTINCT SUB_CATEGORY_CODE AS ID , SUB_CATEGORY AS NAME FROM GAD_CATEGORY 
						        WHERE  ACTIVE = 1  $sAndChar $sStr AND SUB_CATEGORY IS NOT NULL
						        ORDER BY SUB_CATEGORY
						    ) CAT
						    WHERE ROWNUM <= $pager[1]
						)CAT_TBL
						WHERE CAT_TBL.RN >= $pager[0]";
    	  	$campaign_db = 1;
        	 
        	  break;

		case 'Sales Planner':
          $sSql = "SELECT  SP.ID, SP.NAME FROM
                            (
                              SELECT 
                                    ROWNUM AS RN, GSP.ID, GSP.NAME
                              FROM 
                              (
                                SELECT STR_VALUE_ID as ID, DISPLAY AS NAME
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('Sales Planner') ORDER BY UPPER(trim(DISPLAY))
                              ) GSP
                              WHERE ROWNUM <= $pager[1]
                            ) SP      
                            WHERE RN >= $pager[0]"; 
							$campaign_db = 1;
        break;
 	 
	  }

	  $oReportDao = new ReportDAO();
	  $aResult = $oReportDao->getsearchResult($sSql,$campaign_db);
	  
	
	  unset($oReportDao);
	  return  $aResult;
  	}
  	
  	
  	public static function getPageCount($field,$type)
  	{
      $schema_name= sfConfig::get("app_schema_name");
  	  
	  $aField = array();
	  if($_POST['searchField']=='reportfilter[Affiliate List][INCLUDE_AFFILIATE_PRODUCT_LIST]')
		{
			$type = 'INCLUDE_AFFILIATE_PRODUCT_LIST';
		}
		elseif($_POST['searchField']=='reportfilter[Affiliate List][INCLUDE_AFFILIATE_VERTICALS]')
		{
			$type = 'INCLUDE_AFFILIATE_VERTICALS';
		}
		elseif($_POST['searchField']=='reportfilter[Affiliate List][INCLUDE_AFFILIATE_CHANNELS]')
		{
			$type = 'INCLUDE_AFFILIATE_CHANNELS';
		}
		elseif($_POST['searchField']=='reportfilter[Affiliate List][INCLUDE_AFFILIATE_EXCLUSION_LIST]')
		{
			$type = 'INCLUDE_AFFILIATE_EXCLUSION_LIST';
		}
		elseif($_POST['searchField']=='reportfilter[Affiliate List][EXCLUDE_AFFILIATE_EXCLUSION_LIST]')
		{
			$type = 'INCLUDE_AFFILIATE_EXCLUSION_LIST';
		}

	  if(trim($field)!='')
	  {	
	  	$aField = explode(",",$field);
	  }
	   
  	  if($type == "Salesperson" || $type == "Campaign Manager" || $type == "Secondary Campaign Manager" || $type == "Creative Vendor" || $type == "Creative Type" || $type == "Ad Type" || $type == "Sales Planner" || $type == "Impression Flag" || 	 $type == "Tag Type" || $type == "ATF" || $type=="Browser"):
  	  $matchVar = "DISPLAY";
  	  elseif($type == "Affiliate"):
  	  $matchVar = "TITLE";
  	  elseif($type == "Ad Size"):
  	  $matchVar = "SIZE_NAME";
  	  elseif($type == "Ad Category"):
  	  $matchVar = "Category";
  	  elseif($type == "Ad Sub Category"):
  	  $matchVar = "Sub_Category";
	  elseif($type == "GROUP_ID"):  
	  $matchVar = "AD_GROUP_ID";
	  elseif($type == "GROUP_NAME"):  
	  $matchVar = "AD_GROUP_NAME";
	  elseif($type == "Network"):  
	  $matchVar = "NETWORK";
	  elseif($type == "CREATIVE_SIZE"):  
	  $matchVar = "SIZE_NAME";
	  elseif($type == "Geo"):  
	  $matchVar = "ID";
	  elseif($type == 'Affiliate List'):
	  $matchVar = "AFF_LIST_DISP_COL";
	  elseif($type == 'Country of Contract'):
	  $matchVar = "NAME";
	  elseif($type=='Affiliate List Vertical'):
	  $matchVar = "NAME";
	  elseif($type=='Affiliate List Channel'):
	  $matchVar = "NAME";
	  elseif($type=='Zip Code'):
	  $matchVar = "NAME";
	  elseif($type=='Country of Contract'):
	  $matchVar = "country_contract_name";
	  elseif($type=='INCLUDE_AFFILIATE_PRODUCT_LIST'):
	  $matchVar = "ADQ_LIST_NAME";
	  $condAff = 'PRODUCT LISTS';
	  elseif($type=='INCLUDE_AFFILIATE_VERTICALS'):
	  $matchVar = "ADQ_LIST_NAME";
	  $condAff = 'VERTICALS';
	  elseif($type=='INCLUDE_AFFILIATE_CHANNELS'):
	  $matchVar = "ADQ_LIST_NAME";
	  $condAff = 'CHANNELS';
	  elseif($type=='INCLUDE_AFFILIATE_EXCLUSION_LIST'):
	  $matchVar = "ADQ_LIST_NAME";
	  $condAff = 'EXCLUSION LISTS';
	  
  	  else:
  	  $matchVar = "NAME";
  	  endif;
	  

	 if(trim($field)!='')
	  {
	  	foreach( $aField as $key=>$value)
	  	{
	     		$sStr .= "UPPER($matchVar) LIKE UPPER('%".strtoupper(str_replace("'","''",trim($value)))."%')";
	    		if($key != count($aField)-1)
	    		{
	      			$sStr .= " OR ";
	    		}
	  	}
	  }
	  else
		{
		  $sStr = '1=1';
	  }
	 
	  $campaign_db = 0;
      switch($type)
	  {
		case 'Country of IO Origin':
			$sSql = " SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)='META COUNTRY' AND ACTIVE=1";


		break;

		  

		 case 'INCLUDE_AFFILIATE_PRODUCT_LIST':	
		 case 'INCLUDE_AFFILIATE_VERTICALS':	
		 case 'INCLUDE_AFFILIATE_CHANNELS':	
		 case 'INCLUDE_AFFILIATE_EXCLUSION_LIST':
		  $sSql = "	SELECT 
						count(1) as cnt
					FROM 
						ADQ_AFF_LIST_CATEGORY
					where 
						$sStr AND UPPER(LIST_CATEGORY)='".$condAff."'";
	      	  $campaign_db = 1;
        	  break;

		  case 'Browser':
            $sSql = " SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('BROWSER') AND ACTIVE=1";
			$campaign_db = 1;
        	break;

			case 'Tag Type':
            $sSql = " SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('TAG TYPE') AND ACTIVE=1";
			$campaign_db = 1;
        	break;

			case 'Tag Type':
            $sSql = " SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('TAG TYPE') AND ACTIVE=1";
			$campaign_db = 1;
        	break;

			case 'ATF':
            $sSql = " SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('ATF VALUE') AND ACTIVE=1";
			$campaign_db = 1;
        	break;
		   case 'Impression Flag':
            $sSql = " SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('BIT FLAG') AND ACTIVE=1";
			$campaign_db = 1;
        	break;
			case 'Zip Code':
           $sSql = " SELECT  count(1) as cnt 
					 FROM gad_country
					WHERE $sStr";
		   $campaign_db = 1;
        	break;


			

		  case 'Affiliate List Vertical':
		 $sSql = " SELECT  count(1) as cnt 
					 FROM gad_country
					WHERE $sStr";
		$campaign_db = 1;
		break;

		case 'Affiliate List Channel':
		 $sSql = " SELECT  count(1) as cnt 
					 FROM gad_country
					WHERE $sStr";
		$campaign_db = 1;
		break;


		case 'Country of Contract':
		  $sSql = " SELECT  count(DISTINCT country_contract) as cnt 
					 FROM adq_affiliate
					WHERE $sStr AND country_contract IS NOT NULL";
		$campaign_db = 1;
		break;


		case 'Affiliate List':
		 $sSql = " SELECT  count(1) as cnt 
					 FROM lm_affiliate_map
					WHERE $sStr";
		$campaign_db = 1;
		break;


		case 'Geo':
      	 $sSql = "  SELECT  count(1) as cnt
                              FROM gad_city
                              where $sStr";
							  $campaign_db = 1;
    	  break;
		  case 'Sub-channel':
      	 $sSql = "  SELECT  count(1) as cnt
                              FROM ADQ_SUB_CHANNEL
                              where $sStr";
							  $campaign_db = 1;
    	  break;


 		case 'Network Name':
	       $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_NETWORK
                                where $sStr";
			$campaign_db = 1;
        	  break; 

		case 'Unduplicated Channel':
	       $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_CHANNEL
                                where $sStr";
			$campaign_db = 1;
        	  break; 

		case 'Sub Channel Main Name':
	       $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_SUB_CHANNEL_M
                                where $sStr";
			$campaign_db = 1;
        	  break; 

			  case 'Sub Channel Sec Name':
	       $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_SUB_CHANNEL_S
                                where $sStr";
			$campaign_db = 1;
        	  break; 
			case 'Vertical Primary Name':
	       $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_VERTICAL_P
                                where $sStr";
			$campaign_db = 1;
        	  break; 

			  case 'Unduplicated Vertical':
	       $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_VERTICAL_U
                                where $sStr";
			$campaign_db = 1;
        	  break; 



		case 'GROUP_ID':
		case 'GROUP_NAME':
	    	 $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_AD_GROUP
                                where $sStr";
			$campaign_db = 1;
        	  break;  
	    /*Advertiser*/
	    case 'Advertiser':
		case 'Advertiser Name':
	       $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_ADVERTISER
                                where $sStr";
			$campaign_db = 1;
        	  break; 
        	   
         case 'Order':
		 case 'Order Name':
	       $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_ORDER
                                where $sStr";
								$campaign_db = 1;
        	  break;   

         case 'Creative': 	  
	      $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_CREATIVE
                                where $sStr";
								$campaign_db = 1;
        	  break;
        	     
         case 'Ad':
		 case 'Ad Name':
           $sSql = "SELECT  count(1) as cnt
                                FROM ADQ_AD
                                where $sStr";
								$campaign_db = 1;
        	  break;
        
        case 'Salesperson':
            $sSql = " SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('Salesperson')";
								$campaign_db = 1;
        	break;
		
        case 'Campaign Manager':
        	$sSql = "SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP WHERE
                                $sStr AND UPPER(KEY_NAME)=UPPER('Campaign Manager')";
								$campaign_db = 1;
        		
        break;
        
        case 'Secondary Campaign Manager':
        	$sSql = "SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP WHERE
                               $sStr AND UPPER(KEY_NAME)=UPPER('Campaign Manager')";
							   $campaign_db = 1;
        		
        break;
        
        case 'Creative Vendor':
          $sSql = "SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP WHERE
                               $sStr AND UPPER(KEY_NAME)=UPPER('Creative Vendor')";
							   $campaign_db = 1;
        		
        break;
        
       case 'Channel':
    	 $sSql = "SELECT  count(1) as cnt 
                            FROM ADQ_CHANNEL
                            where $sStr";
							$campaign_db = 1;
    	  break;
    	  
    	case 'Sub-channel':
      	 $sSql = "  SELECT  count(1) as cnt
                              FROM ADQ_SUB_CHANNEL
                              where $sStr";
							  $campaign_db = 1;
    	  break;
    	     
    	case 'Affiliate':
    	   $sSql = " SELECT  count(1) as cnt
                              FROM GAD_AFFILIATE
                              where $sStr";
    	  break;

		case 'Creative Type':
    	 	$sAndChar = ($sStr) ? " AND " : "";
    	 	$sSql = "SELECT count(1) as CNT  FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)=UPPER ('Creative Type) $sAndChar $sStr ";
			$campaign_db = 1;
    	 break;

    	 case 'Ad Type':
    	 	$sAndChar = ($sStr) ? " AND " : "";
    	 	$sSql = "SELECT count(1) as CNT  FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)=UPPER ('Ad Type') $sAndChar $sStr ";
			$campaign_db = 1;
    	 break;

    	case 'Ad Size':
    	case 'CREATIVE_SIZE':
    	 	$sAndChar = ($sStr) ? " AND " : "";
    	 	$sSql = "SELECT count(1) as CNT FROM GAD_AD_SIZE WHERE ACTIVE = 1 $sAndChar $sStr";
    	 break;
    	 
    	case 'Ad Category':
    		$sAndChar = ($sStr) ? " AND " : "";
    		$sSql = "SELECT 
    						COUNT(DISTINCT CATEGORY_CODE) as CNT
    					FROM GAD_CATEGORY where  $sStr  AND UPPER(CATEGORY) !='INTERNAL' " ;
    		break;
    	case 'Ad Sub Category':
    		$sAndChar = ($sStr) ? " AND " : "";
    		$sSql = "SELECT
    						COUNT(DISTINCT SUB_CATEGORY_CODE) as CNT
    					FROM GAD_CATEGORY where  $sStr" ;
    		break;

		 case 'Salesperson':
            $sSql = " SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('Salesperson')";
								$campaign_db = 1;
         break;

		// added New filters
		 case 'Sales Planner':
            $sSql = " SELECT  count(1) as cnt 
                                FROM ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('Sales Planner')";
								$campaign_db = 1;
         break;
/*
		 case 'Offer Industry':
         $sSql = " SELECT  count(1) as cnt 
                                FROM ".$schema_name.".ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('Offer Industry')";
         break;
		 
		 case 'TNS Industry':
         $sSql = " SELECT  count(1) as cnt 
                                FROM ".$schema_name.".ADM_OPTION_LOOKUP
                                where $sStr AND UPPER(KEY_NAME)=UPPER('TNS Industry')";
         break;
*/		 // END OF Block added New filters
        	   
	  }	
	  
      $oReportDao = new ReportDAO();
	  $aResult = $oReportDao->getsearchResult($sSql,$campaign_db);
	  unset($oReportDao);
	  return  $aResult[0]['CNT'];
	}
  	
  	public static function getSearchTableLabels()
    {
        if(sfContext::getInstance()->getRequest()->hasParameter('txtReportName'))
        {
                sfContext::getInstance()->getUser()->setAttribute("displaynms", self::$aReportSrchTblLabels);
                return self::$aReportSrchTblLabels;
        }
        elseif(sfContext::getInstance()->getUser()->hasAttribute("displaynms"))
        {
                return sfContext::getInstance()->getUser()->getAttribute("displaynms");
        }        
    }
    
    public static function getReportNameDtInfo($oRequest = null)
    {	
		if(sfContext::getInstance()->getRequest()->hasParameter('txtReportName'))
        {	$sReportStartDt = $sReportEndDt = null;
        	list($sReportStartDt, $sReportEndDt) = ReportUtils::$aReportStartEndDt;
        	$aToSet['start_date'] = $sReportStartDt;
			$aToSet['end_date'] = $sReportEndDt;
			$aToSet['report_id'] =sfContext::getInstance()->getRequest()->getPostParameter('hiddenRunId');
			
			if($oRequest->getParameter('txtReportName'))
			{
				$aToSet['report_name'] = $oRequest->getParameter('txtReportName');
			}
			else
			{
				$aToSet['report_name'] = $oRequest->getPostParameter('txtReportName');
			}
        	sfContext::getInstance()->getUser()->setAttribute("report_info", $aToSet);
        }
        
        if(sfContext::getInstance()->getUser()->hasAttribute("report_info"))
        {
               return sfContext::getInstance()->getUser()->getAttribute("report_info");
        }
        return array();  
    }
    
	public static function getMetricsDimensions()
	{
	    $oReportDao =  new ReportDAO();
		$aResult = $oReportDao->getMetrics();
		
		for($i=0 ; $i<count($aResult);$i++)
		{
		    $aRes[$aResult[$i]['STR_VALUE_ID']] = $aResult[$i]['DISPLAY'];	    
		}
		unset($oReportDao);
		return $aRes;
	}
	
	public static function deleteReport($iRptID)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->doDeleteRecord($iRptID);
		unset($oReportDao);
		return $returnvalue;
	}
	
	/*
	 *this function is used to save schedule report 
	 **/
	public static function saveScheduleReport($aParam,$sAction)
	{
		$oReportDao =  new ReportDAO();
		if($sAction == 'insert')
		{
			$returnvalue = $oReportDao->insertScheduleReportRecord($aParam);
		}
		else
		{
			$returnvalue = $oReportDao->updateScheduleReportRecord($aParam);
		}
		
		if($returnvalue) { $oReportDao->setScheduledReportNextRunDate($returnvalue); }
		
		unset($oReportDao);
		return $returnvalue;
	}
	/*
	 *this function is used to delete schedule report 
	 **/
	public static function deleteScheduleReport($iRptID)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->doDeleteScheduleRecord($iRptID);
		unset($oReportDao);
		return $returnvalue;
	}
	
	public static function getScheduleReportData($iId = null)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getScheduleSavedReportRecord($iId);
		unset($oReportDao);
		return $returnvalue;

	}

	public static function checkReportNameExist($ReportName = null)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->IsReportNameExists($ReportName);
		unset($oReportDao);
		return $returnvalue;
	}
	
	public static function checkReportNameExistNew($ReportName = null)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->IsReportNameExistsNew($ReportName);
		unset($oReportDao);
		return $returnvalue;
	}
	
	public static function checkReportUpdateAllowed($ReportName = null, $iReportId = null)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->IsUpdateAllowed($ReportName, $iReportId);
		unset($oReportDao);
		return $returnvalue;
	}
	
	public static function getEmailAddress($iUserId)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getEmailAddress($iUserId);
		unset($oReportDao);
		return $returnvalue;
	}

	public static function getReportNameDateDtls($aInput = array())
	{
		if(sfContext::getInstance()->getRequest()->hasParameter('filename'))
        {	
        	sfContext::getInstance()->getUser()->setAttribute("report_dtls", $aInput);
        }
        
        if(sfContext::getInstance()->getUser()->hasAttribute("report_dtls"))
        {
               return sfContext::getInstance()->getUser()->getAttribute("report_dtls");
        }
        return array();  
	}
	
	function calculateDateRange($sSerialisePost = null)
	{
		if($sSerialisePost)
		{
			$aSerialisePost = unserialize($sSerialisePost);
			$date_range = $aSerialisePost['date_range'];
			
			if($date_range==1)
			{
				$PreDefineDropDown = $aSerialisePost['PreDefineDropDown'];
				switch($PreDefineDropDown)
				{
					case 'Today':
						$sStartDt = date('m/d/Y', strtotime('now'));
						$sEndDt = '';
					break;
					
					case 'Yesterday':
						$sStartDt = date('m/d/Y', strtotime('-1 day'));
						$sEndDt = '';
					break;
					
					case 'Week to date':
						$sStartDt = date('m/d/Y',strtotime('Last Sunday'));
						$sEndDt = date('m/d/Y',strtotime('now'));
					break;
					
					case 'Past 7 days':
						$sStartDt = date('m/d/Y',strtotime('-7 days'));
						$sEndDt = date('m/d/Y', strtotime('-1 day'));
					break;
					
					case 'Last week':
						$sStartDt = date('m/d/Y',strtotime('Last Sunday',strtotime('Last week')));
						$sEndDt = date('m/d/Y',strtotime('-1 week saturday'));
					break;
					
					case 'Month to date':
						$sStartDt = date('m/01/Y');
						$sEndDt = date('m/d/Y',strtotime('now'));
					break;
					
					case 'Past 30 days':
						$sStartDt = date('m/d/Y',strtotime('-30 days'));
						$sEndDt = date('m/d/Y', strtotime('-1 day'));
					break;
					
					case 'Last month':
						$sStartDt = date('m/01/Y',strtotime('last month'));
						$sEndDt = date('m/d/Y',(strtotime('this month',strtotime(date('m/01/y'))) - 1));
					break;

				        case 'Year to date':
				                $sStartDt = date('01/01/Y');
				                $sEndDt = date('m/d/Y',strtotime('now'));
				        break;


					
					case 'Year to date':
						$sStartDt = date('01/01/Y');
						$sEndDt = date('m/d/Y',strtotime('now'));
					break;
					
					default:
						$sStartDt=null;
						$sEndDt=null;
					break;
				}
			}
			elseif($date_range==2)
			{
				$txtName = $aSerialisePost['txtName'];
				$iDays = (int)$txtName;
				
				$LastDropDown = $aSerialisePost['LastDropDown'];
				if($LastDropDown=='Days')
				{
					$sStartDt = date('m/d/Y',strtotime('-'.$txtName.'days'));
					$sEndDt = date('m/d/Y',strtotime('now'));
				}
				elseif($LastDropDown=='Weeks')
				{	
					$sStartDt = date('m/d/Y',strtotime('Last Sunday',strtotime('-'.$txtName.'weeks')));
					$sEndDt = date('m/d/Y',strtotime('-1 week saturday'));
				}
				else
				{
					$sStartDt=null;
					$sEndDt=null;
				}
			}
			elseif($date_range==3)
			{
				$sStartDt = trim($aSerialisePost['txtStartDt']);
				( !preg_match('/^(0[1-9]|1[0-2])[\/](0[1-9]|[1-2][0-9]|3[0-1])[\/][0-9]{4}$/', $sStartDt) ) ? $sStartDt = null : array_push($aDateRange, $sStartDt);
				
				$sEndDt = trim($aSerialisePost['txtEndDt']);
				( !preg_match('/^(0[1-9]|1[0-2])[\/](0[1-9]|[1-2][0-9]|3[0-1])[\/][0-9]{4}$/', $sEndDt) ) ? $sEndDt = null : array_push($aDateRange, $sEndDt);
				
				if($sStartDt!='' && $sEndDt=='')
				{
					$sSqlStartDt = $sStartDt;
				}
				elseif($sStartDt!='' && $sEndDt!='')
				{
					$sSqlStartDt = $sStartDt;
					$sSqlEndDt = $sEndDt;
				}
			}
			
			if($sStartDt!='' && $sEndDt=='')
			{
				$aDisplayDateRange = array($sStartDt);
			
			}
			elseif($sStartDt!='' && $sEndDt!='')
			{
				$aDisplayDateRange = array($sStartDt, $sEndDt);
			}
		}
		return $aDisplayDateRange;
	}
	
	public static function getReportStatusData($ids)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportStatusData($ids);
		unset($oReportDao);
		return $returnvalue;
	}
	
	public static function generateReportHistory($aQueryParam = array())
	{
		$oReportDao =  new ReportDAO();
		$returnSql = $oReportDao->generateReportHistory($aQueryParam);
		unset($oReportDao);
		return $returnSql;
	}
	
public static function getOptionList($sKey,$sClass=null,$sSelectedOption="",$bBlank=false, $bOnChage=false)
  {
    $aResult = array();
    $sHtml = "";
	$oReportDao = new ReportDAO();
	switch ($sKey) 
	{
		case 'Creative Type':
		// $sSql = "SELECT TYPE as DISPLAY , ROWNUM as STR_VALUE_ID FROM adq_creative_type";
		
		$sSql = "SELECT KEY_NAME,DISPLAY,DISPLAY as STR_VALUE_ID FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)='CREATIVE TYPE'";
		break;
		case 'Source Id':
		$sSql = "SELECT KEY_NAME,DISPLAY,STR_VALUE_ID FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)='SOURCE ID'";
		break;
		//case 'Bit Flags':
		//$sSql = "SELECT KEY_NAME,DISPLAY,STR_VALUE_ID FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)='BIT FLAG'";
		//break;
		case 'Creative Vendor':
		$sSql = "SELECT KEY_NAME,DISPLAY,STR_VALUE_ID FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)='CREATIVE VENDOR'";
		break;
		case 'ATF Value':
		$sSql = "SELECT KEY_NAME,DISPLAY,STR_VALUE_ID FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)='ATF VALUE'";
		break;
		case 'Tag Type':
		$sSql = "SELECT KEY_NAME,DISPLAY,STR_VALUE_ID FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)='TAG TYPE'";
		break;

		case 'Creative Format Type':
		$sSql = "SELECT KEY_NAME,DISPLAY,STR_VALUE_ID FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)='REPORT TYPE SPLASH TEMPLATE'";
		//$sSql = "SELECT 'CREATIVE FORMAT TYPE',DISPLAYNAME AS DISPLAY,ID AS STR_VALUE_ID FROM gac_data.gac_adproduct ORDER BY ID ";
		break;
		
		case 'Ad Group Name':
		$sSql = "SELECT 'Ad Group Name' as KEY_NAME,'['||HUB_NAME ||' ] ' || AD_GROUP_NAME as DISPLAY,preset_id as STR_VALUE_ID FROM ADQ_HUB_PRESET WHERE ACTIVE='Approved' and ad_id is not null and AD_GROUP_NAME is not null order by DISPLAY  ";
		break;

		case 'Device OS':
		$sSql = "SELECT KEY_NAME,DISPLAY,STR_VALUE_ID FROM ADM_OPTION_LOOKUP WHERE ACTIVE=1 AND UPPER(KEY_NAME)='DEVICE OS'";
		break;
	

	}
	
	if($sKey=='Source Id' || $sKey=='ATF Value' || $sKey=='Tag Type' || $sKey=='Ad Group Name' || $sKey=='Creative Format Type' || $sKey == 'Device OS')
	{
		$preselected_text='';
		if($sKey=='Ad Group Name')
		{
			$preselected_text='Select Ad Group Name';
		}
		
			$aResult = $oReportDao->getsearchResult($sSql,1);
		


		$sSelectTag = ($bOnChage) ? "<select name = '".str_replace(" ","",$sKey)."' id='".str_replace(" ","",$sKey)."' class='".$sClass."' onchange='javascript:changeLabelClass(this.options[this.selectedIndex].id);' >"
	    : "<select name = '".str_replace(" ","",$sKey)."' id='".str_replace(" ","",$sKey)."' class='".$sClass."'>";
	    $sHtml.=  $sSelectTag;
	    if($bBlank)
	    {
	      $sIsSelected = (!$sSelectedOption) ? "selected =\"selected\"" : null;
	      $sHtml.="<option value='' $sIsSelected >$preselected_text</option>\n";
	    }

		if($sKey == 'Device OS')
		{
			$sHtml.="<option value='_' >All</option>\n";
		}
	    foreach($aResult as $value)
	    {
	      $sSelected= ($value['STR_VALUE_ID'] == $sSelectedOption)?'selected':'';
	      $sHtml.='<option id= "'.$value['DISPLAY'].'" value="'.$value['STR_VALUE_ID'].'" '.$sSelected.' >'.$value['DISPLAY'].'</option>'."\n";
	    }
	    $sHtml.=" </select>";
	    return $sHtml;
	}
	else
	{
		$aResult = $oReportDao->getsearchResult($sSql,1);
		$sSelectTag = ($bOnChage) ? "<select name = '".str_replace(" ","",$sKey)."' id='".str_replace(" ","",$sKey)."' class='".$sClass."' onchange='javascript:changeLabelClass(this.options[this.selectedIndex].id);' >"
	    : "<select name = '".str_replace(" ","",$sKey)."' id='".str_replace(" ","",$sKey)."' class='".$sClass."'>";
	    $sHtml.=  $sSelectTag;
	    if($bBlank)
	    {
	      $sIsSelected = (!$sSelectedOption) ? "selected =\"selected\"" : null;
	      $sHtml.="<option value='' $sIsSelected ></option>\n";
	    }
	    foreach($aResult as $value)
	    {
	      $sSelected= ($value['DISPLAY'] == $sSelectedOption)?'selected':'';
	      $sHtml.='<option id= "'.$value['DISPLAY'].'" value="'.$value['DISPLAY'].'" '.$sSelected.' >'.$value['DISPLAY'].'</option>'."\n";
	    }
	    $sHtml.=" </select>";
	    return $sHtml;
	}
  }
  
 public static function getScheduleReportStatus($iReportId)
 {
 		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getScheduleReportStatus($iReportId);
		unset($oReportDao);
		return $returnvalue;
 }
 
  public static function getReportForUpdate($ReportName = null, $iReportId = null)
  {
	$oReportDao =  new ReportDAO();
	$returnvalue = $oReportDao->getUpdateId($ReportName, $iReportId);
	unset($oReportDao);
	return $returnvalue;
  }
  
	public static function getSavedReportcustomData($iId = null)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getSavedReportcustomData($iId);
		unset($oReportDao);
		return $returnvalue;
	}
	
	public static function getReportHeaderData($report_id)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportHeaderData($report_id);
		unset($oReportDao);
		return $returnvalue;
	}

	public static function getReportScheduleId($iId)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportScheduleId($iId);
		unset($oReportDao);
		return $returnvalue;
	}
	
	public static function getDDDimension($ireportType)
	{
		$sUserJsWritePath = sfConfig::get("app_userjs_read_path");
		$sDDJsUrl = sfConfig::get("app_js_include_path");
		$reportMasterFileName = "ReportMasterData_$ireportType".'.js';
		if(sfConfig::get('app_instance_id')!='')
		{
			$reportMasterFileName = "ReportMasterData_$ireportType".sfConfig::get('app_instance_id').".js";
		}
		return $getDimension  = "<script>
									actionOverlayDiv('show');
									populateWidgets('".$sUserJsWritePath."ReportData_".session_id().".js', '".$sDDJsUrl.$reportMasterFileName."', 'Report','getReportFilter','','');
									actionOverlayDiv('hide');
								</script>";
	}
	
	
/*
	 *this function is used to save schedule report history 
	 **/
	public static function saveScheduleHistoryReport($aParam)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->insertScheduleHistoryReportRecord($aParam);
		unset($oReportDao);
		return $returnvalue;
	}

	//$this->oLaunchtimezone = ReportUtils::getOptionListTimeZone('Launch Timezone','',$aParam['LaunchTimezone']);
  public  static function getOptionListTimeZone($sKey,$sClass=null,$sSelectedOption="",$bBlank=false, $bOnChage=true)
  {
	  
    $aResult = array();
    $sHtml = "";
    $oReport =  new ReportDAO();
    $aResult = $oReport->findByKeyName($sKey);


    $sSelectTag = ($bOnChage) ? "<select name = '".str_replace(" ","",$sKey)."' id='".str_replace(" ","",$sKey)."' class='".$sClass."' onchange='javascript:getAvailableTimeZoneData(this.options[this.selectedIndex].value);' >"
    : "<select name = '".str_replace(" ","",$sKey)."' id='".str_replace(" ","",$sKey)."' class='".$sClass."'>";
    $sHtml.=  $sSelectTag;
    if($bBlank)
    {
      $sIsSelected = (!$sSelectedOption) ? "selected =\"selected\"" : null;
      $sHtml.="<option value='' $sIsSelected ></option>\n";
    }
    foreach($aResult as $value)
    {
	  $sSelected= ($value['STR_VALUE_ID'] == $sSelectedOption)?'selected="selected"':'';
      $sHtml.='<option id= "'.$value['DISPLAY'].'" value="'.$value['STR_VALUE_ID'].'" '.$sSelected.' >'.$value['DISPLAY'].'</option>'."\n";
    }
    $sHtml.=" </select>";
    return $sHtml;
  }

  public static function getReportParentDisplayName($columnVal,$type='')
  {
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportParentDisplayName($columnVal,$type);
		unset($oReportDao);
		return $returnvalue;
   }

  public static function getAvailableDataTimeZone($timezone='',$reportType='')
  {
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getAvailableDataTimeZone($timezone,$reportType);
		unset($oReportDao);
		return $returnvalue;
   }




  public static function getSchedulingOptionList($sKey = null, $sClass=null,$sSelectedOption=null)
  {
    $aResult = array();
    $sHtml = "";
    $oDao =  new OptionDAO();
    $aResult = $oDao->findByPageName($sKey);
    $sHtml.='<select id="'.$sKey.'" class="'.$sClass.'" name ="'.str_replace(" ","",$sKey).'" onchange="javascript:document.frmschedulereport.schedule_criteria[0].checked=true" >';
    foreach($aResult as $value)
    {
      $sSelected= ($value['STR_VALUE_ID'] == $sSelectedOption)?'selected':'';
      $sHtml.='<option id= "'.$value['DISPLAY'].'" value="'.$value['DISPLAY'].'" '.$sSelected.' >'.$value['DISPLAY'].'</option>'."\n";
    }
    $sHtml.='</select>';
    return $sHtml;
  }


  public static function getLastDateRange($Key=null,$sClass=null,$sSeletedValue=null,$onchangeScript=null)
  {
    $sHtml = "";
    $aVal = array('Days','Weeks');

    if($onchangeScript)
    {
      $sHtml.="<select name='".$Key."' id='".$Key."' class='".$sClass."' $onchangeScript>";
    }
    else
    {
      $sHtml.="<select name='".$Key."' id='".$Key."' class='".$sClass."' onchange='javascript:document.frmgeneratereport.date_range[1].checked=true;'>";
    }
    for($iCount = 0;$iCount<count($aVal);$iCount++)
    {

      if($sSeletedValue == $aVal[$iCount])
      {
        $sHtml.="<option selected value=".$aVal[$iCount]." >".$aVal[$iCount]."</option>";
      }
      else
      {
        $sHtml.="<option value=".$aVal[$iCount]." >".$aVal[$iCount]."</option>";
      }
    }
    $sHtml.=" </select>";

    return $sHtml;

  }

		public function ReportThroughAPI($reportArray,$functionCall)
		{
			/*
			// Setting params
			$wsdlFile = sfConfig::get('app_adq_api_wsdl');
			$uri = 'http://app69.glam.colo/glamadaptservices_qa/api/services/v1.0.3_sp2/v1.0.5/webservices/ReportService.php';
			$user = $this->getUser()->getAttribute('username');
			$password = $this->getUser()->getAttribute('password');
	
			//this should be always 1
			$appId = 1;
			//$instanceId = sfConfig::get('app_instance_id');
			//echo SF_ENVIRONMENT;
			
			$instanceId = sfConfig::get('app_instance_id');

			//exit;
			if($instanceId=='')
			{
				$instanceId = 1000;
		
			}
			
			*/
			$uri = 'http://app69.glam.colo/glamadaptservices_qa/api/services/v1.0.3_sp2/v1.0.5/webservices/ReportService.php';
			$instanceId = 1000;
			$wsdlFile 	=	"/home/prod/www/metrics.glam.com/public/wsdl/glamadaptReportService_alpha.wsdl";
			$user		= "abhijeetk";
			$password	= "abhi@123";
			$appId = 1;
			
			// Creating Client
			$client = new SoapClient($wsdlFile, array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => 1));


			// Setting Soap Headers
			$soapHeaders = array();
			//currently wsdl doenot support instance id 	
		    $soapHeaders[] = new SoapHeader($uri, 'instanceId', array($instanceId), true);
			$soapHeaders[] = new SoapHeader($uri, 'BasicAuth', array($user, $password), true);
			$soapHeaders[] = new SoapHeader($uri, 'appId', array($appId), true);
			$soapHeaders[] = new SoapHeader($uri, 'transactionId', array(), true);
			$client->__setSoapHeaders($soapHeaders);
			
			
			try{
				if($functionCall=='getReportResult')
				{
					$inputdata =explode(',',$reportArray);
					$result = $client->$functionCall((int)$inputdata[0],$inputdata[1]);
				}
				else
				{
					$result = $client->$functionCall($reportArray); // $reportArray is an arry of Report objects passed as argument.
				}
				
				return $result;
			}
			catch(SoapFault $e)
			{
				//echo $logdata;
				//echo "###############################";
				//echo "<pre>";
				//print_r($client->__getLastRequest());
				//exit;

			
				$return_error = array();
			   if (isset($e->detail)) 
				{
					if (count($reportArray) > 1)
					{
						$statusList = (array)$e->detail->status_array->status;
					}
					else
					{
						$statusList = array($e->detail->status_array->status);
					}
					
					if (!is_array($statusList)) 
					{

					 $logs = "SOAP fault detail's status array is not an array";
					
					 $return_error['success'] = 0;
					 $return_error['errorCode'] = '12345'; // for testing purpose
					 $return_error['errorMessage'] = 'Request could not process';

					 
					}
					else
					{
						$logs  = print_r($statusList,1);
					}

					 $return_error['success'] = $statusList[0]->success;
					 $return_error['errorCode'] = $statusList[0]->errorCode;
					 $return_error['errorMessage'] = $statusList[0]->errorMessage;

			   }
			   else
			   {
				  $logs ='Missing SOAP fault detail field';
				  $return_error['success'] = 0;
				  $return_error['errorCode'] = '12345'; // for testing purpose
                 $return_error['errorMessage'] = ' code: '. $e->faultcode. ' Reason: '. $e->faultstring . '  username: '.$user;

			   }

			   $logdata  = ' username: '.$user.' function call: '.$functionCall .' INPUTS: '. print_r($reportArray,1) . "\n" ;
			   $logdata .= ' code: '. $e->faultcode. ' Reason: '. $e->faultstring . ' '.$logs ;

			  

				error_log($logdata,"3","/glamadapt_files/log/adq/api_error.log");
				
				//echo $logdata;
				//echo "###############################";
				//echo "<pre>";
				//print_r($client->__getLastRequest());
				//exit;


				return  $return_error;
			  }

		}

		public function checkUserEmails($emails)
		{

			$oReportDao =  new ReportDAO();
			$returnvalue = $oReportDao->checkUserEmails($emails);
			unset($oReportDao);
			return $returnvalue;
		}
		
		public function generateLookUP($report_type){
			$oReportDao =  new ReportDAO();
			$returnvalue = $oReportDao->generateLookUP($report_type);
			unset($oReportDao);
			return $returnvalue;
		}
		
		public function reverseLookup($report_type)
		{
			$oReportDao =  new ReportDAO();
			$returnvalue = $oReportDao->generateLookUP($report_type);
			$final_arr=array();
			foreach($returnvalue as $keyname1=>$vals_arr1)
			{
				foreach($vals_arr1 as $keyname2=>$vals_arr2)
				{
					
					foreach($vals_arr2 as $finalkey=>$finalval)
					{
						$final_arr[$keyname2][$finalkey] = $finalval;
					}	
					
				}
			}

			return $final_arr;
		}
		
		
public static function getOptionListBoolean($Key=null,$sClass=null,$sSelectedValue=null,$sType,$sTableFieldName,$blank=null)
  {
	if($sSelectedValue==null)
   	{
   		$sSelectedValue='';
   	}
    $sHtml = "";
    $sHtml.='<select name="reportfilter['.$sType.']['.$sTableFieldName.']" id="'.$Key.'" class="'.$sClass.'">';

  	 if($sSelectedValue=='')
	 {
	 	$nullselected  = 'selected';
	 }
	 elseif($sSelectedValue=='0')
	 {
	   	$zeroselected  = 'selected';
	 }
	 else
	 {
	 	$oneselected  = 'selected';
	 }

	 if($blank)
	 {
		$sHtml.="<option $nullselected  value=''></option>";
     }
	 $sHtml.="<option $oneselected value='1'>Yes</option>";
	 $sHtml.="<option $zeroselected value='0'>No</option>";
	 
	/* 
    foreach($aVal as $keys=>$values)
    {
      if((int)$sSelectedValue === (int)$keys)
      {
      	
        $sHtml.="<option selected value=".$keys."  >".$values."</option>";
      }
      else
      {
        $sHtml.="<option value=".$keys." >".$values."</option>";
      }
    }
    */
    $sHtml.=" </select>";
    
    return $sHtml;

  }


  public function getReportType()
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportType();
		unset($oReportDao);
		return $returnvalue;
	}

public static function getOptionListReportType($sKey,$sClass=null,$sSelectedOption="",$bBlank=false, $bOnChage=false, $controlType='')
  {
  
	$aResult = array();
	$sHtml = "";
	$oDao =  new ReportDAO();
	$aResult = $oDao->findByKeyName($sKey);
	$campaignReportType = self::getCampaignReportType();
	$reportTypeArr = array_flip(ReportUtils::getReportType());

	//echo "<pre>";
	//print_r($reportTypeArr);
	//exit;


	//echo "##########".$sSelectedOption."#########";

	if (empty($controlType)){
		
		$sSelectTag = ($bOnChage) ? "<select name = 'report_type' id='report_type' class='".$sClass."' onchange='javascript:showhidebox(this.options[this.selectedIndex].id);' >"
		: "<select name = 'report_type' id='report_type' class='".$sClass."'>";
		$sHtml.=  $sSelectTag;
		if($bBlank)
		{
		  $sIsSelected = (!$sSelectedOption) ? "selected =\"selected\"" : null;
		  $sHtml.="<option value='' $sIsSelected ></option>\n";
		}
		
		foreach($aResult as $value)
		{
			if($value['STR_VALUE_ID']!=$campaignReportType && $value['STR_VALUE_ID']!=$reportTypeArr['Product Report'] && $value['STR_VALUE_ID']!=$reportTypeArr['Inventory Report']	 )
			{
				$sSelected= ($value['STR_VALUE_ID'] == $sSelectedOption)?'selected':'';
				$sHtml.='<option id= "'.$value['DISPLAY'].'" value="'.$value['STR_VALUE_ID'].'" '.$sSelected.' >'.$value['DISPLAY'].'</option>'."\n";
			}
		}
		$sHtml.=" </select>";
		return $sHtml;
	}else{
		if (empty($sSelectedOption)){
			$sSelectedOption = 1;
		}
		
		
		foreach($aResult as $value)
		{
				
		  $sSelected = ($value['STR_VALUE_ID'] == $sSelectedOption)?'checked':'';
		  $sHtml .= '<label class="radio_label"><input type="radio" onclick="var checkval = showhidebox('.$value['STR_VALUE_ID'].');" '.$sSelected.' name="report_type" value="'.$value['STR_VALUE_ID'].'" class="radio_button"></label><label class="form_label_med">'.$value['DISPLAY'].'</label>';
		
		}
		
		return $sHtml;
	
	}	
  }

	public static function getCityByIds($CityIds)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT Name || '(#' || ID || ')' as NAME  FROM gad_city WHERE ID IN ($CityIds)";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$city_id_str .= "'".$row['NAME']."',";
		}
		$city_id_str = rtrim($city_id_str,",");
		return $city_id_str;
	}


	public static function getCityByNames($sCityNames)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$city_part_arr = explode(',',$sCityNames);
		$city_id_str = '';
		foreach($city_part_arr as $keyCity=>$valCity)
		{
			$valCityArr = explode('(#',$valCity);
			$city_id_str .= "'".trim($valCityArr[1],")'")."',";
		}
		$city_id_str = rtrim($city_id_str,",");
		return $city_id_str;
		
	}

public static function getCountryByIds($CountryIds)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		//$CountryIds = "'".str_replace(",","','",$CountryIds)."'";
		$sSql = "SELECT DISTINCT(country_contract_name) as NAME FROM adq_affiliate WHERE country_contract IN ($CountryIds)";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$country_id_str .= "'".$row['NAME']."',";
		}
		$country_id_str = rtrim($country_id_str,",");
		return $country_id_str;
	}


	public static function getCountryByNames($sCountryNames)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT DISTINCT(country_contract) as ID  FROM adq_affiliate WHERE UPPER(country_contract_name) IN ($sCountryNames)";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$country_id_str .= "'".$row['ID']."',";
		}
		
		$country_id_str = rtrim($country_id_str,",");
		return $country_id_str;
	}

	public static function getIncludeAffByIds($IncludeAffIds)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT AFF_LIST_DISP_COL FROM lm_affiliate_map WHERE AFF_LIST_COL_INDEX IN ($IncludeAffIds)";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_Aff_id_str .= "'".$row['AFF_LIST_DISP_COL']."',";
		}
		$include_Aff_id_str = rtrim($include_Aff_id_str,",");
		return $include_Aff_id_str;
	}


	public static function getIncludeAffByNames($sIncludeAffNames)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT AFF_LIST_COL_INDEX  FROM lm_affiliate_map WHERE UPPER(AFF_LIST_DISP_COL) IN ($sIncludeAffNames)";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_Aff_id_str .= "'".$row['AFF_LIST_COL_INDEX']."',";
		}
		
		$include_Aff_id_str = rtrim($include_Aff_id_str,",");
		return $include_Aff_id_str;
	}



	public static function getSearchSql($sSelectClumns = null, $sWhereClause = null, $sIdentifier = null)
        {

           /*
                * to resolved creative getting displayed repeted times issue
                * issue was:- its displaying adwise creative
                * solution: if user not selected ad realted columns then sql will contain join on ad table realted join else without join on ad table.
                */
				//started execution timer
				

                 if($sSelectClumns)
                {
                       $iIsAdColumnNotExist = 1;
                       if(preg_match("/GAD_AD_CREATIVE.GAD_AD_ID|GAD_AD.GAD_ORDER_ID|GAD_ORDER.GAD_ORDER_ID|GAD_AD.NAME|GAD_ORDER.NAME/", $sWhereClause))
                        {
                                 $iIsAdColumnNotExist = 0;
                        }

                        // explode reocord for select query.
                                $aSelectClumns = explode(",", $sSelectClumns);

                        $aToCheck = array("GAD_AD.NAME as GAD_AD_NAME", "GAD_AD.GAD_AD_ID", "GAD_ORDER.NAME as GAD_ORDER_NAME", "GAD_ORDER.DART_ORDER_ID" , "GAD_AD.DART_AD_ID", "GAD_AD_CREATIVE.CLICK_THROUGH_URL AS AD_CLICK_THROUGH_URL", "GAD_AD_CREATIVE.OVERRIDE_CLICK_THROUGH_URL AS OVERRIDE_CLICK_THROUGH_URL");

                        foreach($aSelectClumns as $iKey => $sSelectClumnText)
                        {
                                if( in_array(trim($sSelectClumnText), $aToCheck) )
                                {
                                        $iIsAdColumnNotExist = 0;
                                    break;
                                }
                        }

                }

                
                //preparing IN clause for user group credentials, starts
                if($sIdentifier == "adid"||  $sIdentifier == "aid")
                {
                        $aInClause = array();
                        $sHighPriorityAdvtrIds = Utils::getGroupCredential("highpriorityadvertiser");
                        $sHighPriorityAdvtrIds = Utils::getInClause($sHighPriorityAdvtrIds,'GAD_AD.GAD_ADVERTISER_ID');
                        ( $sHighPriorityAdvtrIds ) ? $aInClause[] = " GAD_AD.GAD_ADVERTISER_ID  $sHighPriorityAdvtrIds " : null;
                        $sHighPriorityOrderIds = Utils::getGroupCredential("highpriorityorder");
                        $sHighPriorityOrderIds = Utils::getInClause($sHighPriorityOrderIds,'GAD_AD.GAD_ORDER_ID');
                        ( $sHighPriorityOrderIds ) ? $aInClause[] =  "  GAD_AD.GAD_ORDER_ID  $sHighPriorityOrderIds " : null;
                        $sAdIdsList = Utils::getGroupCredential("ad");
                        $sAdIdsList = Utils::getInClause($sAdIdsList,'GAD_AD.GAD_AD_ID');
                        if($sIdentifier == "aid")
                        {
                            ($sAdIdsList) ? $aInClause[] = " GAD_AD.GAD_AD_ID $sAdIdsList OR GAD_AD.GAD_AD_ID is null " : null;
                        }
                        else
                        {
                            ($sAdIdsList) ? $aInClause[] = " GAD_AD.GAD_AD_ID $sAdIdsList " : null;
                        }
                        $sInClause = ( count($aInClause) ) ? implode(" OR ", $aInClause) : null;

                        if($sWhereClause != "")
                        {
                        	$sInClause = ($sInClause) ? sprintf(" AND ( %s )", $sInClause) : null;
                        }
                }
                else
                {

                	$sAdvertiserIdsList = Utils::getGroupCredential("advertiser");
                	$sAndChar = ($sWhereClause != "") ? " AND " : "";
                	$sAdvertiserIdsList = Utils::getInClause($sAdvertiserIdsList,'GAD_CREATIVE.GAD_ADVERTISER_ID');
                	$sInClause = ($sAdvertiserIdsList) ? " $sAndChar (GAD_CREATIVE.GAD_ADVERTISER_ID $sAdvertiserIdsList )" : null;
                }

                //ends

                //(!$sWhereClause) ? $sWhereClause = " where ACTIVE=1 $sInClause " : $sWhereClause .= $sInClause;

                $sRowNum = " rownum < 500";
                if(!$sWhereClause)
                {
                 /*if(trim($sInClause) != "")
                 {
                 $sWhereClause = " where $sInClause ";
                 }*/
                 if(trim($sInClause) != "")
                        {  
                                $sWhereClause = " WHERE $sInClause AND ".$sRowNum;
                        }else
                        {
                         $sWhereClause = " WHERE ".$sRowNum;
                        }
                }
                else
                {
                 //$sWhereClause .= "  ".$sInClause;
                 $sWhereClause .= "  ".$sInClause." AND ".$sRowNum;
                }

                if($iIsAdColumnNotExist && $sIdentifier != "aid")
                {
					
                                 return "SELECT $sSelectClumns
                                        FROM GAD_CREATIVE INNER JOIN GAD_ADVERTISER ON GAD_ADVERTISER.GAD_ADVERTISER_ID = GAD_CREATIVE.GAD_ADVERTISER_ID
                                        LEFT OUTER JOIN GAD_OPTION ON UPPER(GAD_OPTION.KEY_NAME) = 'CREATIVE TYPE'
                                        AND TO_CHAR(GAD_CREATIVE.TYPE) = GAD_OPTION.STR_VALUE_ID
                                        LEFT OUTER JOIN GAD_AD_SIZE ON GAD_AD_SIZE.GAD_AD_SIZE_ID = GAD_CREATIVE.CREATIVE_SIZE_ID
                                        $sWhereClause";
                }
                else
                {
					$sSelectClumns  = str_replace('rownum as srno,','',$sSelectClumns);
                    return "SELECT ROWNUM  AS srno,A.* FROM (
							SELECT DISTINCT $sSelectClumns    FROM GAD_CREATIVE
                                    INNER JOIN GAD_ADVERTISER
                                       ON GAD_ADVERTISER.GAD_ADVERTISER_ID = GAD_CREATIVE.GAD_ADVERTISER_ID
                                    LEFT OUTER JOIN GAD_AD_CREATIVE
                                ON GAD_AD_CREATIVE.GAD_CREATIVE_ID = GAD_CREATIVE.GAD_CREATIVE_ID
                                    LEFT OUTER JOIN GAD_AD
                                ON GAD_AD.GAD_AD_ID = GAD_AD_CREATIVE.GAD_AD_ID
                                    LEFT OUTER JOIN GAD_ORDER
                                ON GAD_ORDER.GAD_ORDER_ID = GAD_AD.GAD_ORDER_ID
                                    LEFT OUTER JOIN GAD_OPTION
                                ON UPPER(GAD_OPTION.KEY_NAME) = 'CREATIVE TYPE' AND TO_CHAR(GAD_CREATIVE.TYPE) = GAD_OPTION.STR_VALUE_ID
                                    LEFT OUTER JOIN GAD_AD_SIZE
                                ON GAD_AD_SIZE.GAD_AD_SIZE_ID = GAD_CREATIVE.CREATIVE_SIZE_ID
									LEFT OUTER JOIN GAC_DATA.GAC_ADPRODUCT
								ON GAC_DATA.GAC_ADPRODUCT.ID = GAD_CREATIVE.EXT_GAC_ADPRODUCT
								
								$sWhereClause) A";
                }

        }
		
	public function getCreativeDataByAdId($ad_id,$creative_size,$creative_id='')
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getCreativeDataByAdId($ad_id,$creative_size,$creative_id);
		unset($oReportDao);
		return $returnvalue;
	}
	
	public function getReportTemplateName($reportID)
	{		
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportTemplateName($reportID);
		unset($oReportDao);
		return $returnvalue;
	}

	public function getReportTemplates()
	{		
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportTemplates();
		unset($oReportDao);
		return $returnvalue;
	}

	public function checkCreativeFormatForAdId($adID)
	{		
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->checkCreativeFormatForAdId($adID);
		unset($oReportDao);
		return $returnvalue;
	}


	public function buildInputStr($adID)
	{		
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->buildInputStr($adID);
		unset($oReportDao);
		return $returnvalue;
	}

	
	public function getImpressionFlagByNames($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT STR_VALUE_ID FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)=UPPER('BIT FLAG') AND ACTIVE=1 AND UPPER(DISPLAY) IN (".strtoupper($vals).")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_impression_id_str .= "'".$row['STR_VALUE_ID']."',";
		}
		$include_impression_id_str = rtrim($include_impression_id_str,",");
		return $include_impression_id_str;
	}

	public function getImpressionFlagById($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT DISPLAY FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)=UPPER('BIT FLAG') AND ACTIVE=1 AND UPPER(STR_VALUE_ID) IN (".$vals.")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_impression_str .= "'".$row['DISPLAY']."',";
		}
		$include_impression_str = rtrim($include_impression_str,",");
		return $include_impression_str;
	}

	public function getTagTypeByNames($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT STR_VALUE_ID FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)=UPPER('TAG TYPE') AND ACTIVE=1 AND UPPER(DISPLAY) IN (".strtoupper($vals).")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_impression_id_str .= "'".$row['STR_VALUE_ID']."',";
		}
		$include_impression_id_str = rtrim($include_impression_id_str,",");
		return $include_impression_id_str;
	}

	public function getTagTypeById($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT DISPLAY FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)=UPPER('TAG TYPE') AND ACTIVE=1 AND UPPER(STR_VALUE_ID) IN (".strtoupper($vals).")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_impression_str .= "'".$row['DISPLAY']."',";
		}
		$include_impression_str = rtrim($include_impression_str,",");
		return $include_impression_str;
	}

	public function getATFByNames($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT STR_VALUE_ID FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)=UPPER('ATF VALUE') AND ACTIVE=1 AND UPPER(DISPLAY) IN (".strtoupper($vals).")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_impression_id_str .= "'".$row['STR_VALUE_ID']."',";
		}
		$include_impression_id_str = rtrim($include_impression_id_str,",");
		return $include_impression_id_str;
	}

	public function getATFById($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT DISPLAY FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)=UPPER('ATF VALUE') AND ACTIVE=1 AND UPPER(STR_VALUE_ID) IN (".strtoupper($vals).")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_impression_str .= "'".$row['DISPLAY']."',";
		}
		$include_impression_str = rtrim($include_impression_str,",");
		return $include_impression_str;
	}

	public function checkCreativeExistForAdId($adID)
	{		
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->checkCreativeExistForAdId($adID);
		unset($oReportDao);
		return $returnvalue;
	}

	public function getCampaignReportType()
	{		
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getCampaignReportType();
		unset($oReportDao);
		return $returnvalue;
	}

	public function getReportLookupData($reportType,$field_type)
	{
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportLookupData($reportType,$field_type);
		unset($oReportDao);
		return $returnvalue;
	}

	public function getReportName($reportID){
	
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportName($reportID);
		unset($oReportDao);
		return $returnvalue;
	
	}
	
	public function getReportDisplayProperty($reportID){
	
		$oReportDao =  new ReportDAO();
		$returnvalue = $oReportDao->getReportDisplayProperty($reportID);
		unset($oReportDao);
		return $returnvalue;
	
	}


	public function getBrowserByNames($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT STR_VALUE_ID FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)=UPPER('BROWSER') AND ACTIVE=1 AND UPPER(DISPLAY) IN (".strtoupper($vals).")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_broswer_id_str .= "'".$row['STR_VALUE_ID']."',";
		}
		$include_broswer_id_str = rtrim($include_broswer_id_str,",");
		return $include_broswer_id_str;
	}

	public function getBrowserById($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT DISPLAY FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)=UPPER('BROWSER') AND ACTIVE=1 AND UPPER(STR_VALUE_ID) IN (".strtoupper($vals).")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$include_broswer_str .= "'".$row['DISPLAY']."',";
		}
		$include_broswer_str = rtrim($include_broswer_str,",");
		return $include_broswer_str;
	}


	public function getMetaCountryByNames($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT STR_VALUE_ID FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)='META COUNTRY' AND ACTIVE=1 AND UPPER(DISPLAY) IN (".strtoupper($vals).")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$meta_country_id_str .= "'".$row['STR_VALUE_ID']."',";
		}
		$meta_country_id_str = rtrim($meta_country_id_str,",");
		return $meta_country_id_str;
	}

	public function getMetaCountryByIds($vals)
	{
		$oReport = new ReportDAO();
		$oracleConn = $oReport->getCampaignDbconnection();
		$sSql = "SELECT DISPLAY FROM ADM_OPTION_LOOKUP where UPPER(KEY_NAME)='META COUNTRY' AND ACTIVE=1 AND UPPER(STR_VALUE_ID) IN (".$vals.")";
		$oStmt = oci_parse($oracleConn, $sSql);
		oci_execute($oStmt);
		while ($row = oci_fetch_assoc($oStmt))
		{
			$meta_country_str .= "'".$row['DISPLAY']."',";
		}
		$meta_country_str = rtrim($meta_country_str,",");
		return $meta_country_str;
	}

	public function getReportFilterData($filterName){
		$Sql = '';
		$this->filterSql = "";
		$List = ReportUtils::getInvertoryReportDimension();		
		$filterListArr 		= $List['F'];
		$filterName         = 	$filterListArr[$filterName];	 
			
			switch($filterName)
				{
				case 'Country Of Contract': 
								$Sql = "SELECT upper(country_contract) as ID, upper(country_contract_name) AS NAME from adq_coc";
								$this->dbType = 'inv';
								break;

				case 'Product': 
								$Sql = "select distinct(upper(ad_name)) as ID, upper(ad_name) as NAME from ADQ_INV_ADS where is_product = 1 and active=1";
								$this->dbType = 'inv';
								break;
				case 'Ad Size': 
								$Sql = "SELECT upper(GAD_AD_SIZE_ID) AS ID , upper(SIZE_NAME) AS NAME FROM GAD_AD_SIZE WHERE ACTIVE = 1";
								$this->dbType = 'inv';
								break;
				case 'Vertical': 
								//$Sql = "select upper(adq_list_name) as ID , upper(adq_list_name) as NAME from ADQ_AFF_LIST_CATEGORY where upper(list_category)='VERTICALS'";

								$Sql = "SELECT DISTINCT list_id as ID,
												  upper(verticals) as NAME
												FROM adq_aff_list_verticals
												WHERE verticals IS NOT NULL
												ORDER BY NAME";
								$this->dbType = 'inv';
								break;							
				case 'Network': 
								$Sql = "SELECT upper(NAME) as ID, upper(NAME) as name  FROM ADQ_NETWORK";
								$this->dbType = 'inv';
								break;	
				case 'Channel': 
								//$Sql =  "SELECT distinct(upper(channels))  as ID , upper(channels) as NAME  FROM adq_aff_list_channels where channels is not null";
								$Sql = "SELECT DISTINCT list_id as ID, upper(channels) as NAME
										   FROM adq_aff_list_channels
										  WHERE channels IS NOT NULL
									   ORDER BY NAME";
								
								$this->dbType = 'inv';
								break;	

				case 'Sub Channel': 
								//$Sql =  "SELECT distinct(upper(sub_channels))  as ID , upper(sub_channels) as NAME  FROM adq_aff_list_sub_channels where sub_channels is not null";

								$Sql = "SELECT DISTINCT list_id as ID, upper(sub_channels) as NAME
										   FROM adq_aff_list_sub_channels
										  WHERE sub_channels IS NOT NULL
									   ORDER BY NAME";

								$this->dbType = 'inv';
								break;	
				case 'Exclusion': 
								//$Sql = "select upper(adq_list_name) as ID , upper(adq_list_name) as NAME from ADQ_AFF_LIST_CATEGORY where upper(list_category)='EXCLUSION LISTS'";
								$Sql = "SELECT DISTINCT list_id as ID , upper(exclusion_list) as NAME
										   FROM adq_aff_list_exc_list
										  WHERE exclusion_list IS NOT NULL
									   ORDER BY NAME";

								$this->dbType = 'inv';
								break;

				case 'Site': 
								$Sql = "SELECT affiliate_id as ID , affiliate_id || ':' ||  upper(title) as NAME   FROM adq_affiliate";
								$this->dbType = 'inv';
								break;
				case 'Glam BT': 
								$Sql = "select distinct(upper(ad_name)) as ID, upper(ad_name) as NAME from ADQ_INV_ADS where is_bt = 1 ";
								$this->dbType = 'inv';
								break;	
				case 'Audience Partners': 
								$Sql = "select distinct(upper(ad_name)) as ID, upper(ad_name) as NAME from ADQ_INV_ADS where  is_ap = 1";
								$this->dbType = 'inv';
								break;

				case 'Contextual Channel': 
								$Sql = "select upper(channel_name) as ID , upper(channel_name) as NAME from adq_context_channels ";
								$this->dbType = 'inv';
								break;

				case 'Brand Safety': 
							$Sql = "select display as ID,  display as NAME from adm_option_lookup where key_name = 'Brand Safety'";
							$this->dbType = 'inv';
							break;

				case 'Site List': 
						$Sql = "select ID, ID as NAME as NAME from adm_option_lookup where key_name = 'Brand Safety'";
						$this->dbType = 'inv';
						break;

				case 'Paid Traffic': 
						$Sql = "select display as ID,  display as NAME from adm_option_lookup where key_name = 'Paid Traffic'";
						$this->dbType = 'inv';
						break;				

				
			}
		
		$aResult = array();
		if (!empty($Sql) || !empty($this->filterSql)){
			$oReportDao = new ReportDAO();
			if ($this->dbType == 'campaign'){			
				$aResult = $oReportDao->getsearchResult($Sql, 1);		  
			}else if ($this->dbType == 'inv'){	
				$aResult = $oReportDao->getsearchResult($Sql, 2);
			}else{
				$aResult = $oReportDao->getsearchResult($Sql, 0);
			}
			unset($oReportDao);
		}
		
		//echo $this->dbType;
		//echo "sql=".$this->filterSql;
		//echo "count=".count($aResult);exit;
		return  $aResult;
		
	}
	
	
	public function getReportFilterGeoData($country_code){
	
		$this->filterSql = "select id, name from gad_country where id is not null and active=1";
		$this->dbType = 'inv';
		
		$oReportDao = new ReportDAO();
		if ($this->dbType == 'inv'){			
			$countrysult = $oReportDao->getsearchResult($this->filterSql, 2);		  
		}else{
			$countrysult = $oReportDao->getsearchResult($this->filterSql, 0);
		}
		
		$this->filterSql = "select id, region_code, name from gad_state_region";
		$this->dbType = '';
		
		if ($this->dbType == 'inv'){			
			$stateResult = $oReportDao->getsearchResult($this->filterSql, 2);		  
		}else{
			$stateResult = $oReportDao->getsearchResult($this->filterSql, 0);
		}
		
		$this->filterSql = "SELECT ID as ID, NAME AS NAME FROM GAD_DMA ORDER BY UPPER(NAME)";
		$this->dbType = '';
		
		if ($this->dbType == 'inv'){			
			$dmaResult = $oReportDao->getsearchResult($this->filterSql, 2);		  
		}else{
			$dmaResult = $oReportDao->getsearchResult($this->filterSql, 0);
		}
		//unset($oReportDao);
		
		$this->filterSql = "SELECT ZIP_CODE as ID, ZIP_CODE AS NAME FROM GAD_ZIPCODE WHERE ZIP_CODE IS NOT NULL";
		$this->dbType = 'inv';
		
		if ($this->dbType == 'inv'){			
			$zipcodeResult = $oReportDao->getsearchResult($this->filterSql, 2);		  
		}else{
			$zipcodeResult = $oReportDao->getsearchResult($this->filterSql, 0);
		}
		unset($oReportDao);
		
		$resultSet[]['country']= $countrysult;
		$resultSet[]['states']= $stateResult;
		$resultSet[]['dma']= $dmaResult;
		$resultSet[]['zip_code']= $dmaResult;
		return $resultSet;		
	}
	
	public function getReportCityFilterGeoData($city_intial, $country_code){
	
		$this->filterSql = "SELECT ID , Name || '(#' || ID || ')' as NAME  FROM gad_city where upper(name) like '".$city_intial."%'";
		$this->dbType = 'campaign';		
		$oReportDao = new ReportDAO();
		if ($this->dbType == 'campaign'){			
			$resultSet['geonames'] = $oReportDao->getsearchResult($this->filterSql, 1);		  
		}else{
			$resultSet['geonames'] = $oReportDao->getsearchResult($this->filterSql, 0);
		}
		return $resultSet;		
	}
	
	public function getReportZipCodeFilterGeoData($zip_code){
	
		$this->filterSql = "SELECT zip_code as id ,zip_code as NAME  FROM gad_zipcode where zip_code like '".$zip_code."%'";
		$this->dbType = 'inv';		
		$oReportDao = new ReportDAO();
		$resultSet['zip_code'] = $oReportDao->getsearchResult($this->filterSql, 2);	
		return $resultSet;		
	}
	
	public function getReportInventoryTemplate($template_type){
		$userid = sfContext::getInstance()->getUser()->hasAttribute("userid");
		$this->filterSql = "SELECT TEMPLATE_ID , TEMPLATE_NAME  FROM gad_report_inventory_template where user_id in (1, $userid) AND template_type='".$template_type."'";
		$oReportDao = new ReportDAO();
		$resultSet = $oReportDao->getsearchResult($this->filterSql, 0);
		return $resultSet;	
	}
	
	public function getReportInventoryPreSelectedValue($countryCode){
		$userid = sfContext::getInstance()->getUser()->hasAttribute("userid");
		$this->filterSql = "select * from gad_report_inventory_template gt, gad_report_inventory_filters gf where gt.template_id = gf.template_id and gt.template_id = $countryCode and gt.user_id in(1, $userid)";
		$oReportDao = new ReportDAO();
		$resultSet = $oReportDao->getsearchResult($this->filterSql, 0);
		return $resultSet;	
	}
	
	public function getInvertoryReportDimension(){
		$this->filterSql = "select field_type, display_name, table_field_name, id from gad_report_lookup  where report_type = (select str_value_id from gad_option where key_name = 'Report Type' and display = 'Inventory Report') and active = 1 order by display_order";
		$oReportDao = new ReportDAO();
		$resultSet = $oReportDao->getsearchResult($this->filterSql, 0);
		$finalArr = self::getInventoryReportDMF($resultSet);
		return $finalArr;	
	}
	
	public function getInventoryReportDMF($arr){	
		foreach($arr as $key => $val){
			switch($val['FIELD_TYPE']){
				case 'D':
							$tempArr['D'][$val['ID']] = $val['DISPLAY_NAME'];
							break;
				case 'M':
							$tempArr['M'][] = $val['TABLE_FIELD_NAME'];
							break;
				case 'F':
							switch($val['DISPLAY_NAME']){
								case 'Country':
												$tempArr['F']['Geo'][$val['ID']] = $val['DISPLAY_NAME'];
												break;
								case 'State Region':
												$tempArr['F']['Geo'][$val['ID']] = $val['DISPLAY_NAME'];
												break;
								case 'City':
												$tempArr['F']['Geo'][$val['ID']] = $val['DISPLAY_NAME'];
												break;
								case 'DMA':
												$tempArr['F']['Geo'][$val['ID']] = $val['DISPLAY_NAME'];
												break;
								case 'Zip Code':
												$tempArr['F']['Geo'][$val['ID']] = $val['DISPLAY_NAME'];
												break;
								default:
												$tempArr['F'][$val['ID']] = $val['DISPLAY_NAME'];
												break;
							}
							break;
							
			}			
		}	
		
		return $tempArr;
	}
	
	public function getReportInventoryEditData($edit_report_id){
		$this->filterSql = "select ID, meta_type, column_name, column_val,operator_val from gad_report_detail where report_id = $edit_report_id";
		$oReportDao = new ReportDAO();
		$resultSet = $oReportDao->getsearchResult($this->filterSql, 0);
		$finalArr = self::getEditInventoryReportDMF($resultSet);

		$this->sql = "select report_name, date_range_num,is_run_report from gad_report where id = $edit_report_id";
		$oReportDao = new ReportDAO();
		$rec = $oReportDao->getsearchResult($this->sql, 0);
		$finalArr['REPORT_NAME'] = $rec[0]['REPORT_NAME'];
		$finalArr['DATE_RANGE_NUM'] = $rec[0]['DATE_RANGE_NUM'];
		$finalArr['IS_RUN_REPORT'] = $rec[0]['IS_RUN_REPORT'];
		return $finalArr;	
	}
	
	public function getEditInventoryReportDMF($arr){	
		foreach($arr as $key => $val){
			switch($val['META_TYPE']){
				case 'TMP':
							$tempArr['Customise'] = $val['COLUMN_VAL'];
							break;
				case 'D':
							$tempArr['D'][$val['COLUMN_NAME']] = $val['COLUMN_VAL'];
							break;
				case 'M':
							$tempArr['M'][$val['COLUMN_NAME']] = $val['COLUMN_VAL'];
							break;
				case 'F':
							switch($val['COLUMN_VAL']){
								case 'Country':
												$tempArr['F']['Geo'][$val['COLUMN_NAME']]	 = $val['COLUMN_VAL'];
												break;
								case 'State Region':
												$tempArr['F']['Geo'][$val['COLUMN_NAME']] 	= $val['COLUMN_VAL'];
												break;
								case 'City':
												$tempArr['F']['Geo'][$val['COLUMN_NAME']] 	= $val['COLUMN_VAL'];
												break;
								case 'DMA':
												$tempArr['F']['Geo'][$val['COLUMN_NAME']] 	= $val['COLUMN_VAL'];
												break;
								case 'Zip Code':
												$tempArr['F']['Geo'][$val['COLUMN_NAME']] 	= $val['COLUMN_VAL'];
												break;
								default:
												$tempArr['F'][$val['COLUMN_NAME']]	 		= explode(',', strtoupper(str_replace("'","",$val['COLUMN_VAL'])));
												$tempArr['OPERATOR_VAL'][$val['COLUMN_NAME']] = $val['OPERATOR_VAL'];
												break;
							}
							break;
							
			}			
		}	
		
		return $tempArr;
	}

	public function getListIdsInventoryReport($id){
		$userid = sfContext::getInstance()->getUser()->hasAttribute("userid");
		$this->filterSql = "select * from gad_report_inventory_template gt, gad_report_inventory_filters gf where gt.template_id = gf.template_id and gt.template_id = $countryCode and gt.user_id in(1, $userid)";
		$oReportDao = new ReportDAO();
		$resultSet = $oReportDao->getsearchResult($this->filterSql, 0);
		return $resultSet;
	}

	function getReportDetailsDisplayName($report_type)
	{
		//$conn_meta = oci_connect('ade_data_alpha','ade_data_alpha','GADEVDB');
		$conn_meta = DAO::getDbOCIConnection();
	
		$report_details_arr = array();
		$sql_ReptDetailsDis = "SELECT 
									FIELD_TYPE,
									TABLE_FIELD_NAME,
									DISPLAY_NAME,ID
								FROM 
									GAD_REPORT_LOOKUP
								WHERE 
									REPORT_TYPE=$report_type 
									AND ACTIVE=1 
									AND REPORT_PARENT_FIELD_ID IS NULL";

		$stmt = oci_parse($conn_meta, $sql_ReptDetailsDis);
		oci_execute($stmt);
		while($report_details = oci_fetch_array($stmt))
		{
			$report_details_arr[$report_details['FIELD_TYPE']][$report_details['ID']]['DISPLAY_NAME'] = $report_details['DISPLAY_NAME'];
			$report_details_arr[$report_details['FIELD_TYPE']][$report_details['ID']]['TABLE_FIELD_NAME'] = $report_details['TABLE_FIELD_NAME'];
			$report_details_arr[$report_details['FIELD_TYPE']][$report_details['ID']]['ID'] = $report_details['ID'];
		}
		return $report_details_arr;
	}

	public function getLookupIdWidKey(){
		 $this->filterSql = "select field_type, table_field_name, id, display_name from gad_report_lookup  where report_type = (select str_value_id from gad_option where key_name = 'Report Type' and display = 'Inventory Report') and active = 1";
		$oReportDao = new ReportDAO();
		$resultSet = $oReportDao->getsearchResult($this->filterSql, 0);
		$finalArr = self::getLookupIdWidKeyDMF($resultSet);
		//echo "<pre>";print_r($finalArr);echo "</pre>";exit;
		return $finalArr;	
	}

	public function getLookupIdWidKeyDMF($arr){
	
		foreach($arr as $key => $val){
			switch($val['FIELD_TYPE']){
				case 'D':
							$tempArr['D'][$val['TABLE_FIELD_NAME']] = $val['ID'];
							break;
				case 'M':
							$tempArr['M'][$val['TABLE_FIELD_NAME']] = $val['ID'];
							break;
				case 'F':
							switch($val['DISPLAY_NAME']){
								case 'Country':
												$tempArr['F']['Geo'][$val['DISPLAY_NAME']]	 = $val['ID'];
												break;
								case 'State Region':
												$tempArr['F']['Geo'][$val['DISPLAY_NAME']] 	= $val['ID'];
												break;
								case 'City':
												$tempArr['F']['Geo'][$val['DISPLAY_NAME']] 	= $val['ID'];
												break;
								case 'DMA':
												$tempArr['F']['Geo'][$val['DISPLAY_NAME']] 	= $val['ID'];
												break;
								case 'Zip Code':
												$tempArr['F']['Geo'][$val['DISPLAY_NAME']] 	= $val['ID'];
												break;
								default:
												$tempArr['F'][$val['TABLE_FIELD_NAME']]	 = $val['ID'];
												break;
							}
							break;
							
			}			
		}	
		
		return $tempArr;
	
	}

	public function getLookupKeyWidValue(){
		 $this->filterSql = "select field_type, table_field_name, display_name from gad_report_lookup  where report_type = (select str_value_id from gad_option where key_name = 'Report Type' and display = 'Inventory Report') and active = 1";
		$oReportDao = new ReportDAO();
		$resultSet = $oReportDao->getsearchResult($this->filterSql, 0);
		$finalArr = self::getLookupKeyWidValueDMF($resultSet);
		//echo "<pre>";print_r($finalArr);echo "</pre>";exit;
		return $finalArr;	
	}

	public function getLookupKeyWidValueDMF($arr){
	
		foreach($arr as $key => $val){
			switch($val['FIELD_TYPE']){
				case 'D':
							$tempArr['D'][$val['DISPLAY_NAME']] = $val['TABLE_FIELD_NAME'];
							break;
				case 'M':
							$tempArr['M'][$val['DISPLAY_NAME']] = $val['TABLE_FIELD_NAME'];
							break;
				case 'F':
							$tempArr['F'][$val['DISPLAY_NAME']]	 = $val['TABLE_FIELD_NAME'];
							break;
							
			}			
		}
		return $tempArr;
	
	}

	public function getLookupIDWidValue(){
		 $this->filterSql = "select field_type, table_field_name, display_name, ID from gad_report_lookup  where report_type = (select str_value_id from gad_option where key_name = 'Report Type' and display = 'Inventory Report') and active = 1";
		$oReportDao = new ReportDAO();
		$resultSet = $oReportDao->getsearchResult($this->filterSql, 0);
		$finalArr = self::getLookupIDWidValueDMF($resultSet);
		//echo "<pre>";print_r($finalArr);echo "</pre>";exit;
		return $finalArr;	
	}

	public function getLookupIDWidValueDMF($arr){
	
		foreach($arr as $key => $val){
			switch($val['FIELD_TYPE']){
				case 'D':
							$tempArr['D'][$val['ID']] = $val['DISPLAY_NAME'];
							break;
				case 'M':
							$tempArr['M'][$val['ID']] = $val['DISPLAY_NAME'];
							break;
				case 'F':
							$tempArr['F'][$val['DISPLAY_NAME']]	 = $val['ID'];
							break;
							
			}			
		}
		return $tempArr;
	
	}

	public function getReportSiteListFilterData($site_intial, $country_code){
	
		$this->filterSql = "SELECT distinct(LIST_ID)  as ID , LIST_ID as NAME  FROM ADQ_AFF_LIST_CUSTOM  where LIST_ID like '".trim($site_intial)."%'";
		$oReportDao = new ReportDAO();
		$resultSet['siteids'] = $oReportDao->getsearchResult($this->filterSql, 2);
		return $resultSet;		
	}

  public static function getSelectOptionList($sKey,$sClass=null,$sSelectedOption="",$bBlank=false, $bOnChage=false,$defaultselection=false)
  {
    $aResult = array();
    $sHtml = "";

	if($sKey=='TimePeriod')
	{
		if($sSelectedOption =='')
		{
			$sSelectedOption = '7';
		}
		$aResult = array('0'=>array('KEY_PERIOD'=>'1','VAL_PERIOD'=>'1 Days'),
						 '1'=>array('KEY_PERIOD'=>'7','VAL_PERIOD'=>'7 Days'),
						'2'=>array('KEY_PERIOD'=>'14','VAL_PERIOD'=>'14 Days'),
						'3'=>array('KEY_PERIOD'=>'30','VAL_PERIOD'=>'30 Days')
			);
		$keyname = 'KEY_PERIOD';
		$valname = 'VAL_PERIOD';

	}
	else if($sKey=='Geo')
	{
		if($sSelectedOption =='')
		{
			$sSelectedOption = 'us';
		}
		if(!$defaultselection)
		{
			switch($sSelectedOption){			
				case 'us':
				case 'uk':
				case 'ca':
							$aResult = array('0'=>array('KEY_PERIOD'=>'us','VAL_PERIOD'=>'&nbsp;US&nbsp;'),
							'1'=>array('KEY_PERIOD'=>'ca','VAL_PERIOD'=>'&nbsp;CA&nbsp;'),
							'2'=>array('KEY_PERIOD'=>'uk','VAL_PERIOD'=>'&nbsp;UK&nbsp;'));
							break;
				case 'jp':
							$aResult = array('1'=>array('KEY_PERIOD'=>'jp','VAL_PERIOD'=>'&nbsp;JP&nbsp;'));
							break;
				case 'de':
							$aResult = array('1'=>array('KEY_PERIOD'=>'de','VAL_PERIOD'=>'&nbsp;DE/CH/AT&nbsp;'));
							break;
				case 'fr':
							$aResult = array('1'=>array('KEY_PERIOD'=>'fr','VAL_PERIOD'=>'&nbsp;FR&nbsp;'));
							break;

			}
			
		}
		else
		{
			$aResult = array('0'=>array('KEY_PERIOD'=>'us','VAL_PERIOD'=>'&nbsp;US&nbsp;'));
		}
		$keyname = 'KEY_PERIOD';
		$valname = 'VAL_PERIOD';

	}
	else
	{
		$oReportDao = new ReportDAO();
		switch ($sKey) 
		{
			case 'Canned':
			if($defaultselection)
			{
				$sql_plus = " AND TEMPLATE_NAME='US'";
			}
			$sSql = "select template_id, template_name from gad_report_inventory_template where template_type='CANNED'".$sql_plus;
			$keyname = 'TEMPLATE_ID';
			$valname = 'TEMPLATE_NAME';
			break;
			case 'Network':
			$sSql = "select template_id, template_name from gad_report_inventory_template where template_type='NETWORK' and status=1 order by template_id asc";
			$keyname = 'TEMPLATE_ID';
			$valname = 'TEMPLATE_NAME';
			break;
			case 'Product':
			$sSql = "select template_id, template_name from gad_report_inventory_template where template_type='PRODUCT' and status=1";
			$keyname = 'TEMPLATE_ID';
			$valname = 'TEMPLATE_NAME';
			break;
			case 'PreCanned':
			$sSql = "select template_id, template_name from gad_report_inventory_template where template_type IN ('PRODUCT','NETWORK') and status=1";
			$keyname = 'TEMPLATE_ID';
			$valname = 'TEMPLATE_NAME';
			break;

			
		}
		$aResult = $oReportDao->getsearchResult($sSql);
	}	

	$sSelectTag = "<select name = '".str_replace(" ","",$sKey)."' id='".str_replace(" ","",$sKey)."' class='".$sClass."' ".$bOnChage.">";
	$sHtml.=  $sSelectTag;

	if($sKey=='PreCanned')
	{
		$sHtml.="<option value=''> Select Report Name </option>"."\n";			
	}
	$sSelected ='';
	foreach($aResult as $value)
	{
		$sSelected ='';
		if($value[$keyname] == $sSelectedOption )
		{
			$sSelected = 'selected =selected';
		}
		if($sKey=='PreCanned')
		{
			if($value[$valname] == $sSelectedOption )
			{
				$sSelected = 'selected =selected';
			}
			$sHtml.="<option id= '".$value[$valname]."' value='".$value[$valname]."'  ".$sSelected." > ".$value[$valname]." "." </option>"."\n";
		}
		else
		{
			$sHtml.="<option id= '".$value[$valname]."' value='".$value[$keyname]."'  ".$sSelected." > ".$value[$valname]." "." </option>"."\n";
		}
	}
	
	if($sKey=='PreCanned')
	{	
		$sSelected='';
		if( "Inventory Report" == $sSelectedOption )
		{
			$sSelected = 'selected =selected';
		}
		$sHtml.="<option id= 'Inventory Report' value='Inventory Report'  ".$sSelected." > Inventory Report </option>"."\n";			
	}

	$sHtml.=" </select>";
	return $sHtml;
  }

	public function getFilterData($id){
		$this->filterSql = "select * from gad_report_inventory_filters where template_id = $id";
		$oReportDao = new ReportDAO();
		$resultSet  = $oReportDao->getsearchResult($this->filterSql, 0);
		foreach($resultSet as $key => $val){
			$tmp[$val['COLUMN_NAME']] = $val['COLUMN_VAL'];
		}
		return $tmp;		
	}

	 public function fileExistsCheck($repId){
	  $this->filterSql = "SELECT ID, STATUS, FILE_NAME,REC_COUNT FROM 
       ( 
         SELECT id, status, file_name ,rec_count FROM gad_report_schedule_history
         WHERE report_id = $repId 
         ORDER BY id desc
       )
       WHERE ROWNUM < 2";
  $oReportDao = new ReportDAO();
  $resultSet  = $oReportDao->getsearchResult($this->filterSql, 0);
  foreach($resultSet as $key => $val){
   $tmp['status'] = $val['STATUS'];
   $tmp['file_name'] = $val['FILE_NAME'];
   $tmp['rec_count'] = $val['REC_COUNT'];
  }
  return $tmp; 
 }

  public function showTitle($type)
 {
	  switch($type)
	  {
		  case 'Inventory':
			  $titleHTML ='<div class="title">
						<div style="font: bold 12px Arial,Helvetica,sans-serif">
						<a href=\''.Utils::getApplicationPath('report')."/productreport/index' style='text-decoration:none'>Product Reports</a>  | Inventory Tool
						</div>
					</div>"; 
	      break;
		  case 'Product':
			  $titleHTML ='<div class="title"> Product Reports | <span style="font: bold 12px Arial,Helvetica,sans-serif"> 
						 <a href=\''.Utils::getApplicationPath('report')."/reportinventory/index' style='text-decoration:none'>Inventory Tool</a>
						 </span>
						</div>"; 
	      break;
		  case 'Query':
			  $titleHTML ='<div class="title">
						<div style="font: bold 12px Arial,Helvetica,sans-serif">Query Tool |
						<a href=\''.Utils::getApplicationPath('report')."/campaignreport/index' style='text-decoration:none'>Splash Engagement Reports</a>
						</div>
					</div>"; 
	      break;
		  case 'Campaign':
			  $titleHTML ='<div class="title">
						<div style="font: bold 12px Arial,Helvetica,sans-serif"><a href=\''.Utils::getApplicationPath('report')."/adopsreport/index' style='text-decoration:none'>Query Tool</a> |
						Splash Engagement Reports
						</div>
					</div>"; 
	      break;
		   case 'Network':
			  $titleHTML ='<div class="title">
						<div style="font: bold 12px Arial,Helvetica,sans-serif">Network Report 
						</div>
					</div>'; 
	      break;
		  case 'Save':
			  $titleHTML ='<div class="title">
						<div style="font: bold 12px Arial,Helvetica,sans-serif">Saved & scheduled reports
    					</div>
					</div>'; 
	      break;
		   case 'History':
			  $titleHTML ='<div class="title">
						<div style="font: bold 12px Arial,Helvetica,sans-serif">Report History</div>
					</div>'; 
	      break;

	  }
	
  return $titleHTML;
 
 }

	public function getUserEmailbyID($userId)
	{
	 $select_email_user = 'SELECT EMAIL FROM GAD_USER WHERE ID='.$userId;
	 $oReportDao = new ReportDAO();
	 $resultSet  = $oReportDao->getsearchResult($select_email_user, 0);
	 foreach($resultSet as $key => $val)
	 {
	   $user_email = $val['EMAIL'];
	 }
	 return $user_email;
	}

	public function getInvDataStatus($reportName)
	{
		 $sql = "select distinct(agg_level), is_loading from ADQ_INV_TABLE_CONFIG WHERE agg_level ='".$reportName."' order by 1";
		 $oReportDao = new ReportDAO();
		 $resultSet  = $oReportDao->getsearchResult($sql, 2);
		 foreach($resultSet as $key => $val)
		 {
		   $loading_status = $val['IS_LOADING'];
		 }
		 return $loading_status;
	}

	public function getInventoryDataStatus($reportType1, $reportType2)
	{
		 $sql = "select distinct(agg_level), is_loading from ADQ_INV_TABLE_CONFIG WHERE agg_level ='".$reportType1."' order by 1";
		 $oReportDao = new ReportDAO();
		 $resultSet  = $oReportDao->getsearchResult($sql, 2);
		 foreach($resultSet as $key => $val)
		 {
		     if ($val['IS_LOADING'] == 0){
			 $sql = "select distinct(agg_level), is_loading from ADQ_INV_TABLE_CONFIG WHERE agg_level ='".$reportType2."' order by 1";
			 $rs  = $oReportDao->getsearchResult($sql, 2);
			 foreach($rs as $k => $v)
			{
				if ($v['IS_LOADING'] == 0){
					 return $v['IS_LOADING'];
				}else{
					return $v['IS_LOADING'];
				}
			}
		   }else{
			return $val['IS_LOADING'];
		   }

		 }
	}

	public function getSelectedInventoryReportStatus($reportName)
	{
		switch($reportName){
		case 'Standard Media Report':
							$status = 'INV PROD STD MEDIA';   break;
		case 'Vertical Fill Report':
							$status = 'INV VERTICAL FILL';   break;
		case 'Site Fill Report':
							$status = 'INV SITE FILL';   break;
		case 'Media Takeover Report':
							$status = 'INV MEDIA TAKEOVER AGG';   break;
		case 'Reskin Takeover Report':
							$status = 'INV RESKIN TAKEOVER';   break;
		case 'Pushdown Report':
							$status = 'INV PUSHDOWN AGG';   break;
		case 'Pushdown Takeover Report':
							$status = 'INV PUSHDOWN TAKEOVER';   break;
		case 'Inventory Report':
							$status = 'INV COUNTRY AGG'; break;
		case 'Video Pre-Roll Inventory Report':
							$status = 'INV VIDEO PRE ROLL';   break;
		case 'Mobile Inventory Report':
							$status = 'INV MOBILE';   break;
		}
	//	 $sql = "select is_loading from ADQ_INV_TABLE_CONFIG WHERE agg_level ='".$reportName."' and is_current=0 order by 1";
		 $sql = "select distinct(is_loading) from ADQ_INV_TABLE_CONFIG where agg_level='".$status."' order by 1";
		 $oReportDao = new ReportDAO();
		 $resultSet  = $oReportDao->getsearchResult($sql, 2);
		 foreach($resultSet as $key => $val)
		 {
		   $loading_status = $val['IS_LOADING'];
		 }
		 return $loading_status;
	}

	public function getInvReportDetails()
	{
		$sql = "select distinct ginv.template_name, is_loading , b.agg_level as tablename from gad_report_inventory_template ginv , adq_inv.adq_inv_table_config b where ginv.template_type IN ('PRODUCT','NETWORK','INV') and  ginv.status=1 and ginv.template_id= b.report_type_id";
				
		 $oReportDao = new ReportDAO();
		 $resultSet  = $oReportDao->getsearchResult($sql, 0);
		 foreach($resultSet as $key => $val)	
		 {
			 switch($val['TEMPLATE_NAME']){
				case 'INV PROD STD MEDIA':
									$status = 'Standard Media Report';break;
				case 'INV VERTICAL FILL':
									$status = 'Vertical Fill Report';break;
				case 'INV SITE FILL':
									$status = 'Site Fill Report';break;
				case 'INV MEDIA TAKEOVER AGG':
									$status = 'Media Takeover Report';break;
				case 'INV RESKIN TAKEOVER':
									$status = 'Reskin Takeover Report';break;
				case 'INV PUSHDOWN AGG':
									$status = 'Pushdown Report';break;
				case 'INV PUSHDOWN TAKEOVER':
									$status = 'Pushdown Takeover Report';break;
				case 'INV COUNTRY AGG':
				case 'INV HOTSHOT AGG':
									$status = 'Inventory Report';break;

				case 'INV VIDEO PRE ROLL':
									$status = 'Video Pre-Roll Inventory Report';break;
				case 'INV MOBILE':
									$status = 'Mobile Inventory Report';break;
			}
		   if ($val['IS_LOADING'] == "1"){
				$loading_status = 'Yes';
		   }else{
			   $loading_status = 'No';
		   }
		   
		   $tmp[]  = $val['TEMPLATE_NAME'] . "||" .$loading_status;
		 }
		 return $tmp;
	}
	
	public function getInvReportLogPath($reportName)
	{
		$sql = "select distinct ginv.template_name, log_file from gad_report_inventory_template ginv , adq_inv.adq_inv_table_config b 
					where ginv.template_type IN ('PRODUCT','NETWORK','INV') and  ginv.status=1 
					and ginv.template_id= b.report_type_id and ginv.template_name = '".$reportName."'";					
			
		$oReportDao = new ReportDAO();
		$resultSet  = $oReportDao->getsearchResult($sql, 0);
		return $resultSet;
	}

	 public static function getDatePredefined($Key=null,$sClass=null,$sSeletedValue=null,$report_type=null)
	{
	
		$sHtml = "";
		$reportTypeArr = array_flip(self::getReportType());
		//$aVal = array("Yesterday", "Week to date", "Past 7 days", "Last week", "Month to date", "Past 30 days", "Last month","Year to date","All Data");
		$aVal = array("Yesterday", "Week to date", "Past 7 days", "Last week", "Month to date", "Past 30 days", "Last month","Year to date");
		$hive_report_arr = array($reportTypeArr['URL Report'],$reportTypeArr['Ning Network Traffic'],$reportTypeArr['Intra-day URL Report'],$reportTypeArr['Rich Media  Engagement'],$reportTypeArr['Unique Frequency Report'],$reportTypeArr['Combined Log'],$reportTypeArr['Unique To-Date Report']);
		
		if($report_type!='')
		{
		
			if(in_array($report_type,$hive_report_arr))
			{
				$aVal = array("Yesterday", "Week to date", "Past 7 days", "Last week", "Month to date", "Past 30 days", "Last month","Year to date");
			}
	  	}

	$sHtml.="<select name='".$Key."' id='".$Key."' class='".$sClass."' onchange='javascript:document.frmgeneratereport.date_range[0].checked=true;'>";
	for($iCount = 0;$iCount<count($aVal);$iCount++)
	{

	  if($sSeletedValue == $aVal[$iCount])
	  {
		$sHtml.="<option selected value='".$aVal[$iCount]."' >".$aVal[$iCount]."</option>";
	  }
	  else
	  {
		$sHtml.="<option value='".$aVal[$iCount]."' >".$aVal[$iCount]."</option>";
	  }
	}
	$sHtml.=" </select>";
	
	return $sHtml;

	}
	
	public function getReportETLInfo($reportType){
		 $reportType = strtolower($reportType);
		 $sql = "select * from FILE_STATS where report_type = '".$reportType."' and trunc(load_date) = (select max(trunc(load_date)) from file_stats where report_type = '".$reportType."') order by load_date, numdays";
		 $oReportDao = new ReportDAO();
		 $resultSet  = $oReportDao->getsearchResult($sql, 2);
		 return $resultSet;
	}	
	
	public function getPresetStartNEndDate($preset_id){
		$sql = "select hub_start_date as start_date, hub_end_date as end_date from adq_hub_preset where preset_id = $preset_id";
		$oReportDao = new ReportDAO();
		$resultSet  = $oReportDao->getsearchResult($sql, 1);
		$start_date = $resultSet[0]['START_DATE'];		
		$start_date = date("m/d/Y", strtotime($start_date));

		$end_date = $resultSet[0]['END_DATE'];
		$end_date = date("m/d/Y", strtotime($end_date));
		return $start_date."##".$end_date;
	}

}