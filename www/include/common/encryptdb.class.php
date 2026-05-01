<?php
class DBManager {
	private $DBConnect; // DB Connect Object

	function __construct($DB_CONNECT) {
		$this->DBConnect = $DB_CONNECT;	// DB Connect Setup
	}

	function updateSet($tablename, $infoarr, $where = null, $debug = false) {
		if ($where == null || $where == '')
		return false;

		$sql = "UPDATE $tablename SET ";
		$i = 0;
		foreach($infoarr as $key=>$val){
			$fieldName = explode('|',$key);
			if(count($fieldName) == 1){//일반필드
				$fieldName[1] = ':'.$fieldName[0];
			}

			if($i == 0){
				$sql .= $fieldName[0]." = ".$fieldName[1];
			}else{
				$sql .= ','.$fieldName[0].' = '.$fieldName[1];
			}
			$i++;
		}


		if($where){
			for($i=0;$i<count($where);$i++){
				if($i==0){
					$where_query = $where[$i][0]."=:".$where[$i][0];
				}else{
					$where_query .= " ".$where[$i][2]." ".$where[$i][0]."=:".$where[$i][0];
				}
			}
		}

		$sql .= " WHERE $where_query";
		$stmt = $this->DBConnect->prepare($sql);

		//============= 변수 바인딩 ===============
		foreach($infoarr as $key=>$val){
			$fieldName = explode('|',$key);
			${$key} = $val;
			$stmt->bindParam(':'.$fieldName[0],${$key});
		}

		if($where){
			for($i=0;$i<count($where);$i++){
				${$where[$i][1]} = $where[$i][1];
				$stmt->bindParam(':'.$where[$i][0],${$where[$i][1]});
			}
		}
		//============= 변수 바인딩 ===============

		if ($debug) echo $sql, "<br>\n";
		return $stmt->execute();
	}

	function insertInto($tablename, $infoarr, $debug = false) {
		$sql = "INSERT INTO $tablename ";

		$sql .= ' (';
		$i = 0;
		foreach($infoarr as $key=>$val){
			$fieldName = explode('|',$key);
			if($i == 0){
				$sql .= $fieldName[0];
			}else{
				$sql .= ','.$fieldName[0];
			}
			$i++;
		}
		$sql .=')';


		$sql .= ' VALUES (';
		$i = 0;
		foreach($infoarr as $key=>$val){
			$fieldNameArray = explode('|',$key);
			if(count($fieldNameArray) == 1){//일반필드
				$fieldName = ':'.$fieldNameArray[0];
			}else{//암호화필드
				$fieldName = $fieldNameArray[1];
			}
			if($i == 0){
				$sql .= $fieldName;
			}else{
				$sql .= ','.$fieldName;
			}
			$i++;
		}
		$sql .=')';
		$stmt = $this->DBConnect->prepare($sql);

		//============= 변수 바인딩 ===============
		foreach($infoarr as $key=>$val){
			$fieldName = explode('|',$key);
			${$key} = $val;
			$stmt->bindParam(':'.$fieldName[0],${$key});
		}
		//============= 변수 바인딩 ===============
		if ($debug) echo $sql, "<br>\n";
		return $stmt->execute();
	}

	function delete_db($tablename,$where=null,$debug=false){

		if($where){
			$where_query = "WHERE ";
			for($i=0;$i<count($where);$i++){
				if($i==0){
					$where_query .= $where[$i][0]."=:".$where[$i][0];
				}else{
					$where_query .= " ".$where[$i][2]." ".$where[$i][0]."=:".$where[$i][0];
				}
			}

			$sql = "DELETE FROM `$tablename` $where_query";
			$stmt = $this->DBConnect->prepare($sql);
			
			//============= 변수 바인딩 ===============
			if($where){
				for($i=0;$i<count($where);$i++){
					${$where[$i][1]} = $where[$i][1];
					$stmt->bindParam(':'.$where[$i][0],${$where[$i][1]});
				}
			}
			//============= 변수 바인딩 ===============
			$stmt->execute();
			$result = true;
		}else{
			$result = false;
		}

		if ($debug) echo $sql, "<br>\n";
		return $result;
	}
}
?>