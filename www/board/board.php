<?php
include '../_sub_top.php';
if(!$bbsid){
	$funcLibrary->alert('잘못된 방법으로 접근하셨습니다.');
}

$boardInfo = $queryLibrary->getBoardInfo($bbsid);
$attachTextArray = explode('|',$boardInfo['b_file_text']);
$where = array();
$bindParam = array();

if(!$boardInfo){
	$funcLibrary->alert('존재하지 않는 게시판입니다.');
}

if($idx && !$w){//게시글 상세 내용
	$d = $queryLibrary->getData($idx,'gh_board_'.$bbsid);
	if(!$d){
		$funcLibrary->alert('존재하지 않는 게시글입니다.');
	}
	$regdate = substr($d['regdate'],0,10);
	$regdate2 = date('Y.m.d',strtotime($regdate));
	$b_file = explode('|',$d['b_file'] ??= '');
	$b_file_name = explode('|',$d['b_file_name'] ??= '');
	$d['b_subject'] = html_entity_decode($d['b_subject'],ENT_QUOTES, 'UTF-8');
	$d['b_content'] = html_entity_decode($d['b_content'],ENT_QUOTES, 'UTF-8');

	if($boardInfo['b_read'] && !$adminId){
		if($boardInfo['b_secret'] && !$adminId){
			if($d['user_idx']){
				if($userInfo['idx'] != $d['user_idx']){
					$funcLibrary->alert('해당 글을 보실 권한이 없습니다.');
				}
			}else{
				if(!$b_pwd){
					$funcLibrary->gotoUrl("password.php?bbsid=$bbsid&idx=$idx&w=&url=board.php");
				}else{
					if($b_pwd!=$d['b_password']){
						//$funcLibrary->alert("비밀번호가 일치하지 않습니다.","board.php?bbsid=".$bbsid);
						$funcLibrary->alert('비밀번호가 일치하지 않습니다.');
					}else{
						$pass = 'ok';
					}
				}
			}
		}

		if($user_level < $boardInfo['b_read'] && !$adminId && $pass != 'ok'){
			if($userInfo['idx']){
				if($userInfo['idx'] != $d['user_idx']){
					$funcLibrary->alert('해당 글을 보실 권한이 없습니다.');
				}
			}else{
				$funcLibrary->alert('로그인 하신 후 이용하실 수 있습니다.');
			}
		}
	}else{
		if($boardInfo['b_secret'] && !$adminId){
			if($d['user_idx'] && $d['user_idx'] != 'admin'){
				if($userInfo['idx'] != $d['user_idx']){
					$funcLibrary->alert('해당 글을 보실 권한이 없습니다.');
				}
			}else{
				if(!$b_pwd){
					$funcLibrary->gotoUrl("password.php?bbsid=$bbsid&idx=$idx&w=&url=board.php");
				}else{
					$b_pwd = hash('sha256',$b_password);
					if($b_pwd != $d['b_password']){
						//$funcLibrary->alert("비밀번호가 일치하지 않습니다.","board.php?bbsid=".$bbsid);
						$funcLibrary->alert('비밀번호가 일치하지 않습니다.');
					}else{
						$pass = 'ok';
					}
				}
			}
		}
	}

	$queryLibrary->boardCountUp($idx,$bbsid);
	
	//============ 이전글, 다음글=======================
	$pnWhere = '';
	$pnParam = array();
	$pnWhere = " and b_open = '1' ";
	if($keyword){
		if($keyType == 'b_subject'){
			$pnWhere .= " and b_subject like :keyword ";
		}else if($keyType == 'b_content'){
			$pnWhere .= " and b_content like :keyword ";
		}else{
			$pnWhere .= " and (b_subject like :keyword or b_content like :keyword) ";
		}
		$pnParam[] = array('keyword', $keyword,'like');
	}

	if($cate){
		$pnWhere .= " and b_cate = :b_cate ";
		$pnParam[] = array('b_cate',$cate);
	}

	$prevData = $queryLibrary->prevPost("gh_board_".$bbsid,$idx,"idx,b_subject,regdate",$pnWhere,$pnParam);
	$nextData = $queryLibrary->nextPost("gh_board_".$bbsid,$idx,"idx,b_subject,regdate",$pnWhere,$pnParam);
	//=================== 이전글,다음글 끝 ================}
}

if($idx && $w == 'u'){//게시글 수정
	$d = $queryLibrary->getData($idx,'gh_board_'.$bbsid);
	$updateDate = date('Y.m.d',strtotime(substr($d['udate'],0,10)));

	if($userInfo['idx'] != $d['user_idx']){
		$funcLibrary->alert('해당 글을 수정하실 권한이 없습니다.');
	}
	/*
	if($d['user_idx']){
		if($userInfo['idx'] != $d['user_idx']){
			$funcLibrary->alert('해당 글을 수정하실 권한이 없습니다.');
		}
	}else{
		$d['b_subject'] = html_entity_decode($d['b_subject']);
		$d['b_content'] = html_entity_decode($d['b_content']);

		if(!$b_pwd){
			$funcLibrary->gotoUrl("password.php?bbsid=$bbsid&idx=$idx&w=u&url=".urlencode($_SERVER['REQUEST_URI']));
		}else{
			$b_pwd = hash("sha256",$b_pwd);
			if($b_pwd != $d['b_password'] && !$d['user_idx']){
				$funcLibrary->alert('비밀번호가 일치하지 않습니다.');
			}else{
				$_SESSION['update'] = $idx;
			}
		}
	}
	*/
}else if($idx && $w == 're'){//계층형 답글
}else if($w == 'a'){//글쓰기
	if($boardInfo['b_write']){//글쓰기 권한 확인
		if(!isset($userInfo['email'])){
			$funcLibrary->alert('로그인 하신 후 이용하실 수 있습니다.',SSO_GET_AUTH);
		}
	}
	$d = $queryLibrary->getColumn('gh_board_'.$bbsid);
	$d['regdate'] = date('Y-m-d H:i:s');

}else if(!$w && !$idx){//게시글 리스트
	
	if($boardInfo['b_level']){//게시판 접근권한
		if($userLevel < $boardInfo['b_level'] && !$adminId){
			if($userInfo['idx']){
				$funcLibrary->alert('해당게시판에 접근하실 권한이 없습니다.');
			}else{
				$funcLibrary->alert('로그인 하신 후 이용하실 수 있습니다.',"../member/login.php?r_url=".urlencode("/board/board.php?bbsid=$bbsid"));
			}
		}
	}

	include_once("skin/$boardInfo[b_skin]/list.php");
}else if($idx && !$w){//게시글 상세내용
	include_once("skin/$boardInfo[b_skin]/view.php");
}else if($w){//게시글 작성 및 수정
	include_once("skin/$boardInfo[b_skin]/write.php");
}

include '../_sub_bottom.php';
?>