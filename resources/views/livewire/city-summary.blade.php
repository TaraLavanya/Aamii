<div>
    <div class="page-body">
        <div class="container-xl">
            @include('includes.alerts')
            <div class="row">
                <div class="col-lg-4 ">
                    @livewire('city-handler', ['cityId' => $cityId])
                </div>
                <div class="col-lg-8 ">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div>
                            <h4 class="text">List all city</h4>
                        </div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>State Name</th>
                                        <th>Country Name</th>
                                        <th>Is Active</th>
                                        <th colspan="2" class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($cities) && count($cities) > 0)
                                        @foreach ($cities as $cityIndex => $city)
                                            <tr wire:key='item-{{ $city->id }}'>

                                                <td>
                                                    {{ $cityIndex + $cities->firstItem() }}
                                                </td>

                                                <td>
                                                    <div class="text-capitalize">{{ $city->name }}</div>
                                                </td>

                                                <td>
                                                    <div class="text-capitalize">{{ $city->state->name }}</div>
                                                </td>

                                                <td>
                                                    <div class="text-capitalize">{{ $city->country->name }}</div>
                                                </td>

                                                <td>
                                                    <div @class([
                                                        'badge',
                                                        'me-1',
                                                        'bg-success' => $city->is_active,
                                                        'bg-danger' => !$city->is_active,
                                                    ])></div>
                                                    {{ $city->is_active == 1 ? 'Active' : 'Inactive' }}

                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a
                                                            href="{{ route('city', ['cityId' => $city->id, 'page' => $this->paginators['page'], 'pp' => $this->perPage]) }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="icon icon-tabler icon-tabler-edit" width="24"
                                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                </path>
                                                                <path
                                                                    d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1">
                                                                </path>
                                                                <path
                                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z">
                                                                </path>
                                                                <path d="M16 5l3 3"></path>
                                                            </svg>
                                                        </a>

                                                        <a href="#"
                                                            wire:click.prevent="$dispatch('canDeletecity',{{ $city->id }})"
                                                            class="text-danger">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="icon icon-tabler icon-tabler-trash"
                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                stroke-width="2" stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                </path>
                                                                <path d="M4 7l16 0"></path>
                                                                <path d="M10 11l0 6"></path>
                                                                <path d="M14 11l0 6"></path>
                                                                <path
                                                                    d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                </path>
                                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif
                                    @if (isset($cities) && count($cities) == 0)
                                        <tr>
                                            <td colspan="4" class="text-center text-danger">No Records Found
                                            <td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="row d-flex flex-row mb-3">
                                @if (isset($cities) && count($cities) != 0)
                                    <div class="col">
                                        <div class="d-flex flex-row mb-3">
                                            <div>
                                                <label class="p-2" for="perPage">Per Page</label>
                                            </div>
                                            <div>
                                                <select class="form-select" id="perPage" name="perPage"
                                                    wire:model="perPage"
                                                    wire:change="changePageValue($event.target.value)">
                                                    <option value=10>10</option>
                                                    <option value=50>50</option>
                                                    <option value=100>100</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col d-flex justify-content-end">
                                    @if (isset($cities) && count($cities) >= 0)
                                        {{ $cities->links() }}
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
        Livewire.on('canDeletecity', (cityId) => {
            if (confirm('Are you sure you want to delete this city ?')) {
                Livewire.dispatch('deletecity', {
                    cityId
                });
            }
        });
    </script>
@endpush
