<?
require_once($_SERVER["DOCUMENT_ROOT"]."/phone_replace/const.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/phone_replace/db.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/phone_replace/logging.php");

$JSONArray = null;

// ------------ Скрипт подмены телефонных номеров ---------------
// По каким фрагментам в URI мы будем определять источники трафика
$uri_indication['remarketing'] = 'utm_campaign=%D0%A0%D0%B5%D0%BC%D0%B0%D1%80%D0%BA%D0%B5%D1%82%D0%B8%D0%BD%D0%B3';
$uri_indication['cpc'] = 'utm_medium=cpc';
$uri_indication['email'] = 'utm_medium=email';

// Если используем JS
if (Pr_UseJS())
{
	// Устанавливаем куку для phone_replace.js
	if (Pr_Debug())
	{
		setcookie ( "debug", true, 0, '/' );
	}
	else
	{
		setcookie ( "debug", true, 0, '/' );
	}
}

debug_to_console('HTTP_REFERER: ' . $_SERVER['HTTP_REFERER']);

// Откуда пришёл посетитель?
if ( strpos ( $_SERVER['REQUEST_URI'], $uri_indication['remarketing'] ) )
{
	$source_type = "remarketing";
}
else if ( strpos ( $_SERVER['REQUEST_URI'], $uri_indication['cpc'] ) AND !strpos ( $_SERVER['REQUEST_URI'], $uri_indication['remarketing'] ) )
{
	$source_type = "cpc";
}
else if ( strpos ( $_SERVER['REQUEST_URI'], $uri_indication['email'] ) )
{
	$source_type = "email";
}
else if ( strpos ( $_SERVER['HTTP_REFERER'], 'yandex.ru' ) )
{
	$source_type = "yandex";
}
else if ( strpos ( $_SERVER['HTTP_REFERER'], 'www.google.' ) )
{
	$source_type = "google";
}
else if ( strpos ( $_SERVER['HTTP_REFERER'], 'go.mail.ru/search' ) )
{
	$source_type = "mailru";
}
else if ( strpos ( $_SERVER['HTTP_REFERER'], 'nova.rambler.ru/search' ) )
{
	$source_type = "rambler";
}
else if ( strpos ( $_SERVER['HTTP_REFERER'], 'bing.com/search' ) )
{
	$source_type = "bing";
}
else if ( strpos ( $_SERVER['HTTP_REFERER'], 'ask.com/web?q=' ) )
{
	$source_type = "ask";
}
else if ( strpos ( $_SERVER['HTTP_REFERER'], 'search.yahoo.com' ) )
{	 
	$source_type = "yahoo";
}
else
{
	$source_type = "direct";
}

debug_to_console('$source_type: ' . $source_type);

/*
$db = new pr_db();
$sql = "INSERT INTO `traffic_sources` (`id`, `userip`, `referer`, `datetime`) VALUES (NULL, \'" . $_SERVER['REMOTE_ADDR'] . "\', \'" . $source_type . "\', CURRENT_TIME());";


if ($db->get_error())
{
	die("MySQL error: " . $db->get_error());
}

$res = $db->execute($sql);

if ($res == false)
{
	debug_to_console($db->get_error());
	//var_dump($res->num_rows);
//	while($r = $res->fetch_assoc()) { $result[] = $r; }
}*/

// Устанавливаю куку, если ещё не существует
if ( !isset($_COOKIE['source_type']) && $source_type && $source_type != "direct" )
{
	$options = array (
    	'expires' => time() + 60*60*24*PR_COOKIE_EXPIRATION_TIME,	// 30 дней
		'path' => '/',
		'domain' => PR_DOMAIN_NAME,
		'secure' => true,
		'httponly' => false
	);
	setcookie( 'source_type', $source_type, $options );
}

// Если используется jquery и файл phone_replace.js, то подключаем jquery в <head>
function BuildHeaderStringForJS()
{
	// Указано не использовать JS
	if (!Pr_UseJS())
		return;

	global $APPLICATION;

	$js_filename = 'phone_replace/phone_replace.js';

	if (Pr_Debug())
	{
		$js_filename = 'phone_replace/phone_replace_debug.js';
	}

	debug_to_console('[JS] Using ' . $js_filename);

	// Подключаю битриксовый jquery (уже идёт в составе ядра)
	CJSCore::Init(array("jquery"));

	// Вписываем скрипт в хедер
	$APPLICATION->AddHeadString(
		'<style>.phone_container</style>' .
		'<script type="text/javascript" src="/' . $js_filename . '"></script>',
	true);
}

// Читает конфиг с номерам телефонов (phone_replace.json)
function ReadPhoneSettings()
{
	global $JSONArray;

	$json = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/phone_replace/phone_replace.json");

	$JSONArray = json_decode($json, true); 

	if (Pr_Debug())
	{
		switch (json_last_error())
		{
			case JSON_ERROR_NONE:
				//debug_to_console('Ошибок нет');
			break;
			case JSON_ERROR_DEPTH:
				debug_to_console('Достигнута максимальная глубина стека');
			break;
			case JSON_ERROR_STATE_MISMATCH:
				debug_to_console('Некорректные разряды или несоответствие режимов');
			break;
			case JSON_ERROR_CTRL_CHAR:
				debug_to_console('Некорректный управляющий символ');
			break;
			case JSON_ERROR_SYNTAX:
				debug_to_console('Синтаксическая ошибка, некорректный JSON');
			break;
			case JSON_ERROR_UTF8:
				debug_to_console('Некорректные символы UTF-8, возможно неверно закодирован');
			break;
			default:
				debug_to_console('Неизвестная ошибка');
			break;
		}
	}
}

// Возвращает source_type из куков
function GetSourceType()
{
	global $source_type;
	$SourceType = $_COOKIE['source_type'];
	
	if (is_null($SourceType))
	{
		$SourceType = $source_type;
	}

	return $SourceType;
}

// Возвращает номер телефона в соответствии с реферером, сохранённым в куках пользователя
// $ClassName - имя ветки в phone_replace.json
function GetPhoneContainerValue($ClassName)
{
	global $JSONArray;
	$phone_num = null;
	$KeyName = GetSourceType();

	debug_to_console('Source Type: ' . $KeyName);

	if (is_null($JSONArray))
	{
		debug_to_console('$JSONArray is NULL?');
		return null;
	}

	foreach($JSONArray as $key=>$value)
	{
		if (strcasecmp($key, $ClassName) == 0)
		{
			foreach($value as $referer=>$val)
			{
				$phone_num = $val[$KeyName];
				
				if (is_null($phone_num))
				{
					$phone_num = $val['direct'];
					if (is_null($phone_num))
					{
						debug_to_console('Can not find a key named ' . $KeyName);
						$phone_num = 'Error. Check my json settings';
						return null;
					}
					debug_to_console($phone_num);
				}
			}
			break;
		}
	}
	return $phone_num;
}

?>