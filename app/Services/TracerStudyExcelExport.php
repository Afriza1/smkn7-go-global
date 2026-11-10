<?php

namespace App\Services;

use App\Models\TracerStudy;
use RuntimeException;
use ZipArchive;

class TracerStudyExcelExport
{
    /**
     * Create a minimal, dependency-free XLSX workbook with all values stored as text.
     * This prevents user-provided values from being interpreted as spreadsheet formulas.
     *
     * @param  iterable<TracerStudy>  $responses
     */
    public function create(iterable $responses): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekspor Excel membutuhkan ekstensi PHP ZipArchive.');
        }

        $headers = [
            'Periode', 'Nama Lengkap', 'Tahun Lulus', 'Jurusan', 'Alamat', 'Status Kegiatan',
            'Pekerjaan', 'Nama Perusahaan', 'Tahun Masuk Perusahaan', 'Alamat Tempat Kerja',
            'Status Verifikasi', 'Persetujuan Penggunaan Data', 'Waktu Pengiriman', 'Pembaruan Terakhir',
        ];

        $rows = [$headers];
        foreach ($responses as $response) {
            $rows[] = [
                $response->periode?->tahun,
                $response->nama_lengkap,
                $response->tahun_lulus,
                $response->jurusan,
                $response->alamat,
                $response->status_kegiatan === 'magang' ? 'Magang' : 'Bekerja',
                $response->pekerjaan,
                $response->nama_perusahaan,
                $response->tahun_masuk_perusahaan,
                $response->alamat_tempat_kerja,
                $response->status_label,
                $response->persetujuan_penggunaan_data ? 'Ya' : 'Tidak',
                $response->created_at?->format('Y-m-d H:i:s'),
                $response->updated_at?->format('Y-m-d H:i:s'),
            ];
        }

        $sheetRows = '';
        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $cells = '';

            foreach ($row as $columnIndex => $value) {
                $cell = $this->columnName($columnIndex + 1) . $excelRow;
                $text = $this->xmlText((string) ($value ?? ''));
                $cells .= '<c r="' . $cell . '" t="inlineStr"><is><t xml:space="preserve">' . $text . '</t></is></c>';
            }

            $sheetRows .= '<row r="' . $excelRow . '">' . $cells . '</row>';
        }

        $lastColumn = $this->columnName(count($headers));
        $lastRow = count($rows);
        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<dimension ref="A1:' . $lastColumn . $lastRow . '"/>'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="18"/><sheetData>' . $sheetRows . '</sheetData>'
            . '<autoFilter ref="A1:' . $lastColumn . $lastRow . '"/>'
            . '</worksheet>';

        $file = tempnam(sys_get_temp_dir(), 'tracer-study-');
        if ($file === false) {
            throw new RuntimeException('File sementara untuk ekspor Tracer Study tidak dapat dibuat.');
        }

        $zip = new ZipArchive();
        if ($zip->open($file, ZipArchive::OVERWRITE) !== true) {
            @unlink($file);
            throw new RuntimeException('Workbook Excel Tracer Study tidak dapat dibuat.');
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
            . '<sheets><sheet name="Tracer Study" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);

        if (! $zip->close()) {
            @unlink($file);
            throw new RuntimeException('Workbook Excel Tracer Study tidak dapat disimpan.');
        }

        return $file;
    }

    private function columnName(int $column): string
    {
        $name = '';
        while ($column > 0) {
            $remainder = ($column - 1) % 26;
            $name = chr(65 + $remainder) . $name;
            $column = intdiv($column - 1, 26);
        }

        return $name;
    }

    private function xmlText(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value) ?? $value;

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
