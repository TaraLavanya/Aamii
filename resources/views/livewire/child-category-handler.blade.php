<div>
    <h4>{{ isset($childCategoryId) ? 'Edit child category' : 'New child category' }}</h4>
    <div class="card">
        <form wire:submit={{ isset($childCategoryId) ? 'update' : 'create' }}>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row row-cards">

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Name</label>
                                    <input type="text" id ="name" @class([
                                        'form-control',
                                        'is-invalid' => $errors->has('childCategory.name') ? true : false,
                                    ]) placeholder="Name"
                                        wire:model="childCategory.name">
                                    @error('childCategory.name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- @json($errors->all()) --}}
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Category</label>
                                    <select @class([
                                        'form-select',
                                        'is-invalid' => $errors->has('childCategory.category_id') ? true : false,
                                    ]) wire:model.live="childCategory.category_id"
                                        id="category_id">
                                        <option value="">Select Catagory</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('childCategory.category_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="subcategory_id"> Sub Category</label>
                                    <select @class([
                                        'form-select',
                                        'is-invalid' => $errors->has('childCategory.subcategory_id') ? true : false,
                                    ]) wire:model="childCategory.subcategory_id"
                                        id="subcategory_id">
                                        <option value="">Select Sub Catagory</option>
                                        @foreach ($subCategories as $subCategory)
                                            <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('childCategory.subcategory_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <label class="form-check-label ">
                                            Is Active
                                            <input class="form-check-input " type="checkbox"
                                                wire:model.live="childCategory.is_active">
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                @if ($childCategoryId)
                    <a href={{ route('child-category') }} class="text-danger me-2"> Cancel </a>
                @else
                    <a href=# wire:click.prevent ="resetFields" class="text-danger me-2"> Reset </a>
                @endif
                <button type="submit"
                    class="btn btn-primary ">{{ isset($childCategoryId) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>
