<?

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$MODULE_ID = 'nasledie.multilang';
$MODULE_CODE = 'nasledie_multilang';

$moduleSort = 1;
$i = 0;
$MOD_RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);

if ($MOD_RIGHT > "D") {
	$aMenu = array(
		"parent_menu" => "global_menu_nasledie",
		"sort" => $moduleSort,
		"section" => $MODULE_ID,
		//"url"         => '/bitrix/admin/settings.php?lang=' . LANGUAGE_ID . '&mid=' . $MODULE_ID . '&mid_menu=1',
		"text" => GetMessage($MODULE_CODE . '_MAIN_MENU_LINK_NAME'),
		"title" => GetMessage($MODULE_CODE . '_MAIN_MENU_LINK_DESCRIPTION'),
		"icon" => $MODULE_CODE . '_icon',
		"page_icon" => $MODULE_CODE . '_page_icon',
		"items_id" => $MODULE_CODE . '_main_menu_items',
		"items" => array()
	);


	$arFiles = array(
		'iblock' => array('edit','del'),
		'section' => array('edit','del'),
		'element' => array('edit','del'),
		'property' => array('edit','del'),
		'enum' => array('edit','del'),
		'value' => array('edit','del'),
		'seo' => array('edit','del'),
		'pay' => array('edit','del'),
	);


	$i++;
	foreach ($arFiles as $fname => $arExtFname) {


		$arTmp = array(
			'url' => '/bitrix/admin/' . $MODULE_ID . '_' . $fname . '.php?lang=' . LANGUAGE_ID,
			'more_url' => array(),
			'module_id' => $MODULE_ID,
			'text' => GetMessage($MODULE_CODE . '_' . $fname . '_MENU_LINK_NAME'),
			"title" => GetMessage($MODULE_CODE . '_' . $fname . '_MENU_LINK_DESCRIPTION'),
			//"icon"        => $MODULE_CODE.'_'.$item.'_icon', // ����� ������
			// "page_icon"   => $MODULE_CODE.'_'.$item.'_page_icon', // ������� ������
			'sort' => $moduleSort + $i,
		);

		foreach ($arExtFname as &$extfname) {
			$arTmp['more_url'][] = '/bitrix/admin/' . $MODULE_ID . '_' . $fname . '_' . $extfname . '.php?lang=' . LANGUAGE_ID;
		}
		unset($extfname);

		$aMenu['items'][] = $arTmp;
	}


	$aModuleMenu[] = $aMenu;
	return $aModuleMenu;
}
return false;
