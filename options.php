<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

$arModConf = include __DIR__ . '/mod_conf.php';
// нужна для управления правами модуля
$module_id = strtolower($arModConf['name']);

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);

if($APPLICATION->GetGroupRight($module_id) < "R") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

Loader::includeModule($module_id);

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$aTabs = [
    [// вкладка "Настройки"
        'DIV' => 'edit1', // Код вкладки
        'TAB' => Loc::getMessage($arModConf['name'] . '_TAB_SETTINGS'), // то что написано на табе
        'TITLE' => Loc::getMessage($arModConf['name'] . '_TAB_TITLE_SETTINGS'), // То что написано в области таба
        'OPTIONS' => [
            [
                $arModConf['name'] . '_FTP_PATH', // Имя поля
                'Путь FTP для обмена с 1С', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FTP_PORT', // Имя поля
                'Порт FTP для обмена с 1С', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FTP_USER', // Имя поля
                'Логин FTP для обмена с 1С', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FTP_PASS', // Имя поля
                'Пароль FTP для обмена с 1С', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FILE_PREFIX_IMPORT', // Имя поля
                'Префикс для импорта из 1С', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FILE_PREFIX_EXPORT', // Имя поля
                'Префикс для экспорта в 1С', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FTP_EXCH_DIR', // Имя поля
                'Путь к директории обмена на FTP', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_SERVER_EXCH_DIR', // Имя поля
                'Путь к директории обмена на сервере', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FILE_NAME_USERS', // Имя поля
                'Название файла контрагентов', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FILE_NAME_STORES', // Имя поля
                'Название файла остатков', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FILE_NAME_ORDERS', // Имя поля
                'Название файла заказов', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_FILE_NAME_STATISTICS', // Имя поля
                'Название файла статистики заказов', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_IB_CODE', // Имя поля
                'Код инфоблока для заявок на регистрацию', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
            $arModConf['name'] . '_EMAIL_TMPL_REGCONFIRM', // Имя поля
                'Почтовый шаблон уведомления о регистрации', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_EMAIL_TMPL_EDITCONFIRM', // Имя поля
                'Почтовый шаблон уведомления об изменении данных', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_EMAIL_TMPL_REGREQUEST', // Имя поля
                'Почтовый шаблон заявки на регистрацию', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],

            [
                $arModConf['name'] . '_EMAIL_TMPL_EDITREQUEST', // Имя поля
                'Почтовый шаблон изменение данных клиента', // Подпись поля
                '', // Значение по умолчанию
                [
                    'text',
                    50, // длина
                    'noautocomplete' => 1, // отключение автодополнения в браузере
                ], // тип с настройками
                'N', // Деактивировать (ReadOnly)
            ],
        ]
    ],
    [// вкладка "Права"
        'DIV' => 'edit2',
        'TAB' => Loc::getMessage($arModConf['name'] . '_TAB_RIGHTS'),
        'TITLE' => Loc::getMessage($arModConf['name'] . '_TAB_TITLE_RIGHTS'),
    ]
];

// сохранение

if ($request->isPost() && $request['update'] && check_bitrix_sessid()) {
    // Сохраняем настройки
    foreach ($aTabs as $aTab) {
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption)) {
                continue;
            }

            // пропустим статические куски
            if ($arOption['note'] || in_array($arOption[3][0], ['statichtml', 'statictext'])) {
                continue;
            }

            $optionName = $arOption[0];
            $optionValue = $request->getPost(str_replace('.', '_', $optionName));

            Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
        }
    }

    // Что бы повторно не отправилась форма при обновлении страницы
    LocalRedirect($APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID . '&mid_menu=1&mid=' . urlencode($module_id) .
        '&tabControl_active_tab=' . urlencode($request['tabControl_active_tab']) . '&sid=' . SITE_ID);
}

// рисуем форму
$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>

<?php $tabControl->Begin();?>
    <form method="POST"
          action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request['mid'])?>&amp;lang=<?=$request['lang']?>"
          name="<?=strtolower($arModConf['name'])?>_settings"
          enctype="multipart/form-data">
        <? foreach($aTabs as $aTab): ?>
            <? if($aTab['OPTIONS']): ?>
                <? $tabControl->BeginNextTab(); ?>
                <? __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>
            <? endif; ?>
        <? endforeach; ?>

        <? /* Свое поле ?>
        <tr>
            <td class="adm-detail-content-cell-l" width="50%">БД АВТО для загрузки</td>
            <td class="adm-detail-content-cell-r" width="50%"><input type="file" name="FILE_SQL_AVTO"></td>
        </tr>
        <tr>
            <td class="adm-detail-content-cell-l" width="50%">БД ДИСКИ для загрузки</td>
            <td class="adm-detail-content-cell-r" width="50%"><input type="file" name="FILE_SQL_DISK"></td>
        </tr>
        <tr>
            <td class="adm-detail-content-cell-l" width="50%">БД ШИНЫ для загрузки</td>
            <td class="adm-detail-content-cell-r" width="50%"><input type="file" name="FILE_SQL_SHINA"></td>
        </tr>
        <? // */?>

        <? $tabControl->BeginNextTab(); ?>

        <? // функционал настройки прав доступа к модулю
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php');?>

        <? $tabControl->Buttons(); ?>

        <input type="submit" name="update" value="<?=Loc::getMessage('MAIN_SAVE')?>">
        <input type="reset" name="reset" value="<?=Loc::getMessage('MAIN_RESET')?>">
        <?=bitrix_sessid_post();?>
    </form>
<?php $tabControl->End();?>