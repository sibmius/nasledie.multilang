<?php

/* 
 * @author  Andi SiBmius <sibmius@gmail.com>
 * @license LGPL 3.0
 * @copyright 2007 RoW-Team
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $DB;

$sql="DELETE FROM `n_multilang_iblock` WHERE `ID`='".$_REQUEST['ID']."' LIMIT 1;";

$res=$DB->Query($sql, false, __FILE__." > ".__LINE__);

LocalRedirect("/bitrix/admin/nasledie.multilang_iblock.php?lang=" . LANG);
