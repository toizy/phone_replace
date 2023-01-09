// Если в phone_replace.php включена отладка, то в куках будет debug, равный единице
// Если так, то включаем отладку и в js скрипте (этом)
const DEBUG = get_cookie ( 'debug' );

// Отладочные переменные (для красивой консоли)
const
	css = [
		'',
		'color:white;background-color:orange;font-style:italic',
		'color:white;background-color:steelblue;font-style:italic',
		'color:white;background-color:red;font-style:italic',
		'color:white;background-color:green;font-style:italic',
		'color:white;background-color:gray;font-style:italic',
	],
	clNone		= 0,
	clOrange	= 1,
	clSteelBlue	= 2,
	clRed		= 3,
	clGreen		= 4,
	clGray		= 5
;

var
	phone	= null,
	source	= null
;

function getJsonData(json_url)
{
	// Делаю асинхронный запрос, он не тормозит страницу. 
	// Обработку перенёс в анонимную функцию в .done
	$.ajax({
		type: "GET",
		url: json_url,
		dataType: "json"
	}).done(function(data)
		{
			// Пришли пустые данные, что-то не так.
			if (data == null)
			{
				if ( DEBUG )
				{
					console.log('%c data is null, stopping', css[clRed]);
				}
				return;
			}

			if (data[source] != null)
			{
				phone = data[source]
			}
			
			if ( DEBUG )
			{
				console.log('%c source_type: %c %s ', css[clOrange], css[clSteelBlue], source);
				console.log('%c json array: %o', css[clOrange], data);
			}
			let stylename;
			let stylevalue;
			//Подмена номера телефона во всех контейнерах
			Object.keys(data).forEach(function(key)
			{
				stylename = key;
				stylevalue = this[key][0][source];
				$(stylename).html( stylevalue );
				console.log(stylevalue);
			}, data);
		}
	).fail(function (jqXHR, status, error)
		{
    		console.log('%c Error while getting URL %s (%s) : %c %s', css[clRed], json_url, status, css[clNone], error);
    	}
	);
}

function get_cookie ( cookie_name )
{
	let results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
	if ( results )
		return ( unescape ( results[2] ) );
	return null;
}

$(document).ready(
	function change() 
	{
		// Забрали куку
		source = get_cookie ( 'source_type' );

		// Если куки нет
		if (source == null)
		{
			source = 'direct';
		}

		if ( DEBUG )
		{
			// Подменяем source для отладки. Например, на google
			source = "google";
			console.log('%c source changed to: %c %s ', css[clGray], css[clSteelBlue], source);
		}

		// Посылаем асинхронный запрос к нашему списку телефонов в json файле
		getJsonData("/phone_replace/phone_replace.json");
	} 
);