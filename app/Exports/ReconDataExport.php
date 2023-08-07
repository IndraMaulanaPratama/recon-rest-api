<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReconDataExport implements
    WithMultipleSheets
{
    protected $setData;
    protected $setHeader;
    protected $setTitle;
    protected $setParameter;
    protected $setDate;
    protected $setProduct;
    protected $setCountProduct;


    public function __construct(array $data, array $header, string $title, array $parameter)
    {
        $this->setData = $data;
        $this->setHeader = $header;
        $this->setTitle = $title;
        $this->setParameter = $parameter;
        $this->setDate = $this->setParameter['date'];
        $this->setProduct = $this->setParameter['product'];
        $this->setCountProduct = $this->setParameter['countProduct'];
    }

    public function sheets(): array
    {
        // Inisialisasi Variable
        $sheet = [];

        // Put Recon Data Summary to Data Sheet
        $sheet[0] = new SummaryReconData(
            $this->setData,
            $this->setHeader,
            $this->setTitle,
            $this->setParameter
        );

        // Put Suspect Product to Data Sheet
        for ($i=0; $i < $this->setCountProduct; $i++) {
            $sheet[] = new ProductReconData($this->setProduct[$i], $this->setDate);
        }

        return $sheet;
    }
}
