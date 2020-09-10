<?php
/*
 * @author  Andi SiBmius <sibmius@gmail.com>
 * @license LGPL 3.0
 * @copyright 2007 RoW-Team
 */


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");


$MODULE_ID = 'nasledie.multilang';

use \Bitrix\Main\Localization\Loc as Loc;

\Bitrix\Main\Loader::includeModule($MODULE_ID);

Loc::loadMessages(__FILE__);

$sTableID = "tbl_enum"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "ENUM_XML_ID", "asc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка


$FilterArr = Array(
	"find_id",
	"find_prop",
	"find_enum",
	"find_value",
	"find_lang",
);

$lAdmin->InitFilter($FilterArr);

$arFilter = Array(
	"ID" => $find_id,
	"PROPERTY_ID"=>$find_prop,
	"ENUM_XML_ID" => $find_enum,
	"VALUE" => $find_value,
	"LANG" => $find_lang,
);

global $DB;

$sql = "SELECT * FROM `n_multilang_enum`";


if ($set_filter == 'Y') {

	$where = array();
	if ($find != '' && $find_type != '') {
		$where[] = "`" . $find_type . "` = '" . $DB->ForSql($find) . "' ";
	}

	foreach ($arFilter as $F => $V) {
		if ($V != '') {
			$where[] = "`" . $F . "` = '" . $DB->ForSql($V) . "' ";
		}
	}
	if (!empty($where)) {
		$sql .= " WHERE " . implode(' AND ', $where);
	}
}

if ($by != '' && $order != '') {
	$sql .= " ORDER BY `" . $by . "` " . $order;
}
$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("IBLOCK_TITLE")));


$lAdmin->AddHeaders(array(
	array("id" => "ID",
		"content" => "ID",
		"sort" => "ID",
		"default" => true,
	),
	array("id" => "PROPERTY_ID",
		"content" => "PROPERTY_ID",
		"sort" => "PROPERTY_ID",
		"default" => true,
	),
	array("id" => "ENUM_XML_ID",
		"content" => "ENUM_XML_ID",
		"sort" => "ENUM_XML_ID",
		"default" => true,
	),
	array("id" => "VALUE",
		"content" => GetMessage("TBL_VALUE"),
		"sort" => "VALUE",
		"default" => true,
	),
	array("id" => "LANG",
		"content" => GetMessage("TBL_LANG"),
		"sort" => "LANG",
		"default" => true,
	),
));

$arPropsList = array();

while ($arRes = $rsData->NavNext(true, "f_")) {  // создаем строку. результат - экземпляр класса CAdminListRow
	$row = & $lAdmin->AddRow($f_ID, $arRes);

	$row->AddViewField("ENUM_XML_ID", '<a href="nasledie.multilang_enum_edit.php?ENUM_XML_ID=' . $f_ENUM_XML_ID . '&lang=' . LANG . '&PROPERTY_ID='.$f_PROPERTY_ID.'">' . $f_ENUM_XML_ID . '</a>');

	$row->AddViewField("ID", $f_ID);
	$row->AddViewField("PROPERTY_ID", $f_PROPERTY_ID);
	$row->AddViewField("VALUE", $f_VALUE);
	$row->AddViewField("LANG", $f_LANG);

	$arActions = Array();

	$arActions[] = array(
		"ICON" => "edit",
		"DEFAULT" => true,
		"TEXT" => GetMessage("IBLOCK_EDIT"),
		"ACTION" => $lAdmin->ActionRedirect("nasledie.multilang_enum_edit.php?ENUM_XML_ID=" . $f_ENUM_XML_ID)
	);

	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => GetMessage("IBLOCK_DEL"),
		"ACTION" => "if(confirm('" . GetMessage('DEL_CONFIRM') . "')) " . $lAdmin->ActionRedirect("nasledie.multilang_enum_del.php?ID=" . $f_ID)
	);

	$row->AddActions($arActions);
}
$lAdmin->AddFooter(
	array(
		array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()), // кол-во элементов
		array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"), // счетчик выбранных элементов
	)
);

$aContext = array(
	array(
		"TEXT" => GetMessage("POST_ADD"),
		"LINK" => "nasledie.multilang_enum_edit.php?lang=" . LANG,
		"TITLE" => GetMessage("POST_ADD_TITLE"),
		"ICON" => "btn_new",
	),
);
$lAdmin->CheckListMode();


$lAdmin->AddAdminContextMenu($aContext);

$APPLICATION->SetTitle(GetMessage("IBLOCK_TITLE"));


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");




$oFilter = new CAdminFilter(
	$sTableID . "_filter", array(
	"ID",
	"ENUM_XML_ID",
	GetMessage("TBL_VALUE"),
	GetMessage("TBL_LANG")
	)
);
?>
<form name="find_form" method="get" action="<? echo $APPLICATION->GetCurPage(); ?>">
	<? $oFilter->Begin(); ?>
	<tr>
		<td><b><?= GetMessage("TBL_FIND") ?>:</b></td>
		<td>
			<input type="text" size="25" name="find" value="<? echo htmlspecialchars($find) ?>">
			<?
			$arr = array(
				"reference" => array(
					"ID",
					"ENUM_XML_ID",
					GetMessage("TBL_VALUE"),
					GetMessage("TBL_LANG"),
				),
				"reference_id" => array(
					"ID",
					"ENUM_XML_ID",
					"VALUE",
					"LANG",
				)
			);
			echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
			?>
		</td>
	</tr>

	<tr>
		<td><?= "ID" ?>:</td>
		<td>
			<input type="text" name="find_id"  value="<? echo htmlspecialchars($find_id) ?>">
		</td>
	</tr>
	<tr>
		<td>ENUM_XML_ID:</td>
		<td><input type="text" name="find_enum"  value="<? echo htmlspecialchars($find_enum) ?>"></td>
	</tr>

	<tr>
		<td><?= GetMessage("TBL_VALUE") . ":" ?></td>
		<td><input type="text" name="find_value"  value="<? echo htmlspecialchars($find_value) ?>"></td>
	</tr>
	<tr>
		<td><?= GetMessage("TBL_LANG") . ":" ?></td>
		<td><input type="text" name="find_lang"  value="<? echo htmlspecialchars($find_lang) ?>"></td>
	</tr>

	<?php
	$oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
	$oFilter->End();
	?>
</form>

<?php
// выведем таблицу списка элементов
$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");

