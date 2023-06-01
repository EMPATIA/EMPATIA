<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;
use Illuminate\Support\Str;

class JsonTable extends Component
{
    const DEFAULT_TRANSLATABLE = false;
    const DEFAULT_SORTABLE = false;
    const DEFAULT_SEARCHABLE = true;
    const DEFAULT_TYPE = 'string';

    public array $tableData = [];
    public array $renderColumns = [];

    public function mount($columns, $data) {
        $this->renderColumns = $this->getColumns($columns);
        $this->tableData = $this->getTableData($data);
    }

    public function render()
    {
        return view('livewire.backend.json-table-component', [
            'columns' => $this->renderColumns,
            'data' => $this->tableData,
            'searching' => true,
        ]);
    }

    public function getTableData($data) : array {
        $lang = getLang();
        $columns = $this->renderColumns['columns'];

        $newData = [];

        if (!$data) {
            return $newData;
        }

        foreach ($data as $item) {
            $row = [];
            foreach ($columns as $column) {
                $value = data_get($item, $column['key'], ' ');

                if (is_array($value) && isset($value[$lang])) {
                    $row[$column['key']] = $value[$lang];
                } else {
                    $row[$column['key']] = $value;
                }
            }

            $newData[] = $row;
        }

        return $newData;
    }

    public function getColumns($columns) : array {
        $newColumns = [
            'columns' => [],
            'options' => [],
        ];

        foreach ($columns as $column) {
            // validate key and label
            $column['key'] = !empty($column['key']) ? Str::slug($column['key']) : Str::slug($column['label']);
            $column['label'] = !empty($column['label']) ? Str::slug($column['label']) : Str::headline($column['key']);

            // check column setting requirements
            if (empty($column['key']) && empty($column['label'])) {
                continue;
            }

            $object = [
                'key' => $column['key'],
                'label' => $column['label'],
                'type' => $column['type'] ?? self::DEFAULT_TYPE,
            ];

            $optionsObject = [
                'translatable' => $column['translatable'] ?? self::DEFAULT_TRANSLATABLE,
                'sortable' => $column['sortable'] ?? self::DEFAULT_SORTABLE,
                'searchable' => $column['searchable'] ?? self::DEFAULT_SEARCHABLE,
            ];

            $newColumns['columns'][] = $object;
            $newColumns['options'][] = $optionsObject;
        }
        return $newColumns;
    }
}

