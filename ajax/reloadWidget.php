<?php

OCP\User::checkLoggedIn();
OCP\App::checkAppEnabled('ocDashboard');
OCP\JSON::callCheck();

OC::$CLASSPATH['widgets'] = 'ocDashboard/appinfo/widgetConfigs.php';
$id = str_replace(array('/', '\\'), '',  $_GET['widget']);
$user = OCP\User::getUser();

$widgetArray = widgets::getWidgetConfigById($id);

OC::$CLASSPATH['factory'] = 'ocDashboard/lib/factory.php';

if (OCP\Config::getUserValue($user, "ocDashboard", "ocDashboard_".$id) == "yes") {
	
	$widgetData = factory::getWidget($widgetArray)->getData();
	$tpl = new OCP\Template("ocDashboard", "main", "user");
	$tpl->assign('widgets', Array($widgetData));
	$tpl->assign('singleOutput', true);
	$widgetHtml = $tpl->fetchPage();
	$tmp = explode('###?###', $widgetHtml);
	$html = $tmp[1];
	
	$RESPONSE['data'] = "";
	if($html) {
		$RESPONSE["success"] = true;
		$RESPONSE["HTML"] = $html;
		$RESPONSE['STATUS'] = $widgetData['status'];
	} else {
		$RESPONSE["success"] = false;
	}
} else {
	$RESPONSE["success"] = false;
}

$RESPONSE["id"] = $id;
die(json_encode($RESPONSE));