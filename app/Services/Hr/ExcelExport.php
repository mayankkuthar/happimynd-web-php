<?php

namespace App\Services\Hr;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Candidate export workbook (API_SPEC §5, POST /export).
 *
 * A port of the intern app's server/src/services/excel.ts, kept cell-for-cell:
 * sheet "Candidates" with Reviewed + Department leading, then one column per
 * extracted field in schema order; amber fill on low-confidence cells; widths
 * clamped to 12..60; frozen through column C; autofilter; and an
 * "Extraction Summary" sheet.
 *
 * Records are plain arrays: viewed (bool), department (?string), used_ocr
 * (bool), fields (array), confidence (array).
 */
class ExcelExport
{
    private const HEADER_FILL = '1E3A5F';
    private const REVIEW_FILL = 'FFF3CD';
    private const BORDER_COLOR = 'D9D9D9';
    private const MIN_WIDTH = 12;
    private const MAX_WIDTH = 60;
    private const LEADING_COLUMNS = 2;

    /** @var array<string,string> field => label, in export order */
    private $labels;

    /** @param array<string,string> $labels */
    public function __construct(array $labels)
    {
        $this->labels = $labels;
    }

    /** Labels from resources/hr/schema.json (the intern app's GET /schema output). */
    public static function fromSchemaFile(string $path): self
    {
        $schema = json_decode((string) file_get_contents($path), true);

        return new self((array) ($schema['labels'] ?? []));
    }

    /** candidates_YYYYMMDD_HHMMSS.xlsx in the app's local time. */
    public static function fileName(?\DateTimeInterface $at = null): string
    {
        $at = $at ?? new \DateTime('now');

        return 'candidates_' . $at->format('Ymd_His') . '.xlsx';
    }

    /** Same as excel.ts flatten(): lists joined with "; ", everything trimmed. */
    public static function flatten($value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_array($value)) {
            $items = array_filter(array_map(function ($item) {
                return trim(is_scalar($item) ? (string) $item : json_encode($item));
            }, $value), function ($item) {
                return $item !== '';
            });

            return implode('; ', $items);
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return trim((string) $value);
    }

    /** Build the workbook and return the .xlsx bytes. */
    public function build(array $records, bool $highlightLowConfidence = true): string
    {
        $book = new Spreadsheet();
        $book->getProperties()->setCreated(time());
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Candidates');

        $columns = array_keys($this->labels);
        $headers = array_merge(['Reviewed', 'Department'], array_values($this->labels));
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));

        $thin = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_COLOR]];
        $borders = ['borders' => ['top' => $thin, 'left' => $thin, 'bottom' => $thin, 'right' => $thin]];

        // Header row.
        $sheet->fromArray([$headers], null, 'A1', true);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($borders + [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::HEADER_FILL]],
            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true, 'size' => 11],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'wrapText' => true,
            ],
        ]);

        $widths = array_map('mb_strlen', $headers);
        $rowNumber = 1;

        foreach ($records as $record) {
            $rowNumber++;
            $fields = (array) ($record['fields'] ?? []);
            $confidence = (array) ($record['confidence'] ?? []);

            $values = [
                !empty($record['viewed']) ? 'Yes' : 'No',
                $record['department'] ?? 'Unassigned',
            ];
            foreach ($columns as $key) {
                $values[] = self::flatten($fields[$key] ?? null);
            }

            foreach ($values as $index => $text) {
                $cell = Coordinate::stringFromColumnIndex($index + 1) . $rowNumber;
                // Explicit string type: phone numbers and IDs must not turn into numbers.
                $sheet->setCellValueExplicit($cell, (string) $text, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $field = $columns[$index - self::LEADING_COLUMNS] ?? null;
                if ($highlightLowConfidence && $index >= self::LEADING_COLUMNS && $field !== null
                    && ($confidence[$field] ?? null) === 'low') {
                    $sheet->getStyle($cell)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB(self::REVIEW_FILL);
                }

                // Longest single line, so wrapped cells don't blow out the width.
                $longest = 0;
                foreach (explode("\n", (string) $text) as $line) {
                    $longest = max($longest, mb_strlen($line));
                }
                $widths[$index] = max($widths[$index] ?? 0, $longest);
            }
        }

        if ($rowNumber > 1) {
            $sheet->getStyle('A2:' . $lastColumn . $rowNumber)->applyFromArray($borders + [
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
            ]);
        }

        foreach ($widths as $index => $width) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index + 1))
                ->setWidth(min(max($width + 2, self::MIN_WIDTH), self::MAX_WIDTH));
        }

        // Freeze through the file-name column (C) and the header row.
        $sheet->freezePane('D2');
        $sheet->setAutoFilter('A1:' . $lastColumn . max($rowNumber, 1));

        $this->addSummarySheet($book, $records);
        $book->setActiveSheetIndex(0);

        $tmp = tempnam(sys_get_temp_dir(), 'hrx');
        try {
            (new Xlsx($book))->save($tmp);

            return (string) file_get_contents($tmp);
        } finally {
            @unlink($tmp);
            $book->disconnectWorksheets();
        }
    }

    /** Provenance sheet: recruiters pass these files around. */
    private function addSummarySheet(Spreadsheet $book, array $records): void
    {
        $sheet = $book->createSheet();
        $sheet->setTitle('Extraction Summary');

        $flagged = 0;
        $ocr = 0;
        foreach ($records as $record) {
            if (in_array('low', array_values((array) ($record['confidence'] ?? [])), true)) {
                $flagged++;
            }
            if (!empty($record['used_ocr'])) {
                $ocr++;
            }
        }

        $rows = [
            ['Generated', (new \DateTime('now'))->format('Y-m-d H:i:s')],
            ['Candidates exported', count($records)],
            ['Rows with low-confidence fields', $flagged],
            ['Resumes requiring OCR', $ocr],
            ['', ''],
            ['Note', 'Cells shaded amber were flagged for manual review.'],
        ];

        foreach ($rows as $i => $row) {
            $r = $i + 1;
            $sheet->setCellValue('A' . $r, $row[0]);
            $sheet->setCellValue('B' . $r, $row[1]);
            $sheet->getStyle('A' . $r)->getFont()->setBold(true);
        }

        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(52);
    }
}
