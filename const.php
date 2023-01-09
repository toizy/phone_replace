<?
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

define("PR_DEBUG", false);
define("PR_USE_JS", false);
define("PR_DOMAIN_NAME", ".nbr.ru");
define("PR_COOKIE_EXPIRATION_TIME", 30);	// Время хранения куков (в днях)

function Pr_Debug()
{
	return (PR_DEBUG == true);
}

function Pr_UseJS()
{
	return (PR_USE_JS == true);
}

?>