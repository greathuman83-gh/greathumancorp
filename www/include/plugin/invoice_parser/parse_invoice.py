#!/usr/bin/env python3
"""
한국 전자세금계산서(홈택스) PDF 파서
PDF를 이미지로 렌더링 후 OCR로 각 항목을 추출하여 JSON으로 출력한다.
"""
import sys
import json
import re
import pypdfium2 as pdfium
import pytesseract
from PIL import Image


def clean_text(text: str) -> str:
    text = text.strip()
    text = re.sub(r'^[\|｜\s]+', '', text)
    text = re.sub(r'[\|｜\s]+$', '', text)
    text = text.strip()
    return text


def clean_number(text: str) -> int:
    text = re.sub(r'[^0-9]', '', text)
    return int(text) if text else 0


def ocr_region(img: Image.Image, x1: int, y1: int, x2: int, y2: int,
               psm: int = 7, whitelist: str = '') -> str:
    x1 = max(0, x1)
    y1 = max(0, y1)
    x2 = min(img.width, x2)
    y2 = min(img.height, y2)
    cropped = img.crop((x1, y1, x2, y2))
    config = f'--psm {psm} --oem 3'
    if whitelist:
        config += f' -c tessedit_char_whitelist={whitelist}'
    text = pytesseract.image_to_string(cropped, lang='kor+eng', config=config).strip()
    return clean_text(text)


def parse_date(text: str) -> str:
    text = re.sub(r'[^\d/\-]', '', text)
    match = re.search(r'(\d{4})[/\-](\d{2})[/\-](\d{2})', text)
    if match:
        return f"{match.group(1)}-{match.group(2)}-{match.group(3)}"
    return text


def parse_biz_no(text: str) -> str:
    digits = re.sub(r'[^0-9\-]', '', text)
    digits_only = re.sub(r'[^0-9]', '', digits)
    if len(digits_only) == 10:
        return f"{digits_only[:3]}-{digits_only[3:5]}-{digits_only[5:]}"
    return digits


def extract_name_field(text: str) -> str:
    text = re.sub(r'성명\s*[\|｜]?\s*', '', text)
    return clean_text(text)


def parse_invoice(pdf_path: str) -> dict:
    pdf = pdfium.PdfDocument(pdf_path)
    page = pdf[0]
    bitmap = page.render(scale=4)
    img = bitmap.to_pil()

    result = {
        'approval_no': '',
        'supplier': {
            'biz_no': '',
            'corp_no': '',
            'company': '',
            'name': '',
            'address': '',
            'biz_type': '',
            'biz_item': '',
            'email': '',
        },
        'receiver': {
            'biz_no': '',
            'corp_no': '',
            'company': '',
            'name': '',
            'address': '',
            'biz_type': '',
            'biz_item': '',
            'email': '',
            'email2': '',
        },
        'issue_date': '',
        'supply_amount': 0,
        'tax_amount': 0,
        'total_amount': 0,
        'modify_reason': '',
        'remark': '',
        'cash_amount': 0,
        'check_amount': 0,
        'note_amount': 0,
        'credit_amount': 0,
        'claim_type': '',
        'items': [],
    }

    # 승인번호
    raw = ocr_region(img, 1452, 184, 2160, 276)
    raw = re.sub(r'[^0-9\-]', '', raw)
    result['approval_no'] = raw

    # 공급자 정보
    result['supplier']['biz_no'] = parse_biz_no(ocr_region(img, 456, 276, 848, 380))
    result['supplier']['corp_no'] = clean_text(ocr_region(img, 848, 276, 1200, 380))
    if result['supplier']['corp_no'] and not re.search(r'\d', result['supplier']['corp_no']):
        result['supplier']['corp_no'] = ''
    raw_company = ocr_region(img, 456, 380, 848, 490)
    raw_company = re.sub(r'^(상호|법인명|\(법인명\))\s*', '', raw_company)
    raw_company = re.sub(r'[\n\r].*', '', raw_company)
    result['supplier']['company'] = clean_text(raw_company)
    raw_name = extract_name_field(ocr_region(img, 848, 380, 1200, 490))
    raw_name = re.sub(r'^[」\|｜\s]+', '', raw_name)
    result['supplier']['name'] = clean_text(raw_name)
    result['supplier']['address'] = ocr_region(img, 456, 490, 1200, 592, psm=6)
    sup_biz_type = ocr_region(img, 456, 600, 690, 680)
    sup_biz_item_raw = ocr_region(img, 740, 600, 1200, 680)
    sup_biz_type = re.sub(r'(업태|종목|ㅣ|\|)\s*', '', sup_biz_type).strip()
    sup_biz_item_raw = re.sub(r'(업태|종목|ㅣ|\|)\s*', '', sup_biz_item_raw).strip()
    if sup_biz_type and re.search(r'[가-힣]', sup_biz_type):
        result['supplier']['biz_type'] = sup_biz_type
    if sup_biz_item_raw and re.search(r'[가-힣]', sup_biz_item_raw):
        result['supplier']['biz_item'] = re.sub(r'\n.*', '', sup_biz_item_raw)
    result['supplier']['email'] = ocr_region(img, 456, 692, 1200, 780)

    # 공급받는자 정보
    result['receiver']['biz_no'] = parse_biz_no(ocr_region(img, 1416, 276, 1808, 380))
    result['receiver']['corp_no'] = clean_text(ocr_region(img, 1808, 276, 2160, 380))
    if result['receiver']['corp_no'] and not re.search(r'\d', result['receiver']['corp_no']):
        result['receiver']['corp_no'] = ''
    result['receiver']['company'] = clean_text(
        re.sub(r'^(상호|법인명|\(법인명\))\s*', '', ocr_region(img, 1416, 380, 1808, 490))
    )
    result['receiver']['name'] = extract_name_field(ocr_region(img, 1808, 380, 2160, 490))
    result['receiver']['address'] = ocr_region(img, 1416, 490, 2160, 592, psm=6)
    recv_biz_type = ocr_region(img, 1416, 600, 1700, 680)
    recv_biz_item = ocr_region(img, 1740, 600, 2120, 680)
    recv_biz_type = re.sub(r'(업태|종목|ㅣ|\|)\s*', '', recv_biz_type).strip()
    recv_biz_item = re.sub(r'(업태|종목|ㅣ|\|)\s*', '', recv_biz_item).strip()
    if recv_biz_type and re.search(r'[가-힣]', recv_biz_type):
        result['receiver']['biz_type'] = recv_biz_type
    if recv_biz_item and re.search(r'[가-힣]', recv_biz_item):
        result['receiver']['biz_item'] = re.sub(r'\n.*', '', recv_biz_item)
    result['receiver']['email'] = ocr_region(img, 1456, 692, 2120, 750)
    result['receiver']['email2'] = ocr_region(img, 1456, 750, 2120, 810)

    # 이메일에서 non-email 문자 정리
    for party in ['supplier', 'receiver']:
        email = result[party]['email']
        match = re.search(r'[\w.+-]+@[\w.-]+\.\w+', email)
        result[party]['email'] = match.group(0) if match else email
        if party == 'receiver':
            email2 = result[party]['email2']
            match2 = re.search(r'[\w.+-]+@[\w.-]+\.\w+', email2)
            result[party]['email2'] = match2.group(0) if match2 else ''

    # 작성일자, 공급가액, 세액
    issue_raw = ocr_region(img, 236, 892, 548, 955)
    result['issue_date'] = parse_date(issue_raw)
    result['supply_amount'] = clean_number(
        ocr_region(img, 548, 892, 1012, 955, whitelist='0123456789,.')
    )
    result['tax_amount'] = clean_number(
        ocr_region(img, 1012, 892, 1480, 955, whitelist='0123456789,.')
    )

    # 수정사유
    modify = ocr_region(img, 1480, 892, 2160, 955)
    modify = re.sub(r'(수정사유|수정 사유)\s*', '', modify).strip()
    if modify and len(modify) > 1 and re.search(r'[가-힣a-zA-Z0-9]{3,}', modify):
        result['modify_reason'] = modify
    else:
        result['modify_reason'] = ''

    # 비고
    remark_text = ocr_region(img, 236, 955, 2160, 1020)
    if remark_text and remark_text != '비고' and len(remark_text) > 1:
        result['remark'] = remark_text
    else:
        result['remark'] = ''

    # 품목 (최대 4행)
    item_y_start = 1088
    item_row_height = 68
    for row_idx in range(4):
        y_top = item_y_start + (row_idx * item_row_height)
        y_bot = y_top + item_row_height

        month = ocr_region(img, 236, y_top, 330, y_bot, whitelist='0123456789')
        day = ocr_region(img, 330, y_top, 420, y_bot, whitelist='0123456789')
        name = ocr_region(img, 420, y_top, 864, y_bot)
        spec = ocr_region(img, 864, y_top, 988, y_bot)
        qty = ocr_region(img, 988, y_top, 1116, y_bot, whitelist='0123456789,.')
        unit_price = ocr_region(img, 1116, y_top, 1412, y_bot, whitelist='0123456789,.')
        supply = ocr_region(img, 1412, y_top, 1732, y_bot, whitelist='0123456789,.')
        tax = ocr_region(img, 1732, y_top, 2028, y_bot, whitelist='0123456789,.')
        item_remark = ocr_region(img, 2028, y_top, 2160, y_bot)

        # 헤더 행 스킵 (월/일/품목 등)
        if name and re.match(r'^[월일품목규격수량단가공급가액세비고\s]+$', name):
            continue

        if name and len(name) >= 2 and re.search(r'[가-힣a-zA-Z]', name) and not re.match(r'^[\|｜\s\-po—_]+$', name):
            item = {
                'month': month if month else '',
                'day': day if day else '',
                'name': name,
                'spec': spec if spec and not re.match(r'^[\|｜\s]+$', spec) else '',
                'qty': clean_number(qty) if qty else 0,
                'unit_price': clean_number(unit_price) if unit_price else 0,
                'supply_amount': clean_number(supply) if supply else 0,
                'tax_amount': clean_number(tax) if tax else 0,
                'remark': item_remark if item_remark and len(item_remark) > 0 else '',
            }
            result['items'].append(item)

    # 합계 금액
    total_raw = ocr_region(img, 236, 1400, 504, 1490)
    result['total_amount'] = clean_number(total_raw)
    if result['total_amount'] == 0:
        result['total_amount'] = result['supply_amount'] + result['tax_amount']

    # 현금, 수표, 어음, 외상미수금
    result['cash_amount'] = clean_number(
        ocr_region(img, 504, 1400, 756, 1490, whitelist='0123456789,.')
    )
    result['check_amount'] = clean_number(
        ocr_region(img, 756, 1400, 1012, 1490, whitelist='0123456789,.')
    )
    result['note_amount'] = clean_number(
        ocr_region(img, 1012, 1400, 1268, 1490, whitelist='0123456789,.')
    )
    result['credit_amount'] = clean_number(
        ocr_region(img, 1268, 1400, 1528, 1490, whitelist='0123456789,.')
    )

    # 청구/영수
    claim_raw = ocr_region(img, 1528, 1340, 2160, 1490, psm=6)
    if '청구' in claim_raw:
        result['claim_type'] = '청구'
    elif '영수' in claim_raw:
        result['claim_type'] = '영수'
    else:
        result['claim_type'] = ''

    pdf.close()
    return result


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print(json.dumps({'error': 'PDF 파일 경로를 인자로 전달해주세요.'}), file=sys.stderr)
        sys.exit(1)

    pdf_path = sys.argv[1]
    try:
        data = parse_invoice(pdf_path)
        print(json.dumps(data, ensure_ascii=False, indent=2))
    except Exception as e:
        print(json.dumps({'error': str(e)}, ensure_ascii=False), file=sys.stderr)
        sys.exit(1)
