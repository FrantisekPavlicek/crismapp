<?php
/*
 * Mysql Ajax Table Editor
 *
 * Copyright (c) 2014 Chris Kitchen <info@mysqlajaxtableeditor.com>
 * All rights reserved.
 *
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://www.mysqlajaxtableeditor.com
 */
require_once('DBC.php');
require_once('Common.php');
require_once('php/lang/LangVars-cz.php');
require_once('php/AjaxTableEditor.php');
class InternationalExample extends Common
{
	protected $Editor;
	protected $mateInstances = array('mate1_');
	
	protected function setHeaderFiles()
	{
		$this->headerFiles[] = '<script type="text/javascript" src="js/jquery/js/lang/jquery-ui-datepicker-es.js"></script>';
		$this->headerFiles[] = '<script type="text/javascript" src="js/lang/lang_vars-cz.js"></script>';
	}
	
	protected function displayHtml()
	{
		$html = '
			
			
			
			<div class="mateAjaxLoaderDiv"><div id="ajaxLoader1"><img src="images/ajax_loader.gif" alt="Loading..." /></div></div>
			
			
			
			<div id="'.$this->mateInstances[0].'information">
			</div>

			<div id="mateTooltipErrorDiv" style="display: none;"></div>
			
			<div id="'.$this->mateInstances[0].'titleLayer" class="mateTitleDiv">
			</div>
			
			<div id="'.$this->mateInstances[0].'tableLayer" class="mateTableDiv">
			</div>
			
			<div id="'.$this->mateInstances[0].'recordLayer" class="mateRecordLayerDiv">
			</div>		
			
			<div id="'.$this->mateInstances[0].'searchButtonsLayer" class="mateSearchBtnsDiv">
			</div>';
			
		echo $html;
		
		// Set default session configuration variables here
		$defaultSessionData['orderByColumn'] = 'id';

		$defaultSessionData = base64_encode($this->Editor->jsonEncode($defaultSessionData));
		
		$javascript = '	
			<script type="text/javascript">
				var mateHashes = {};
				var mateForward = false;
				var '.$this->mateInstances[0].' = new mate("'.$this->mateInstances[0].'");
				'.$this->mateInstances[0].'.setAjaxInfo({url: "'.$this->getAjaxUrl().'", history: true});
				//'.$this->mateInstances[0].'.mateSubmitEmptyUpload = true;
				if('.$this->mateInstances[0].'.ajaxInfo.history == false) {
					'.$this->mateInstances[0].'.toAjaxTableEditor("update_html","");
				} else if(window.location.hash.length == 0) {
					mateHashes.'.$this->mateInstances[0].' = {info: "", action: "update_html", sessionData: "'.$defaultSessionData.'"};
					mateForward = true;
				}
				if(mateForward) {
					var sessionCookieName = '.$this->mateInstances[0].'.getSessionCookieName();
					if($.cookie(sessionCookieName) != undefined) {
						window.location.href = window.location.href+"#"+$.cookie(sessionCookieName);
					} else {
						window.location.href = window.location.href+"#"+Base64.encode($.toJSON(mateHashes));
					}
				}
				
			</script>';
		echo $javascript;
	}

	public function formatFileSize($col,$size,$row)
	{
		$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$retstring = '%01.2f %s';
		$lastsizestring = end($sizes);
		foreach ($sizes as $sizestring) 
		{
				if ($size < 1024) { break; }
				if ($sizestring != $lastsizestring) { $size /= 1024; }
		}
		if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; }
		return sprintf($retstring, $size, $sizestring);
	}
	
	public function formatImage($col,$val,$row)
	{
		$html = '';
		if(strlen($val) > 0)
		{
			$html .= '<a target="_blank" href="DisplayFileFromDb.php?emp_id='.$row['id'].'"><img style="border: none;" src="DisplayFileFromDb.php?emp_id='.$row['id'].'" alt="'.$val.'" width="100" /></a>';
		}
		return $html;
	}
	
	public function deleteFile($info)
	{
		$query = "update emp_upload_db set file_data = '', file_type = '', file_name = '' where id = :id limit 1";
		$result = $this->Editor->doQuery($query,array('id' => $info['id']));
		if($result)
		{
			return true;
		}
		$this->Editor->warnings[] = 'There was an error deleting the file.';
		return false;
	}
	
	protected function initiateEditor()
	{
		$tableColumns['id'] = array(
			'display_text' => 'ID', 
			'perms' => 'TVQSXO'
		);
		
		$tableColumns['basemap'] = array(
			'display_text' => 'ID podkladové mapa <br><span class="popisek">která je zapnuta na začátku, typicky pro OSM: 1 . Musí být vyplněno! </span>', 
			'perms' => 'EVCTAXQSHOF', 'req' => true
		);
		
		$tableColumns['x'] = array(
			'display_text' => 'Výchozí bod - X <br><span class="popisek">souřadnice lat</span>', 
			'perms' => 'EVCTAXQSHOF', 'req' => true
		);
		
		$tableColumns['y'] = array(
			'display_text' => 'Výchozí bod - Y<br><span class="popisek">souřadnice lon </span>', 
			'perms' => 'EVCTAXQSHOF', 'req' => true
		);
		
		
		$tableColumns['zoom'] = array(
			'display_text' => 'Výchozí zoom <br><span class="popisek">např. 18 </span>', 
			'perms' => 'EVCTAXQSHOF', 'req' => true
		);
		
		$tableColumns['titulek'] = array(
			'display_text' => 'Titulek <br><span class="popisek">v levém sloupci</span>', 
			'perms' => 'EVCTAXQSHOF', 'req' => true
		);
		
		$tableColumns['gist'] = array(
			'display_text' => 'Název účtu Github  <br><span class="popisek"> odkud se načítají Geojsony, např.: geocentrum 1 </span>', 
			'perms' => 'EVCTAXQSHOF', 'req' => true
		);
		
		$tableColumns['meta_title'] = array(
			'display_text' => 'Metatag &lt;title&gt; <br><span class="popisek">metatag title (titulek stránky) v horní liště prohlížeče  </span>', 
			'perms' => 'EVCTAXQSHOF', 'req' => true
		);
		
		$tableColumns['adapt_nazev'] = array(
			'display_text' => 'Adaptivní - link', 
			'perms' => 'EVCTAXQSHOF'
		);
		
		
		$tableColumns['adapt_css'] = array(
			'display_text' => 'Adaptivní - název css souboru <br><span class="popisek">např. sidebar_red.css, pokud prázdné pak výchozí sidebar.css </span>', 
			'perms' => 'EVCTAXQSHOF'
		);
		
		$tableColumns['adapt_load'] = array(
			'display_text' => 'Adaptivní - při načtení <br><span class="popisek">funkce co se má provést při načtení (body onload) </span>', 
			'perms' => 'EVCTAXQSHOF'
		);
		
		$tableName = 'nastaveni';
		$primaryCol = 'id';
		$errorFun = array(&$this,'logError');
		$permissions = 'EI';
		
		$this->Editor = new AjaxTableEditor($tableName,$primaryCol,$errorFun,$permissions,$tableColumns);
		$this->Editor->setConfig('tableInfo','cellpadding="1" cellspacing="1" align="center" width="1100" class="mateTable"');
		//$this->Editor->setConfig('orderByColumn','first_name');
		$this->Editor->setConfig('tableTitle','<!--Seznam POI bodů <div style="font-size: 12px; font-weight: normal;">...</div>-->');
		$this->Editor->setConfig('addRowTitle','Přidat nový záznam (ve formátu 49.50, 17.50)');
		$this->Editor->setConfig('editRowTitle','Editovat záznam');
		//$this->Editor->setConfig('addScreenFun',array(&$this,'autoComplete'));
		//$this->Editor->setConfig('editScreenFun',array(&$this,'autoComplete'));
		$this->Editor->setConfig('instanceName',$this->mateInstances[0]);
		$this->Editor->setConfig('paginationLinks',true);
		//$this->Editor->setConfig('viewQuery',true);
		
		
	}
	
	
	protected function setMysqlLocale()
	{
		$query = "SET lc_time_names = 'cs_CZ'";
		$result = DBC::get()->query($query);
	}
	
	function __construct()
	{
		session_start();
		ob_start();
		$this->setMysqlLocale();
		$this->initiateEditor();
		if(isset($_POST['json']))
		{
			if(get_magic_quotes_gpc())
			{
				$_POST['json'] = stripslashes($_POST['json']);
			}
			$this->Editor->data = $this->Editor->jsonDecode($_POST['json'],true);
			$this->Editor->setDefaults();
			$this->Editor->main();
			//echo $this->Editor->jsonEncode($this->Editor->retArr);
		}
		else if(isset($_GET['mate_export']))
		{
			$this->Editor->data['sessionData'] = $_GET['session_data'];
			$this->Editor->setDefaults();
			ob_end_clean();
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
			header('Content-type: application/x-msexcel');
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'.$this->Editor->tableName.'.csv"');
			// Add utf-8 signature for windows/excel
			echo chr(0xEF).chr(0xBB).chr(0xBF);
			echo $this->Editor->exportInfo();
			exit();
		}
		else
		{
			$this->setHeaderFiles();
			$this->displayHeaderHtml();
			$this->displayHtml();
			$this->displayFooterHtml();
		}
	}
}
$page = new InternationalExample();
?>
