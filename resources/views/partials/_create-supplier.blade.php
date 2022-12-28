<link rel="stylesheet" href="{{ asset('css/create-supplier.css') }}">
<h2 class="create">Add New Supplier</h2>
<form action="{{ route('supplier.create') }}" method="POST" id="create-supplier">
    @csrf
    <div class="create-supplier">
        <div class="item create-supplier">
            <div class="right">
                <div class="mb-3">
                    <label for="supplier" class="form-label">Supplier</label>
                    <input type="text" class="form-control p-2" name="supplier" placeholder="e.g. Juan Supplier"
                        required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control p-2" name="email" placeholder="example@email.com"
                        required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control p-2" name="address" placeholder="Bldg, Street, City"
                        required>
                </div>
                <div class="mb-3">
                    <label for="contact-person" class="form-label">Contact person</label>
                    <input type="text" class="form-control p-2" name="contact_person" placeholder="e.g. John Doe"
                        required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="number" min="0" class="form-control p-2" name="phone"
                        placeholder="11 digit mobile number" required>
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
