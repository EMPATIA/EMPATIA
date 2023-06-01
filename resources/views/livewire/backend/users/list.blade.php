<div>
    @include('livewire.indicator-spinner')

    <div class="row fw-bold">
        <div class="col-6">Name <button class="btn btn-link" wire:click="toggleSort()"><i class="fa-solid fa-sort"></i></button></div>
        <div class="col-4">Email</div>
        <div class="col-2 input-group-sm"><input wire:model.debounce.500ms="search" class="form-control" type="text" name="search" id="search" placeholder="{{ __('backend.users.list.search') }}" /></div>
    </div>
    @foreach($users as $key => $user)
        <div class="row">
            <div class="col-6">{{ getField($user, 'firstName')." ".getField($user, 'lastName') }}</div>
            <div class="col-4">{{ getField($user, 'email') }}</div>
            <div class="col-2">ACTIONS</div>
        </div>
    @endforeach
</div>