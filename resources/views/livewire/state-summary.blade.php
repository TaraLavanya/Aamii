<div>
    <div class="page-body">
        <div class="container-xl">
            @include('includes.alerts')
            <div class="row">
                <div class="col-lg-4 ">
                    @livewire('state-handler', ['stateId' => $stateId])
                </div>
                <div class="col-lg-8 ">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div>
                            <h4 class="text">List all state</h4>
                        </div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Country Name</th>
                                        <th>Is Active</th>
                                        <th colspan="2" class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($states) && count($states) > 0)
                                        @foreach ($states as $stateIndex => $state)
                                            <tr wire:key='item-{{ $state->id }}'>

                                                <td>
                                                    {{ $stateIndex + $states->firstItem() }}
                                                </td>

                                                <td>
                                                    <div class="text-capitalize">{{ $state->name }}</div>
                                                </td>

                                                <td>
                                                    <div class="text-capitalize">{{ $state->country->name }}</div>
                                                </td>

                                                <td>
                                                    <div @class([
                                                        'badge',
                                                        'me-1',
                                                        'bg-success' => $state->is_active,
                                                        'bg-danger' => !$state->is_active,
                                                    ])></div>
                                                    {{ $state->is_active == 1 ? 'Active' : 'Inactive' }}

                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a
                                                            href="{{ route('state', ['stateId' => $state->id, 'page' => $this->paginators['page'], 'pp' => $this->perPage]) }}">
                                                            @include('icons.edit')
                                                        </a>

                                                        <a href="#"
                                                            wire:click.prevent="$dispatch('canDeletestate',{{ $state->id }})"
                                                            class="text-danger">
                                                            @include('icons.trash')
                                                        </a>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif
                                    @if (isset($states) && count($states) == 0)
                                        <tr>
                                            <td colspan="4" class="text-center text-danger">No Records Found
                                            <td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex flex-row mb-3">
                                @if (isset($states) && count($states) != 0)
                                    <div>
                                        <div>
                                            <label class="p-2" for="perPage">Per Page</label>
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            <select class="form-select" id="perPage" name="perPage"
                                                wire:model="perPage" wire:change="changePageValue($event.target.value)">
                                                <option value=10>10</option>
                                                <option value=50>50</option>
                                                <option value=100>100</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col d-flex justify-content-end">
                                    @if (isset($states) && count($states) >= 0)
                                        {{ $states->links() }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        Livewire.on('canDeletestate', (stateId) => {
            if (confirm('Are you sure you want to delete this state ?')) {
                Livewire.dispatch('deletestate', {
                    stateId
                });
            }
        });
    </script>
@endpush
