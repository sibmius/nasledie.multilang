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

$sTableID = "tbl_props"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "PROPERTY_ID", "asc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка


$FilterArr = Array(
	"find_id",
	"find_props",
	"find_name",
	"find_lang",
);

$lAdmin->InitFilter($FilterArr);

$arFilter = Array(
	"ID" => $find_id,
	"PROPERTY_ID" => $find_props,
	"NAME" => $find_name,
	"LANG" => $find_lang,
);

global $DB;

$sql = "SELECT * FROM `n_multilang_props`";


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
	$sql .= " ORDER BY " . $by . " " . $order;
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
		"content" => GetMessage("TBL_PROPS"),
		"sort" => "PROPERTY_ID",
		"default" => true,
	),
	array("id" => "NAME",
		"content" => GetMessage("TBL_NAME"),
		"sort" => "NAME",
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

	$row->AddViewField("PROPERTY_ID", '<a href="nasledie.multilang_property_edit.php?PROPERTY_ID=' . $f_PROPERTY_ID . '&lang=' . LANG . '">' . $f_PROPERTY_ID . '</a>');


	$row->AddViewField("ID", $f_ID);
	$row->AddViewField("NAME", $f_NAME);
	$row->AddViewField("LANG", $f_LANG);

	$arActions = Array();

	$arActions[] = array(
		"ICON" => "edit",
		"DEFAULT" => true,
		"TEXT" => GetMessage("IBLOCK_EDIT"),
		"ACTION" => $lAdmin->ActionRedirect("nasledie.multilang_property_edit.php?PROPERTY_ID=" . $f_PROPERTY_ID)
	);

	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => GetMessage("IBLOCK_DEL"),
		"ACTION" => "if(confirm('" . GetMessage('DEL_CONFIRM') . "')) " . $lAdmin->ActionRedirect("nasledie.multilang_property_del.php?ID=" . $f_ID)
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
		"LINK" => "nasledie.multilang_property_edit.php?lang=" . LANG,
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
	"PROPERTY_ID",
	GetMessage("TBL_NAME"),
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
					"PROPERTY_ID",
					GetMessage("TBL_NAME"),
					GetMessage("TBL_LANG"),
				),
				"reference_id" => array(
					"ID",
					"PROPERTY_ID",
					"NAME",
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
		<td>PROPERTY_ID:</td>
		<td><input type="text" name="find_props"  value="<? echo htmlspecialchars($find_props) ?>"></td>
	</tr>

	<tr>
		<td><?= GetMessage("TBL_NAME") . ":" ?></td>
		<td><input type="text" name="find_name"  value="<? echo htmlspecialchars($find_name) ?>"></td>
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
