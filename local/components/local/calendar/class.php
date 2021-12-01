<?php

use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use CBitrixComponent;
\Bitrix\Main\Loader::includeModule('iblock');

class FeedbackComponent extends CBitrixComponent implements Controllerable, Errorable
{
    protected ErrorCollection $errorCollection;
    static $iblockId = 1;
    public function onPrepareComponentParams($arParams)
    {
        $this->errorCollection = new ErrorCollection();
        return $arParams;
    }

    public function executeComponent()
    {
        global $USER;
        $this->arResult['IS_AUTH'] = $USER->IsAuthorized();
        $this->IncludeComponentTemplate();
    }

    public function getErrors(): array
    {
        return $this->errorCollection->toArray();
    }

    public function getErrorByCode($code): Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    // Описываем действия
    public function configureActions(): array
    {
        return [
            'save' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                ]
            ],
            'get' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                ]
                ],
            'getTask' => [
                'prefilters' => [
                    new ActionFilter\Authentication(), 
                ]
            ],
            'updateTask' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                ]
            ],
        ];
    }

    // Сюда передаются параметры из ajax запроса, навания точно такие же как и при отправке запроса.
    // $_REQUEST['username'] будет передан в $username, $_REQUEST['email'] будет передан в $email и т.д.
    public function saveAction(string $name = '', string $date = '', string $description = '', string $status): array
    {
        global $USER;
        try {
            $el = new CIBlockElement;
            $arLoadProductArray = Array(
                "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                "IBLOCK_ID"      => self::$iblockId,
                "NAME"           => $name,
                "PROPERTY_VALUES"=> [
                    'DESCRIPTION' => $description,
                    'DATE' => $date,
                    'STATUS' => $status,
                    'USER' => $USER->GetID()
                ],
                "ACTIVE"         => "Y",            // активен
            );

            if($taskID = $el->Add($arLoadProductArray))
            {
                return [
                    'success' => true,
                    'id' => $taskID,
                    'name' => $name
                ];
            }
            else
            {
                return [
                    'success' => false,
                    'message' => $el->LAST_ERROR
                ];
            }
        } catch (Exceptions\EmptyEmail $e) {
            $this->errorCollection[] = new Error($e->getMessage());
            return [
                "result" => "Произошла ошибка",
            ];
        }
    }
    public function getAction(string $year, string $month): array
    {
        global $USER;
        try {
            $datePostfix = $month.$year;
            $result = [];
            $res = CIBlockElement::GetList(
                array("ID"=>"ASC"),
                array(
                    "IBLOCK_ID" => self::$iblockId,
                    "ACTIVE" => "Y",
                    "PROPERTY_USER"=> $USER->GetID(),
                    ">=PROPERTY_DATE"=>"01". $datePostfix,
                    "<=PROPERTY_DATE" => "31" . $datePostfix
                ),
                false,
                array(),
                array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*")
            );
            while ($ob = $res->GetNextElement()
            ) {
                $el = [];
                $arFields = $ob->GetFields();
                $arProps = $ob->GetProperties();
                $el = array_merge($arFields, $arProps);
                $result[] = [
                    'id'   => $el['ID'], 
                    'date' => $el['DATE']['VALUE'],
                    'name' => $el['NAME']
                ];
            }
            if(!empty($result))
            {
                return [
                    'success' => true,
                    'items' => $result
                ];
            }
            else {
                return [
                    'success' => false
                ];
            }
        } catch (Exceptions\EmptyEmail $e) {
            $this->errorCollection[] = new Error($e->getMessage());
            return [
                "result" => "Произошла ошибка",
            ];
        }
    }
    public function getTaskAction(string $id): array
    {
        try {
            $result = [];
            $res = CIBlockElement::GetList(
                array("ID" => "ASC"),
                array(
                    "IBLOCK_ID" => self::$iblockId,
                    "ACTIVE" => "Y",
                    "ID"=> $id
                ),
                false,
                array(),
                array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*")
            );
            while ($ob = $res->GetNextElement()) {
                $el = [];
                $arFields = $ob->GetFields();
                $arProps = $ob->GetProperties();
                $el = array_merge($arFields, $arProps);
                $result = [
                    'id'   => $el['ID'],
                    'date' => $el['DATE']['VALUE'],
                    'name' => $el['NAME'],
                    'description' => $el['DESCRIPTION']['VALUE'],
                    'status' => $el['STATUS']['VALUE_ENUM_ID'],
                ];
            }
            if (!empty($result)) {
                return [
                    'success' => true,
                    'item' => $result
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        } catch (Exceptions\EmptyEmail $e) {
            $this->errorCollection[] = new Error($e->getMessage());
            return [
                "result" => "Произошла ошибка",
            ];
        }
    }
    public function updateTaskAction(string $id, string $name = '',string $date, string $description = '', string $status): array
    {
        global $USER;
        try {
            $arLoadProductArray = array(
                "MODIFIED_BY"    => $USER->GetID(),
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID"      => self::$iblockId,
                "NAME"           => $name,
                "PROPERTY_VALUES" => [
                    'DESCRIPTION' => $description,
                    'STATUS' => $status,
                    "USER" => $USER->GetID(),
                    "DATE" => $date
                ],
                "ACTIVE"         => "Y",            
            );

            $el = new CIBlockElement;
            $res = $el->Update($id, $arLoadProductArray);
            if ($res) {
                return [
                    'success' => true,
                    'item' => [
                        'id' => $id,
                        'name' => $name
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $res->LAST_ERROR
                ];
            }
        } catch (Exceptions\EmptyEmail $e) {
            $this->errorCollection[] = new Error($e->getMessage());
            return [
                "result" => "Произошла ошибка",
            ];
        }
    }
}
