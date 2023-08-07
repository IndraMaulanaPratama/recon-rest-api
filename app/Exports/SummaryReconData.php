<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SummaryReconData implements
    FromArray,
    WithColumnFormatting,
    WithHeadings,
    ShouldAutoSize,
    WithEvents,
    WithTitle
{
    protected $setData;
    protected $setHeader;
    protected $setTitle;
    protected $setParameter;

    public function __construct(array $data, array $header, string $title, array $parameter)
    {
        $this->setData = $data;
        $this->setHeader = $header;
        $this->setTitle = $title;
        $this->setParameter = $parameter;
    }

    public function title(): string
    {
        return $this->setTitle;
    }

    public function headings(): array
    {
        return [
            $this->setHeader,
        ];
    }

    public function array(): array
    {
        return $this->setData;
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // *** STYLE AREA *** //
                $defaultStyle = [
                    'font' => [
                        'name' => 'Courier New',
                        'size' => '10'
                    ]
                ];

                $headerColumnstyle = [
                    'font' => [
                        'name' => 'Calibri',
                        'size' => '11',
                        'bold' => 'true',
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => '7FFFD4'],
                    ],
                ];

                $valueColumnstyle = [
                    'font' => [
                        'name' => 'Calibri',
                        'size' => '11',
                    ],
                ];
                // *** END OF STYLE AREA *** //

                // Inisialisasi phpSpreadSheet Variable
                $sheet = $event->sheet->getDelegate();
                $countColumn = count($this->setData[0]);
                $countData = count($this->setData) + 4;
                $lastColumn = Coordinate::stringFromColumnIndex($countColumn);
                $rangeData = sprintf('A4:%s%d', $lastColumn, $countData);
                $footerRow = $countData + 2;

                // Set Default Style
                $sheet->getParent()->getDefaultStyle()->applyFromArray($defaultStyle);

                // *** HEADER AREA *** ///
                // Menambahkan 3 baris mulai dari baris ke 1
                $sheet->insertNewRowBefore(1, 3);

                // Menambahkan Header dan Sub Header
                $sheet->setCellValue(
                    'A1',
                    'Resume Recon Data Transaction',
                );
                $sheet->setCellValue(
                    'A2',
                    'Periode : '. $this->setParameter['date'][0]. ' s.d. ' .$this->setParameter['date'][1]
                );

                // Merge Cell Bagian Header dan Sub Header
                $sheet->mergeCells(sprintf('A1:%s1', $lastColumn));
                $sheet->mergeCells(sprintf('A2:%s2', $lastColumn));
                $sheet->getStyle('A1')->getFont()->setBold(true);
                // *** END OF HEADER AREA *** ///

                // *** DATA AREA *** ///
                // Set Bold Untuk Header Colom
                $sheet->getStyle(sprintf('A4:%s4', $lastColumn))->applyFromArray($headerColumnstyle);

                // Set Border Data
                $sheet->getStyle($rangeData)
                ->applyFromArray($valueColumnstyle)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

                // Set Separated Number
                $sheet->getStyle(sprintf('B5:H%d', $countData))->getNumberFormat()
                ->setFormatCode('#,##0');
                // *** END OF DATA AREA *** ///

                //  *** FOOTER AREA *** //
                $sheet->setCellValue(
                    sprintf('A%d', $footerRow),
                    'Disclaimer : File ini digenerate dari aplikasi Pengolahan Data di Auto Recon,'
                );
                $sheet->setCellValue(
                    sprintf('A%d', $footerRow + 1),
                    'Pastikan Setting Region diset Indonesia, dan jika ada perbedaan data antara file ' .
                    'dengan aplikasi maka data di aplikasi adalah data yang lebih valid.1'
                );
                $sheet->setCellValue(
                    sprintf('A%d', $footerRow + 2),
                    'Data pada file ini bisa saja telah diubah setelah dibuka ( bukan oleh aplikasi )'
                );

                // Merge Cell Bagian Footer
                $sheet->mergeCells(sprintf('A%d:%s%d', $footerRow, $lastColumn, $footerRow));
                $sheet->mergeCells(sprintf('A%d:O%d', $footerRow + 1, $footerRow + 1));
                $sheet->mergeCells(sprintf('A%d:%s%d', $footerRow + 2, $lastColumn, $footerRow + 2));
                $sheet->getStyle('A1')->getFont()->setBold(true);
                //  *** END OF FOOTER AREA *** //
            },
        ];
    }
}
