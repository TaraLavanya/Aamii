<div>
    <div class="col-md-10 ms-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">New Product</h3>
                <a href="{{ route('show.product') }}" class="btn btn-secondary">Go To Summary </a>
            </div>
            <div class="card-body">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row col-md-12 ">
                        <div class="mb-3 col-md-4 mt-2">
                            <label for="file">Upload Product Image</label>
                            <div>
                                <input @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('image') ? true : false,
                                ]) type="file" wire:model="image" id="file"
                                    multiple required>
                                @error('image')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">Name</label>
                            <div>
                                <input type="text" @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('name') ? true : false,
                                ]) placeholder="name" wire:model="name">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label class="form-label required">Stock Quantity</label>
                            <div>
                                <input type="text" @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('stock_qty') ? true : false,
                                ]) placeholder="stock qty"
                                    wire:model="stock_qty">
                                @error('stock_qty')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label class="form-label required">Price</label>
                            <div>
                                <input type="text" @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('price') ? true : false,
                                ]) wire:model ="price"
                                    placeholder="price">
                                @error('stock_qty')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12 ">
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">Ingredient</label>
                            <div>
                                <input type="text" @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('ingredient') ? true : false,
                                ]) placeholder="ingredient"
                                    wire:model="ingredient">
                                @error('ingredient')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">Bar Code</label>
                            <div>
                                <input type="text" @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('bar_code') ? true : false,
                                ]) placeholder="bar_code">
                                @error('bar_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">HSN code</label>
                            <div>
                                <input type="text" @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('hsn_code') ? true : false,
                                ]) placeholder="hsn_code">
                                @error('hsn_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">Category</label>
                            <div>
                                <select @class([
                                    'form-select',
                                    'is-invalid' => $errors->has('category_id') ? true : false,
                                ]) wire:model.live="category_id">
                                    <option>Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value='{{ $category->id }}'>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">Sub Category</label>
                            <div>
                                <select @class([
                                    'form-select',
                                    'is-invalid' => $errors->has('sub_category_id') ? true : false,
                                ]) wire:model.live="sub_category_id">
                                    <option>Select Sub Category</option>
                                    @foreach ($sub_categories as $sub_category)
                                        <option value='{{ $sub_category->id }}'>{{ $sub_category->name }}</option>
                                    @endforeach
                                </select>
                                @error('sub_category_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">Child Category</label>
                            <div>
                                <select @class([
                                    'form-select',
                                    'is-invalid' => $errors->has('child_category_id') ? true : false,
                                ]) wire:model="child_category_id">
                                    <option>Select Child Category</option>
                                    @foreach ($child_categories as $child_category)
                                        <option value='{{ $child_category->id }}'>{{ $child_category->name }}</option>
                                    @endforeach
                                </select>
                                @error('child_category_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">Short Description</label>
                            <div>
                                <textarea @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('short_description') ? true : false,
                                ]) name="short_description"></textarea>
                                @error('short_description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">Benefits</label>
                            <div>
                                <textarea @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('benefits') ? true : false,
                                ]) name="benefits"></textarea>
                                @error('benefits')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label required">How to use</label>
                            <div>
                                <textarea @class([
                                    'form-control',
                                    'is-invalid' => $errors->has('how_to_use') ? true : false,
                                ]) name="how_to_use"></textarea>
                                @error('how_to_use')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
