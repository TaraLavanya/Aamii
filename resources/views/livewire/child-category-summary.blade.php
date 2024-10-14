<div>
    <div class="page-body">
        <div class="container-xl">
            @include('includes.alerts')
            <div class="row">
                <div class="col-lg-4 ">
                    @livewire('child-category-handler', ['childCategoryId' => $childCategoryId])
                </div>
                <div class="col-lg-8 ">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div>
                            <h4 class="text">List all child Categories</h4>
                        </div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Category Name</th>
                                        <th>Sub Category Name</th>
                                        <th>Is Active</th>
                                        <th colspan="2" class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($childCategories) && count($childCategories) > 0)
                                        @foreach ($childCategories as $childCategoryIndex => $childCategory)
                                            <tr wire:key='item-{{ $childCategory->id }}'>

                                                <td>
                                                    {{ $childCategoryIndex + $childCategories->firstItem() }}
                                                </td>

                                                <td>
                                                    <div class="text-capitalize">{{ $childCategory->name }}</div>
                                                </td>
                                                <td>
                                                    <div class="text-capitalize">
                                                        {{ $childCategory?->category?->name ?? '--' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-capitalize">
                                                        {{ $childCategory?->subCategory?->name ?? '--' }}
                                                    </div>
                                                </td>

                                                <td>
                                                    <div @class([
                                                        'badge',
                                                        'me-1',
                                                        'bg-success' => $childCategory->is_active,
                                                        'bg-danger' => !$childCategory->is_active,
                                                    ])></div>
                                                    {{ $childCategory->is_active == 1 ? 'Active' : 'Inactive' }}

                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a
                                                            href="{{ route('child-category', ['childCategoryId' => $childCategory->id, 'page' => $this->paginators['page'], 'pp' => $this->perPage]) }}">
                                                            @include('icons.edit')
                                                        </a>

                                                        <a href="#"
                                                            wire:click.prevent="$dispatch('canDeleteChildCategory',{{ $childCategory->id }})"
                                                            class="text-danger">
                                                            @include('icons.trash')
                                                        </a>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif
                                    @if (isset($childCategories) && count($childCategories) == 0)
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
                                @if (isset($childCategories) && count($childCategories) != 0)
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
                                    @if (isset($childCategories) && count($childCategories) >= 0)
                                        {{ $childCategories->links() }}
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
        Livewire.on('canDeleteChildCategory', (childCategoryId) => {
            if (confirm('Are you sure you want to delete this childCategory ?')) {
                Livewire.dispatch('deleteChildCategory', {
                    childCategoryId
                });
            }
        });
    </script>
@endpush
