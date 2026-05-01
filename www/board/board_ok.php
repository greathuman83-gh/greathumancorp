<?php
$ghPath = '../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/db.class.php');

if(!$bbsid){
	$funcLibrary->alert('잘못된 방법으로 접근하셨습니다.');
}
$boardInfo = $queryLibrary->getBoardInfo($bbsid);


if($boardInfo['b_write']){//글쓰기 권한 확인
	if(!isset($userInfo['idx'])){
		$funcLibrary->alert('로그인 하신 후 이용하실 수 있습니다.','/');
	}
}


/*============= AWS S3 SDK =================*/
require $ghPath.'sdk/aws/aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;

$ncpCredentials = new Credentials(NCP_ACCESS_KEY,NCP_SECRET_KEY);

//Create a S3Client
$s3 = new S3Client([
	'version' => NCP_VERSION,
	'region' => NCP_REGION,
	'endpoint' => NCP_ENDPOINT,
	'credentials' => $ncpCredentials,
]);
/*=======================================*/


$DB = new DBManager($conn);

$tableName = 'tb_bbs'; //게시판 테이블
$uploadTableName = 'tb_bbs_attach'; //첨부파일 테이블
$uploadDirectory = $bbsid;

if($w != 'd' &&  $w != 'fd'){
	$inputs['flag'] = 'Y';
	$inputs['category'] = $category ?? 0;
	$inputs['b_notice'] = $b_notice ?? '';
	$inputs['title'] = $title;
	$inputs['content'] = $content;

}

$inputs['b_number'] = $b_number ?? null;
$inputs['b_data1'] = $b_data1 ?? null;
$inputs['b_data2'] = $b_data2 ?? null;
$inputs['b_data3'] = $b_data3 ?? null;
$inputs['b_data4'] = $b_data4 ?? null;
$inputs['b_data5'] = $b_data5 ?? null;

$inputs['program'] = $program ?? '';
$inputs['youtube'] = $youtube ?? '';

if(isset($_FILES['file_thumb'])){//파일 업로드 체크
	if($_FILES['file_thumb']['size'] > 0){
		$fileInfo = $funcLibrary->ncpUploadFile('file_thumb',$_FILES['file_thumb']['name']);
		$inputs['file_thumb'] = $fileInfo['filename'];
	}
}


if($w == 'a'){
	$inputs['bbstype'] = $bbsid;
	$inputs['rdate'] = date('Y-m-d H:i:s');
	$inputs['udate'] = date('Y-m-d H:i:s');
	$inputs['nick_name'] = $userInfo['nickname'];
	$inputs['user_idx'] = $userIdx['sub'];
	$inputs['ip'] = $_SERVER['REMOTE_ADDR'];

	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$inputs = array();
		$idx = $conn->lastInsertId();

		//================= 썸네일 시작 =========================
		if(isset($_FILES['file_thumb'])){
			if($_FILES['file_thumb']['size'] > 0){
				/*==================== NCP OBJECT STORAGE ==================*/
				$uploadFileName = 'board/'.$bbsid.'/'.$fileInfo['filename'];

				try{
					$objectResult = $s3->putObject([
						'Bucket' => NCP_BUCKET,
						'Key' => $uploadFileName,
						'Body' => file_get_contents($_FILES['file_thumb']['tmp_name']),
						'ContentType' => $_FILES['file_thumb']['type'],
						'ACL' => 'public-read'
					]);
				}catch(S3Exception $e){
					$error = $e->getMessage();
				}
				$objectResultArray = $objectResult->toArray();

				if(!isset($objectResultArray['ObjectURL'])){
					//echo '파일 첨부 실패';
				}
				/*==================== NCP OBJECT STORAGE END ===============*/
			}
			@unlink($_FILES['file_thumb']['tmp_name']);
		}
		//==================================================

		
		//================= 파일 첨부 시작 =========================
		if(isset($_FILES['attachFile'])){
			for ($i=0;$i<count((array)$_FILES['attachFile']['name']);$i++){
				if($_FILES['attachFile']['size'][$i] > 0){
					$fileInfo = $funcLibrary->ncpUploadFile('attachFile',$_FILES['attachFile']['name'][$i],$i);
					if($fileInfo['filename']){
						/*==================== NCP OBJECT STORAGE ==================*/
						$uploadFileName = 'board/'.$bbsid.'/'.$fileInfo['filename'];

						try{
							$objectResult = $s3->putObject([
								'Bucket' => NCP_BUCKET,
								'Key' => $uploadFileName,
								'Body' => file_get_contents($_FILES['attachFile']['tmp_name'][$i]),
								'ContentType' => $_FILES['attachFile']['type'][$i],
								'ACL' => 'private'
							]);
						}catch(S3Exception $e){
							$error = $e->getMessage();
						}
						$objectResultArray = $objectResult->toArray();

						if(isset($objectResultArray['ObjectURL'])){
							//DB 처리
							$inputFiles['bbs_idx'] = $idx;
							$inputFiles['filename'] =$fileInfo['filename'];
							$inputFiles['realfilename'] = $_FILES['attachFile']['name'][$i];
							$inputFiles['filesize'] = $_FILES['attachFile']['size'][$i];
							$inputFiles['mimetype'] = $_FILES['attachFile']['type'][$i];
							if(!$DB->insertInto($uploadTableName, $inputFiles)){
								$funcLibrary->alert('첨부파일 등록에 문제가 발생하였습니다.');
							}
						}
						/*==================== NCP OBJECT STORAGE END ===============*/
					}
					@unlink($_FILES['attachFile']['tmp_name'][$i]);
				}
			}
		}
		//================= 파일 첨부 끝 =========================

		$inputs['ref'] = $idx;
		$where[] = array('bbs_idx', $idx,'and');
		if(!$DB->updateSet($tableName, $inputs, $where)){
			$funcLibrary->alert('문제가 발생하였습니다.');
		}

		//================= 메일 전송 시작 =========================
		$filePath = '';
		$fileName = '';
		$sendEmail = SMTP_ID;
		$sendName = 'CADIAN';
		$receiveName = '관리자';
		// cad
		$receiveEmail = SMTP_CAD_EMAIL;
		// icad
		$cc = SMTP_ICAD_EMAIL;
		$emailBody = '
		<!DOCTYPE html>
		<html charset="utf-8">
		<head>
		<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
		<title>CADian</title>
		</head>
		<body>
		<div class="emailForm">
			<div>
				질문 제목 : '.$title.'
			</div>
			<a href= "'.HOMEPAGE_URL.'/board/board.php?bbsid=qna&idx='.$idx.'">게시글 보러가기</a> 
		</div>
		</body>
		</html>';
		$emailSubject = '[QNA] 사용자 질문이 등록되었습니다.';
		
		
		$funcLibrary->smtpSend(SMTP_HOST,SMTP_ID,SMTP_PASSWORD,SMTP_PORT,SMTP_AUTH,$emailBody,$emailSubject,$sendEmail,$sendName,$receiveEmail,$receiveName,$filePath,$fileName,$cc,0);
		//================= 메일 전송 끝 =========================

		$funcLibrary->alert('등록되었습니다.','./board.php?'.$funcLibrary->queryString('idx,w'));
	}

}else if($w == 'u'){
	$d = $queryLibrary->getData($idx,$tableName,'bbs_idx');
	if($userIdx['sub'] != $d['user_idx']){
		$funcLibrary->alert('해당 글을 수정하실 권한이 없습니다.');
	}

	//================= 썸네일 시작 =========================
	if(isset($_FILES['file_thumb'])){
		if($_FILES['file_thumb']['size'] > 0){
			/*==================== NCP OBJECT STORAGE ==================*/
			$uploadFileName = 'board/'.$bbsid.'/'.$fileInfo['filename'];

			try{
				$objectResult = $s3->putObject([
					'Bucket' => NCP_BUCKET,
					'Key' => $uploadFileName,
					'Body' => file_get_contents($_FILES['file_thumb']['tmp_name']),
					'ContentType' => $_FILES['file_thumb']['type'],
					'ACL' => 'public-read'
				]);
			}catch(S3Exception $e){
				$error = $e->getMessage();
			}
			$objectResultArray = $objectResult->toArray();

			if(!isset($objectResultArray['ObjectURL'])){
				//echo '파일 첨부 실패';
			}
			/*==================== NCP OBJECT STORAGE END ===============*/
		}
		@unlink($_FILES['file_thumb']['tmp_name']);
	}
	//==================================================

	//================= 파일 첨부 시작 =========================
	if(isset($_FILES['attachFile'])){
		for ($i=0;$i<count((array)$_FILES['attachFile']['name']);$i++){
			if($_FILES['attachFile']['size'][$i] > 0){
				$fileInfo = $funcLibrary->ncpUploadFile('attachFile',$_FILES['attachFile']['name'][$i],$i);
				if($fileInfo['filename']){
					/*==================== NCP OBJECT STORAGE ==================*/
					$uploadFileName = 'board/'.$bbsid.'/'.$fileInfo['filename'];

					try{
						$objectResult = $s3->putObject([
							'Bucket' => NCP_BUCKET,
							'Key' => $uploadFileName,
							'Body' => file_get_contents($_FILES['attachFile']['tmp_name'][$i]),
							'ContentType' => $_FILES['attachFile']['type'][$i],
							'ACL' => 'private'
						]);
					}catch(S3Exception $e){
						$error = $e->getMessage();
					}
					$objectResultArray = $objectResult->toArray();

					if(isset($objectResultArray['ObjectURL'])){
						//DB 처리
						$inputFiles['bbs_idx'] = $idx;
						$inputFiles['filename'] =$fileInfo['filename'];
						$inputFiles['realfilename'] = $_FILES['attachFile']['name'][$i];
						$inputFiles['filesize'] = $_FILES['attachFile']['size'][$i];
						$inputFiles['mimetype'] = $_FILES['attachFile']['type'][$i];
						//기존파일 존재시 삭제 및 DB업데이트
						if($oldAttachFile[$i]){
							$whereFiles[] = array('attach_idx', $oldAttachIdx[$i],'and');
							if(!$DB->updateSet($uploadTableName, $inputFiles, $whereFiles)){
								$funcLibrary->alert('문제가 발생하였습니다.');
							}
							$deleteFileName = 'board/'.$bbsid.'/'.$oldAttachFile[$i];
							try{
								$objectResult = $s3->deleteObject([
									'Bucket'               => NCP_BUCKET,
									'Key'                  => $deleteFileName,
								]);
							}catch(S3Exception $e){
								$error = $e->getMessage();
							}
						}else{//기존파일 없을시 인서트
							if(!$DB->insertInto($uploadTableName, $inputFiles)){
								$funcLibrary->alert('첨부파일 등록에 문제가 발생하였습니다.');
							}
						}

					}
					/*==================== NCP OBJECT STORAGE END ===============*/
				}
				@unlink($_FILES['attachFile']['tmp_name'][$i]);
			}
		}
	}
	//================= 파일 첨부 끝 =========================

	$inputs['udate'] = date('Y-m-d H:i:s');
	$where[] = array('bbs_idx', $idx,'and');
	if(!$DB->updateSet($tableName, $inputs, $where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./board.php?'.$funcLibrary->queryString('w'));
	}

}else if($w == 're'){
	$inputs['bbstype'] = $bbsid;
	$inputs['udate'] = date('Y-m-d H:i:s');
	$inputs['rdate'] = date('Y-m-d H:i:s');
	$inputs['ref'] = $parentIdx;
	$inputs['userid'] = $adminId;
	$inputs['ip'] = $_SERVER['REMOTE_ADDR'];

	//최대 댓글 20개
	if ((int)$depth >= 20)
		$funcLibrary->alert('더 이상 답글을 달 수 없습니다..\\n\\n답글은 20개 까지만 가능합니다.');

	$depthLevel = (int)$depth + 1;


	$inputs['ref_level'] = $depthLevel;
	$inputs['ref_step'] = 0; //최신 댓글이 최상단(ASC)
	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		/*================ 댓글 작성 시 같은 레벨 댓글 step +1 ===================*/
		$sql = "update $tableName set ref_step = ref_step+1 where ref = :parentIdx and ref_level = :depthLevel ";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':parentIdx',$parentIdx);
		$stmt->bindParam(':depthLevel',$depthLevel);
		$stmt->execute();
		/*===================================================================*/
		$funcLibrary->alert('등록되었습니다.','./board_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}else if($w == 'd') {
	$d = $queryLibrary->getData($idx,$tableName,'bbs_idx');
	if($userIdx['sub'] != $d['user_idx']){
		$funcLibrary->alert('해당 글을 삭제하실 권한이 없습니다.');
	}

	$where = "where ref = :parentIdx and ref_level > :depth ";
	$bindParam = array();
	$bindParam[] = array('parentIdx', $d['ref']);
	$bindParam[] = array('depth', $d['ref_level']);
	$total = $queryLibrary->dataTotal($where,$bindParam,$tableName);

	if($total > 0){
		$funcLibrary->alert('삭제하려는 게시글에 답변이 존재합니다.\\n답변이 달린 게시글은 삭제하실 수 없습니다.');
	}

	/*==================== 썸네일 삭제 ========================*/
	$deleteFileName = 'board/'.$bbsid.'/'.$d['file_thumb'];
	try{
		$objectResult = $s3->deleteObject([
			'Bucket'               => NCP_BUCKET,
			'Key'                  => $deleteFileName,
		]);
	}catch(S3Exception $e){
		$error = $e->getMessage();
	}
	/*==================== 썸네일 삭제 END =====================*/
	
	/*====================== 첨부 파일 삭제 ====================*/
	$fileBindParam = array();
	$fileWhere = " where bbs_idx = :bbs_idx ";
	$fileBindParam[] = array('bbs_idx', $idx);
	$orderby = "attach_idx asc";
	$attachCount = $queryLibrary->dataTotal($fileWhere, $fileBindParam,$uploadTableName);
	$listResult = $queryLibrary->getList($fileWhere,$fileBindParam,$uploadTableName,$orderby,1,(int)$attachCount,'attach_idx');
	/*
	foreach($listResult['result'] as $fileData){
		@unlink($ghPath."data/board/$bbsid/".$fileData['filename']);
	}
	*/
	foreach($listResult['result'] as $fileData){
		$deleteFileName = 'board/'.$bbsid.'/'.$fileData['filename'];
		try{
			$objectResult = $s3->deleteObject([
				'Bucket'               => NCP_BUCKET,
				'Key'                  => $deleteFileName,
			]);
		}catch(S3Exception $e){
			$error = $e->getMessage();
		}
	}

	$where = array();
	$where[] = array('bbs_idx', $idx);

	if(!$DB->delete_db($uploadTableName,$where)){
		$funcLibrary->alert('첨부파일 삭제시 문제가 발생하였습니다.');
	}
	/*====================== 첨부 파일 삭제 END ==================*/

	$where = array();
	$where[] = array('bbs_idx', $idx);

	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('삭제 되었습니다.','./board.php?'.$funcLibrary->queryString('idx,w'));
	}
}
include_once($ghPath.'include/common/dbclose.php');
?>