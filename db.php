<?
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

class pr_db
{
	private $dbInst = 0;
	private $dbError = 0;

	public function __construct()
	{
		$this->open();
	}

	public function __destruct()
	{
		$this->close();
		
	}

	public function open()
	{
		//global $DBHost;
		//global $DBLogin;
		//global $DBPassword;

		$this->dbError = 0;
		$this->dbInst = new mysqli('localhost', 'root', 'd81G2amanm4d', 'nbr_referers');

		if (isset($this->dbInst))
		{
			$this->dbError = $this->dbInst->connect_error;
		}
		else
		{
			$this->dbError = "mysqli instance is not set.";
		}

		return ($this->dbError != false);
	}

	public function close()
	{
		if (isset($this->dbInst))
		{
			$this->dbInst->close();
		}
	}

	public function get_error()
	{
		return $this->dbInst->error;
		return $this->dbError;
	}
	//TODO: Добавить функцию типа IsError()

	public function execute($query)
	{
		//file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/debug.txt", print_r($query, true));

		if (isset($this->dbInst))
		{
			return $this->dbInst->query($query);
		}
		return null;
	}
}
?>