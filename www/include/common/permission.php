<?php
if(!isset($adminId)){
	$funcLibrary->alert($_pageText['로그인하신 후 이용하실 수 있습니다.'],ADMIN_DIR);
}

if($_SERVER['PHP_SELF'] != '/admode/main.php'){

	if(!isset($menuCode) && !$adminSuper){
		$funcLibrary->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
	}else{
		if(isset($menuCode)){//메뉴 접근 권한 체크
			$authorParam = array();
			$authorWhere = ' where m_code = :menuCode and language = :language';
			$authorParam[] = array('menuCode',$menuCode);
			$authorParam[] = array('language',LANGUAGE);
			$authorData = $queryLibrary->getData2($authorWhere,$authorParam,'gh_admin_menu_table');
			$menuCodeData = explode('|',$authorData['m_codeName']);
			//메뉴코드와 DB에 저장된 경로가 일치하는지 확인
			if(strpos($_SERVER['REQUEST_URI'],$authorData['m_link']) === false){
				if($bbsid){
					if($bbsid != $authorData['m_codeName']){
						$funcLibrary->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
					}
				}else{
					$parts = explode('/', $_SERVER['REQUEST_URI']);  // 먼저 '/' 기준으로 나누기
					$lastPart = array_pop($parts);  // 마지막 요소 추출
					$pathName = explode('_', $lastPart);
					if($pathName[0] != $menuCodeData[0]){
						$funcLibrary->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
					}
					if(isset($pageType)){
						if($pageType != ($menuCodeData[1] ?? null)){
							$funcLibrary->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
						}
					}
				}
			}else{
				if(isset($pageType)){
					if($pageType != ($menuCodeData[1] ?? null)){
						$funcLibrary->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
					}
				}
			}
			$funcLibrary->adminAuthor($authorData['m_code'],$adminSuper,$adminAuth); //관리자 메뉴 접근 권한 체크
		}
	}
}
?>