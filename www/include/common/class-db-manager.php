<?php
class DBManager
{
	/** @var \PDO */
	private $DBConnect; // DB Connect Object

	/**
	 * @param \PDO $DB_CONNECT
	 */
	function __construct($DB_CONNECT)
	{
		$this->DBConnect = $DB_CONNECT;	// DB Connect Setup
	}

	/**
	 * @param string $tablename
	 * @param array<string, mixed> $infoarr
	 * @param array<int, array<int, mixed>>|null $where
	 * @param bool $debug
	 * @return bool
	 */
	function updateSet($tablename, $infoarr, $where = null, $debug = false)
	{
		if ($where == null || $where == '' || !is_array($where) || count($where) === 0)
			return false;
		$sqlup = array();
		$where_query = '';

		$sql = "UPDATE $tablename SET ";
		foreach ($infoarr as $key => $val) {
			$sqlup[] = "$key = :" . $key;
		}
		$sql .= join(", ", $sqlup);

		if ($where) {
			for ($i = 0; $i < count($where); $i++) {
				if ($i == 0) {
					$where_query = $where[$i][0] . "=:" . $where[$i][0];
				} else {
					$where_query .= " " . $where[$i][2] . " " . $where[$i][0] . "=:" . $where[$i][0];
				}
			}
		}

		$sql .= " WHERE $where_query";
		$stmt = $this->DBConnect->prepare($sql);
		//============= 변수 바인딩 ===============
		foreach ($infoarr as $key => $val) {
			${$key} = $val;
			$stmt->bindParam(':' . $key, ${$key});
		}

		if ($where) {
			for ($i = 0; $i < count($where); $i++) {
				${$where[$i][1]} = $where[$i][1];
				$stmt->bindParam(':' . $where[$i][0], ${$where[$i][1]});
			}
		}
		//============= 변수 바인딩 ===============

		if ($debug) echo $sql, "<br>\n";
		return $stmt->execute();
	}

	/**
	 * @param string $tablename
	 * @param array<string, mixed> $infoarr
	 * @param bool $debug
	 * @return bool
	 */
	function insertInto($tablename, $infoarr, $debug = false)
	{
		$fields = array_keys($infoarr);
		$sql = "INSERT INTO $tablename ";
		$sql .= " (`" . join("`,`", $fields) . "`)";
		$sql .= " VALUES (:" . join(",:", $fields) . ")";

		$stmt = $this->DBConnect->prepare($sql);

		//============= 변수 바인딩 ===============
		foreach ($infoarr as $key => $val) {
			${$key} = $val;
			$stmt->bindParam(':' . $key, ${$key});
		}
		//============= 변수 바인딩 ===============
		if ($debug) echo $sql, "<br>\n";
		return $stmt->execute();
	}

	/**
	 * @param string $tablename
	 * @param array<int, array<int, mixed>>|null $where
	 * @param bool $debug
	 * @return bool
	 */
	function delete_db($tablename, $where = null, $debug = false)
	{
		$sql = '';

		if (is_array($where) && count($where) > 0) {
			$where_query = "WHERE ";
			for ($i = 0; $i < count($where); $i++) {
				if ($i == 0) {
					$where_query .= $where[$i][0] . "=:" . $where[$i][0];
				} else {
					$where_query .= " " . $where[$i][2] . " " . $where[$i][0] . "=:" . $where[$i][0];
				}
			}

			$sql = "DELETE FROM `$tablename` $where_query";
			$stmt = $this->DBConnect->prepare($sql);

			//============= 변수 바인딩 ===============
			if ($where) {
				for ($i = 0; $i < count($where); $i++) {
					${$where[$i][1]} = $where[$i][1];
					$stmt->bindParam(':' . $where[$i][0], ${$where[$i][1]});
				}
			}
			//============= 변수 바인딩 ===============
			$stmt->execute();
			$result = true;
		} else {
			$result = false;
		}

		if ($debug) echo $sql, "<br>\n";
		return $result;
	}
}
