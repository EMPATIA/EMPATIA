<x-backend.card class="bg-secondary mb-3">
    <x-backend.card-body>
        <div class="d-flex">
            <div class="flex-grow-1">
                {{ $slot }}
            </div>

            <div>
                {{ $buttons ?? '' }}
            </div>

            <div>
                {{ $right ?? '' }}
            </div>
        </div>
    </x-backend.card-body>
</x-backend.card>