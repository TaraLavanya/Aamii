<div>
    <h4>{{ isset($cityId) ? 'Edit city' : 'New city' }}</h4>
    <div class="card">
        <form wire:submit={{ isset($cityId) ? 'update' : 'create' }}>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row row-cards">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Name</label>
                                    <input type="text" id ="name" @class([
                                        'form-control',
                                        'is-invalid' => $errors->has('city.name') ? true : false,
                                    ]) placeholder="Name"
                                        wire:model="city.name">
                                    @error('city.name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">State</label>
                                    <select class="form-select" wire:model="city.state_id" id="state_id">
                                        <option value="">Select Country</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city.state_idf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Country</label>
                                    <select class="form-select" wire:model="city.country_id" id="country_id">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city.country_id')
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
                                                wire:model.live="city.is_active">
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                @if ($cityId)
                    <a href={{ route('city') }} class="text-danger me-2"> Cancel </a>
                @else
                    <a href=# wire:click.prevent ="resetFields" class="text-danger me-2"> Reset </a>
                @endif
                <button type="submit" class="btn btn-primary ">{{ isset($cityId) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>
