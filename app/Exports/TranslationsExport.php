<?php

namespace App\Exports;

use App\Http\Livewire\Backend\CMS\Translation\TranslationsTable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;


class TranslationsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    protected $translations;
    
    public function __construct($translations)
    {
        $this->translations = $translations;
    }
    
    public function headings(): array
    {
        return TranslationsTable::$fieldsToExport;
    }
    
    public function collection()
    {
        return $this->translations;
    }
}