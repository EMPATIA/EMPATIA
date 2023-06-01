<x-backend.card container="col-12">
    <x-backend.card-header>
        Settings
        <span
            class="badge bg-secondary rounded-pill text-lowercase py-1"
        >laravel-admin</span>
    </x-backend.card-header>

    <x-backend.card-body>
        <div class="col">
            {{--   PHASES   --}}
            <div class="mb-4">
                <h4 class="text-uppercase fs-6 fw-bold text-info">PHASES</h4>

                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">code</th>
                        <th scope="col">enabled</th>
                        <th scope="col">cms</th>
                        <th scope="col">start</th>
                        <th scope="col">end</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($model->phases() ?? [] as $code => $phase)
                        <tr>
                            <th scope="row">{{ $code }}</th>
                            <td>
                                @if( data_get($phase, 'enabled') )
                                    <i class="fa-regular fa-circle-check text-success"></i>
                                @else
                                    <i class="fa-regular fa-circle-xmark text-danger"></i>
                                @endif
                            </td>
                            <td>{{ data_get($phase, 'cms') }}</td>
                            @if( $schedule = $model->operationSchedule(data_get($phase, 'operation_schedule')) )
                                <td>{{ data_get($schedule, 'start_date') }}</td>
                                <td>{{ data_get($schedule, 'end_date') }}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{--   ACTIONS   --}}
            <div class="mb-4">
                <h4 class="text-uppercase fs-6 fw-bold text-info">Actions</h4>

                <h5 class="fs-6">Topic</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">code</th>
                        <th scope="col">enabled</th>
                        <th scope="col">start</th>
                        <th scope="col">end</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($model->topicActions() ?? [] as $code => $action)
                        <tr>
                            <th scope="row">{{ $code }}</th>
                            <td>
                                @if( data_get($action, 'enabled') )
                                    <i class="fa-regular fa-circle-check text-success"></i>
                                @else
                                    <i class="fa-regular fa-circle-xmark text-danger"></i>
                                @endif
                            </td>
                            @if( $schedule = $model->operationSchedule(data_get($action, 'operation_schedule')) )
                                <td>{{ data_get($schedule, 'start_date') }}</td>
                                <td>{{ data_get($schedule, 'end_date') }}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <h5 class="fs-6">Vote</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">code</th>
                        <th scope="col">enabled</th>
                        <th scope="col">start</th>
                        <th scope="col">end</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($model->voteActions() ?? [] as $code => $action)
                        <tr>
                            <th scope="row">{{ $code }}</th>
                            <td>
                                @if( data_get($action, 'enabled') )
                                    <i class="fa-regular fa-circle-check text-success"></i>
                                @else
                                    <i class="fa-regular fa-circle-xmark text-danger"></i>
                                @endif
                            </td>
                            @if( $schedule = $model->operationSchedule(data_get($action, 'operation_schedule')) )
                                <td>{{ data_get($schedule, 'start_date') }}</td>
                                <td>{{ data_get($schedule, 'end_date') }}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{--   OPERATION SCHEDULES   --}}
            <div class="mb-4">
                <h4 class="text-uppercase fs-6 fw-bold text-info">Operation Schedules</h4>

                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">code</th>
                        <th scope="col">enabled</th>
                        <th scope="col">start</th>
                        <th scope="col">end</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($model->operationSchedules() ?? [] as $code => $schedule)
                        <tr>
                            <th scope="row">{{ $code }}</th>
                            <td>
                                @if( data_get($schedule, 'enabled') )
                                    <i class="fa-regular fa-circle-check text-success"></i>
                                @else
                                    <i class="fa-regular fa-circle-xmark text-danger"></i>
                                @endif
                            </td>
                            <td>{{ data_get($schedule, 'start_date') }}</td>
                            <td>{{ data_get($schedule, 'end_date') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{--   CONTENTS   --}}
            <div class="mb-4">
                <h4 class="text-uppercase fs-6 fw-bold text-info">Contents</h4>

                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">code</th>
                        <th scope="col">content</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($model->contents() ?? [] as $code => $content)
                        <tr>
                            <th scope="row">{{ $code }}</th>
                            <td>{{ data_get($content, 'code') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-backend.card-body>
</x-backend.card>
