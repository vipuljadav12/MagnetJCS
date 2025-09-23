<?php
class MySQLCN
{
	public $CONN = '';
	function __construct($server = "", $user = "", $pass = "", $dbase = "")
	{
		if ($server == "") {
			$user = "root";
			$pass = "";
			$server = "localhost";
			$dbase = "magnet_jeflive";
		}
		$conn = new mysqli($server, $user, $pass, $dbase);
		if ($conn->connect_errno) {
			echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
		}
		$conn->query("SET SESSION character_set_results = 'UTF8'");
		$conn->query('SET NAMES UTF8');
		$this->CONN = $conn;
		return true;
	}

	function close()
	{
		$this->CONN->close();
	}
	function error($text)
	{
		$no = $this->CONN->errno;
		$msg = $this->CONN->error;
		exit;
	}

	function fetch_insert_id()
	{
		return $this->CONN->insert_id;
	}

	function fetch_affected_rows()
	{
		return $this->CONN->affected_rows;
	}

	function select($sql = "")
	{
		/*if(empty($sql)) { return false; }
		if(!preg_match("/^select/i",$sql))
		{
			$ret_msg =  "Wrong Query : ".$sql."";
			if($_SERVER['HTTP_HOST'] == '192.168.0.18')
			{
				header("location:".$_SERVER['HTTP_HOST']."/admin/error_handle.php?err=1&msg=Data fetch error !&ret_msg=".base64_encode($ret_msg));
			}else
			{
				header("location: ".ERROR_PATH."error_handle.php?err=1&msg=Data fetch error !&ret_msg=".base64_encode($ret_msg));
			}
			exit;
		}*/
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;
		$results = $conn->query($sql);

		/*if((!$results) or (empty($results))) 
		{
			$ret_msg =  "Wrong Query : ".$sql."--- Error :".$conn->error."";
			if($_SERVER['HTTP_HOST'] == '192.168.0.18')
			{
				header("location:".$_SERVER['HTTP_HOST']."/admin/error_handle.php?err=1&msg=Data fetch error !&ret_msg=".base64_encode($ret_msg));
			}else
			{
				header("location: ".ERROR_PATH."error_handle.php?err=1&msg=Data fetch error !&ret_msg=".base64_encode($ret_msg));
			}
			exit;
		}*/

		$count = 0;
		$data = array();
		while ($row = $results->fetch_array()) {
			$data[$count] = $row;
			$count++;
		}
		mysqli_free_result($results);
		return $data;
	}

	function newselect($sql = "")
	{
		if (empty($sql)) {
			return false;
		}
		if (!preg_match("/^select/i", $sql)) {
			echo "wrongquery<br>$sql<p>";
			echo "<H2>Wrong function silly!</H2>\n";
			return false;
		}
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;
		$results = $conn->query($sql);
		if ((!$results) or (empty($results))) {
			return false;
		}
		$count = 0;
		$data = array();
		while ($row = $results->fetch_array()) {
			$data[$count] = $row;
			$count++;
		}
		mysqli_free_result($results);
		return $data;
	}

	function affected($sql = "")
	{
		if (empty($sql)) {
			return false;
		}
		if (!preg_match("/^select/i", $sql)) {
			echo "wrongquery<br>$sql<p>";
			echo "<H2>Wrong function silly!</H2>\n";
			return false;
		}
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;
		$results = $conn->query($sql);
		if ((!$results) or (empty($results))) {
			return false;
		}
		$tot = 0;
		$tot = $conn->affected_rows;
		return $tot;
	}

	function insert($sql = "")
	{
		if (empty($sql)) {
			return false;
		}
		if (!preg_match("/^insert/i", $sql)) {
			return false;
		}
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;
		$results = $conn->query($sql);
		if (!$results) {
			echo "Insert Operation Failed..<hr>" . $conn->error;
			$this->error("Insert Operation Failed..");
			$this->error("<H2>No results!</H2>\n");
			return false;
		}
		$id = $conn->insert_id;
		return $id;
	}

	//Dont remove this - Added by sreejan//
	function adder($sql = "")
	{
		if (empty($sql)) {
			return false;
		}
		if (!preg_match("/^insert/i", $sql)) {
			return false;
		}
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;
		$results = $conn->query($sql);

		if (!$results) $id = "";
		else $id = $conn->insert_id;
		return $id;
	}

	function edit($sql = "")
	{
		if (empty($sql)) {
			return false;
		}
		if (!preg_match("/^update/i", $sql)) {
			return false;
		}
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;
		$results = $conn->query($sql);
		if (!$results) {
			$this->error("<H2>No results!</H2>\n");
			return false;
		}
		$rows = 0;
		$rows = $conn->affected_rows;
		return $rows;
	}

	function sql_query($sql = "")
	{

		if (empty($sql)) {
			return false;
		}
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;
		$results = $conn->query($sql);
		if (!$results) {
			$ret_msg =  "Wrong Query : " . $sql . "<br>" . $conn->error . "";

			if ($_SERVER['HTTP_HOST'] == '192.168.0.18') {
				echo $ret_msg;
			} else {
				echo $ret_msg;
			}
			exit;
		}
		// (Martin Huba) also SHOW... commands return some results
		if (!(preg_match("/^select/i", $sql) || preg_match("/^show/i", $sql) || preg_match("/^update/i", $sql) || preg_match("/^delete/i", $sql) || preg_match("/^insert/i", $sql) || preg_match("/^alter/i", $sql) || preg_match("/^create/i", $sql))) {
			$ret_msg =  "Wrong Query : " . $sql . "";
			if ($_SERVER['HTTP_HOST'] == '192.168.0.18') {
				echo $ret_msg;
			} else {
				echo $ret_msg;
			}
			exit;
		}

		if (preg_match("/^delete/i", $sql) || preg_match("/^insert/i", $sql) || preg_match("/^update/i", $sql) || preg_match("/^alter/i", $sql) || preg_match("/^create/i", $sql)) {
			return true;
		} else {
			$count = 0;
			$data = array();
			while ($row = $results->fetch_array()) {
				$data[$count] = $row;
				$count++;
			}
			mysqli_free_result($results);
			return $data;
		}
	}

	function sql_assoc($sql = "")
	{

		if (empty($sql)) {
			return false;
		}
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;

		$results = $conn->query($sql);
		if (!$results) {
			$message = "Query went bad!";
			$this->error($message);
			return false;
		}
		// (Martin Huba) also SHOW... commands return some results
		if (!(preg_match("/^select/i", $sql) || preg_match("/^show/i", $sql))) {
			return true;
		} else {
			$count = 0;
			$data = array();
			while ($row = $results->fetch_assoc()) {
				$data[$count] = $row;
				$count++;
			}
			mysqli_free_result($results);
			return $data;
		}
	}

	function sql_get_fields($sql = "")
	{
		if (empty($sql)) {
			return false;
		}
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;

		$results = $conn->query($sql);
		if (!$results) {
			$message = "Query went bad!";
			$this->error($message);
			return false;
		}
		// (Martin Huba) also SHOW... commands return some results
		if (!(preg_match("/^select/i", $sql) || preg_match("/^show/i", $sql))) {
			return true;
		} else {
			$count = 0;

			$data = array();
			while ($count < $results->field_count) {
				$nm = $results->fetch_field();
				$data[$count]['name'] = $nm->name;
				$count++;
			}
			mysqli_free_result($results);
			return $data;
		}
	}

	function call_stored_proc($procName, $para = '', $returnType = 'array')
	{
		if (empty($procName)) {
			return false;
		}
		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;
		return $this->c_mysqli_call($procName, $para, $returnType);
	}

	function c_mysqli_call($procName, $params = "", $returnType)
	{
		$returnType = 'fetch_' . $returnType;
		$dbLink = $this->CONN;
		if (!$dbLink) {
			$ret_msg =  "Wrong Query : " . $sql . "--- Error :" . $dbLink->error . "";
			if ($_SERVER['HTTP_HOST'] == '192.168.0.18') {
				header("location:" . $_SERVER['HTTP_HOST'] . "/admin/error_handle.php?err=1&msg=Data fetch error !&ret_msg=" . base64_encode($ret_msg));
			} else {
				header("location: " . ERROR_PATH . "error_handle.php?err=1&msg=Data fetch error !&ret_msg=" . base64_encode($ret_msg));
			}
			exit;
		} else {
			$sql = "CALL {$procName}({$params});";
			$sqlSuccess = $dbLink->multi_query($sql);
			if ($sqlSuccess) {
				if ($dbLink->more_results()) {
					$result = $dbLink->use_result();
					$output = array();
					while ($row = $result->{$returnType}()) {
						$output[] = $row;
					}
					$result->free();
					while ($dbLink->more_results() && $dbLink->next_result()) {
						$extraResult = $dbLink->use_result();
						if ($extraResult instanceof mysqli_result) {
							$extraResult->free();
						}
					}
					return $output;
				} else {
					return false;
				}
			} else {
				$ret_msg =  "Wrong Query : " . $sql . "--- Error :" . $dbLink->error . "";
				if ($_SERVER['HTTP_HOST'] == '192.168.0.18') {
					header("location:" . $_SERVER['HTTP_HOST'] . "/admin/error_handle.php?err=1&msg=Data fetch error !&ret_msg=" . base64_encode($ret_msg));
				} else {
					header("location: " . ERROR_PATH . "error_handle.php?err=1&msg=Data fetch error !&ret_msg=" . base64_encode($ret_msg));
				}
				exit;
			}
		}
	}

	function union_select($sql = "")
	{
		if (empty($sql)) {
			return false;
		}

		if (empty($this->CONN)) {
			return false;
		}
		$conn = $this->CONN;
		$results = mysqli_query($conn, $sql);

		if ((!$results) or (empty($results))) {
			$ret_msg =  "Wrong Query : " . $sql . "--- Error :" . mysqli_error() . "";
			if ($_SERVER['HTTP_REFERER'] == '') {
				header("location:" . ERROR_PATH . "error_handle.php?err=1&msg=Data fetch error !&ret_msg=" . base64_encode($ret_msg));
			} else {
				$tmp = explode('admin/', $_SERVER['HTTP_REFERER']);
				header("location:" . ERROR_PATH . "error_handle.php?err=1&msg=Data fetch error !&ret_msg=" . base64_encode($ret_msg));
			}
			exit;
		}

		$count = 0;
		$data = array();
		while ($row = mysqli_fetch_array($results)) {
			$data[$count] = $row;
			$count++;
		}
		mysqli_free_result($results);
		return $data;
	}
}
