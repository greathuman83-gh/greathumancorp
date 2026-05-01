<?php
declare(strict_types=1);

class InvoiceParser
{
    private string $pythonScript;

    public function __construct()
    {
        $this->pythonScript = __DIR__ . '/parse_invoice.py';
    }

    public function parse(string $pdfFilePath): array
    {
        if (!file_exists($pdfFilePath)) {
            throw new \RuntimeException('PDF 파일을 찾을 수 없습니다: ' . $pdfFilePath);
        }

        if (!file_exists($this->pythonScript)) {
            throw new \RuntimeException('파서 스크립트를 찾을 수 없습니다.');
        }

        $escapedPath = escapeshellarg($pdfFilePath);
        $escapedScript = escapeshellarg($this->pythonScript);
        $command = "python3 {$escapedScript} {$escapedPath} 2>&1";

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        $jsonStr = implode("\n", $output);

        if ($returnCode !== 0) {
            throw new \RuntimeException('PDF 파싱 오류: ' . $jsonStr);
        }

        $data = json_decode($jsonStr, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('JSON 파싱 오류: ' . json_last_error_msg());
        }

        return $data;
    }

    public function saveToDb(PDO $conn, int $invoiceIdx, array $parsedData): int
    {
        $now = date('Y-m-d H:i:s');

        $sql = "INSERT INTO gh_invoice_detail_table (
            invoice_idx, approval_no,
            supplier_biz_no, supplier_corp_no, supplier_company, supplier_name,
            supplier_address, supplier_biz_type, supplier_biz_item, supplier_email,
            receiver_biz_no, receiver_corp_no, receiver_company, receiver_name,
            receiver_address, receiver_biz_type, receiver_biz_item, receiver_email, receiver_email2,
            issue_date, supply_amount, tax_amount, total_amount,
            modify_reason, cash_amount, check_amount, note_amount, credit_amount,
            claim_type, parsed_json, regdate
        ) VALUES (
            :invoice_idx, :approval_no,
            :supplier_biz_no, :supplier_corp_no, :supplier_company, :supplier_name,
            :supplier_address, :supplier_biz_type, :supplier_biz_item, :supplier_email,
            :receiver_biz_no, :receiver_corp_no, :receiver_company, :receiver_name,
            :receiver_address, :receiver_biz_type, :receiver_biz_item, :receiver_email, :receiver_email2,
            :issue_date, :supply_amount, :tax_amount, :total_amount,
            :modify_reason, :cash_amount, :check_amount, :note_amount, :credit_amount,
            :claim_type, :parsed_json, :regdate
        )";

        $stmt = $conn->prepare($sql);

        $supplier = $parsedData['supplier'] ?? [];
        $receiver = $parsedData['receiver'] ?? [];

        $stmt->execute([
            ':invoice_idx' => $invoiceIdx,
            ':approval_no' => $parsedData['approval_no'] ?? '',
            ':supplier_biz_no' => $supplier['biz_no'] ?? '',
            ':supplier_corp_no' => $supplier['corp_no'] ?? '',
            ':supplier_company' => $supplier['company'] ?? '',
            ':supplier_name' => $supplier['name'] ?? '',
            ':supplier_address' => $supplier['address'] ?? '',
            ':supplier_biz_type' => $supplier['biz_type'] ?? '',
            ':supplier_biz_item' => $supplier['biz_item'] ?? '',
            ':supplier_email' => $supplier['email'] ?? '',
            ':receiver_biz_no' => $receiver['biz_no'] ?? '',
            ':receiver_corp_no' => $receiver['corp_no'] ?? '',
            ':receiver_company' => $receiver['company'] ?? '',
            ':receiver_name' => $receiver['name'] ?? '',
            ':receiver_address' => $receiver['address'] ?? '',
            ':receiver_biz_type' => $receiver['biz_type'] ?? '',
            ':receiver_biz_item' => $receiver['biz_item'] ?? '',
            ':receiver_email' => $receiver['email'] ?? '',
            ':receiver_email2' => $receiver['email2'] ?? '',
            ':issue_date' => $parsedData['issue_date'] ?? '',
            ':supply_amount' => (int)($parsedData['supply_amount'] ?? 0),
            ':tax_amount' => (int)($parsedData['tax_amount'] ?? 0),
            ':total_amount' => (int)($parsedData['total_amount'] ?? 0),
            ':modify_reason' => $parsedData['modify_reason'] ?? '',
            ':cash_amount' => (int)($parsedData['cash_amount'] ?? 0),
            ':check_amount' => (int)($parsedData['check_amount'] ?? 0),
            ':note_amount' => (int)($parsedData['note_amount'] ?? 0),
            ':credit_amount' => (int)($parsedData['credit_amount'] ?? 0),
            ':claim_type' => $parsedData['claim_type'] ?? '',
            ':parsed_json' => json_encode($parsedData, JSON_UNESCAPED_UNICODE),
            ':regdate' => $now,
        ]);

        $detailIdx = (int)$conn->lastInsertId();

        $items = $parsedData['items'] ?? [];
        if (!empty($items)) {
            $itemSql = "INSERT INTO gh_invoice_item_table (
                detail_idx, item_month, item_day, item_name, item_spec,
                item_qty, item_unit_price, item_supply_amount, item_tax_amount,
                item_remark, regdate
            ) VALUES (
                :detail_idx, :item_month, :item_day, :item_name, :item_spec,
                :item_qty, :item_unit_price, :item_supply_amount, :item_tax_amount,
                :item_remark, :regdate
            )";
            $itemStmt = $conn->prepare($itemSql);

            foreach ($items as $item) {
                $itemStmt->execute([
                    ':detail_idx' => $detailIdx,
                    ':item_month' => $item['month'] ?? '',
                    ':item_day' => $item['day'] ?? '',
                    ':item_name' => $item['name'] ?? '',
                    ':item_spec' => $item['spec'] ?? '',
                    ':item_qty' => $item['qty'] ?? 0,
                    ':item_unit_price' => (int)($item['unit_price'] ?? 0),
                    ':item_supply_amount' => (int)($item['supply_amount'] ?? 0),
                    ':item_tax_amount' => (int)($item['tax_amount'] ?? 0),
                    ':item_remark' => $item['remark'] ?? '',
                    ':regdate' => $now,
                ]);
            }
        }

        return $detailIdx;
    }

    public function deleteByInvoiceIdx(PDO $conn, int $invoiceIdx): void
    {
        $stmt = $conn->prepare("DELETE FROM gh_invoice_detail_table WHERE invoice_idx = :idx");
        $stmt->execute([':idx' => $invoiceIdx]);
    }

    public function getDetailByInvoiceIdx(PDO $conn, int $invoiceIdx): ?array
    {
        $stmt = $conn->prepare("SELECT * FROM gh_invoice_detail_table WHERE invoice_idx = :idx ORDER BY idx DESC LIMIT 1");
        $stmt->execute([':idx' => $invoiceIdx]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getItemsByDetailIdx(PDO $conn, int $detailIdx): array
    {
        $stmt = $conn->prepare("SELECT * FROM gh_invoice_item_table WHERE detail_idx = :idx ORDER BY idx ASC");
        $stmt->execute([':idx' => $detailIdx]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
