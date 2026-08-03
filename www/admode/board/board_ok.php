<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';


$board_info = $query_library->getBoardInfo($bbsid); //게시판 설정

$DB = new DBManager($conn);

if($w != 'd'){
	$inputs['b_open'] = $b_open;
	$inputs['b_cate'] = $b_cate ?? null;
	$inputs['b_subject'] = $b_subject ?? null;
	$inputs['b_content'] = $b_content ?? null;
	$inputs['b_secret'] = $b_secret ?? null;
	$inputs['b_notice'] = $b_notice ?? null;

	if($b_password ??= null){
		$inputs['b_password'] = hash('sha256',$b_password);
	}

	$inputs['b_data1'] = $b_data1 ?? null;
	$inputs['b_data2'] = $b_data2 ?? null;
	$inputs['b_data3'] = $b_data3 ?? null;
	$inputs['b_data4'] = $b_data4 ?? null;
	$inputs['b_data5'] = $b_data5 ?? null;
	$inputs['b_data6'] = $b_data6 ?? null;
	$inputs['b_data7'] = $b_data7 ?? null;
	$inputs['b_data8'] = $b_data8 ?? null;
	$inputs['b_data9'] = $b_data9 ?? null;
	$inputs['b_data10'] = $b_data10 ?? null;
	$inputs['link_url'] = $link_url ?? null;
	
	if($bbsid == 'recruit'){
		$recruit_type_array = '';
		for($i=0;$i<count((array)$b_data5);$i++){
			$recruit_type_array .= $b_data5[$i].'|';
		}
		$recruit_type_array = substr($recruit_type_array,0,-1);
		$inputs['b_data5'] = $recruit_type_array;
	}


	//================= 파일 첨부 시작 =========================
	if($del_file_thumb ??= null){
		@unlink($gh_path."data/board/$bbsid/$old_file_thumb");
		$inputs['file_thumb'] = '';
	}

	if($_FILES['file_thumb'] ??= null){
		$file = $_FILES['file_thumb']['tmp_name'];
		$file_size = $_FILES['file_thumb']['size'];
		if($file && $file_size>0){
			@unlink($gh_path."data/board/$bbsid/$old_file_thumb");
			$mfile = $func_library->uploadFile('file_thumb','',$gh_path."data/board/$bbsid");
			$inputs['file_thumb'] = $mfile['filename'];
			//$resize_data = image_resize($gh_path.'data/board/$bbsid/',$mfile['filename'],397,235,$mfile['img_type'],1,95);
			//$inputs['file_thumb'] = $resize_data['fileName'];
		}
	}

	$insert_file = '';
	$insert_file_name = '';
	if($w != 're' && $board_info['b_file']){
		for ($i=0;$i<count((array)$_FILES['b_file']['name']);$i++){
			$b_file_name=$_FILES['b_file']['name'][$i];
			$b_file_name_size=$_FILES['b_file']['size'][$i];
			if($b_file_name == ''){
				if (${'del_file'.$i} ??= null){
					@unlink($gh_path."data/board/$bbsid/".$o_name[$i] ??= '');
					$b_file_01[$i] = '';
					$b_file_01_name[$i] = '';

				}else{
					if ($w == 'u'){
						$b_file_01[$i] = $o_name[$i] ??= '';
						$b_file_01_name[$i] = $o_ori_name[$i] ??= '';
					}else{
						$b_file_01[$i] = '';
						$b_file_01_name[$i] = '';
					}
				}
			}else{
				if($_FILES['b_file']['size'][$i] > 0){
					$mfile = $func_library->uploadMultiFiles('b_file','',$gh_path."data/board/$bbsid",$i);
					@unlink($gh_path."data/board/$bbsid/".$o_name[$i]);
					if($mfile['filename']){
						$b_file_01[$i] = $mfile['filename'];
						$b_file_01_name[$i] = $mfile['original_file_name'];
					}
				}
			}

			if($b_file_01[$i]){
				$insert_file .= $b_file_01[$i].'|';
				$insert_file_name .= $b_file_01_name[$i].'|';
			}
		}

		$insert_file = substr($insert_file,0,-1);
		$insert_file_name = substr($insert_file_name,0,-1);
		if($insert_file == '|'){//첨부파일 삭제시 첨부된 파일이 한개도 없다면 초기화
			$insert_file = '';
			$insert_file_name = '';
		}

		$inputs['b_file'] = $insert_file;
		$inputs['b_file_name'] = $insert_file_name;
	}
	//================= 이미지 첨부 끝 =========================
	if($b_name){
		$inputs['b_name'] = $b_name;
	}else{
		$inputs['b_name'] = $admin_name;
	}

	//$inputs['list_num'] = $list_num;
	$inputs['regdate'] = $regdate ?? date('Y-m-d H:i:s');
}

if($w == 'a'){
	$inputs['editdate'] = date('Y-m-d H:i:s');
	$inputs['m_id'] = $admin_id;

	if(!$DB->insertInto("gh_board_$bbsid", $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$idx = $conn->lastInsertId();
		$sql = "update gh_board_$bbsid set b_parent = :b_parent,list_num = :b_parent where idx = :idx";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':b_parent',$idx);
		$stmt->bindParam(':idx',$idx);
		$stmt->execute();

		$func_library->alert('등록되었습니다.','./board_list.php?'.$func_library->queryString('idx,w'));
	}
}else if($w == 'u'){
	$inputs['editdate'] = date('Y-m-d H:i:s');
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet("gh_board_$bbsid", $inputs, $where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./board_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 're'){

	$inputs['regdate'] = date('Y-m-d H:i:s');
	$inputs['b_parent'] = $b_parent;
	$inputs['b_name'] = $admin_name;
	$inputs['m_id'] = $m_id ??= '';

	// 최대 답변은 테이블에 잡아놓은 depth 사이즈만큼만 가능합니다.
	if (strlen($depth) == 26)
		$func_library->alert('더 이상 답글을 달 수 없습니다..\\n\\n답글은 26개 까지만 가능합니다.');

	$depth_len = strlen($depth) + 1;
	$begin_depth_char = 'A';
	$end_depth_char = 'Z';
	$depth_number = +1;
	$sql = " select MAX(SUBSTRING(depth, :depth_len, 1)) as depth from gh_board_$bbsid where b_parent = :b_parent and SUBSTRING(depth, :depth_len, 1) <> '' and depth like :depth ";
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':b_parent',$b_parent);
	$stmt->bindParam(':depth_len',$depth_len);
	$stmt->bindValue(':depth','%'.$depth.'%');
	$row = $stmt->execute();

	if (!$row['depth']){
		$depth_char = $begin_depth_char;
	}else if($row['depth'] == $end_depth_char){ // A~Z은 26 입니다.
		$func_library->alert('더 이상 답변을 달 수 없습니다.\\n\\n답변은 26개 까지만 가능합니다.');
		//$depth_char = chr(ord($row[depth]));
	}else{
		$depth_char = chr(ord($row['depth']) + $depth_number);
	}

	$depth = $depth . $depth_char;

	$inputs['regdate'] = date('Y-m-d H:i:s');
	$inputs['depth'] = $depth;

	if(!$DB->insertInto("gh_board_$bbsid", $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('등록되었습니다.','./board_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'd') {
	$d = $query_library->getData($idx,"gh_board_$bbsid");
	$depth_len = strlen($d['depth'] ??=0);
	
	$where = "where b_parent = :b_parent and depth like :depth and length(depth) > :depth_len";
	$bind_param[] = array('b_parent', $d['b_parent'],'and','');
	$bind_param[] = array('depth', $d['depth'],'and','like');
	$bind_param[] = array('depth_len', $depth_len,'and','');
	$total = $query_library->dataTotal($where,$bind_param,"gh_board_$bbsid");

	if($total > 0){
		$func_library->alert('삭제하려는 게시글에 답변이 존재합니다.\\n\\답변을 삭제하시고 다시 시도해 주세요.');
	}

	$where = array();
	$where[] = array('idx', $idx,'');

	if(!$DB->delete_db("gh_board_$bbsid",$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$b_file = explode('|',$d['b_file'] ??='');
		for($i=0;$i<count((array)$b_file);$i++){
			@unlink($gh_path."data/board/$bbsid/".$b_file[$i]);
		}
		@unlink($gh_path."data/board/$bbsid/".$d['file_thumb']);
		$func_library->alert('삭제 되었습니다.','./board_list.php?'.$func_library->queryString('idx,w'));
	}
}
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>