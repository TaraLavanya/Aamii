<div>
    <h4>{{ isset($categoryId) ? 'Edit category' : 'New category' }}</h4>
    <div class="card">
        <form wire:submit={{ isset($categoryId) ? 'update' : 'create' }}>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row row-cards">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Name</label>
                                    <input type="text" id ="name" @class([
                                        'form-control',
                                        'is-invalid' => $errors->has('category.name') ? true : false,
                                    ]) placeholder="Name"
                                        wire:model="category.name">
                                    @error('category.name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <label class="form-check-label ">
                                            Is Active
                                            <input class="form-check-input " type="checkbox"
                                                wire:model.live="category.is_active">
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                @if ($categoryId)
                    <a href={{ route('category') }} class="text-danger me-2"> Cancel </a>
                @else
                    <a href=# wire:click.prevent ="resetFields" class="text-danger me-2"> Reset </a>
                @endif
                <button type="submit" class="btn btn-primary ">{{ isset($categoryId) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>
