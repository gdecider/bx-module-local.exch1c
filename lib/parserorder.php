<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;

class ParserOrder implements IParser
{

    private $_fileDir;
    private $_fileName;
    private $_filePrefixImport;
    private $_filePrefixExport;

    public function __construct($fileName, $filePrefixImport = '', $filePrefixExport = '')
    {
        $this->_fileName = $fileName;
        $this->_filePrefixImport = $filePrefixImport;
        $this->_filePrefixExport = $filePrefixExport;
    }

    public function getFileNameImport()
    {
        return  $this->_filePrefixImport . $this->_fileName;
    }

    public function getFileNameExport()
    {
        return  $this->_filePrefixExport . $this->_fileName;
    }

    public function setDir($dir)
    {
        $this->_fileDir = $dir;
    }

    static public function clearStr($str, $placeholder = ' ', $isFloat = false) {
        $str = preg_replace('/\s+/', $placeholder, trim($str));

        $arBadChars = [
//            chr(182) => '',
//            chr(194) => '',
//            chr(160) => '',
            ' ' => '', // это не пробел, это спецсимвол из 1С в ценах, ord показывает 194, но это не так
        ];
        $str = strtr($str, $arBadChars);

        if($isFloat) {
            $str = str_replace(',', '.', $str);
        }

        return $str;
    }

    public function getArray()
    {
        $filePath = $this->_fileDir . $this->_filePrefixImport . $this->_fileName;

        if(!file_exists($filePath)) {
            throw new \Exception('Не верный путь к файлу ' . $filePath);
        }

        $fileData = file_get_contents($filePath);
        $xml = new \SimpleXMLElement($fileData);

        $expDate = \DateTime::createFromFormat('d.m.Y H:i:s', (string)$xml["ДатаВремя"]);

        $arOrders = [];
        $arIds = [];
        $arXmlIds = [];
        $arAccountNumbers = [];

        $xmlObjects = $xml->ЗаказКлиента;

        foreach ($xmlObjects as $xmlObject) {
            $xmlId = self::clearStr((string) $xmlObject->ИД, '');
            $idSite = self::clearStr((string) $xmlObject->ИДСайт, '');
            $accountNumber = self::clearStr((string) $xmlObject->Номер);
            $arXmlIds[] = $xmlId;
            $arIds[] = $idSite;
            $arAccountNumbers[] = $accountNumber;

            $arOrder = [
                'ИД' => $xmlId,
                'ИДСайт' => $idSite,
                'Номер' => $accountNumber,
                'КодКлиента' => self::clearStr((string) $xmlObject->КодКлиента),
                'ДатаСоздания' => self::clearStr((string) $xmlObject->ДатаСоздания),
                'Сумма' => self::clearStr((string) $xmlObject->Сумма),
                'Комментарий' => self::clearStr((string) $xmlObject->Комментарий),
                'Статус' => self::clearStr((string) $xmlObject->Статус),
                'Товары' => [],
            ];

//            Debug::dump(isset($xmlObject->Товары));
//            Debug::dump(count($xmlObject->Товары));
//            Debug::dump($xmlObject->Товары);

            if(isset($xmlObject->Товары)) {
                foreach ($xmlObject->Товары->Товар as $item) {
                    $arOrder['Товары'][] = [
                        'ИД' => self::clearStr((string) $item->ИД),
                        'ИДСайт' => self::clearStr((string) $item->ИДСайт),
                        'Название' => self::clearStr((string) $item->Название),
                        'Количество' => self::clearStr((string) $item->Количество, '', true),
                        'Цена' => self::clearStr((string) $item->Цена, '', true),
                        'Сумма' => self::clearStr((string) $item->Сумма, '', true),
                        'Статус' => self::clearStr((string) $item->Статус),
                    ];
                }
            }

            $key = $accountNumber ? $accountNumber : $xmlId;
            $arOrders[$key] = $arOrder;
        }

        $arResult = [
            'DATE' => $expDate,
            'CODES' => $arAccountNumbers,
            'OBJECTS' => $arOrders,
        ];

//        Debug::dump($arResult);

        return $arResult;
    }

    public function makeXml($arData) {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><V8Exch:_1CV8DtUD xmlns:V8Exch="http://www.1c.ru/V8/1CV8DtUD/" xmlns:core="http://v8.1c.ru/data" xmlns:v8="http://v8.1c.ru/8.1/data/enterprise/current-config" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>');
        $NS = array(
            'V8Exch' => 'http://www.1c.ru/V8/1CV8DtUD/',
            'v8' => 'http://v8.1c.ru/8.1/data/enterprise/current-config',
            // whatever other namespaces you want
        );

        // now register them all in the root
        foreach ($NS as $prefix => $name) {
            $xml->registerXPathNamespace($prefix, $name);
        }

        $rootNode = $xml->addChild('V8Exch:Data', null, $NS['V8Exch']);
        foreach ($arData as $arRow) {
            $orderStatus = $arRow['PROPS']['EXT_STATUS']
                ? $arRow['PROPS']['EXT_STATUS']
                : $arRow['PROPS']['EXT_STATUS_UR'];

            $orderNode = $rootNode->addChild('v8:DocumentObject.ЗаказКлиента', null, $NS['v8']);
            $orderNode->addChild('v8:ИД', $arRow['XML_ID']);
            $orderNode->addChild('v8:ИДСайт', $arRow['ID']);
            $orderNode->addChild('v8:Номер', $arRow['ACCOUNT_NUMBER']);
            $orderNode->addChild('v8:КодКлиента', $arRow['USER_LOGIN']);
            $orderNode->addChild('v8:ДатаСоздания', $arRow['DATE_INSERT']);
            $orderNode->addChild('v8:Сумма', $arRow['PRICE']);
            $orderNode->addChild('v8:Комментарий', $arRow['COMMENTS']);
            $orderNode->addChild('v8:Статус', $orderStatus);

            $prodsNode = $orderNode->addChild('v8:Товары', null);
            foreach($arRow['ITEMS'] as $arProd) {
                $prodNode = $prodsNode->addChild('v8:Товар', null);

                $sum = (float)$arProd['PRICE'] * (float)$arProd['QUANTITY'];

                $prodNode->addChild('v8:ИД', $arProd['PRODUCT_XML_ID']);
                $prodNode->addChild('v8:ИДСайт', $arProd['ID']);
                $prodNode->addChild('v8:Название', $arProd['NAME']);
                $prodNode->addChild('v8:Количество', $arProd['QUANTITY']);
                $prodNode->addChild('v8:Цена', $arProd['PRICE']);
                $prodNode->addChild('v8:Сумма', $sum);
                $prodNode->addChild('v8:Статус', $arProd['EXCH_STATUS']);
            }
        }

        return $xml;
    }
}