-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- 호스트: localhost
-- 생성 시간: 26-05-01 13:19
-- 서버 버전: 10.6.17-MariaDB-log
-- PHP 버전: 8.4.10p1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `greathumancorp`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_admin`
--

CREATE TABLE `gh_admin` (
  `idx` int(11) NOT NULL COMMENT '고유번호',
  `language` varchar(10) DEFAULT NULL COMMENT '언어',
  `category` varchar(10) DEFAULT NULL COMMENT '분류',
  `a_level` varchar(10) DEFAULT NULL,
  `super` varchar(1) DEFAULT NULL,
  `a_id` varchar(60) DEFAULT NULL,
  `a_pwd` varchar(80) DEFAULT NULL,
  `a_name` varchar(20) DEFAULT NULL,
  `a_nick` varchar(30) DEFAULT NULL,
  `a_tel` varchar(20) DEFAULT NULL,
  `a_hp` varchar(20) DEFAULT NULL,
  `a_authority` text DEFAULT NULL,
  `a_email` varchar(100) DEFAULT NULL,
  `a_data1` varchar(50) DEFAULT NULL,
  `a_data2` varchar(50) DEFAULT NULL,
  `a_data3` varchar(50) DEFAULT NULL,
  `a_data4` varchar(200) DEFAULT NULL,
  `file1` varchar(100) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `gh_admin`
--

INSERT INTO `gh_admin` (`idx`, `language`, `category`, `a_level`, `super`, `a_id`, `a_pwd`, `a_name`, `a_nick`, `a_tel`, `a_hp`, `a_authority`, `a_email`, `a_data1`, `a_data2`, `a_data3`, `a_data4`, `file1`, `regdate`) VALUES
(5, 'kr', '10', '10', '1', 'admin', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '관리자', 'abcd', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', '2021-07-06 11:52:17');

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_admin_menu_table`
--

CREATE TABLE `gh_admin_menu_table` (
  `idx` int(20) NOT NULL,
  `num` int(11) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL COMMENT '언어',
  `category` varchar(30) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `depth` varchar(10) DEFAULT NULL,
  `m_code` varchar(30) DEFAULT NULL COMMENT '메뉴코드',
  `m_name` varchar(50) DEFAULT NULL COMMENT '메뉴명',
  `m_codeName` varchar(30) DEFAULT NULL COMMENT '메뉴코드명',
  `m_open` varchar(10) DEFAULT '1',
  `m_link` varchar(200) DEFAULT NULL COMMENT '메뉴링크',
  `m_link_target` varchar(10) DEFAULT NULL COMMENT '타겟(_blank:새창,_self:현재창)',
  `m_link_type` varchar(10) DEFAULT NULL COMMENT '링크위치(1:외부)',
  `regdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `gh_admin_menu_table`
--

INSERT INTO `gh_admin_menu_table` (`idx`, `num`, `language`, `category`, `parent`, `depth`, `m_code`, `m_name`, `m_codeName`, `m_open`, `m_link`, `m_link_target`, `m_link_type`, `regdate`) VALUES
(1, 0, 'kr', '', 1, '1', '001', '관리자관리', 'manager', '1', '/member/manager_list.php?', '_self', '', '2021-07-05 23:28:57'),
(2, NULL, 'kr', '', 1, '2', '001001', '관리자관리', 'manager', '1', '/member/manager_list.php?', '_self', '', '2021-07-06 05:45:58'),
(3, 4, 'kr', '', 3, '1', '002', '회원관리', 'member', NULL, '/member/member_list.php?', '_self', NULL, '2021-07-06 07:17:05'),
(4, NULL, 'kr', '', 3, '2', '002001', '회원리스트', 'member', '1', '/member/member_list.php?', '_self', NULL, '2021-07-06 07:17:35'),
(13, 1, 'kr', '', 13, '1', '003', '게시판관리', 'notice', '1', '/board/board_list.php?bbsid=notice', '_self', '', '2021-07-08 07:49:18'),
(14, 0, 'kr', '', 13, '2', '003001', '공지사항', 'notice', '1', '/board/board_list.php?bbsid=notice', '_self', '', '2021-07-08 07:49:42'),
(18, 8, 'kr', NULL, 18, '1', '004', '팝업관리', 'popup|kr', '1', '/popup/popup_list.php?pageType=kr', '_self', '', '2021-09-17 13:40:07'),
(19, NULL, 'kr', NULL, 18, '2', '004001', '팝업 관리', 'popup|kr', '1', '/popup/popup_list.php?pageType=kr', '_self', '', '2021-09-17 13:43:10'),
(24, 6, 'kr', NULL, 24, '1', '006', '문의관리', 'inquiry|inquiry', '', '/inquiry/inquiry_list.php?pageType=inquiry', '_self', '', '2021-10-01 11:58:26'),
(25, 0, 'kr', NULL, 24, '2', '006001', '온라인문의 관리', 'inquiry|inquiry', '1', '/inquiry/inquiry_list.php?pageType=inquiry', '_self', '', '2021-10-01 12:00:07'),
(35, NULL, 'kr', NULL, 3, '2', '002002', '탈퇴회원리스트', 'member', '1', '/member/member_leave_list.php?', '_self', NULL, '2021-12-24 08:12:53'),
(40, 2, 'kr', NULL, 40, '1', '008', '계산서관리', 'invoice|1', '1', '/invoice/invoice_list.php?pageType=1', '_self', '', '2022-05-23 13:32:44'),
(41, 0, 'kr', NULL, 40, '2', '008001', '매출', 'invoice|1', '1', '/invoice/invoice_list.php?pageType=1', '_self', '', '2022-05-23 13:32:57'),
(57, 5, 'kr', NULL, 57, '1', '010', '채용관리', 'recruit', '', '', '_self', '', '2022-08-09 13:45:24'),
(58, NULL, 'kr', NULL, 57, '2', '010001', '채용공고관리', 'recruit', '1', '/recruit/recruit_list.php?', '_self', NULL, '2022-08-09 13:45:58'),
(59, NULL, 'kr', NULL, 57, '2', '010002', '지원분야관리', 'category', '1', '/recruit/category_list.php?cate=recruit', '_self', NULL, '2022-08-09 13:47:39'),
(60, NULL, 'kr', NULL, 57, '2', '010003', '사업장관리', 'category', '1', '/recruit/category_list.php?cate=workplace', '_self', NULL, '2022-08-09 15:15:31'),
(61, NULL, 'kr', NULL, 57, '2', '010004', '이력서리스트', 'resume', '1', '/recruit/resume_list.php?', '_self', NULL, '2022-08-29 08:47:49'),
(86, 7, 'kr', NULL, 86, '1', '007', 'SEO관리', 'seo', '1', '/seo/seo_form.php?idx=1&amp;w=u', '_self', '', '2025-07-24 09:54:05'),
(87, NULL, 'kr', NULL, 86, '2', '007001', '메타태그 관리', 'seo', '1', '/seo/seo_form.php?idx=1&amp;w=u', '_self', '', '2025-07-24 09:55:31'),
(88, NULL, 'kr', NULL, 86, '2', '007002', 'OG 태그 관리', 'seo', '1', '/seo/seo_form.php?idx=2&amp;w=u', '_self', '', '2025-07-24 10:06:50'),
(89, NULL, 'kr', NULL, 86, '2', '007003', 'Robots 설정', 'robots', '1', '/seo/robots_form.php?', '_self', '', '2025-07-24 10:08:17'),
(107, 1, 'kr', NULL, 40, '2', '008003', '매입', 'invoice|2', '1', '/invoice/invoice_list.php?pageType=2', '_self', '', '2026-03-04 13:44:31'),
(108, 3, 'kr', NULL, 108, '1', '011', '직원관리', 'worker', '1', '/member/worker_list.php?', '_self', '', '2026-03-22 17:42:20'),
(109, NULL, 'kr', NULL, 108, '2', '011001', '직원 관리', 'worker', '1', '/member/worker_list.php?', '_self', '', '2026-03-22 17:43:03');

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_board`
--

CREATE TABLE `gh_board` (
  `idx` int(20) NOT NULL COMMENT '고유번호',
  `num` varchar(10) DEFAULT NULL,
  `b_name` varchar(50) NOT NULL COMMENT '게시판이름',
  `bbsid` varchar(50) NOT NULL COMMENT '테이블명',
  `b_skin` varchar(100) NOT NULL COMMENT '스킨명',
  `b_cate` varchar(10) DEFAULT NULL COMMENT '분류',
  `b_list_num` varchar(100) DEFAULT NULL COMMENT '순서관리여부',
  `b_link` varchar(10) DEFAULT NULL COMMENT '링크(1:사용)',
  `b_type` varchar(10) DEFAULT NULL COMMENT '게시판타입(1:일반 2:갤러리)',
  `b_thumb_text` varchar(100) NOT NULL COMMENT '썸네일텍스트',
  `b_comment` varchar(10) DEFAULT NULL COMMENT '댓글사용여부(1:사용)',
  `b_content_type` varchar(1) DEFAULT NULL COMMENT '본문사용(1:editor:2:일반:3사용안함',
  `b_reply` varchar(1) DEFAULT NULL COMMENT '게시판형태(1:계층형)',
  `b_level` varchar(30) DEFAULT NULL COMMENT '접근허용등급 1~100',
  `b_secret` varchar(10) DEFAULT NULL COMMENT '비밀글(1:사용)	',
  `b_write` varchar(10) DEFAULT NULL COMMENT '쓰기권한 1~100',
  `b_read` varchar(10) DEFAULT NULL COMMENT '읽기권한 1~100',
  `b_notice` varchar(10) DEFAULT NULL COMMENT '공지(1:사용)	',
  `b_file` varchar(10) DEFAULT NULL COMMENT '첨부파일갯수',
  `b_file_text` text DEFAULT NULL COMMENT '첨부파일텍스트설정',
  `regdate` datetime NOT NULL COMMENT '등록일'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='게시판설정';

--
-- 테이블의 덤프 데이터 `gh_board`
--

INSERT INTO `gh_board` (`idx`, `num`, `b_name`, `bbsid`, `b_skin`, `b_cate`, `b_list_num`, `b_link`, `b_type`, `b_thumb_text`, `b_comment`, `b_content_type`, `b_reply`, `b_level`, `b_secret`, `b_write`, `b_read`, `b_notice`, `b_file`, `b_file_text`, `regdate`) VALUES
(1, NULL, '뉴스룸', 'news', 'news', '', '', '', '2', '(530x294)', '', '1', '', '', '', '100', '', '', '3', '첨부파일', '2020-12-21 11:21:23'),
(2, '4', '공지사항', 'notice', 'notice', '', '', '', '1', '', '', '1', '', '', '', '100', '', '', '3', '첨부파일', '2020-12-21 11:21:23'),
(3, NULL, 'FAQ', 'faq', 'faq', '', '', '', '1', '', '', NULL, NULL, '', '', '100', '', '', '1', '첨부파일', '2020-12-21 11:21:23'),
(4, '11', '입사지원', 'recruit', 'recruit', '', '', '', '1', '', '', '1', NULL, '', '', '100', '', '', '1', '첨부파일', '2020-08-18 16:34:00');

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_board_notice`
--

CREATE TABLE `gh_board_notice` (
  `idx` int(20) NOT NULL COMMENT '고유번호',
  `b_parent` int(11) DEFAULT NULL COMMENT '부모글고유번호',
  `list_num` int(11) DEFAULT NULL COMMENT '순서',
  `depth` varchar(50) DEFAULT NULL COMMENT '계층형깊이',
  `category` varchar(10) DEFAULT NULL COMMENT '분류',
  `b_file` varchar(500) DEFAULT NULL COMMENT '저장되는파일명',
  `b_file_name` varchar(500) DEFAULT NULL COMMENT '원본파일명',
  `file_thumb` varchar(100) DEFAULT NULL COMMENT '리스트썸네일',
  `link_url` varchar(200) DEFAULT NULL COMMENT '링크',
  `regdate` datetime NOT NULL COMMENT '등록일',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `b_subject` varchar(200) NOT NULL COMMENT '타이틀',
  `b_cate` varchar(30) DEFAULT NULL COMMENT '분류',
  `b_name` varchar(50) DEFAULT NULL COMMENT '작성자',
  `m_id` varchar(100) DEFAULT NULL COMMENT '작성자아이디',
  `b_content` longtext DEFAULT NULL COMMENT '상세내용',
  `b_count` int(11) NOT NULL DEFAULT 0 COMMENT '조회수',
  `b_notice` varchar(10) DEFAULT '2' COMMENT '1:공지설정',
  `b_secret` varchar(10) DEFAULT NULL COMMENT '비밀글',
  `b_password` varchar(30) DEFAULT NULL COMMENT '비밀번호',
  `b_open` varchar(10) DEFAULT NULL COMMENT '공개여부(1:공개 2:비공개)',
  `b_data1` text DEFAULT NULL COMMENT '여분필드',
  `b_data2` text DEFAULT NULL COMMENT '여분필드',
  `b_data3` text DEFAULT NULL COMMENT '여분필드',
  `b_data4` text DEFAULT NULL COMMENT '여분필드',
  `b_data5` text DEFAULT NULL COMMENT '여분필드',
  `b_data6` varchar(300) DEFAULT NULL COMMENT '여분필드',
  `b_data7` varchar(300) DEFAULT NULL COMMENT '여분필드',
  `b_data8` varchar(300) DEFAULT NULL COMMENT '여분필드',
  `b_data9` varchar(300) DEFAULT NULL COMMENT '여분필드',
  `b_data10` text DEFAULT NULL COMMENT '여분필드'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Notice' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_category_table`
--

CREATE TABLE `gh_category_table` (
  `idx` int(20) NOT NULL,
  `num` int(11) DEFAULT NULL,
  `category` varchar(30) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `depth` varchar(10) DEFAULT NULL,
  `c_code` varchar(30) DEFAULT NULL,
  `c_name` varchar(50) DEFAULT NULL,
  `c_open` varchar(10) DEFAULT '1',
  `c_text1` varchar(300) DEFAULT NULL,
  `c_text2` varchar(200) DEFAULT NULL,
  `c_text3` varchar(200) DEFAULT NULL,
  `startNumber` int(11) DEFAULT NULL,
  `endNumber` int(11) DEFAULT NULL,
  `file1` varchar(100) DEFAULT NULL,
  `file2` varchar(100) DEFAULT NULL,
  `regdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_inquiry_table`
--

CREATE TABLE `gh_inquiry_table` (
  `idx` int(20) NOT NULL,
  `page_type` varchar(30) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `r_content` text DEFAULT NULL,
  `r_part` varchar(50) DEFAULT NULL,
  `r_company` varchar(50) DEFAULT NULL,
  `r_name` varchar(30) DEFAULT NULL,
  `r_tel` varchar(30) DEFAULT NULL,
  `r_position` varchar(50) DEFAULT NULL,
  `r_mobile` varchar(30) DEFAULT NULL,
  `r_email` varchar(100) DEFAULT NULL,
  `r_type` varchar(10) DEFAULT NULL,
  `r_product` text DEFAULT NULL,
  `r_etc` text DEFAULT NULL,
  `r_referer` text DEFAULT NULL,
  `r_etc_text` varchar(50) DEFAULT NULL,
  `form_value` text DEFAULT NULL,
  `r_agree` varchar(1) DEFAULT NULL,
  `file1` varchar(300) DEFAULT NULL,
  `file1_name` varchar(100) DEFAULT NULL,
  `attach_files` text DEFAULT NULL COMMENT '다중첨부파일',
  `attach_files_name` text DEFAULT NULL COMMENT '다중첨부파일명',
  `status` varchar(10) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='폼DB';

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_invoice_table`
--

CREATE TABLE `gh_invoice_table` (
  `idx` int(20) NOT NULL COMMENT '고유값',
  `num` varchar(10) DEFAULT NULL COMMENT '순서',
  `category` varchar(1) DEFAULT NULL COMMENT '분류(1:매출 2:매입)',
  `title` varchar(200) DEFAULT NULL COMMENT '타이틀',
  `content` text DEFAULT NULL COMMENT '비고',
  `i_date` varchar(10) DEFAULT NULL COMMENT '발행일',
  `i_company` varchar(50) DEFAULT NULL COMMENT '회사명',
  `i_price` int(11) DEFAULT NULL COMMENT '금액',
  `i_price_vat` varchar(30) DEFAULT NULL COMMENT 'vat',
  `file1` varchar(100) DEFAULT NULL COMMENT '세금계산서파일명',
  `file1_name` varchar(100) DEFAULT NULL COMMENT '세금계산서원본파일명',
  `regdate` datetime NOT NULL COMMENT '등록일'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='메인관리' ROW_FORMAT=DYNAMIC;

--
-- 테이블의 덤프 데이터 `gh_invoice_table`
--

INSERT INTO `gh_invoice_table` (`idx`, `num`, `category`, `title`, `content`, `i_date`, `i_company`, `i_price`, `i_price_vat`, `file1`, `file1_name`, `regdate`) VALUES
(2, NULL, '1', '매입 테스트', '비고', '2026-03-25', '와이시큐어', 500000, NULL, 'file1_1774166965i74v278erh.jpg', '800x600_blue.jpg', '2026-03-22 17:09:25'),
(3, NULL, '2', 'test', '', '', '', 0, NULL, NULL, NULL, '2026-04-13 11:52:43'),
(4, NULL, '1', 'test', '', '', '', 20000, NULL, NULL, NULL, '2026-04-13 13:13:03');

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_main_table`
--

CREATE TABLE `gh_main_table` (
  `idx` int(20) NOT NULL COMMENT '고유값',
  `num` varchar(10) DEFAULT NULL COMMENT '순서',
  `page_type` varchar(30) DEFAULT NULL COMMENT '메뉴명',
  `c_code` varchar(10) DEFAULT NULL COMMENT '분류코드',
  `title` varchar(200) DEFAULT NULL COMMENT '타이틀',
  `content` text DEFAULT NULL,
  `title_jp` varchar(100) DEFAULT NULL,
  `title_en` varchar(100) DEFAULT NULL,
  `content_jp` text DEFAULT NULL,
  `content_en` text DEFAULT NULL,
  `text1` varchar(300) DEFAULT NULL,
  `text2` varchar(300) DEFAULT NULL COMMENT '입력텍스트',
  `bgColor` varchar(10) DEFAULT NULL,
  `link_url` varchar(200) DEFAULT NULL COMMENT '링크',
  `link_target` varchar(10) DEFAULT NULL COMMENT '타겟(_blank:새창,_self:현재창)',
  `file1` varchar(100) DEFAULT NULL,
  `file2` varchar(100) DEFAULT NULL,
  `file3` varchar(100) DEFAULT NULL,
  `regdate` datetime NOT NULL COMMENT '등록일'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='메인관리' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_popup_table`
--

CREATE TABLE `gh_popup_table` (
  `idx` int(11) NOT NULL COMMENT '고유값',
  `num` int(11) DEFAULT NULL COMMENT '순서',
  `category` varchar(10) DEFAULT NULL COMMENT '분류',
  `pop_subject` varchar(250) DEFAULT NULL COMMENT '제목',
  `pop_size_d` varchar(1) DEFAULT NULL COMMENT '기본사이즈 1:가로 : 300 Ⅹ 세로 : 350 2:가로 : 350 Ⅹ  세로 : 300',
  `pop_size_w` varchar(5) DEFAULT NULL COMMENT '가로사이즈',
  `pop_size_h` varchar(5) DEFAULT NULL COMMENT '세로사이즈',
  `pop_location_left` varchar(5) DEFAULT NULL COMMENT '좌측위치',
  `pop_location_top` varchar(5) DEFAULT NULL COMMENT '상단위치',
  `pop_link_url` varchar(250) DEFAULT NULL COMMENT '팝업링크',
  `pop_target` varchar(10) DEFAULT NULL COMMENT '링크타입',
  `file1` varchar(100) DEFAULT NULL COMMENT '첨부이미지',
  `pop_content` text DEFAULT NULL COMMENT '팝업내용',
  `pop_view` varchar(1) DEFAULT '0' COMMENT '사용여부 1:사용 0:사용안함',
  `start_date` varchar(10) DEFAULT NULL COMMENT '시작일',
  `end_date` varchar(10) DEFAULT NULL COMMENT '종료일',
  `always` varchar(10) DEFAULT NULL COMMENT 'Y:항상노출',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='팝업관리' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_product_table`
--

CREATE TABLE `gh_product_table` (
  `idx` int(20) NOT NULL COMMENT '고유값',
  `num` int(11) DEFAULT NULL COMMENT '순서',
  `c_code` varchar(10) DEFAULT NULL COMMENT '분류코드',
  `p_open` varchar(1) DEFAULT NULL COMMENT '공개여부(1:공개)',
  `title` varchar(200) DEFAULT NULL COMMENT '타이틀',
  `title2` varchar(200) DEFAULT NULL COMMENT '내용',
  `content` text DEFAULT NULL,
  `content2` text DEFAULT NULL COMMENT '추가텍스트',
  `p_spec` text DEFAULT NULL COMMENT '제품사양(JSON)',
  `thumb_file` varchar(100) DEFAULT NULL,
  `attach_files` text DEFAULT NULL,
  `regdate` datetime NOT NULL COMMENT '등록일'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='제품관리';

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_seo_table`
--

CREATE TABLE `gh_seo_table` (
  `idx` int(20) NOT NULL COMMENT '고유값',
  `category` varchar(30) DEFAULT NULL COMMENT '분류',
  `title` varchar(100) DEFAULT NULL COMMENT '사이트 타이틀',
  `title_en` varchar(100) DEFAULT NULL COMMENT '사이트 타이틀(영문)',
  `title_jp` varchar(100) DEFAULT NULL COMMENT '사이트 타이틀(일문)',
  `meta_keywords` text DEFAULT NULL COMMENT '메타태그 키워드',
  `meta_description` text DEFAULT NULL COMMENT '메타태그 디스크립션',
  `meta_keywords_jp` text DEFAULT NULL COMMENT '메타태그 키워드(일문)',
  `meta_description_jp` text DEFAULT NULL COMMENT '메타태그 디스크립션(일문)',
  `meta_keywords_en` text DEFAULT NULL COMMENT '메타태그 키워드(영문)',
  `meta_description_en` text DEFAULT NULL COMMENT '메타태그 디스크립션(영문)',
  `og_use` varchar(1) DEFAULT NULL COMMENT 'og태그사용(1:사용)',
  `og_title` varchar(100) DEFAULT NULL COMMENT '페이지 타이틀',
  `og_description` text DEFAULT NULL COMMENT '페이지에 대한 간략한 설명',
  `og_url` varchar(200) DEFAULT NULL COMMENT '페이지의 정식 URL',
  `og_type` varchar(50) DEFAULT NULL COMMENT '페이지의 콘텐츠 유형',
  `og_locale` varchar(50) DEFAULT NULL COMMENT '콘텐츠의 지역 및 언어',
  `og_sitename` varchar(100) DEFAULT NULL COMMENT '웹사이트의 이름',
  `og_image_width` varchar(10) DEFAULT NULL COMMENT '이미지 가로',
  `og_image_height` varchar(10) DEFAULT NULL COMMENT '이미지 세로',
  `og_title_en` varchar(100) DEFAULT NULL COMMENT '페이지 타이틀(영문)',
  `og_description_en` text DEFAULT NULL COMMENT '페이지에 대한 간략한 설명(영문)',
  `og_url_en` varchar(200) DEFAULT NULL COMMENT '페이지의 정식 URL(영문)',
  `og_locale_en` varchar(50) DEFAULT NULL COMMENT '콘텐츠의 지역 및 언어(영문)',
  `og_sitename_en` varchar(100) DEFAULT NULL COMMENT '웹사이트의 이름(영문)',
  `og_title_jp` varchar(100) DEFAULT NULL COMMENT '페이지 타이틀(일문)',
  `og_description_jp` text DEFAULT NULL COMMENT '페이지에 대한 간략한 설명(일문)',
  `og_url_jp` varchar(200) DEFAULT NULL COMMENT '페이지의 정식 URL(일문)',
  `og_locale_jp` varchar(50) DEFAULT NULL COMMENT '콘텐츠의 지역 및 언어(일문)',
  `og_sitename_jp` varchar(100) DEFAULT NULL COMMENT '웹사이트의 이름(일문)',
  `file1` varchar(100) DEFAULT NULL COMMENT '이미지'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='SEO 관리';

--
-- 테이블의 덤프 데이터 `gh_seo_table`
--

INSERT INTO `gh_seo_table` (`idx`, `category`, `title`, `title_en`, `title_jp`, `meta_keywords`, `meta_description`, `meta_keywords_jp`, `meta_description_jp`, `meta_keywords_en`, `meta_description_en`, `og_use`, `og_title`, `og_description`, `og_url`, `og_type`, `og_locale`, `og_sitename`, `og_image_width`, `og_image_height`, `og_title_en`, `og_description_en`, `og_url_en`, `og_locale_en`, `og_sitename_en`, `og_title_jp`, `og_description_jp`, `og_url_jp`, `og_locale_jp`, `og_sitename_jp`, `file1`) VALUES
(1, 'meta', '그레이트휴먼', '', NULL, '', '', NULL, NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(2, 'og', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', '그레이트휴먼', '', NULL, '', '', '', '', '', '', '', NULL, '', '', NULL, NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- 테이블 구조 `gh_worker_table`
--

CREATE TABLE `gh_worker_table` (
  `idx` int(20) NOT NULL COMMENT '고유값',
  `w_type` varchar(1) DEFAULT NULL COMMENT '근무형태(1:정직원 2:계약직 3:프리랜서)',
  `w_name` varchar(200) DEFAULT NULL COMMENT '이름',
  `content` text DEFAULT NULL COMMENT '비고',
  `w_enterdate` varchar(10) DEFAULT NULL COMMENT '입사일',
  `w_leavedate` varchar(10) DEFAULT NULL COMMENT '퇴사일',
  `w_bankname` varchar(50) DEFAULT NULL COMMENT '은행명',
  `w_banknumber` varchar(30) DEFAULT NULL COMMENT '계좌번호',
  `attach_files` varchar(100) DEFAULT NULL COMMENT '첨부파일시스템파일명',
  `attach_files_name` varchar(100) DEFAULT NULL COMMENT '첨부파일원본파일명',
  `regdate` datetime NOT NULL COMMENT '등록일'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='근로자 관리' ROW_FORMAT=DYNAMIC;

--
-- 테이블의 덤프 데이터 `gh_worker_table`
--

INSERT INTO `gh_worker_table` (`idx`, `w_type`, `w_name`, `content`, `w_enterdate`, `w_leavedate`, `w_bankname`, `w_banknumber`, `attach_files`, `attach_files_name`, `regdate`) VALUES
(2, '1', '테스터', '비고', '2026-03-04', '', '은행', '01212121212', '', '', '2026-03-22 17:50:33');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `gh_admin`
--
ALTER TABLE `gh_admin`
  ADD PRIMARY KEY (`idx`),
  ADD UNIQUE KEY `idx` (`idx`);

--
-- 테이블의 인덱스 `gh_admin_menu_table`
--
ALTER TABLE `gh_admin_menu_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `idx` (`idx`,`num`,`regdate`,`m_name`) USING BTREE;

--
-- 테이블의 인덱스 `gh_board`
--
ALTER TABLE `gh_board`
  ADD PRIMARY KEY (`idx`) USING BTREE,
  ADD KEY `idx_2` (`idx`);

--
-- 테이블의 인덱스 `gh_board_notice`
--
ALTER TABLE `gh_board_notice`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `idx` (`idx`,`regdate`,`b_subject`) USING BTREE;

--
-- 테이블의 인덱스 `gh_category_table`
--
ALTER TABLE `gh_category_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `idx` (`idx`,`num`,`regdate`,`c_name`) USING BTREE;

--
-- 테이블의 인덱스 `gh_inquiry_table`
--
ALTER TABLE `gh_inquiry_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `index` (`idx`,`r_name`,`regdate`) USING BTREE;

--
-- 테이블의 인덱스 `gh_invoice_table`
--
ALTER TABLE `gh_invoice_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `index` (`idx`,`regdate`);

--
-- 테이블의 인덱스 `gh_main_table`
--
ALTER TABLE `gh_main_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `index` (`idx`,`regdate`);

--
-- 테이블의 인덱스 `gh_popup_table`
--
ALTER TABLE `gh_popup_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `idx` (`idx`) USING BTREE;

--
-- 테이블의 인덱스 `gh_product_table`
--
ALTER TABLE `gh_product_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `index` (`idx`,`regdate`) USING BTREE;

--
-- 테이블의 인덱스 `gh_seo_table`
--
ALTER TABLE `gh_seo_table`
  ADD PRIMARY KEY (`idx`);

--
-- 테이블의 인덱스 `gh_worker_table`
--
ALTER TABLE `gh_worker_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `index` (`idx`,`regdate`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `gh_admin`
--
ALTER TABLE `gh_admin`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호', AUTO_INCREMENT=60;

--
-- 테이블의 AUTO_INCREMENT `gh_admin_menu_table`
--
ALTER TABLE `gh_admin_menu_table`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- 테이블의 AUTO_INCREMENT `gh_board`
--
ALTER TABLE `gh_board`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT COMMENT '고유번호', AUTO_INCREMENT=7;

--
-- 테이블의 AUTO_INCREMENT `gh_board_notice`
--
ALTER TABLE `gh_board_notice`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT COMMENT '고유번호', AUTO_INCREMENT=2;

--
-- 테이블의 AUTO_INCREMENT `gh_category_table`
--
ALTER TABLE `gh_category_table`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 테이블의 AUTO_INCREMENT `gh_inquiry_table`
--
ALTER TABLE `gh_inquiry_table`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `gh_invoice_table`
--
ALTER TABLE `gh_invoice_table`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT COMMENT '고유값', AUTO_INCREMENT=5;

--
-- 테이블의 AUTO_INCREMENT `gh_main_table`
--
ALTER TABLE `gh_main_table`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT COMMENT '고유값', AUTO_INCREMENT=233;

--
-- 테이블의 AUTO_INCREMENT `gh_popup_table`
--
ALTER TABLE `gh_popup_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유값', AUTO_INCREMENT=8;

--
-- 테이블의 AUTO_INCREMENT `gh_product_table`
--
ALTER TABLE `gh_product_table`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT COMMENT '고유값';

--
-- 테이블의 AUTO_INCREMENT `gh_seo_table`
--
ALTER TABLE `gh_seo_table`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT COMMENT '고유값', AUTO_INCREMENT=3;

--
-- 테이블의 AUTO_INCREMENT `gh_worker_table`
--
ALTER TABLE `gh_worker_table`
  MODIFY `idx` int(20) NOT NULL AUTO_INCREMENT COMMENT '고유값', AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
