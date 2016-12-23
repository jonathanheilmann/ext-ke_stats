<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$TCA["tx_kestats_statdata"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata',
		'label'     => 'uid',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'tca.php',
		'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'icon_tx_kestats_statdata.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "type, category, element_uid, element_title, element_language, counter, year, month, day, hour",
	)
);

/*
$TCA["tx_kestats_cache"] = array (
	"ctrl" => array (
		'title'     => 'ke_stats cache',
		'label'     => 'uid',
		'default_sortby' => "ORDER BY uid",
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'tca.php',
		'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'icon_tx_kestats_statdata.gif',
	),
);
*/

if (TYPO3_MODE == 'BE')	{
	$tmp_moduleConfiguration = array(
		'access' => 'user,group',
		'extensionName' => 'KeStats',
		'extRelPath' => '../typo3conf/ext/ke_stats/Classes/',
		'icon' => 'EXT:ke_Stats/ext_icon.gif',
		'labels' => 'LLL:EXT:ke_stats/mod1/locallang_mod.xml',
		'name' => 'web_txkestatsM1',
		'navigationComponentId' => 'typo3-pagetree',
		'vendorName' => 'Kennziffer'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule('web', 'txkestatsM1', '', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Classes/Controller/index.php', $tmp_moduleConfiguration);
}
