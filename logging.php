<?
require_once($_SERVER["DOCUMENT_ROOT"]."/phone_replace/const.php");

$css = [
	"clRed" => "color:white;background-color:red;font-style:italic",
	"clGray" => "color:white;background-color:gray;font-style:italic",
	"clSteelBlue" => "color:white;background-color:steelblue;font-style:italic",
	"clGreen" => "color:white;background-color:green;font-style:italic",
	"clOrange" => "color:white;background-color:orange;font-style:italic",
];

function debug_to_console_object( $data )
{
	if (!Pr_Debug())
		return;

	global $css;

	echo "<script>console.log( '%c [object] %c %o" . $data . " ', '" . $css["clOrange"] . "', '" . $css["clSteelBlue"] . "' );</script>";
}

function debug_to_console( $data )
{
	if (!Pr_Debug())
		return;

	global $css;

	$output = $data;

	if (is_object($output))
	{
		debug_to_console_object($output);
		return;
	}

	if ( is_array( $output ) )
		$output = implode( ',', $output);

	echo "<script>console.log( '%c [string] %c " . $output . " ', '" . $css["clOrange"] . "', '" . $css["clSteelBlue"] . "' );</script>";
}

?>