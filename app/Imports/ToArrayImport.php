<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Events\AfterImport;

class ToArrayImport implements ToArray, WithHeadingRow, WithEvents
{
    private $headingRow;
    private $sheetNames;

    public function __construct(int $headingRow = 1){
        $this->headingRow = $headingRow;
        $this->sheetNames = [];
    }

    public function array(array $array)
    {
        return $array;
    }

    public function headingRow(): int
    {
        return $this->headingRow;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                $this->sheetNames = array_keys($event->getDelegate()->getWorksheets($event->getConcernable()));
            }
        ];
    }

    public function getSheetNames() {
        return $this->sheetNames;
    }
}
