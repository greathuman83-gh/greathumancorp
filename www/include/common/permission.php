<?php
if (!isset($admin_id)) {
	$func_library->alert($_pageText['로그인하신 후 이용하실 수 있습니다.'], ADMIN_DIR);
}

if ($_SERVER['PHP_SELF'] != '/admode/main.php') {

	if (!isset($menu_code) && !$admin_super) {
		$func_library->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
	} else {
		if (isset($menu_code)) { //메뉴 접근 권한 체크
			$author_param = array();
			$author_where = ' where m_code = :menu_code and language = :language';
			$author_param[] = array('menu_code', $menu_code);
			$author_param[] = array('language', LANGUAGE);
			$author_data = $query_library->getData2($author_where, $author_param, 'gh_admin_menu_table');
			$menu_code_data = explode('|', $author_data['m_code_name']);
			//메뉴코드와 DB에 저장된 경로가 일치하는지 확인
			if (strpos($_SERVER['REQUEST_URI'], $author_data['m_link']) === false) {
				if ($bbsid) {
					if ($bbsid != $author_data['m_code_name']) {
						$func_library->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
					}
				} else {
					$parts = explode('/', $_SERVER['REQUEST_URI']);  // 먼저 '/' 기준으로 나누기
					$last_part = array_pop($parts);  // 마지막 요소 추출
					$path_name = explode('_', $last_part);
					if ($path_name[0] != $menu_code_data[0]) {
						$func_library->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
					}
					if (isset($page_type)) {
						if ($page_type != ($menu_code_data[1] ?? null)) {
							$func_library->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
						}
					}
				}
			} else {
				if (isset($page_type)) {
					if ($page_type != ($menu_code_data[1] ?? null)) {
						$func_library->alert($_pageText['잘못된 방법으로 접근하셨습니다.']);
					}
				}
			}
			$func_library->adminAuthor($author_data['m_code'], $admin_super, $admin_auth); //관리자 메뉴 접근 권한 체크
		}
	}
}
