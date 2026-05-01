function menu_link(on){

//store
	if (on==11){
		location="/sub_main/shop/shop_list.php";
	}
	if (on==12){
		location="/sub_main/shop/cart.php";
	}
	if (on==13){
		location="/sub_main/member/order_list.php";
	}
	

//event
	if (on==21){
		location="/sub_main/event/event_list.php?bbs_idx=1";
	}
	if (on==22){
		location="/sub_main/event/event_list.php?bbs_idx=2";
	}
	if (on==23){
		location="/sub_main/event/event_list.php?bbs_idx=3";
	}

//community
	if (on==31){
//		ready();
		location="/sub_main/bbs/bbs_list.php?bbs_idx=17";
	}
	if (on==32){
		location="/sub_main/bbs/bbs_list.php?bbs_idx=18";
	}
	if (on==33){
		location="/sub_main/bbs/bbs_list.php?bbs_idx=19";
	}
	
	if (on==34){
		location="/sub_main/bbs/bbs_faq.php";
	}
	
//about farm
	if (on==41){
		location="/sub_main/aboutfarm/bum.php";
	}
	if (on==42){
		location="/sub_main/aboutfarm/sung.php";
	}
	if (on==43){
		location="/sub_main/aboutfarm/gana.php";
	}

//about us
	if (on==51){
//		ready();
		location="/sub_main/aboutus/about.php";
	}



//member
	if (on==61){
		location="/sub_main/member/member_agree.php";
	}
	if (on==62){
		location="/sub_main/member/login.php";
	}
	if (on==63){
		location="/sub_main/member/mypage.php";
	}


//로그아웃
	if (on==91){
		location="/sub_main/member/logout_ok.php";
	}
}