<?

namespace Nasledie\MultiLang;

global $DB, $APPLICATION, $MESS, $DBType;

$MODULE_ID = 'nasledie.multilang';

class NMLAgent {

	public static function TRElement(array &$arItem) {
		if (LANGUAGE_ID == 'ua' && $arItem['ID'] > 0) {
			global $DB;

			$sql = "SELECT * FROM `n_multilang_element` WHERE `ELEMENT_ID`='" . $DB->ForSql($arItem['ID']) . "' LIMIT 1;";
			$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			if ($row = $res->Fetch()) {
				$arItem['NAME'] = $row['NAME'];
				$arItem['PREVIEW_TEXT'] = $row['PREVIEW_TEXT'];
				$arItem['PREVIEW_TEXT_TYPE'] = $row['PREVIEW_TEXT_TYPE'];
				$arItem['DETAIL_TEXT'] = $row['DETAIL_TEXT'];
				$arItem['DETAIL_TEXT_TYPE'] = $row['DETAIL_TEXT_TYPE'];
			}
			foreach ($arItem['PROPERTIES'] as &$props) {
				$sql = "SELECT * FROM `n_multilang_props` WHERE `PROPERTY_ID`='" . $DB->ForSql($props['ID']) . "' LIMIT 1;";
				$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
				if ($row = $res->Fetch()) {
					$props['NAME'] = $row['NAME'];
				}
				if ($props['PROPERTY_TYPE'] == 'L') {
					if ($props['MULTIPLE'] == 'Y') {
						foreach ($props['VALUE_XML_ID'] as $n => $value_xml_id) {
							$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $DB->ForSql($value_xml_id) . "' LIMIT 1;";
							$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
							if ($row = $res->Fetch()) {
								$props['VALUE'][$n] = $row['VALUE'];
							}
						}
					} else {
						$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $DB->ForSql($props['VALUE_XML_ID']) . "' LIMIT 1;";
						$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
						if ($row = $res->Fetch()) {
							$props['VALUE'] = $row['VALUE'];
						}
					}
				} elseif ($props['PROPERTY_TYPE'] == 'S' && is_null($props['USER_TYPE'])) {
					if ($props['MULTIPLE'] == 'Y') {
						foreach ($props['VALUE'] as $n => &$value) {
							$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $DB->ForSql($value) . "' LIMIT 1;";
							$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
							if ($row = $res->Fetch()) {
								$value = $row['TRANSLATE'];
							}
						}
					} else {
						$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $DB->ForSql($props['VALUE']) . "' LIMIT 1;";
						$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
						if ($row = $res->Fetch()) {
							$props['VALUE'] = $row['TRANSLATE'];
						}
					}
				}
			}
			foreach ($arItem['DISPLAY_PROPERTIES'] as &$props) {
				$sql = "SELECT * FROM `n_multilang_props` WHERE `PROPERTY_ID`='" . $DB->ForSql($props['ID']) . "' LIMIT 1;";
				$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
				if ($row = $res->Fetch()) {
					$props['NAME'] = $row['NAME'];
				}
				if ($props['PROPERTY_TYPE'] == 'L') {
					if ($props['MULTIPLE'] == 'Y') {
						foreach ($props['VALUE_XML_ID'] as $n => $value_xml_id) {
							$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $DB->ForSql($value_xml_id) . "' LIMIT 1;";
							$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
							if ($row = $res->Fetch()) {
								$props['VALUE'][$n] = $row['VALUE'];
							}
						}
					} else {
						$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $DB->ForSql($props['VALUE_XML_ID']) . "' LIMIT 1;";
						$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
						if ($row = $res->Fetch()) {
							$props['VALUE'] = $row['VALUE'];
						}
					}
				} elseif ($props['PROPERTY_TYPE'] == 'S' && is_null($props['USER_TYPE'])) {
					if ($props['MULTIPLE'] == 'Y') {
						foreach ($props['VALUE'] as $n => &$value) {
							$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $DB->ForSql($value) . "' LIMIT 1;";
							$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
							if ($row = $res->Fetch()) {
								$value = $row['TRANSLATE'];
							}
						}
					} else {
						$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $DB->ForSql($props['VALUE']) . "' LIMIT 1;";
						$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
						if ($row = $res->Fetch()) {
							$props['VALUE'] = $row['TRANSLATE'];
						}
					}
				}
			}
			$arItem['IPROPERTY_VALUES'] = static::TRSeo('E', $arItem['ID'], $arItem['IPROPERTY_VALUES']);
		}

		return $arItem;
	}

	public static function TRSection(array &$arItem) {
		if (LANGUAGE_ID == 'ua' && $arItem['ID'] > 0) {
			global $DB;

			$sql = "SELECT * FROM `n_multilang_section` WHERE `SECTION_ID`='" . $DB->ForSql($arItem['ID']) . "' LIMIT 1;";
			$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			if ($row = $res->Fetch()) {
				$arItem['NAME'] = $row['NAME'];
				$arItem['DESCRIPTION'] = $row['DESCRIPTION'];
				$arItem['DESCRIPTION_TYPE'] = $row['DESCRIPTION_TYPE'];
			}
			$arItem['IPROPERTY_VALUES'] = static::TRSeo('S', $arItem['ID'], $arItem['IPROPERTY_VALUES']);
		}

		return $arItem;
	}

	public static function TRIblock(array &$arItem) {
		if (LANGUAGE_ID == 'ua' && $arItem['ID'] > 0) {
			global $DB;

			$sql = "SELECT * FROM `n_multilang_iblock` WHERE `IBLOCK_ID`='" . $DB->ForSql($arItem['ID']) . "' LIMIT 1;";
			$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			if ($row = $res->Fetch()) {
				$arItem['NAME'] = $row['NAME'];
			}
			$arItem['IPROPERTY_VALUES'] = static::TRSeo('I', $arItem['ID'], $arItem['IPROPERTY_VALUES']);
		}

		return $arItem;
	}

	public static function TRString(string $str) {
		if (LANGUAGE_ID == 'ua') {
			global $DB;

			$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $DB->ForSql($str) . "' AND `LANG`='ua' LIMIT 1;";
			$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			if ($row = $res->Fetch()) {
				if ($row['TRANSLATE'] != '') {
					return $row['TRANSLATE'];
				}
			}
		}
		return $str;
	}

	public static function TRSeo(string $type, int $ID, $seo_str = array()) {
		if (LANGUAGE_ID == 'ua') {
			global $DB;

			$sql = "SELECT * FROM `n_multilang_seo` WHERE `OBJECT_TYPE`='" . $DB->ForSql($type) . "' AND `OBJECT_ID`='" . $DB->ForSql($ID) . "' AND `LANG`='ua' LIMIT 1;";
			$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			if ($row = $res->Fetch()) {
				if ($type == 'E') {
					$seo_str['ELEMENT_META_TITLE'] = $row['TITLE'];
					$seo_str['ELEMENT_META_KEYWORDS'] = $row['KEY'];
					$seo_str['ELEMENT_META_DESCRIPTION'] = $row['DESC'];
					$seo_str['ELEMENT_PAGE_TITLE'] = $row['H1'];
				} else {
					$seo_str['SECTION_META_TITLE'] = $row['TITLE'];
					$seo_str['SECTION_META_KEYWORDS'] = $row['KEY'];
					$seo_str['SECTION_META_DESCRIPTION'] = $row['DESC'];
					$seo_str['SECTION_PAGE_TITLE'] = $row['H1'];
				}
			}
		}
		return $seo_str;
	}

	public static function TRPay(array &$arItem) {
		if (LANGUAGE_ID == 'ua' && $arItem['ID'] > 0) {
			global $DB;

			$sql = "SELECT * FROM `n_multilang_pay` WHERE `PAY_ID`='" . $DB->ForSql($arItem['ID']) . "' LIMIT 1;";
			$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			if ($row = $res->Fetch()) {
				$arItem['NAME'] = $row['NAME'];
				$arItem['DESCRIPTION'] = $row['DESCRIPTION'];
				$arItem['DESCRIPTION_TYPE'] = $row['DESCRIPTION_TYPE'];
			}
		}

		return $arItem;
	}

	public static function TRSmart(array &$arItem) {
		if (LANGUAGE_ID == 'ua' && $arItem['ID'] > 0) {
			if (!isset($arItem["PRICE"]) && !empty($arItem["VALUES"])) {
				global $DB;
				$sql = "SELECT * FROM `n_multilang_props` WHERE `PROPERTY_ID`='" . $DB->ForSql($arItem['ID']) . "' LIMIT 1;";
				$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
				if ($row = $res->Fetch()) {
					$arItem['NAME'] = $row['NAME'];
				}
				foreach ($arItem["VALUES"] as &$val) {
					$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $DB->ForSql($val['URL_ID']) . "' LIMIT 1;";
					$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
					if ($row = $res->Fetch()) {
						$val['VALUE'] = $row['VALUE'];
					} else {
						$sql = "SELECT * FROM `n_multilang_value` WHERE `ORIG`='" . $DB->ForSql($val['VALUE']) . "' LIMIT 1;";
						$res = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
						if ($row = $res->Fetch()) {
							$val['VALUE'] = $row['TRANSLATE'];
						}
					}
				}
			}
		}
		return $arItem;
	}

}

?>