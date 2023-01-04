@vite('resources/js/app.js')

<style>
    .row {
        --bs-gutter-x: 0 !important;
        margin: 6% auto !important;
    }

    img {
        width: 15rem !important;
        margin: auto !important;
        margin-bottom: 1rem !important;
    }

    .primary {
        color: #7380ec !important;
    }
</style>

<title>Forbidden Page</title>
<link rel="shortcut icon" href="{{ asset('images/aclc tacloban.png') }}" type="image/x-icon">

<div class="row p-5">
    <img src="{{ asset('images/aclc tacloban.png') }}">
    <div class="container">
        <h3 class="fw-bold fs-1 text-center">Forbidden Page</h3>
        <p class="text-muted text-center fs-4">Only authorized users are allowed </p>
    </div>
    <div class="container w-25 d-flex justify-content-center">
        <button class="btn ps-4 pe-4">
            <a href="/" class="fs-5 primary text-decoration-none">Go back</a>
        </button>
    </div>
</div>
