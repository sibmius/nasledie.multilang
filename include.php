<?

namespace Nasledie\MultiLang;

global $DB, $APPLICATION, $MESS, $DBType;

$MODULE_ID = 'nasledie.multilang';


/*
  \Bitrix\Main\Loader::registerAutoLoadClasses(
  $MODULE_ID,
  array(
  'NSeo' => 'classes/general/seo_utils.php',
  'CSeoKeywords' => 'classes/general/seo_keywords.php',
  'CSeoPageChecker' => 'classes/general/seo_page_checker.php'
  )
  );
 */

class NMLAgent {

	public static function Calc() {
		if (\CModule::IncludeModule("iblock")) {
			global $DB;
			/* $sql = "SELECT * FROM `n_seolink_url`;";
			  $res = $DB->Query($sql, false, $err_mess . __LINE__);
			  while ($row = $res->Fetch()) {
			  if ($row['GLOBAL_URL'] != '') {
			  $row['GLOBAL_URL'] = '"' . $row['GLOBAL_URL'] . '"';
			  }
			  if ($row['LOCAL_URL'] != '') {
			  $row['LOCAL_URL'] = '"' . $row['LOCAL_URL'] . '"';
			  }
			  self::$URL[$row['ID']] = $row;
			  }

			 */
		}



		return '\Nasledie\MultiLang\NMLAgent::Calc();';
	}

	public static function TRElement(array &$arItem) {
		if (LANGUAGE_ID == 'ua') {
			global $DB;

			$sql = "SELECT * FROM `n_multilang_element` WHERE `ELEMENT_ID`='" . $arItem['ID'] . "' LIMIT 1;";
			$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			if ($row = $res->Fetch()) {
				$arItem['NAME'] = $row['NAME'];
				$arItem['PREVIEW_TEXT'] = $row['PREVIEW_TEXT'];
				$arItem['PREVIEW_TEXT_TYPE'] = $row['PREVIEW_TEXT_TYPE'];
				$arItem['DETAIL_TEXT'] = $row['DETAIL_TEXT'];
				$arItem['DETAIL_TEXT_TYPE'] = $row['DETAIL_TEXT_TYPE'];
			}
			foreach ($arItem['PROPERTIES'] as &$props) {
				$sql = "SELECT * FROM `n_multilang_props` WHERE `PROPERTY_ID`='" . $props['ID'] . "' LIMIT 1;";
				$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
				if ($row = $res->Fetch()) {
					$props['NAME'] = $row['NAME'];
				}
				if ($props['PROPERTY_TYPE'] == 'L') {
					if ($props['MULTIPLE'] == 'Y') {
						foreach ($props['VALUE_XML_ID'] as $n => $value_xml_id) {
							$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $value_xml_id . "' LIMIT 1;";
							$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
							if ($row = $res->Fetch()) {
								$props['VALUE'][$n] = $row['VALUE'];
							}
						}
					} else {
						$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $props['VALUE_XML_ID'] . "' LIMIT 1;";
						$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
						if ($row = $res->Fetch()) {
							$props['VALUE'] = $row['VALUE'];
						}
					}
				} elseif ($props['PROPERTY_TYPE'] == 'S' && is_null($props['USER_TYPE'])) {
					if ($props['MULTIPLE'] == 'Y') {
						foreach ($props['VALUE'] as $n => &$value) {
							$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $value . "' LIMIT 1;";
							$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
							if ($row = $res->Fetch()) {
								$value = $row['TRANSLATE'];
							}
						}
					} else {
						$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $props['VALUE'] . "' LIMIT 1;";
						$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
						if ($row = $res->Fetch()) {
							$props['VALUE'] = $row['TRANSLATE'];
						}
					}
				}
			}
			foreach ($arItem['DISPLAY_PROPERTIES'] as &$props) {
				$sql = "SELECT * FROM `n_multilang_props` WHERE `PROPERTY_ID`='" . $props['ID'] . "' LIMIT 1;";
				$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
				if ($row = $res->Fetch()) {
					$props['NAME'] = $row['NAME'];
				}
				if ($props['PROPERTY_TYPE'] == 'L') {
					if ($props['MULTIPLE'] == 'Y') {
						foreach ($props['VALUE_XML_ID'] as $n => $value_xml_id) {
							$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $value_xml_id . "' LIMIT 1;";
							$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
							if ($row = $res->Fetch()) {
								$props['VALUE'][$n] = $row['VALUE'];
							}
						}
					} else {
						$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $props['VALUE_XML_ID'] . "' LIMIT 1;";
						$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
						if ($row = $res->Fetch()) {
							$props['VALUE'] = $row['VALUE'];
						}
					}
				} elseif ($props['PROPERTY_TYPE'] == 'S' && is_null($props['USER_TYPE'])) {
					if ($props['MULTIPLE'] == 'Y') {
						foreach ($props['VALUE'] as $n => &$value) {
							$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $value . "' LIMIT 1;";
							$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
							if ($row = $res->Fetch()) {
								$value = $row['TRANSLATE'];
							}
						}
					} else {
						$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $props['VALUE'] . "' LIMIT 1;";
						$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
						if ($row = $res->Fetch()) {
							$props['VALUE'] = $row['TRANSLATE'];
						}
					}
				}
			}
		}
		return $arItem;
	}

	public static function TRSection(array &$arItem) {
		if (LANGUAGE_ID == 'ua') {
			global $DB;

			$sql = "SELECT * FROM `n_multilang_section` WHERE `SECTION_ID`='" . $arItem['ID'] . "' LIMIT 1;";
			$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			if ($row = $res->Fetch()) {
				$arItem['NAME'] = $row['NAME'];
				$arItem['DESCRIPTION'] = $row['DESCRIPTION'];
				$arItem['DESCRIPTION_TYPE'] = $row['DESCRIPTION_TYPE'];
			}
		}
		return $arItem;
	}

}

?>