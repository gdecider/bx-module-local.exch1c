<?
/**
 * @var array $arModConf
 */

use \Local\Exch1c\FtpClient;
use \Local\Exch1c\ParserUser;
use \Local\Exch1c\SyncerUser;

$request = \Bitrix\Main\Context::getCurrent()->getRequest();

$module_id = strtolower($arModConf['name']);
$module_prefix = str_replace('.', '_', $arModConf['name']);

$ftp = [
    'path' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_PATH'),
    'user' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_USER'),
    'pass' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_PASS'),
    'dir' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_EXCH_DIR'),
];

$fileName = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_NAME_USERS');
$filePrefix = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_IMPORT');
$dirServer = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_SERVER_EXCH_DIR');

$ftpClient = new FtpClient($ftp['path'], $ftp['user'], $ftp['pass'], $ftp['dir'], $dirServer);
$xmlParser = new ParserUser($fileName, $filePrefix);

$ftpClient->setParser($xmlParser);
$arData = $ftpClient->syncFile();

$syncer = new SyncerUser();

if ($request->isPost() && $request->getPost('rqType') === 'userDoImport') {
    $syncer->import($arData);
}

if ($request->isPost() && $request->getPost('rqType') === 'userDoExport') {
    \Bitrix\Main\Diag\Debug::dump('Do export...');
    $syncer->export();
}
?>

<form method="post" action="">
    <input type="hidden" name="rqType" value="userDoImport">
    <input type="submit" value="Получить пользователей из 1С">
</form>
<br>
<br>
<form method="post" action="">
    <input type="hidden" name="rqType" value="userDoExport">
    <input type="submit" value="Передать пользователей в 1С">
</form>
