<?php
class QueryLibrary
{
	function __construct(private PDO $DBConnect, private FuncLibrary $FunctionLibrary) {}

	/* 데이터 리스트 */
	public function getList($where = '', $bindParam = array(), $tableName = '', $orderby = '', $page = '', $listCount = '', $indexID = 'idx', $debug = false)
	{
		//$bindParam 배열 값 0: 필드명, 1: 필드 값, 2: 비교 연산자(=,like,<>)
		if (!$page) {
			$page = 1;
		}

		if (!$listCount) {
			$listCount = 10;
		}

		if (!$orderby) {
			$orderby = 'idx desc';
		}
		$orderby = $this->FunctionLibrary->sanitizeOrderByPipeList($orderby);
		$orderby = explode('|', $orderby);


		$listStart = ($page - 1) * $listCount; //시작 번호
		/* 커버링 인덱스 */
		$sql = "SELECT * FROM " . $this->FunctionLibrary->escapeQuery($tableName) . " as a JOIN ( ";
		$sql .= "select " . $this->FunctionLibrary->escapeQuery($indexID) . " from " . $this->FunctionLibrary->escapeQuery($tableName) . " $where";
		$sql .= " order by ";
		foreach ($orderby as $key => $val) {
			if ($key == 0) {
				$sql .= $val;
			} else {
				$sql .= ',' . $val;
			}
		}
		$sql .= " limit :listStart, :listCount ";
		$sql .= " ) as b ON a." . $this->FunctionLibrary->escapeQuery($indexID) . " = b." . $this->FunctionLibrary->escapeQuery($indexID) . " ";
		$sql .= " order by ";
		foreach ($orderby as $key => $val) {
			if ($key == 0) {
				if (trim($val) != 'rand()') {
					$sql .= 'a.' . $val;
				} else { //랜덤 처리
					$sql .= $val;
				}
			} else {
				$sql .= ',a.' . $val;
			}
		}

		if ($debug === true) {
			echo $sql;
		}


		$stmt = $this->DBConnect->prepare($sql);

		$stmt->bindParam(':listStart', $listStart, PDO::PARAM_INT);
		$stmt->bindParam(':listCount', $listCount, PDO::PARAM_INT);

		//============= 조건절 바인딩 ====================
		if ($bindParam) {
			for ($i = 0; $i < count((array)$bindParam); $i++) {
				if (isset($bindParam[$i][2]) && $bindParam[$i][2] === 'like') {
					$stmt->bindValue(':' . $bindParam[$i][0], '%' . $bindParam[$i][1] . '%');
				} else {
					${$bindParam[$i][0]} = $bindParam[$i][1];
					$stmt->bindParam(':' . $bindParam[$i][0], ${$bindParam[$i][0]});
				}
			}
		}
		//============= 조건절 바인딩 ====================
		$stmt->execute(); //쿼리 실행


		$listTotal = $this->dataTotal($where, $bindParam, $tableName); //총 레코드
		$totalPage = ceil($listTotal / $listCount);
		$number = $listTotal - $listStart;
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$listResult['result'] = $data;
		$listResult['total_page'] = $totalPage;
		$listResult['list_total'] = $listTotal;
		$listResult['number'] = $number;

		return $listResult;
	}

	//다중 테이블 리스트 
	public function getUnionList(string $fullQuery, $bindParam = array(), $orderby = '', $page = 1, $listCount = 10, $indexID = 'idx', $discriminator = 'tableName', $debug = false)
	{
		//기본값 설정
		if (!$page) $page = 1;
		if (!$listCount) $listCount = 10;
		if (!$orderby) $orderby = 'idx desc';
		$orderby = $this->FunctionLibrary->sanitizeOrderByPipeList($orderby);
		$orderbyArr = explode('|', $orderby);

		$listStart = ($page - 1) * $listCount;

		//전체 카운트 조회
		$countSql = "SELECT COUNT(*) as cnt FROM (" . $fullQuery . ") as temp_table";
		$countStmt = $this->DBConnect->prepare($countSql);

		if ($bindParam) {
			foreach ($bindParam as $param) {
				$key = ':' . $param[0];
				$value = isset($param[2]) && $param[2] === 'like' ? '%' . $param[1] . '%' : $param[1];
				$countStmt->bindValue($key, $value);
			}
		}
		$countStmt->execute();
		$totalRow = $countStmt->fetch(PDO::FETCH_ASSOC);
		$listTotal = $totalRow['cnt'] ?? 0;

		if ($listTotal < 1) {
			return ['result' => [], 'total_page' => 0, 'list_total' => 0, 'number' => 0];
		}

		$innerOrderBy = [];
		$outerOrderBy = [];
		foreach ($orderbyArr as $val) {
			$trimmedVal = trim($val);
			if ($trimmedVal == 'rand()') {
				$innerOrderBy[] = 'rand()';
				$outerOrderBy[] = 'rand()';
			} else {
				$innerOrderBy[] = $trimmedVal;
				$outerOrderBy[] = 'a.' . $trimmedVal;
			}
		}
		$innerOrderBySql = implode(', ', $innerOrderBy);
		$outerOrderBySql = implode(', ', $outerOrderBy);

		// 컬럼명 이스케이프 처리
		$escapedIndexID = $this->FunctionLibrary->escapeQuery($indexID);
		$escapedDiscriminator = $this->FunctionLibrary->escapeQuery($discriminator);

		// WITH 절(CTE)을 사용하여 쿼리 작성
		$sql = "WITH UnionedData AS ( " . $fullQuery . " ) ";
		$sql .= "SELECT * FROM UnionedData as a JOIN ( ";
		$sql .= "    SELECT " . $escapedIndexID . ", " . $escapedDiscriminator . " FROM UnionedData";
		$sql .= "    ORDER BY " . $innerOrderBySql;
		$sql .= "    LIMIT :listStart, :listCount ";
		$sql .= ") as b ON ";
		$sql .= " a." . $escapedIndexID . " = b." . $escapedIndexID;
		$sql .= " AND a." . $escapedDiscriminator . " = b." . $escapedDiscriminator;
		$sql .= " ORDER BY " . $outerOrderBySql;

		if ($debug === true) {
			echo $sql;
			print_r($bindParam);
		}

		//쿼리 실행 및 결과 반환
		$stmt = $this->DBConnect->prepare($sql);

		$stmt->bindParam(':listStart', $listStart, PDO::PARAM_INT);
		$stmt->bindParam(':listCount', $listCount, PDO::PARAM_INT);

		if ($bindParam) {
			foreach ($bindParam as $param) {
				$key = ':' . $param[0];
				$value = isset($param[2]) && $param[2] === 'like' ? '%' . $param[1] . '%' : $param[1];
				$stmt->bindValue($key, $value);
			}
		}

		$stmt->execute();

		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$totalPage = ceil($listTotal / $listCount);
		$number = $listTotal - $listStart;

		return [
			'result' => $data,
			'total_page' => $totalPage,
			'list_total' => $listTotal,
			'number' => $number,
		];
	}

	/* 총 데이터 조회 */
	public function dataTotal(string $where = '', $bindParam = array(), string $tableName = '', string $count = "count(*) as total")
	{
		$sql = "SELECT $count FROM " . $this->FunctionLibrary->escapeQuery($tableName) . " $where";
		$stmt = $this->DBConnect->prepare($sql);

		//============= 변수 바인딩 ===============
		if ($bindParam) {
			for ($i = 0; $i < count((array)$bindParam); $i++) {
				if (isset($bindParam[$i][2]) && $bindParam[$i][2] === 'like') {
					$stmt->bindValue(':' . $bindParam[$i][0], '%' . $bindParam[$i][1] . '%');
				} else {
					${$bindParam[$i][1]} = $bindParam[$i][1];
					$stmt->bindParam(':' . $bindParam[$i][0], ${$bindParam[$i][1]});
				}
			}
		}
		//============= 변수 바인딩 ===============

		$stmt->execute();
		$d = $stmt->fetch(PDO::FETCH_ASSOC);
		return $d['total'];
	}

	/* 단일 데이터 조회 */
	public function getData(int $idx, string $tableName, $idxText = '')
	{
		if ($idxText) {
			$idxText = $this->FunctionLibrary->escapeQuery($idxText) ?? 'idx';
		} else {
			$idxText = 'idx';
		}

		$sql = "SELECT * FROM " . $this->FunctionLibrary->escapeQuery($tableName) . " WHERE $idxText = :idx";
		$stmt = $this->DBConnect->prepare($sql);
		$stmt->bindParam(':idx', $idx);
		$stmt->execute();
		$d = $stmt->fetch(PDO::FETCH_ASSOC);
		return $d;
	}

	/* 단일 데이터 조회(조건식) */
	public function getData2(string $where = '', $bindParam = array(), string $tableName = '', $idxText = '')
	{
		if ($idxText) {
			$idxText = $this->FunctionLibrary->escapeQuery($idxText) ?? 'idx';
		} else {
			$idxText = 'idx';
		}
		$sql = "select * from " . $this->FunctionLibrary->escapeQuery($tableName) . " $where order by $idxText desc limit 0,1";

		$stmt = $this->DBConnect->prepare($sql);

		//============= 변수 바인딩 ===============
		if ($bindParam) {
			for ($i = 0; $i < count((array)$bindParam); $i++) {
				if (isset($bindParam[$i][2]) && $bindParam[$i][2] === 'like') {
					$stmt->bindValue(':' . $bindParam[$i][0], '%' . $bindParam[$i][1] . '%');
				} else {
					${$bindParam[$i][1]} = $bindParam[$i][1];
					$stmt->bindParam(':' . $bindParam[$i][0], ${$bindParam[$i][1]});
				}
			}
		}
		//============= 변수 바인딩 ===============
		$stmt->execute();
		$d = $stmt->fetch(PDO::FETCH_ASSOC);
		return $d;
	}

	/* 단일 데이터 조회(커스텀) */
	public function getDataCustom(string $tableName = '', string $sort = '', string $column = '', string $where = '', $bindParam = array(), $queryPrint = false)
	{
		if (!$sort) {
			$sort = 'idx desc';
		} else {
			$sort = $this->FunctionLibrary->sanitizeOrderByPipeList($sort, 'idx desc');
			$sort = implode(', ', explode('|', $sort));
		}

		if (!$column) {
			$column = '*';
		} else {
			$column = $this->FunctionLibrary->sanitizeSelectColumnExpr($column);
		}

		$sql = "select $column from " . $this->FunctionLibrary->escapeQuery($tableName) . " $where order by $sort limit 0,1 ";

		$stmt = $this->DBConnect->prepare($sql);
		//============= 변수 바인딩 ===============
		if ($bindParam) {
			for ($i = 0; $i < count((array)$bindParam); $i++) {
				if (isset($bindParam[$i][2]) && $bindParam[$i][2] === 'like') {
					$stmt->bindValue(':' . $bindParam[$i][0], '%' . $bindParam[$i][1] . '%');
				} else {
					${$bindParam[$i][1]} = $bindParam[$i][1];
					$stmt->bindParam(':' . $bindParam[$i][0], ${$bindParam[$i][1]});
				}
			}
		}
		//============= 변수 바인딩 ===============
		$stmt->execute();
		$d = $stmt->fetch(PDO::FETCH_ASSOC);
		return $d;
	}


	/*========================== 암호화 쿼리 =========================*/
	//데이터 리스트
	public function encryptGetList($where = '', $bindParam = array(), $tableName = '', $orderby = '', $fieldArray = array(), $page = '', $listCount = '', $indexID = 'idx', $debug = false)
	{
		//$bindParam 배열 값 0: 필드명, 1: 필드 값, 2: 비교 연산자(=,like,<>)
		if (!$page) {
			$page = 1;
		}

		if (!$listCount) {
			$listCount = 10;
		}

		if (!$orderby) {
			$orderby = 'idx desc';
		}
		$orderby = $this->FunctionLibrary->sanitizeOrderByPipeList($orderby);
		$orderby = explode('|', $orderby);
		$listStart = ($page - 1) * $listCount; //시작 번호

		$fieldListParts = [];
		foreach ($fieldArray as $fieldName) { //암호화 처리 필드
			$fn = $this->FunctionLibrary->escapeQuery($fieldName);
			if ($fn === null) {
				continue;
			}
			$fieldListParts[] = "CONVERT(AES_DECRYPT(UNHEX($fn), '" . $fn . "_encrypt') USING UTF8) as " . $fn . "_convert ";
		}
		$fieldList = $fieldListParts !== [] ? implode(',', $fieldListParts) : '';


		/* 커버링 인덱스 */
		$selectList = $fieldList !== '' ? '*,' . $fieldList : '*';
		$sql = "SELECT $selectList FROM " . $this->FunctionLibrary->escapeQuery($tableName) . " as a JOIN ( ";
		$sql .= "select " . $this->FunctionLibrary->escapeQuery($indexID) . " from " . $this->FunctionLibrary->escapeQuery($tableName) . " $where";
		$sql .= " order by ";
		foreach ($orderby as $key => $val) {
			if ($key == 0) {
				$sql .= $val;
			} else {
				$sql .= ',' . $val;
			}
		}
		$sql .= " limit :listStart, :listCount ";
		$sql .= " ) as b ON a." . $this->FunctionLibrary->escapeQuery($indexID) . " = b." . $this->FunctionLibrary->escapeQuery($indexID) . " ";
		$sql .= " order by ";
		foreach ($orderby as $key => $val) {
			if ($key == 0) {
				if (trim($val) != 'rand()') {
					$sql .= 'a.' . $val;
				} else {
					$sql .= $val;
				}
			} else {
				$sql .= ',a.' . $val;
			}
		}

		if ($debug === true) {
			echo $sql;
		}


		$stmt = $this->DBConnect->prepare($sql);

		$stmt->bindParam(':listStart', $listStart, PDO::PARAM_INT);
		$stmt->bindParam(':listCount', $listCount, PDO::PARAM_INT);

		//============= 조건절 바인딩 ====================
		if ($bindParam) {
			for ($i = 0; $i < count((array)$bindParam); $i++) {
				if (isset($bindParam[$i][2]) && $bindParam[$i][2] === 'like') {
					$stmt->bindValue(':' . $bindParam[$i][0], '%' . $bindParam[$i][1] . '%');
				} else {
					${$bindParam[$i][0]} = $bindParam[$i][1];
					$stmt->bindParam(':' . $bindParam[$i][0], ${$bindParam[$i][0]});
				}
			}
		}
		//============= 조건절 바인딩 ====================
		$stmt->execute(); //쿼리 실행


		$listTotal = $this->dataTotal($where, $bindParam, $tableName); //총 레코드
		$totalPage = ceil($listTotal / $listCount);
		$number = $listTotal - $listStart;
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$listResult['result'] = $data;
		$listResult['total_page'] = $totalPage;
		$listResult['list_total'] = $listTotal;
		$listResult['number'] = $number;

		return $listResult;
	}

	//단일 데이터 조회
	public function encryptGetData(int $idx, string $tableName, $fieldArray = array())
	{
		$fieldListParts = [];
		foreach ($fieldArray as $fieldName) {
			$fn = $this->FunctionLibrary->escapeQuery($fieldName);
			if ($fn === null) {
				continue;
			}
			$fieldListParts[] = "CONVERT(AES_DECRYPT(UNHEX($fn), '" . $fn . "_encrypt') USING UTF8) as " . $fn . "_convert ";
		}
		$fieldList = $fieldListParts !== [] ? implode(',', $fieldListParts) : '';

		$sql = "SELECT " . ($fieldList !== '' ? "*,$fieldList" : '*') . " FROM " . $this->FunctionLibrary->escapeQuery($tableName) . " WHERE idx = :idx";
		$stmt = $this->DBConnect->prepare($sql);
		$stmt->bindParam(':idx', $idx);
		$stmt->execute();
		$d = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($d) {
			$d = str_replace('&quot;', '"', $d);
		}
		return $d;
	}
	/*========================== 암호화 쿼리 =========================*/

	/* 게시판 설정 */
	public function getBoardInfo(string $bbsid = '')
	{

		$sql = "SELECT * FROM `gh_board` WHERE bbsid = :bbsid";
		$stmt = $this->DBConnect->prepare($sql);
		$stmt->bindParam(':bbsid', $bbsid);
		$stmt->execute();
		$d = $stmt->fetch(PDO::FETCH_ASSOC);
		return $d;
	}


	/* 테이블 컬럼 가져오기*/
	public function getColumn(string $tableName)
	{
		$sql = "DESC " . $this->FunctionLibrary->escapeQuery($tableName);
		$stmt = $this->DBConnect->prepare($sql);
		$stmt->execute();
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($data as $row) {
			$d[$row['Field']] = null;
		}
		return $d;
	}

	/* 게시판 이전글 */
	public function prevPost($tableName = '', int $postIdx = 1, string $column = 'idx', string $where = '', $pnParam = array(), string $orderby = 'idx asc', string $standard = 'idx')
	{
		$columnSql = $this->FunctionLibrary->sanitizeSelectColumnExpr($column);
		$standardEsc = $this->FunctionLibrary->escapeQuery($standard) ?? 'idx';
		$orderbySql = implode(', ', explode('|', $this->FunctionLibrary->sanitizeOrderByPipeList($orderby, 'idx asc')));
		$sql = "select $columnSql from " . $this->FunctionLibrary->escapeQuery($tableName) . " where $standardEsc > :postIdx $where order by $orderbySql limit 1";

		$stmt = $this->DBConnect->prepare($sql);
		$stmt->bindParam(':postIdx', $postIdx);
		//============= 조건절 바인딩 ====================
		if ($pnParam) {
			for ($i = 0; $i < count((array)$pnParam); $i++) {
				if (isset($pnParam[$i][2]) && $pnParam[$i][2] === 'like') {
					$stmt->bindValue(':' . $pnParam[$i][0], '%' . $pnParam[$i][1] . '%');
				} else {
					${$pnParam[$i][0]} = $pnParam[$i][1];
					$stmt->bindParam(':' . $pnParam[$i][0], ${$pnParam[$i][0]});
				}
			}
		}
		//============= 조건절 바인딩 ====================

		$stmt->execute();
		$prevRow = $stmt->rowCount();
		if ($prevRow > 0) {
			$prev = $stmt->fetch(PDO::FETCH_ASSOC);
			$prev = array_map('stripslashes', $prev);
			$columnKeys = array_map('trim', explode(',', $columnSql));
			$prevData = array();
			for ($i = 0; $i < count($columnKeys); $i++) {
				$prevData[$columnKeys[$i]] = $prev[$columnKeys[$i]];
			}
		} else {
			$prevData = '';
		}
		return $prevData;
	}

	/* 게시판 다음글 */
	public function nextPost($tableName = '', int $postIdx = 1, string $column = 'idx', string $where = '', $pnParam = array(), string $orderby = 'idx desc', string $standard = 'idx')
	{
		$columnSql = $this->FunctionLibrary->sanitizeSelectColumnExpr($column);
		$standardEsc = $this->FunctionLibrary->escapeQuery($standard) ?? 'idx';
		$orderbySql = implode(', ', explode('|', $this->FunctionLibrary->sanitizeOrderByPipeList($orderby, 'idx desc')));
		$sql = "select $columnSql from " . $this->FunctionLibrary->escapeQuery($tableName) . " where $standardEsc < :postIdx $where order by $orderbySql limit 1";
		$stmt = $this->DBConnect->prepare($sql);
		$stmt->bindParam(':postIdx', $postIdx);
		//============= 조건절 바인딩 ====================
		if ($pnParam) {
			for ($i = 0; $i < count((array)$pnParam); $i++) {
				if (isset($pnParam[$i][2]) && $pnParam[$i][2] === 'like') {
					$stmt->bindValue(':' . $pnParam[$i][0], '%' . $pnParam[$i][1] . '%');
				} else {
					${$pnParam[$i][0]} = $pnParam[$i][1];
					$stmt->bindParam(':' . $pnParam[$i][0], ${$pnParam[$i][0]});
				}
			}
		}
		//============= 조건절 바인딩 ====================

		$stmt->execute();
		$nextRow = $stmt->rowCount();
		if ($nextRow > 0) {
			$next = $stmt->fetch(PDO::FETCH_ASSOC);
			$next = array_map('stripslashes', $next);
			$columnKeys = array_map('trim', explode(',', $columnSql));
			$nextData = [];
			for ($i = 0; $i < count($columnKeys); $i++) {
				$nextData[$columnKeys[$i]] = $next[$columnKeys[$i]];
			}
		} else {
			$nextData = '';
		}
		return $nextData;
	}

	/* 게시판 조회수 카운트 */
	public function boardCountUp(int $idx, string $bbsid, ?string $tableName = null)
	{
		$table = ($tableName !== null && $tableName !== '')
			? $this->FunctionLibrary->escapeQuery($tableName)
			: ('gh_board_' . $this->FunctionLibrary->escapeQuery($bbsid));
		$sql = "update " . $table . " set b_count=b_count+1 where idx=:idx";
		$stmt = $this->DBConnect->prepare($sql);
		$stmt->bindParam(':idx', $idx);
		$stmt->execute();
		//return $sql;
	}

	/* 추천/유입물건 카운트 */
	public function productCountUp(int $idx, string $tableName)
	{
		$sql = "update " . $this->FunctionLibrary->escapeQuery($tableName) . " set p_hit=p_hit+1 where idx=:idx";
		$stmt = $this->DBConnect->prepare($sql);
		$stmt->bindParam(':idx', $idx);
		$stmt->execute();
		//return $sql;
	}

	//관리자 로그 등록
	public function logInsert($paramArray = array())
	{
		require_once __DIR__ . '/class-db-manager.php';

		$DB = new DBManager($this->DBConnect);

		$logTable = 'gh_log_table';

		$inputs = array();
		$inputs['p_idx'] = $paramArray['p_idx'];
		$inputs['c_code'] = $paramArray['c_code'];
		$inputs['l_name'] = $paramArray['admin_name'];
		$inputs['l_id'] = $paramArray['adminID'];
		$inputs['l_info'] = $paramArray['title'];
		$inputs['l_type'] = $paramArray['type'];
		$inputs['l_ip'] = $paramArray['ip'];
		$inputs['regdate'] = date('Y-m-d H:i:s');

		//단순 열람의 경우 10분 미만의 같은 데이터 열람은 처리하지 않음
		if ($paramArray['type'] == '1' || $paramArray['type'] == '2') {
			$logWhere = " where p_idx = :p_idx and c_code = :c_code and l_id = :l_id and substring(regdate,1,15) = :nowDate";
			$logBindParam = array();
			$logBindParam[] = array('p_idx', $paramArray['p_idx']);
			$logBindParam[] = array('c_code', $paramArray['c_code']);
			$logBindParam[] = array('l_id', $paramArray['adminID']);
			$logBindParam[] = array('nowDate', substr($inputs['regdate'], 0, 15));
			$logData = $this->getData2($logWhere, $logBindParam, $logTable);

			if ($logData) {
				return;
			}
		}

		if (!$DB->insertInto($logTable, $inputs)) {
			$result = 'N';
		} else {
			$result = 'Y';
		}
		return $result;
	}
}
