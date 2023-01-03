<x-layout :section='$section'>
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <h1 class="mt-4 mb-5">Account Settings</h1>
    <div class="account-settings d-flex justify-content-between align-items-center gap-2">
        <div class="col-3">
            <img class="rounded-circle m-auto card-img"
                src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/aclc tacloban.png') }}"alt="profile">
            <form action="{{ route('user.upload-photo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="photo" id="image" class="mt-3 @error('photo') is-invalid @enderror"
                    style="width: 98% !important;" required>
                @error('photo')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <button class="bg-transparent text-center primary p-2 w-100 rounded m-auto mt-2">Upload photo</button>
            </form>
        </div>
        <div class="col-9 p-3 m-auto">
            <label class="fs-5 mb-3">Account Information</label>
            <form action="{{ route('user.update') }}" method="POST" class="row" id="profile_form">
                @csrf
                @method('PUT')
                <div class="col-md-5 mb-3 me-4">
                    <label for="name" class="fs-6 text-muted">Name</label>
                    <input type="text" class="form-control-plaintext p-2 rounded @error('name') is-invalid @enderror"
                        id="name" name="name" placeholder="Your name" value="{{ Auth::user()->name }}" readonly>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-md-5 mb-3">
                    <label for="email" class="fs-6 text-muted">Email</label>
                    <input type="email" readonly
                        class="form-control-plaintext p-2 rounded @error('email') is-invalid @enderror" id="email"
                        name="email" placeholder="name@example.com" value="{{ Auth::user()->email }}">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row">
                    <label for="department" class="fs-6 form-label text-muted mb-1">Department</label>
                    <select id="department" name="department"
                        class="form-select p-2 w-75 @error('department') is-invalid @enderror" disabled>
                        @foreach ($departments as $department)
                            @if ($department->id > 3)
                                <option value="{{ $department->id }}">{{ $department->department }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('department')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row mt-4 align-items-center">
                    <div class="form-check form-switch col-10">
                        <input class="form-check-input" type="checkbox" role="switch" id="decision_settings">
                        <label class="form-check-label fs-5 fw-normal text-muted ms-2" for="decision_settings">
                            Edit Account Information
                        </label>
                    </div>
                    <div class="col save-changes d-none">
                        <button class="btn p-2 bg-primary-mine text-white" type="submit" id="save_changes"
                            form="profile_form">
                            Save changes</button>
                    </div>
                </div>
            </form>
            <div class="container mt-5">
                <label class="fs-5">Change password</label>
            </div>

            <form action="{{ route('user.change-password') }}" method="POST" id="password_form">
                @csrf
                <div class="col-md-5 mt-3 mb-3 me-4">
                    <label for="current_password" class="form-label fs-6 text-muted">Current password</label>
                    <input type="password"
                        class="form-control p-2 rounded  @error('current_password') is-invalid @enderror"
                        name="current_password" id="current_password" placeholder="Enter current password">
                    @error('current_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="row mt-3 password_container">
                    <div class="col-md-5 mb-3 me-4">
                        <label for="password" class="form-label fs-6 text-muted">New password</label>
                        <input type="password" class="form-control p-2 rounded  @error('password') is-invalid @enderror"
                            name="password" id="password" placeholder="Enter new password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <label for="confirm" class="form-label fs-6 text-muted">Confrim password</label>
                        <input type="password" class="form-control p-2 rounded" name="password_confirmation"
                            id="confirm" placeholder="Re-type your password">
                    </div>

                    <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                    <div class="container bg-transparent">
                        <button class="p-2 rounded px-3 me-2" type="button" id="cancel_password">Cancel</button>
                        <button class="p-2 rounded bg-primary-mine text-white" type="submit" id="save_password">
                            Save password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var msg = "{{ Session::get('alert') }}";
            var exist = "{{ Session::has('alert') }}";
            if (exist) {
                alert(msg);
            }

            let editing = false;
            const name = '{{ Auth::user()->name }}';
            const email = '{{ Auth::user()->email }}';
            const departmentID = "{{ Auth::user()->department }}"

            $('#department').val(departmentID);

            $(document).on('click', '#decision_settings', function() {
                editing = !editing;
                $('.save-changes').toggleClass('d-none');;
                if (editing) {
                    $('#name').prop('readonly', false);
                    $('#email').prop('readonly', false)
                    $('#department').prop('disabled', false);
                } else {
                    $('#department').val(departmentID);
                    $('#department').prop('disabled', true);

                    $('#name').prop('readonly', true);
                    $('#name').val(name);

                    $('#email').prop('readonly', true)
                    $('#email').val(email);
                }
            });

            $(document).on('click', '#cancel_password', function() {
                $('#password').val('');
                $('#confirm').val('');
            });
        });
    </script>
</x-layout>
