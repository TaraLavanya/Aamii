<div>
    <h4>{{ isset($countryId) ? 'Edit country' : 'New country' }}</h4>
    <div class="card">
        <form wire:submit={{ isset($countryId) ? 'update' : 'create' }}>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row row-cards">

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Name</label>
                                    <input type="text" id ="name" @class([
                                        'form-control',
                                        'is-invalid' => $errors->has('country.name') ? true : false,
                                    ]) placeholder="Name"
                                        wire:model="country.name">
                                    @error('country.name')
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
                                                wire:model.live="country.is_active">
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                @if ($countryId)
                    <a href={{ route('country') }} class="text-danger me-2"> Cancel </a>
                @else
                    <a href=# wire:click.prevent ="resetFields" class="text-danger me-2"> Reset </a>
                @endif
                <button type="submit" class="btn btn-primary ">{{ isset($countryId) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>
