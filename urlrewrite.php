<?php
$arUrlRewrite=array (
  [
    "CONDITION" => "#^/api/rest-component/([a-zA-Z0-9]+)/([a-zA-Z0-9\.]+)/([a-zA-Z0-9]+)/?.*#",
    "RULE" => "mode=class&c=$1:$2&action=$3",
    "PATH" => "/bitrix/services/main/ajax.php",
  ]
);
