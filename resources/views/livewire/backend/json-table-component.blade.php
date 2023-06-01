@if($data)
        <table id="myTable" class="table table-striped table-hover" style="table-layout: fixed;">
            <thead class="bg-secondary">
            <tr>
                @foreach ($columns['columns'] as $column)
                    <th class="col col-auto col-md-2">
                        <div class="d-flex align-items-center justify-center">
                            {{ $column['label'] }}
                            <span class="relative d-flex align-items center">
                                <svg xmls="http://ww.w3.org/2000/svg" class="m1-1" style="width:1em;height:1em;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8M4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </span>
                        </div>
                    </th>
               @endforeach
            </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        {{-- FIXME: este nível a mais tem algum propósito? --}}
                        @foreach($item as $name)
                            @foreach ($columns['columns'] as $column)
                                    @if(isset($name[$column['key']]))
                                    <td>
                                        @if($column['type'] === 'string')
                                            {{ $name[$column['key']] }}
                                        @else
                                                <div class="d-block mb-2">
                                                    @if($name[$column['key']] === 'true')
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1.2em;height:1.2em;" class="d-inline-block  text-success " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1.2em;height:1.2em;" class="d-inline-block  text-danger " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                        @endif
                                    </td>
                                  @endif
                          @endforeach
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

@else
    {{-- FIXME: não se coloca texto cru nas vistas; usar funções de tradução --}}
    <h1>No data to be shown</h1>
@endif

@push('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        const jsonColumns = @json($columns['options']);
            $(document).ready(function () {
                $('#myTable').DataTable({
                    'columns': jsonColumns,
                    'language': {
                        'paginate': {
                            'previous': '&lt;',
                            'next': '&gt',
                        },
                        'searchPlaceholder': 'Search',
                        'info': 'Showing _END_ entries',
                        'lengthMenu': '_MENU_',
                        'search': '_INPUT_',
                    },
                    'initComplete': function () {
                        $('div.dataTables_filter label').contents().filter(function () {
                            return this.nodeType === 3;
                        }).remove();
                        $('div.dataTables_filter input[type="search"]').attr('placeholder', 'Search');
                    },
                });
            })
    </script>
@endpush
