<div>
    <div class="page-body">
        <div class="container-xl">
            @include('includes.alerts')
            <div class="row">
                <div class="col-lg-4 ">
                    @livewire('sub-category-handler', ['subCategoryId' => $subCategoryId])
                </div>
                <div class="col-lg-8 ">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div>
                            <h4 class="text">List all sub Categories</h4>
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
                                        <th>Is Active</th>
                                        <th colspan="2" class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($subCategories) && count($subCategories) > 0)
                                        @foreach ($subCategories as $subCategoryIndex => $subCategory)
                                            <tr wire:key='item-{{ $subCategory->id }}'>

                                                <td>
                                                    {{ $subCategoryIndex + $subCategories->firstItem() }}
                                                </td>

                                                <td>
                                                    <div class="text-capitalize">{{ $subCategory->name }}</div>
                                                </td>
                                                <td>
                                                    <div class="text-capitalize">
                                                        {{ $subCategory?->category?->name ?? '--' }}
                                                    </div>
                                                </td>

                                                <td>
                                                    <div @class([
                                                        'badge',
                                                        'me-1',
                                                        'bg-success' => $subCategory->is_active,
                                                        'bg-danger' => !$subCategory->is_active,
                                                    ])></div>
                                                    {{ $subCategory->is_active == 1 ? 'Active' : 'Inactive' }}

                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a
                                                            href="{{ route('sub-category', ['subCategoryId' => $subCategory->id, 'page' => $this->paginators['page'], 'pp' => $this->perPage]) }}">
                                                            @include('icons.edit')
                                                        </a>

                                                        <a href="#"
                                                            wire:click.prevent="$dispatch('canDeleteSubCategory',{{ $subCategory->id }})"
                                                            class="text-danger">
                                                            @include('icons.trash')
                                                        </a>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif
                                    @if (isset($subCategories) && count($subCategories) == 0)
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
                                @if (isset($subCategories) && count($subCategories) != 0)
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
                                    @if (isset($subCategories) && count($subCategories) >= 0)
                                        {{ $subCategories->links() }}
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
        Livewire.on('canDeleteSubCategory', (subCategoryId) => {
            if (confirm('Are you sure you want to delete this subCategory ?')) {
                Livewire.dispatch('deleteSubCategory', {
                    subCategoryId
                });
            }
        });
    </script>
@endpush
