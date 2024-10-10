<div>
    <div class="page-body">
        <div class="container-xl">
            @include('includes.alerts')
            <div class="row">
                <div class="col-lg-4 ">
                    @livewire('country-handler', ['countryId' => $countryId])
                </div>
                <div class="col-lg-8 ">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div>
                            <h4 class="text">List all country</h4>
                        </div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Is Active</th>
                                        <th colspan="2" class="w-1">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($countries) && count($countries) > 0)
                                        @foreach ($countries as $countryIndex => $country)
                                            <tr wire:key='item-{{ $country->id }}'>

                                                <td>
                                                    {{ $countryIndex + $countries->firstItem() }}
                                                </td>

                                                <td>
                                                    <div class="text-capitalize">{{ $country->name }}</div>
                                                </td>

                                                <td>
                                                    <div @class([
                                                        'badge',
                                                        'me-1',
                                                        'bg-success' => $country->is_active,
                                                        'bg-danger' => !$country->is_active,
                                                    ])></div>
                                                    {{ $country->is_active == 1 ? 'Active' : 'Inactive' }}

                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a
                                                            href="{{ route('country', ['countryId' => $country->id, 'page' => $this->paginators['page'], 'pp' => $this->perPage]) }}">
                                                            @include('icons.edit')
                                                        </a>

                                                        <a href="#"
                                                            wire:click.prevent="$dispatch('canDeletecountry',{{ $country->id }})"
                                                            class="text-danger">
                                                            @include('icons.trash')
                                                        </a>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif
                                    @if (isset($countries) && count($countries) == 0)
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
                                @if (isset($countries) && count($countries) != 0)
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
                                    @if (isset($countries) && count($countries) >= 0)
                                        {{ $countries->links() }}
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
        Livewire.on('canDeletecountry', (countryId) => {
            if (confirm('Are you sure you want to delete this country ?')) {
                Livewire.dispatch('deletecountry', {
                    countryId
                });
            }
        });
    </script>
@endpush
