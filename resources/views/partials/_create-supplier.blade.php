<link rel="stylesheet" href="{{ asset('css/create-supplier.css') }}">
<h2 class="create">Add New Supplier</h2>
<form action="{{ route('supplier.create') }}" method="POST" id="create-supplier">
    @csrf
    <div class="create-supplier">
        <div class="item create-supplier">
            <div class="right">
                <div class="mb-3">
                    <label for="supplier" class="form-label">Supplier</label>
                    <input type="text" class="form-control p-2 @error('supplier') is-invalid @enderror"
                        name="supplier" placeholder="e.g. Juan Supplier" value="{{ old('supplier') }}" required>
                    @error('supplier')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control p-2 @error('email') is-invalid @enderror" name="email"
                        placeholder="example@email.com" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control p-2 @error('address') is-invalid @enderror" name="address"
                        placeholder="Bldg, Street, City" value="{{ old('address') }}" required>
                    @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="contact_person" class="form-label">Contact person</label>
                    <input type="text" class="form-control p-2 @error('contact_person') is-invalid @enderror"
                        name="contact_person" placeholder="e.g. John Doe" value="{{ old('contact_person') }}" required>
                    @error('contact_person')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="number" min="0" class="form-control p-2 @error('phone') is-invalid @enderror"
                        name="phone" placeholder="11 digit mobile number" value="{{ old('phone') }}" required>
                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="item submit">
            @auth
                <button id="submit" form="create-supplier" type="submit" name="submit">
                    <span class="material-icons-sharp">add</span>
                    <h3>Add Supplier</h3>
                </button>
            @endauth
        </div>
    </div>
</form>
<script>
    var msg = "{{ Session::get('alert') }}";
    var exist = "{{ Session::has('alert') }}";
    if (exist) {
        alert(msg);
        location.reload();
    }
</script>
