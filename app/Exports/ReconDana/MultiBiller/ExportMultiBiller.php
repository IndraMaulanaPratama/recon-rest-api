<?php

namespace App\Exports\ReconDana\MultiBiller;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportMultiBiller implements WithMultipleSheets
{
    protected $setDataResume;
    protected $setDataGroupTransfer;
    protected $setParams;
    protected $setTitleSheet;

    public function __construct(array $data, array $params)
    {
        $this->setDataResume = $data['data_resume'];
        $this->setDataGroupTransfer = $data['data_group_transfer'];
        $this->setParams = $params;
        $this->setTitleSheet = $params['title_sheet'];
    }

    public function sheets(): array
    {
        $count = count($this->setDataGroupTransfer);
        $sheet = [];
        $sheet[0] = new SummaryData($this->setDataResume, $this->setParams);

        for ($i=0; $i < $count; $i++) {
            $title = $this->setTitleSheet[$i];
            $sheet[] = new GroupTransferData($this->setDataGroupTransfer[$title], $this->setParams, $title);
        }

        return $sheet;
    }
}
