@props(['section', 'suppliers', 'purchasedOrders'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <script src="{{ asset('js/jquery-3.6.1.min.js') }}"></script>
    <title>{{ $section['title'] }}</title>
</head>

<body>
    <div class="content">
        <div class="container">
            <aside id="sidebar">
                <div class="top">
                    <div class="logo">
                        <img src="{{ asset('images/logo.svg') }}" alt="">
                        <span class="">
                            <h2>Procurement</h2>
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
                        <a href="/create_req" @if ($section['page'] == 'create_req') class="active" @endif>
                            <span class="material-icons-sharp">request_quote</span>
                            <h3>Create Requisition</h3>
                        </a>
                        <a href="/requisitions" @if ($section['page'] == 'requisitions') class="active" @endif>
                            <span class="material-icons-sharp">folder</span>
                            <h3>Requisitions</h3>
                        </a>
                        @if (auth()->user()->department == 'Admin')
                            <a href="/purchased_orders" @if ($section['page'] == 'purchased_orders') class="active" @endif>
                                <span class="material-icons-sharp">receipt_long</span>
                                <h3>Purchased Orders</h3>
                            </a>
                        @endif

                        @if (auth()->user()->department == 'Admin' || auth()->user()->department == 'Property Custodian')
                            <a href="/" @if ($section['page'] == 'distributions') class="active" @endif>
                                <span class="material-icons-sharp">view_list</span>
                                <h3>Distributions</h3>
                            </a>
                        @endif

                        <a href="/supplier" @if ($section['page'] == 'supplier') class="active" @endif>
                            <span class="material-icons-sharp">contact_page</span>
                            <h3>Supplier</h3>
                        </a>

                        <a href="/" @if ($section['page'] == 'inventory') class="active" @endif>
                            <span class="material-icons-sharp">inventory</span>
                            <h3>Inventory</h3>
                        </a>

                        <a href="/" @if ($section['page'] == 'settings') class="active" @endif>
                            <span class="material-icons-sharp">settings</span>
                            <h3>Settings</h3>
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
                @include('partials._userbar')
            </div>
            <div class="middle">
                @if ($section['middle'] != null)
                    @if ($section['title'] == 'Requisition' and $section['userDepartment'] == 'Admin')
                        @include($section['middle'], [
                            'suppliers' => $suppliers,
                            'purchasedOrders' => $purchasedOrders,
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
        </div>
    </div>
    <x-flash-message />
    <script src="{{ asset('js/menuBtn.js') }}"></script>
    <script src="{{ asset('js/createReqScript.js') }}"></script>
</body>

</html>
