<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;

class ProgramReportExcelExport
{
    /** @param array<int, array{title: string, headers: array<int, string>, rows: array<int, array<int, mixed>>}> $sections */
    public function create(string $title, array $sections): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekspor Excel memerlukan ekstensi PHP ZipArchive.');
        }

        $rows = [[$title], ['Dibuat pada', now()->format('Y-m-d H:i:s')], []];
        foreach ($sections as $section) {
            $rows[] = [$section['title']];
            $rows[] = $section['headers'];
            foreach ($section['rows'] as $row) {
                $rows[] = $row;
            }
            $rows[] = [];
        }

        $sheetRows = '';
        foreach ($rows as $rowIndex => $row) {
            if ($row === []) {
                $sheetRows .= '<row r="' . ($rowIndex + 1) . '"/>';
                continue;
            }

            $cells = '';
            foreach (array_values($row) as $columnIndex => $value) {
                $cell = $this->columnName($columnIndex + 1) . ($rowIndex + 1);
                $text = htmlspecialchars((string) ($value ?? ''), ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $text) ?? $text;
                $cells .= '<c r="' . $cell . '" t="inlineStr"><is><t xml:space="preserve">' . $text . '</t></is></c>';
            }
            $sheetRows .= '<row r="' . ($rowIndex + 1) . '">' . $cells . '</row>';
        }

        $file = tempnam(sys_get_temp_dir(), 'program-report-');
        if ($file === false) {
            throw new RuntimeException('File sementara untuk ekspor laporan tidak dapat dibuat.');
        }

        $zip = new ZipArchive;
        if ($zip->open($file, ZipArchive::OVERWRITE) !== true) {
            @unlink($file);
            throw new RuntimeException('Workbook Excel laporan tidak dapat dibuat.');
        }

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Laporan Program" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>');
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . $sheetRows . '</sheetData></worksheet>');

        if (! $zip->close()) {
            @unlink($file);
            throw new RuntimeException('Workbook Excel laporan tidak dapat disimpan.');
        }

        return $file;
    }

    private function columnName(int $column): string
    {
        $name = '';
        while ($column > 0) {
            $name = chr(65 + (($column - 1) % 26)) . $name;
            $column = intdiv($column - 1, 26);
        }

        return $name;
    }
}
