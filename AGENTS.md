# AGENTS.md

## Cursor Cloud specific instructions

### 서비스 개요
PHP 8.x 기반 커스텀 CMS (기업 웹사이트 관리 시스템). LAMP 스택(MariaDB + PHP 내장 서버 또는 Apache).

### 개발 환경 실행
```bash
# MariaDB 시작
service mariadb start

# PHP 내장 개발 서버 (Document Root: /workspace/www)
cd /workspace/www && php -S 0.0.0.0:8080
```

- 관리자 로그인: http://localhost:8080/admode/login/login.php (admin / 1234)
- DB 접속: `mariadb -u root -p'ghlocalpw26!@' greathumancorp`

### 주요 주의사항
- DB 비밀번호는 `www/include/common/dbopen.class.php`에 하드코딩됨 (`ghlocalpw26!@`)
- Composer vendor는 `/workspace/www/include/plugin/vendor/`에 이미 커밋되어 있음
- PDF 파싱 기능은 Python 3 + `pypdfium2`, `pytesseract`, `pdfplumber` 의존성 필요
- Tesseract OCR + 한국어 데이터(`tesseract-ocr-kor`)가 시스템에 설치되어 있어야 함
- 파일 업로드 디렉토리: `/workspace/www/data/` (하위 디렉토리별 구분)
- `.cursorrules`에 코딩 규칙 명시됨 (PSR-12, PHP 8.x, 한국어 답변)

### 린트/테스트
- PHP syntax check: `php -l <file.php>`
- 이 프로젝트에는 자동화된 테스트 프레임워크가 없음. 수동 브라우저 테스트 필요.

### 계산서 PDF 파싱 기능
- 파서 스크립트: `www/include/plugin/invoice_parser/parse_invoice.py`
- PHP 래퍼: `www/include/plugin/invoice_parser/InvoiceParser.php`
- 테이블: `gh_invoice_detail_table` (계산서 상세), `gh_invoice_item_table` (품목)
- PDF 업로드 시 자동으로 OCR 파싱 수행 (처리 시간 약 6초)
