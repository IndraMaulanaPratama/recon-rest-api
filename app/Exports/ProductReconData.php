<?php

namespace App\Exports;

use App\Models\CoreReconData;
use App\Models\TrxCorrection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductReconData implements
    WithEvents,
    ShouldAutoSize,
    WithTitle,
    WithHeadings,
    FromArray,
    WithColumnFormatting
{
    protected $setProduct;
    protected $setDate;
    protected $setSuspect;
    protected $setCountSuspect;

    public function __construct(string $product, array $date)
    {
        $this->setProduct = $product;
        $this->setDate = $date;
    }

    public function title(): string
    {
        return $this->setProduct;
    }

    public function headings(): array
    {
        return [
            // Transaction
            "PRODUCT",
            "CID",
            "CID_NAME",
            "TRX_DT",
            'NBILL',
            'NMONTH',
            'RpTag',
            'FEE_ADMIN',
            'FEE_ADMIN_AMOUNT',
            'FEE_VSI',
            'FEE_VSI_AMOUNT',
            'TOTAL_FEE',
            'FEE_BILLER',
            'FEE_BILLER_AMOUNT',
            'CLAIM_VSI',
            'CLAIM_VSI_AMOUNT',
            'BILLER',
            'CLAIM_PARTNER',
            'CLAIM_PARTNER_AMOUNT',
            // Paid
            'NBILL',
            'NMONTH',
            'RpTag',
            'FEE_ADMIN',
            'FEE_ADMIN_AMOUNT',
            'FEE_VSI',
            'FEE_VSI_AMOUNT',
            'TOTAL_FEE',
            'FEE_BILLER',
            'FEE_BILLER_AMOUNT',
            'CLAIM_VSI',
            'CLAIM_VSI_AMOUNT',
            'BILLER',
            'CLAIM_PARTNER',
            'CLAIM_PARTNER_AMOUNT',
            // Cancel
            'NBILL',
            'NMONTH',
            'RpTag',
            'FEE_ADMIN',
            'FEE_ADMIN_AMOUNT',
            'FEE_VSI',
            'FEE_VSI_AMOUNT',
            'TOTAL_FEE',
            'FEE_BILLER',
            'FEE_BILLER_AMOUNT',
            'CLAIM_VSI',
            'CLAIM_VSI_AMOUNT',
            'BILLER',
            'CLAIM_PARTNER',
            'CLAIM_PARTNER_AMOUNT',
            // Total
            'NBILL',
            'NMONTH',
            'FEE',
            'FEE_ADMIN_AMOUNT',
            'FEE_VSI_AMOUNT',
            'TOTAL_FEE',
            'FEE_BILLER_AMOUNT',
            'CLAIM_VSI_AMOUNT',
            'BILLER_AMOUNT',
            'CLAIM_PARTNER_AMOUNT',
        ];
    }

    public function array(): array
    {
        // *** Logic Get Data Transaction *** //
        $transaction = CoreReconData::select(
            'CSC_RDT_PRODUCT AS PRODUCT',
            'CSC_RDT_CID AS CID',
            'CID.CSC_DC_NAME AS CID_NAME',
            'CSC_RDT_TRX_DT AS TRX_DT',
            DB::raw('SUM(CSC_RDT_NBILL) AS NBILL'),
            DB::raw('SUM(CSC_RDT_NMONTH) AS NMONTH'),
            DB::raw('SUM(CSC_RDT_FEE) AS FEE'),
            DB::raw('SUM(CSC_RDT_FEE_ADMIN) AS FEE_ADMIN'),
            DB::raw('SUM(CSC_RDT_FEE_ADMIN_AMOUNT) AS FEE_ADMIN_AMOUNT'),
            DB::raw('SUM(CSC_RDT_FEE_VSI) AS FEE_VSI'),
            DB::raw('SUM(CSC_RDT_FEE_VSI_AMOUNT) AS FEE_VSI_AMOUNT'),
            DB::raw('SUM(CSC_RDT_FEE+CSC_RDT_FEE_ADMIN_AMOUNT) AS TOTAL_FEE'),
            DB::raw('SUM(CSC_RDT_FEE_BILLER) AS FEE_BILLER'),
            DB::raw('SUM(CSC_RDT_FEE_BILLER_AMOUNT) AS FEE_BILLER_AMOUNT'),
            DB::raw('SUM(CSC_RDT_CLAIM_VSI) AS CLAIM_VSI'),
            DB::raw('SUM(CSC_RDT_CLAIM_VSI_AMOUNT) AS CLAIM_VSI_AMOUNT'),
            DB::raw('SUM(CSC_RDT_BILLER_AMOUNT) AS BILLER_AMOUNT'),
            DB::raw('SUM(CSC_RDT_CLAIM_PARTNER) AS CLAIM_PARTNER'),
            DB::raw('SUM(CSC_RDT_CLAIM_PARTNER_AMOUNT) AS CLAIM_PARTNER_AMOUNT'),
        )
        ->join(
            'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS CID',
            'CSC_RDT_CID',
            '=',
            'CID.CSC_DC_ID'
        )
        ->product($this->setProduct)
        ->dateRange($this->setDate)
        ->groupBy('CSC_RDT_PRODUCT')
        ->groupBy('CSC_RDT_CID')
        ->groupBy('CSC_RDT_TRX_DT')
        ->orderBy('CSC_RDT_TRX_DT', 'ASC')
        ->get();

        // Hitung Data Product
        $countTransaction = count($transaction);

        // Logic Get Data Paid //
        for ($i=0; $i < $countTransaction; $i++) {
            $paid[] = TrxCorrection::select(
                'CSM_TC_PRODUCT AS PRODUCT',
                'CSM_TC_CID AS CID',
                'CID.CSC_DC_NAME AS CID_NAME',
                'CSM_TC_TRX_DT AS TRX_DT',
                DB::raw('SUM(CSM_TC_NBILL) AS NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS RpTag'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS TOTAL_FEE'),
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CLAIM_PARTNER_AMOUNT'),
                'CSM_TC_STATUS_TRX AS STATUS_TRX',
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS CID',
                'CSM_TC_CID',
                '=',
                'CID.CSC_DC_ID'
            )
            ->product($transaction[$i]['PRODUCT'])
            ->cid($transaction[$i]['CID'])
            ->status(0)
            ->date($transaction[$i]['TRX_DT'])
            ->groupBy('CSM_TC_TRX_DT')
            ->first();

            // Handle Null Paid
            if (null == $paid[$i]) :
                $paid[$i] = [
                    "TRX_DT" => null,
                    'NBILL' => null,
                    'NMONTH' => null,
                    'RpTag' => null,
                    'FEE_ADMIN' => null,
                    'FEE_ADMIN_AMOUNT' => null,
                    'FEE_VSI' => null,
                    'FEE_VSI_AMOUNT' => null,
                    'TOTAL_FEE' => null,
                    'FEE_BILLER' => null,
                    'FEE_BILLER_AMOUNT' => null,
                    'CLAIM_VSI' => null,
                    'CLAIM_VSI_AMOUNT' => null,
                    'BILLER' => null,
                    'CLAIM_PARTNER' => null,
                    'CLAIM_PARTNER_AMOUNT' => null,
                    "STATUS_TRX" => null,
                ];
            endif;
        }

        // Logic Get Data Canceled
        for ($i=0; $i < $countTransaction; $i++) {
            $canceled[] = TrxCorrection::select(
                'CSM_TC_PRODUCT AS PRODUCT',
                'CSM_TC_CID AS CID',
                'CID.CSC_DC_NAME AS CID_NAME',
                'CSM_TC_TRX_DT AS TRX_DT',
                DB::raw('SUM(CSM_TC_NBILL) AS NBILL'),
                DB::raw('SUM(CSM_TC_NMONTH) AS NMONTH'),
                DB::raw('SUM(CSM_TC_FEE) AS RpTag'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN) AS FEE_ADMIN'),
                DB::raw('SUM(CSM_TC_FEE_ADMIN_AMOUNT) AS FEE_ADMIN_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE_VSI) AS FEE_VSI'),
                DB::raw('SUM(CSM_TC_FEE_VSI_AMOUNT) AS FEE_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_FEE+CSM_TC_FEE_ADMIN_AMOUNT) AS TOTAL_FEE'),
                DB::raw('SUM(CSM_TC_FEE_BILLER) AS FEE_BILLER'),
                DB::raw('SUM(CSM_TC_FEE_BILLER_AMOUNT) AS FEE_BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI) AS CLAIM_VSI'),
                DB::raw('SUM(CSM_TC_CLAIM_VSI_AMOUNT) AS CLAIM_VSI_AMOUNT'),
                DB::raw('SUM(CSM_TC_BILLER_AMOUNT) AS BILLER_AMOUNT'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER) AS CLAIM_PARTNER'),
                DB::raw('SUM(CSM_TC_CLAIM_PARTNER_AMOUNT) AS CLAIM_PARTNER_AMOUNT'),
                'CSM_TC_STATUS_TRX AS STATUS_TRX',
            )
            ->join(
                'VSI_DEVEL_RECON.CSCCORE_DOWN_CENTRAL AS CID',
                'CSM_TC_CID',
                '=',
                'CID.CSC_DC_ID'
            )
            ->product($transaction[$i]['PRODUCT'])
            ->cid($transaction[$i]['CID'])
            ->status(1)
            ->date($transaction[$i]['TRX_DT'])
            ->groupBy('CSM_TC_TRX_DT')
            ->first();

            // Handle Null Canceled
            if (null == $canceled[$i]) :
                $canceled[$i] = [
                    "TRX_DT" => '0',
                    'NBILL' => '0',
                    'NMONTH' => '0',
                    'RpTag' => '0',
                    'FEE_ADMIN' => '0',
                    'FEE_ADMIN_AMOUNT' => '0',
                    'FEE_VSI' => '0',
                    'FEE_VSI_AMOUNT' => '0',
                    'TOTAL_FEE' => '0',
                    'FEE_BILLER' => '0',
                    'FEE_BILLER_AMOUNT' => '0',
                    'CLAIM_VSI' => '0',
                    'CLAIM_VSI_AMOUNT' => '0',
                    'BILLER' => '0',
                    'CLAIM_PARTNER' => '0',
                    'CLAIM_PARTNER_AMOUNT' => '0',
                    "STATUS_TRX" => '0',
                ];
            endif;
        }

        // Merging data product
        if (null == $countTransaction) :
            $transaction = null;
            $paid = null;
            $canceled = null;
            $dataProduct[] = [
            "PRODUCT" => $this->setProduct,
            "CID" => null,
            "CID_NAME" => null,
            "TRX_DT" => null,
            'NBILL' => '0',
            'NMONTH' => '0',
            'RpTag' => '0',
            'FEE_ADMIN' => '0',
            'FEE_ADMIN_AMOUNT' => '0',
            'FEE_VSI' => '0',
            'FEE_VSI_AMOUNT' => '0',
            'TOTAL_FEE' => '0',
            'FEE_BILLER' => '0',
            'FEE_BILLER_AMOUNT' => '0',
            'CLAIM_VSI' => '0',
            'CLAIM_VSI_AMOUNT' => '0',
            'BILLER' => '0',
            'CLAIM_PARTNER' => '0',
            'CLAIM_PARTNER_AMOUNT' => '0',
            // Paid
            'PAID_NBILL' => '0',
            'PAID_NMONTH' => '0',
            'PAID_RpTag' => '0',
            'PAID_FEE_ADMIN' => '0',
            'PAID_FEE_ADMIN_AMOUNT' => '0',
            'PAID_FEE_VSI' => '0',
            'PAID_FEE_VSI_AMOUNT' => '0',
            'PAID_TOTAL_FEE' => '0',
            'PAID_FEE_BILLER' => '0',
            'PAID_FEE_BILLER_AMOUNT' => '0',
            'PAID_CLAIM_VSI' => '0',
            'PAID_CLAIM_VSI_AMOUNT' => '0',
            'PAID_BILLER' => '0',
            'PAID_CLAIM_PARTNER' => '0',
            'PAID_CLAIM_PARTNER_AMOUNT' => '0',
            // Cancel
            'CANCELED_NBILL' => '0',
            'CANCELED_NMONTH' => '0',
            'CANCELED_RpTag' => '0',
            'CANCELED_FEE_ADMIN' => '0',
            'CANCELED_FEE_ADMIN_AMOUNT' => '0',
            'CANCELED_FEE_VSI' => '0',
            'CANCELED_FEE_VSI_AMOUNT' => '0',
            'CANCELED_TOTAL_FEE' => '0',
            'CANCELED_FEE_BILLER' => '0',
            'CANCELED_FEE_BILLER_AMOUNT' => '0',
            'CANCELED_CLAIM_VSI' => '0',
            'CANCELED_CLAIM_VSI_AMOUNT' => '0',
            'CANCELED_BILLER' => '0',
            'CANCELED_CLAIM_PARTNER' => '0',
            'CANCELED_CLAIM_PARTNER_AMOUNT' => '0',
            // TOTAL
            'TOTAL_NBILL' => '0',
            'TOTAL_NMONTH' => '0',
            'TOTAL_RpTag' => '0',
            'TOTAL_FEE_ADMIN' => '0',
            'TOTAL_FEE_ADMIN_AMOUNT' => '0',
            'TOTAL_FEE_VSI' => '0',
            'TOTAL_FEE_VSI_AMOUNT' => '0',
            'TOTAL_TOTAL_FEE' => '0',
            'TOTAL_FEE_BILLER' => '0',
            'TOTAL_FEE_BILLER_AMOUNT' => '0',
            'TOTAL_CLAIM_VSI' => '0',
            'TOTAL_CLAIM_VSI_AMOUNT' => '0',
            'TOTAL_BILLER' => '0',
            'TOTAL_CLAIM_PARTNER' => '0',
            'TOTAL_CLAIM_PARTNER_AMOUNT' => '0',
            ];
        else :
            // Merge Data Product
            for ($i=0; $i < $countTransaction; $i++) {
                // Trx
                $tnbil = $transaction[$i]['NBILL'];
                $tnmonth = $transaction[$i]['NMONTH'];
                $tfee = $transaction[$i]['RpTag'];
                $tFeeAdminAmount = $transaction[$i]['FEE_ADMIN_AMOUNT'];
                $tFeeVsiAmount = $transaction[$i]['FEE_VSI_AMOUNT'];
                $tTotalFee = $transaction[$i]['TOTAL_FEE'];
                $tFeeBillerAmount = $transaction[$i]['FEE_BILLER_AMOUNT'];
                $tClaimVsiAmount = $transaction[$i]['CLAIM_VSI_AMOUNT'];
                $tBiller = $transaction[$i]['BILLER'];
                $tClaimPartnerAmount = $transaction[$i]['CLAIM_PARTNER_AMOUNT'];

                // Paid
                $pnbil = $paid[$i]['NBILL'];
                $pnmonth = $paid[$i]['NMONTH'];
                $pfee = $paid[$i]['RpTag'];
                $pFeeAdminAmount = $paid[$i]['FEE_ADMIN_AMOUNT'];
                $pFeeVsiAmount = $paid[$i]['FEE_VSI_AMOUNT'];
                $pTotalFee = $paid[$i]['TOTAL_FEE'];
                $pFeeBillerAmount = $paid[$i]['FEE_BILLER_AMOUNT'];
                $pClaimVsiAmount = $paid[$i]['CLAIM_VSI_AMOUNT'];
                $pBiller = $paid[$i]['BILLER'];
                $pClaimPartnerAmount = $paid[$i]['CLAIM_PARTNER_AMOUNT'];

                // Canceled
                $cnbil = $canceled[$i]['NBILL'];
                $cnmonth = $canceled[$i]['NMONTH'];
                $cfee = $canceled[$i]['RpTag'];
                $cFeeAdminAmount = $canceled[$i]['FEE_ADMIN_AMOUNT'];
                $cFeeVsiAmount = $canceled[$i]['FEE_VSI_AMOUNT'];
                $cTotalFee = $canceled[$i]['TOTAL_FEE'];
                $cFeeBillerAmount = $canceled[$i]['FEE_BILLER_AMOUNT'];
                $cClaimVsiAmount = $canceled[$i]['CLAIM_VSI_AMOUNT'];
                $cBiller = $canceled[$i]['BILLER'];
                $cClaimPartnerAmount = $canceled[$i]['CLAIM_PARTNER_AMOUNT'];

                // Create Row Data
                $dataProduct[] = [
                    "PRODUCT" => $transaction[$i]['PRODUCT'],
                    "CID" => $transaction[$i]['CID'],
                    "CID_NAME" => $transaction[$i]['CID_NAME'],
                    "TRX_DT" => $transaction[$i]['TRX_DT'],
                    'NBILL' => $transaction[$i]['NBILL'],
                    'NMONTH' => $transaction[$i]['NMONTH'],
                    'Rptag' => $transaction[$i]['RpTag'],
                    'FEE_ADMIN' => $transaction[$i]['FEE_ADMIN'],
                    'FEE_ADMIN_AMOUNT' => $transaction[$i]['FEE_ADMIN_AMOUNT'],
                    'FEE_VSI' => $transaction[$i]['FEE_VSI'],
                    'FEE_VSI_AMOUNT' => $transaction[$i]['FEE_VSI_AMOUNT'],
                    'TOTAL_FEE' => $transaction[$i]['TOTAL_FEE'],
                    'FEE_BILLER' => $transaction[$i]['FEE_BILLER'],
                    'FEE_BILLER_AMOUNT' => $transaction[$i]['FEE_BILLER_AMOUNT'],
                    'CLAIM_VSI' => $transaction[$i]['CLAIM_VSI'],
                    'CLAIM_VSI_AMOUNT' => $transaction[$i]['CLAIM_VSI_AMOUNT'],
                    'BILLER' => $transaction[$i]['BILLER'],
                    'CLAIM_PARTNER' => $transaction[$i]['CLAIM_PARTNER'],
                    'CLAIM_PARTNER_AMOUNT' => $transaction[$i]['CLAIM_PARTNER_AMOUNT'],
                    // Paid
                    'PAID_NBILL' => $paid[$i]['NBILL'],
                    'PAID_NMONTH' => $paid[$i]['NMONTH'],
                    'PAID_Rptag' => $paid[$i]['RpTag'],
                    'PAID_FEE_ADMIN' => $paid[$i]['FEE_ADMIN'],
                    'PAID_FEE_ADMIN_AMOUNT' => $paid[$i]['FEE_ADMIN_AMOUNT'],
                    'PAID_FEE_VSI' => $paid[$i]['FEE_VSI'],
                    'PAID_FEE_VSI_AMOUNT' => $paid[$i]['FEE_VSI_AMOUNT'],
                    'PAID_TOTAL_FEE' => $paid[$i]['TOTAL_FEE'],
                    'PAID_FEE_BILLER' => $paid[$i]['FEE_BILLER'],
                    'PAID_FEE_BILLER_AMOUNT' => $paid[$i]['FEE_BILLER_AMOUNT'],
                    'PAID_CLAIM_VSI' => $paid[$i]['CLAIM_VSI'],
                    'PAID_CLAIM_VSI_AMOUNT' => $paid[$i]['CLAIM_VSI_AMOUNT'],
                    'PAID_BILLER' => $paid[$i]['BILLER'],
                    'PAID_CLAIM_PARTNER' => $paid[$i]['CLAIM_PARTNER'],
                    'PAID_CLAIM_PARTNER_AMOUNT' => $paid[$i]['CLAIM_PARTNER_AMOUNT'],
                    // Cancel
                    'CANCELED_NBILL' => $canceled[$i]['NBILL'],
                    'CANCELED_NMONTH' => $canceled[$i]['NMONTH'],
                    'CANCELED_Rptag' => $canceled[$i]['RpTag'],
                    'CANCELED_FEE_ADMIN' => $canceled[$i]['FEE_ADMIN'],
                    'CANCELED_FEE_ADMIN_AMOUNT' => $canceled[$i]['FEE_ADMIN_AMOUNT'],
                    'CANCELED_FEE_VSI' => $canceled[$i]['FEE_VSI'],
                    'CANCELED_FEE_VSI_AMOUNT' => $canceled[$i]['FEE_VSI_AMOUNT'],
                    'CANCELED_TOTAL_FEE' => $canceled[$i]['TOTAL_FEE'],
                    'CANCELED_FEE_BILLER' => $canceled[$i]['FEE_BILLER'],
                    'CANCELED_FEE_BILLER_AMOUNT' => $canceled[$i]['FEE_BILLER_AMOUNT'],
                    'CANCELED_CLAIM_VSI' => $canceled[$i]['CLAIM_VSI'],
                    'CANCELED_CLAIM_VSI_AMOUNT' => $canceled[$i]['CLAIM_VSI_AMOUNT'],
                    'CANCELED_BILLER' => $canceled[$i]['BILLER'],
                    'CANCELED_CLAIM_PARTNER' => $canceled[$i]['CLAIM_PARTNER'],
                    'CANCELED_CLAIM_PARTNER_AMOUNT' => $canceled[$i]['CLAIM_PARTNER_AMOUNT'],
                    // TOTAL
                    'TOTAL_NBILL' => $tnbil + $pnbil - $cnbil,
                    'TOTAL_NMONTH' => $tnmonth + $pnmonth - $cnmonth,
                    'TOTAL_FEE' => $tfee + $pfee - $cfee,
                    'TOTAL_FEE_ADMIN_AMOUNT' => $tFeeAdminAmount + $pFeeAdminAmount - $cFeeAdminAmount,
                    'TOTAL_FEE_VSI_AMOUNT' => $tFeeVsiAmount + $pFeeVsiAmount - $cFeeVsiAmount,
                    'TOTAL_TOTAL_FEE' => $tTotalFee + $pTotalFee - $cTotalFee,
                    'TOTAL_FEE_BILLER_AMOUNT' => $tFeeBillerAmount + $pFeeBillerAmount - $cFeeBillerAmount,
                    'TOTAL_CLAIM_VSI_AMOUNT' => $tClaimVsiAmount + $pClaimVsiAmount - $cClaimVsiAmount,
                    'TOTAL_BILLER_AMOUNT' => $tBiller + $pBiller - $cBiller,
                    'TOTAL_CLAIM_PARTNER_AMOUNT' => $tClaimPartnerAmount + $pClaimPartnerAmount - $cClaimPartnerAmount,

                ];
            }
        endif;
        // *** END OF Logic Get Data Product *** //

        $this->setSuspect = $dataProduct;
        $this->setCountSuspect = count($dataProduct);

        return $this->setSuspect;
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'E:AZ' => NumberFormat::FORMAT_NUMBER,
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
                    ],
                    'alignment' => [
                        Alignment::HORIZONTAL_LEFT,
                    ],
                ];

                $groupHeaderStyle = [
                    'font' => [
                        'name' => 'Calibri',
                        'bold' => 'true',
                        'size' => '10',
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'F7F7F7'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
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
                $countColumn = count($this->setSuspect[0]) + 1;
                $countData = $this->setCountSuspect + 7;
                $lastColumn = Coordinate::stringFromColumnIndex($countColumn);
                $rangeData = sprintf('A6:%s%d', $lastColumn, $countData);
                $numberCell = sprintf('E7:%s%d', $lastColumn, $countData);
                $footerRow = $countData + 2;

                // Set Default Style
                $sheet->getParent()->getDefaultStyle()->applyFromArray($defaultStyle);

                // *** HEADER AREA *** ///
                // Menambahkan 3 baris mulai dari baris ke 1
                $sheet->insertNewRowBefore(1, 5);

                // *** Menambahkan Header dan Sub Header *** //
                $sheet->setCellValue(
                    'A1',
                    'Resume Recon Data Transaction',
                );
                $sheet->setCellValue(
                    'A2',
                    'Product : '. $this->setProduct,
                );
                $sheet->setCellValue(
                    'A3',
                    'Periode : '. $this->setDate[0]. ' s.d. ' .$this->setDate[1]
                );
                // *** END OF Menambahkan Header dan Sub Header *** //

                // Merge Cell Bagian Header dan Sub Header
                $sheet->mergeCells(sprintf('A1:%s1', $lastColumn));
                $sheet->mergeCells(sprintf('A2:%s2', $lastColumn));
                $sheet->mergeCells(sprintf('A3:%s3', $lastColumn));
                $sheet->getStyle('A1')->getFont()->setBold(true);
                // *** END OF HEADER AREA *** ///

                // *** Menambahkan Group Header *** //
                $sheet->setCellValue(
                    'E5',
                    'TRANSACTION',
                );
                $sheet->setCellValue(
                    'T5',
                    'SUSPECT PAID',
                );
                $sheet->setCellValue(
                    'AI5',
                    'SUSPECT CANCELED',
                );
                $sheet->setCellValue(
                    'AX5',
                    'TOTAL',
                );
                // *** END OF Menambahkan Group Header *** //

                // *** Menambahkan Group Header Untuk Transaction, Paid, Cancleled *** //
                $sheet->mergeCells('E5:S5'); // Transaction
                $sheet->mergeCells('T5:AH5'); // Paid
                $sheet->mergeCells('AI5:AW5'); // Canceled
                $sheet->mergeCells('AX5:BL5'); // TOTAL
                // *** Menambahkan Group Header Untuk Transaction, Paid, Cancleled *** //

                // *** DATA AREA *** ///
                // Set Bold Untuk Header Colom
                $sheet->getStyle(sprintf('E5:%s5', $lastColumn))->applyFromArray($groupHeaderStyle);
                $sheet->getStyle(sprintf('A6:%s6', $lastColumn))->applyFromArray($headerColumnstyle);


                // Set Border Group Header
                $sheet->getStyle(sprintf('E5:%s5', $lastColumn))
                ->applyFromArray($groupHeaderStyle)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

                // Set Border Data //
                $sheet->getStyle($rangeData)
                ->applyFromArray($valueColumnstyle)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

                // Set Separated Number
                $sheet->getStyle($numberCell)->getNumberFormat()
                ->setFormatCode('#,##0');
                // *** END OF DATA AREA *** ///

                // *** TOTAL AREA *** ///
                $sheet->setCellValue(sprintf('A%d', $countData), 'TOTAL');
                $sheet->getStyle(sprintf('A%d:%s%d', $countData, $lastColumn, $countData))
                ->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => '45FFE9'],
                    ],
                ]);

                // Total Transaction
                for ($i=5; $i < ($countColumn+1); $i++) {
                    $col = Coordinate::stringFromColumnIndex($i);
                    $sheet->setCellValue(
                        sprintf('%s%d', $col, $countData),
                        '= SUM('. sprintf('%s7:%s%d', $col, $col, $countData - 1) .')'
                    );
                }
                // *** End Of TOTAL AREA *** ///

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
                //  *** END OF FOOTER AREA *** //
            },
        ];
    }
}
