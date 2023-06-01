<?php

namespace App\Http\Livewire\Backend\Notifications;

use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\Notifications\EmailsController;
use App\Models\Backend\Notifications\Email;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Carbon\Carbon;

class EmailsTable extends DataTableComponent
{

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        return Email::query();
    }

    /**
     * Laravel Livewire Tables columns builder
     *
     * @return array
     */
    public function configure() : void {
        HDatatable::applyTableConfigureDefaults($this);

        $this->setDefaultSort('id');
        $this->setAdditionalSelects(['id']);

        HDatatable::applyTableConfigureTable($this);

        HDatatable::applyTableConfigureHeader($this, [
        ]);

        HDatatable::applyTableConfigureColumn($this, [
        ]);

    }

    /**
     * Laravel Livewire Tables filters
     *
     * @return array
     */
    public function filters(): array {
        $filters = [];

        $filters = HDatatable::addFilterDeleted($filters);
        $filters = HDatatable::addFilterDates($filters);

        return $filters;
    }

    /**
     * Laravel Livewire Tables columns builder
     *
     * @return array
     */
    public function columns() : array {
        $columns = [
            Column::make(__('backend.generic.id'), 'id')
                ->sortable()
                ->searchable()
                ->collapseOnTablet(),
            Column::make(__('backend.notifications.emails.generic.subject'), 'subject')
                ->format(fn($value, $row, Column $column) => "<a href='".route('notifications.emails.show', ['id' => $row->id])."'>".$value."</a>")
                ->html()
                ->sortable()
                ->searchable(),
            // Column::make(__($this->prefix.'table-column.sender-name'), 'from_name')
            //     ->sortable()
            //     ->searchable()
            //     ->collapseOnTablet(),
            Column::make(__('backend.notifications.emails.generic.sender-email'), 'from_email')
                ->sortable()
                ->searchable()
                ->collapseOnTablet(),
            Column::make(__('backend.notifications.emails.generic.recipient-email'), 'user_email')
                ->sortable()
                ->searchable(),
            Column::make(__('backend.generic.created-at'), 'created_at')
                ->searchable()
                ->sortable()
                ->format(function ($value) {
                    return Carbon::parse($value)->isoFormat('Y-MM-DD • HH:mm:ss');
                })
                ->collapseOnTablet(),
            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'show' => route('notifications.emails.show', ['id' => $model->id]),
                            'delete' => route('notifications.emails.delete', ['id' => $model->id]),
                            'restore' => route('notifications.emails.restore', ['id' => $model->id])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'), ["show", "delete"]);
                    }
                )
                ->html()
        ];

        return $columns;
    }
}
