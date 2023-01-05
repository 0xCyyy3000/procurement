@props(['section', 'suppliers', 'purchasedOrders', 'requisitions', 'delivery_address', 'distributions', 'recipients', 'units', 'items'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/aclc tacloban.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <script src="{{ asset('js/jquery-3.6.1.min.js') }}"></script>
    <script src="{{ asset('js/menuBtn.js') }}" defer></script>
    <script src="{{ asset('js/createReqScript.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <title>{{ $section['title'] }}</title>
</head>

<body>
    {{-- @include('components.flash-message') --}}
    <div class="content">
        <div class="container">
            <aside id="sidebar">
                <div class="top">
                    <div class="logo">
                        <img src="{{ asset('images/aclc tacloban.png') }}" alt="" height="44">
                        <span class="">
                            <h2 class="fw-bolder">Procurement</h2>
                            <p>System</p>
                        </span>
                    </div>
                    <div class="close" id="close-btn">
                        <span class="material-icons-sharp">close</span>
                    </div>
                </div>
                <div class="sidebar">
                    @auth
                        <a href="/" @if ($section['page'] == 'dashboard') class="active" @endif>
                            <span class="material-icons-sharp">home</span>
                            <h3>Dashboard</h3>
                        </a>

                        <a href="/notifications" @if ($section['page'] == 'notifications') class="active" @endif>
                            <span class="material-icons-sharp">work_history</span>
                            <h3>Procurement Logs</h3>
                        </a>

                        <a href="/create_req" @if ($section['page'] == 'create_req') class="active" @endif>
                            <span class="material-icons-sharp">request_quote</span>
                            <h3>Create Requisition</h3>
                        </a>
                        <a href="/requisitions" @if ($section['page'] == 'requisitions') class="active" @endif>
                            <span class="material-icons-sharp">folder</span>
                            <h3>Requisitions</h3>
                        </a>
                        @if (auth()->user()->department <= 3)
                            <a href="/purchased_orders" @if ($section['page'] == 'purchased_orders') class="active" @endif>
                                <span class="material-icons-sharp">receipt_long</span>
                                <h3>Purchased Orders</h3>
                            </a>

                            <a href="/distributions" @if ($section['page'] == 'distributions') class="active" @endif>
                                <span class="material-icons-sharp">view_list</span>
                                <h3>Distributions</h3>
                            </a>

                            <a href="/suppliers" @if ($section['page'] == 'suppliers') class="active" @endif>
                                <span class="material-icons-sharp">contact_page</span>
                                <h3>Suppliers</h3>
                            </a>

                            <a href="/inventory" @if ($section['page'] == 'inventory') class="active" @endif>
                                <span class="material-icons-sharp">inventory</span>
                                <h3>Inventory</h3>
                            </a>
                        @endif
                        <a href="/settings" @if ($section['page'] == 'settings') class="active" @endif>
                            <span class="material-icons-sharp">settings</span>
                            <h3>Account Settings</h3>
                        </a>

                        <form action="/logout" method="POST">
                            @csrf
                            <button class="logout_btn"type="submit">
                                <span class="material-icons-sharp">logout</span>
                                <h3>Logout</h3>
                            </button>
                        </form>
                    @endauth
                </div>
            </aside>
        </div>
        <main>
            {{ $slot }}
        </main>
        <div class="right">
            <div class="top">
                @include('partials._userbar', ['user' => $section['user']])
            </div>
            <div class="middle">
                @if ($section['middle'] != null)
                    @if ($section['title'] == 'Requisition' and auth()->user()->department <= 3)
                        @include($section['middle'], [
                            'delivery_address' => $delivery_address,
                            'requisitions' => $requisitions,
                            'suppliers' => $suppliers,
                        ])
                    @elseif($section['title'] == 'Inventory' and auth()->user()->department <= 3)
                        @include($section['middle'], ['units' => $units, 'items' => $items])
                    @elseif($section['title'] == 'Distributions' and auth()->user()->department <= 3)
                        @include($section['middle'], [
                            'recipients' => $recipients,
                            'addresses' => $delivery_address,
                        ])
                    @else
                        @include($section['middle'])
                    @endif
                @endif
            </div>
            <div class="bottom">
                @if ($section['bottom'] != null)
                    @include($section['bottom'])
                @endif
            </div>
            @include('partials._pusher')
            @include('components.flash-message')
        </div>
    </div>
    @vite('resources/js/app.js')
</body>

</html>
