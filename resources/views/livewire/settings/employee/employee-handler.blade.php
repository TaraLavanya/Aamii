<div>
    @if (isset($employee['id']))
        <h4>Edit Employee <a class="btn btn-outline-primary btn-sm ms-3" href="{{ route('employees.index') }}">Add New</a>
        </h4>
    @else
        <h4>Add new Employee</h4>
    @endif
    <div class='card'>
        @if (isset($employee['id']))
            <form wire:submit="update">
            @else
                <form wire:submit="create">
        @endif
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row row-cards">
                        <div class="col-md-12">
                            <div class="mb-1">
                                <label class="form-label required">User ID</label>
                                <input type="text" @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('employee.user_id') ? true : false,
                                ]) placeholder="Enter employee no"
                                    wire:model="employee.user_id">
                                @error('employee.user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-1">
                                <label class="form-label required">Name</label>
                                <input type="text" @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('employee.name') ? true : false,
                                ]) placeholder="Enter employee Name"
                                    wire:model="employee.name">
                                @error('employee.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-1">
                                <label class="form-label required">Email</label>
                                <input type="text" @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('employee.email') ? true : false,
                                ])
                                    placeholder="Enter Email Address Employee " wire:model="employee.email">
                                @error('employee.email')
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
                                            wire:model.live="employee.is_active">
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('employees.index') }}" class="text-danger me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        </form>
    </div>
</div>
