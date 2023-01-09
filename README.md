# Phone Replace

Замена номера телефона в любых местах на сайте Bitrix.

## Подключение

- Сперва надо подключить _phone_replace.php_ в header шаблона:

```<? require_once($_SERVER['DOCUMENT_ROOT'].'/phone_replace/phone_replace.php'); ?>```


- Теперь, в удобном месте вызываем _ReadPhoneSettings()_:

```<? ReadPhoneSettings(); ?>```

- Затем, если будет использоваться jquery, надо вызвать _BuildHeaderStringForJS()_ внутри тега ```<head>```:

```<? BuildHeaderStringForJS(); ?>```

Это подключит битриксовский jquery и вставит в ```<head>``` необходимые строки (новый стиль и путь к _phone_replace_debug.js_)

- Затем, в любом месте, где есть номер телефона, необходимо вызывать функцию _GetPhoneContainerValue()_, например:

Было:

```
<div class="phone pull-right hidden-xs">
	<?$APPLICATION->IncludeFile(SITE_DIR."include/site-phone.php", array(), array(
					"MODE" => "text",
					"NAME" => "Phone",
			)
	);?>
</div>
```

Стало:

```
<div class="phone pull-right hidden-xs">
	<? echo GetPhoneContainerValue('phone_container_header'); ?>
	<!-- <?$APPLICATION->IncludeFile(SITE_DIR."include/site-phone.php", array(), array(
					"MODE" => "text",
					"NAME" => "Phone",
			)
	);?> -->
</div>
```

Аргумент функции в этом случае - это имя ветки второго уровня из файла _phone_replace.json_
