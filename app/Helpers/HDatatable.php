<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Exception;
use Arr;

class HDatatable {

    /**
     * Apply configure defaults to datatable
     *
     * @param  mixed $class DataTableComponent class
     * @param  mixed $primaryKey Primary key to configure
     * @param  mixed $defaultSort Default sort field
     * @param  mixed $setAction If to set action column size or not
     * @param  mixed $maxPerPage Max results per page
     * @return void
     */
    public static function applyTableConfigureDefaults($class, String $primaryKey = 'id', int $maxPerPage = 25): void {
        try {
            $class->setPrimaryKey($primaryKey);
            $class->setColumnSelectStatus(false);
            $class->setPerPageAccepted([10, 25, 50, 100, -1]);
            $class->setPerPage($maxPerPage);
            $class->setSortingPillsDisabled();
            // $class->setFilterPillsDisabled();
            $class->setFilterLayoutSlideDown();
            $class->setEmptyMessage('backend.datatable.empty');
            $class->setTheadAttributes(['class' => 'bg-secondary']);
            $class->setComponentWrapperAttributes(['class' => 'laravel-livewire-table']);
        } catch(Exception $e) {
            logError("Error applying datatable configure defaults: " . $e->getMessage());
        }
    }

    public static function applyTableConfigureTable($class, array $attributes = [], $addDefault = true): void {
        if($addDefault) {
            $attributes['class'] = ($attributes['class'] ?? '').' table-hover';
            $attributes['style'] = ($attributes['style'] ?? '').' table-layout: fixed;';
        }

        $class->setTableAttributes($attributes);
    }

    public static function applyTableConfigureHeader($class, array $attributes = [], $addId = true, $addAction = true): void {
        try {
            if($addId) {
                $attributes['id'] = [
                    'class' => 'col-1 d-none d-md-table-cell',
                ];
            }
            if($addAction) {
                $attributes['action'] = [
                    'class' => 'col-4 col-sm-3 col-md-2 text-end',
                ];
            }

            $class->setThAttributes(function(Column $column) use ($attributes) {
                $index = $column->getField() ?? $column->getSlug();

                if(empty($index) || !Arr::exists($attributes, $index)) return [];

                return $attributes[$index];
            });
        } catch(Exception $e) {
            logError("Error applying datatable configure header");
        }
    }

    /**
     * Apply table column (td) configure
     *
     * @param  mixed $class
     * @param  mixed $attributes
     * @param  mixed $addId
     * @param  mixed $addAction
     * @return void
     */
    public static function applyTableConfigureColumn($class, array $attributes = [], $addId = true, $addAction = true): void {
        try {
            if($addId) {
                $attributes['id'] = fn($row) => [
                    'class' => 'd-none d-md-table-cell',
                ];
            }
            if($addAction) {
                $attributes['action'] = fn($row) => [
                    'class' => 'py-2 text-end',
                ];
            }

            $class->setTdAttributes(function(Column $column, $row, $columnIndex, $rowIndex) use ($attributes) {
                $index = $column->getField() ?? $column->getSlug();

                if(empty($index) || !Arr::exists($attributes, $index) || !is_callable($attributes[$index])) return [];

                return $attributes[$index]($row);
            });
        } catch(Exception $e) {
            logError("Error applying datatable configure td");
        }
    }

    public static function addFilterDeleted(array $filter): array {
        try {
            array_push($filter,
                SelectFilter::make(__('backend.generic.deletion-status'), 'deleted_at')
                    ->options([
                        '' => __('backend.generic.not-deleted'),
                        '1' => __('backend.generic.deleted'),
                    ])
                    ->filter(function(Builder $builder, string $value) {
                        if ($value === '1') {
                            return $builder->onlyTrashed();
                        }
                    }),
            );

            return $filter;
        } catch(Exception $e) {
            logError("Error applying datatable deleted filter");
            return [];
        }
    }

    public static function addFilterDates(array $filter): array {
        try {
            array_push($filter,
            DateFilter::make(__('backend.generic.start-date'), 'start_date')
                ->filter(function(Builder $builder, string $value) {
                    return $builder->whereDate('created_at','>=',$value);
                }),
            DateFilter::make(__('backend.generic.end-date'), 'end_date')
                ->filter(function(Builder $builder, string $value) {
                    return $builder->whereDate('created_at','<=',$value);
                }),
            );

            return $filter;
        } catch(Exception $e) {
            logsError("Error applying datatable dates filter");
            return [];
        }
    }

    /**
     * @param object $model             Model object
     * @param array $routes             Actions routes
     * @param bool $deletionStatus      Flag to know if it is to show the entries deleted or not
     * @param array $actionsList   Array of action buttons to show
     * @param string|null $component    Component to where the actions are emitted
     * @param array $permissions        Permissions to perform the actions
     * @param string $identifier        Identifier of the model ("id", "code", ...)
     * @return string
     */
    public static function getTableActions(object $model, array $routes = [], bool $deletionStatus = false, array $actionsList = [], string $component = null, array $permissions = [], string $identifier = "id") : string
    {
        try {
            // TODO: Implement permissions
            
            $actionsList = empty($actionsList) ? ['show', 'edit', 'delete'] : $actionsList;
            $modelIdentifier = data_get($model, $identifier);

            $actions = '';

            if ($deletionStatus) {  // Filter deleted entries
                $actions .= '<a
                          class="btn-fontawesome-icon btn btn-sm btn-light text-primary ms-1 restore-entry"
                          title="' . __('backend::generic.restore') . '"
                          data-action = " ' . data_get($routes, 'restore') . ' "
                          data-identifier = "' . $identifier . '"
                          data-component = " ' . $component . ' "
                          >
                          <i class="fa-solid fa-rotate-left"></i>
                         </a>';
                return $actions;
            } else {
                foreach ($actionsList ?? [] as $action) {
                    $route = data_get($routes, $action);

                    if ($action == "show") {
                        $btnShow = '<a
                                        class="fas fa-eye btn-fontawesome-icon btn btn-sm btn-light text-info ms-1"
                                        href="' . $route . '"
                                        title="' . __('backend::generic.show') . '">
                                   </a>';
                        $actions .= $btnShow;

                    } elseif ($action == "edit") {
                        $btnEdit = '<a
                                        class="fas fa-edit btn-fontawesome-icon btn btn-sm btn-light text-success ms-1"
                                        href="' . $route . '"
                                        title="' . __('backend::generic.edit') . '">
                                        <i class=""></i>
                                   </a>';
                        $actions .= $btnEdit;

                    } elseif ($action == "delete") {
                        $btnDelete = '<a
                                        class="far fa-trash-alt btn-fontawesome-icon btn btn-sm btn-light text-danger ms-1 delete-entry"
                                        title="' . __('backend::generic.delete') . '"
                                        data-action = " ' . $route . ' "
                                        data-identifier="' . $modelIdentifier . '"
                                        data-component =" ' . $component . ' "
                                        >
                                   </a>';
                        $actions .= $btnDelete;

                    } else {
                        logError("Action not recognized!");
                    }
                }
            }

            return $actions;
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            return '';
        }
    }
}
