function menuLink(on){
	//관리자 링크
	if (on==11){
		location="/admin/member/manager_list.php";
	}
	if (on==12){
		location="/admin/member/member_list.php";
	}
	if (on==21){
		location="/admin/category/category_list.php";
	}
	if (on==31){
		location="/admin/content/content_list.php";
	}
	if (on==41){
		location="/admin/main/main_list.php";
	}
	if (on==42){
		location="/admin/main/newsletter_list.php";
	}
	if (on==43){
		location="/admin/main/customercase_list.php";
	}


	if (on==51){
		location="/admin/board/board_list.php?bo_table=pupdate";
	}
	if (on==52){
		location="/admin/board/board_list.php?bo_table=edu";
	}
	if (on==53){
		location="/admin/board/board_list.php?bo_table=recruit";
	}
	if (on==54){
		location="/admin/board/board_list.php?bo_table=notice";
	}
	if (on==55){
		location="/admin/board/board_list.php?bo_table=invest";
	}
	if (on==56){
		location="/admin/board/board_list.php?bo_table=bodo";
	}


	if (on==61){
		location="/admin/forum/topic_list.php";
	}

	if (on==62){
		location="/admin/forum/forum_list.php";
	}

	if (on==71){
		location="/admin/resource/resource_list.php";
	}

	if (on==81){
		location="/admin/success/success_list.php";
	}

	if (on==91){
		location="/admin/library/file_library_list.php";
	}

	if (on==101){
		location="/admin/apply/tech_support_list.php";
	}

	if (on==102){
		location="/admin/apply/product_list.php?category=product";
	}

	if (on==103){
		location="/admin/apply/form_list.php";
	}

	if (on==104){
		location="/admin/apply/product_list.php?category=newsletter";
	}





}

function comLink(on){
	location="/admin/data/data_list.php?bbs_idx="+on;
}