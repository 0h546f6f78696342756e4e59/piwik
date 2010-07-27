<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: UserCountryMap.php 1665 2010-07-25 21:25:57Z gka $
 * 
 * @category Piwik_Plugins
 * @package Piwik_UserCountryMap
 */

/**
 *
 * @package Piwik_UserCountryMap
 */
class Piwik_UserCountryMap extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'User Country Map',
			'description' => 'This plugin shows a zoomable world map of your visitors location.',
			'author' => 'Gregor Aisch',
			'author_homepage' => 'http://vis4.net/',
			'version' => '0.7',
			'translationAvailable' => true,
		);
	}
	
	function postLoad()
	{
		// we register the widgets so they appear in the "Add a new widget" window in the dashboard
		// Note that the first two parameters can be either a normal string, or an index to a translation string
		Piwik_AddWidget('General_Visitors', Piwik_Translate('UserCountry_WidgetCountries').' ('.Piwik_Translate('UserCountryMap_worldMap').')', 'UserCountryMap', 'worldMap');
		
	}
}

/**
 *
 * @package Piwik_ExamplePlugin
 */
class Piwik_UserCountryMap_Controller extends Piwik_Controller
{	
	
	
	/**
	 * See the result on piwik/?module=ExamplePlugin&action=exampleWidget
	 * or in the dashboard > Add a new widget 
	 *
	 */
	function worldMap()
	{
		
		
		$view = Piwik_View::factory('worldmap');
		
		$view->dataUrl = "?module=API"
			. "&method=API.getProcessedReport&format=XML"
			. "&apiModule=UserCountry&apiAction=getCountry"
			. "&idSite=" . Piwik_Common::getRequestVar('idSite', 1, 'int')
			. "&period=" . Piwik_Common::getRequestVar('period')
			. "&date=" . Piwik_Common::getRequestVar('date')
			. "&token_auth=" . Piwik::getCurrentUserTokenAuth()
			. "&filter_limit=-1";
		
		// definition of the color scale
		$view->hueMin = 218; 	
		$view->hueMax = 216; 	
		$view->satMin = "0.22"; 	
		$view->satMax = "0.9";
		$view->lgtMin = ".97";
		$view->lgtMax = ".4";
		
		$request = new Piwik_API_Request(
			'method=API.getMetadata&format=PHP'
			. '&apiModule=UserCountry&apiAction=getCountry'
			. '&idSite=' . Piwik_Common::getRequestVar('idSite', 1, 'int')
			. '&period=' . Piwik_Common::getRequestVar('period')
			. '&date=' . Piwik_Common::getRequestVar('date')
			. '&token_auth=' . Piwik::getCurrentUserTokenAuth()
			. '&filter_limit=-1'
		);
		$metaData = $request->process();
		
		$metrics = array();
		foreach ($metaData[0]['metrics'] as $id => $val) $metrics[] = array($id, $val);
		foreach ($metaData[0]['processedMetrics'] as $id => $val) $metrics[] = array($id, $val);
		
		$view->metrics = $metrics;
		
		$view->defaultMetric = 'nb_visits';
		
		echo $view->render();
		
	}

	/*
	 *
	 */
	function exportImage()
	{
		$view = Piwik_View::factory('exportImage');
		$view->imageData = 'data:image/png;base64,'.$_POST['imageData'];		
		echo $view->render();
	
	}
	
	function outputImage()
	{
		header('Content-Type: image/png');
		echo base64_decode($_POST['imagedata']);
		exit;
	}
	
	
}