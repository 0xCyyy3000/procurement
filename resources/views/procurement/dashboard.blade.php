<x-layout :section='$section'>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <h1 class="">Dashboard</h1>
    <div class="date">
        <input type="date" name="" value="">
    </div>
    <div class="insights">
        <div class="sales">
            <span class="material-icons-sharp">analytics</span>
            <div class="middle">
                <div class="left">
                    <h3>Total Expsenses</h3>
                    <h1>₱35,490</h1>
                </div>
                <div class="date-scope">
                    <small>Last mm/dd/yy</small>
                </div>
            </div>
        </div>
        <div class="requisitions">
            <span class="material-icons-sharp">folder</span>
            <div class="middle">
                <div class="left">
                    <h3>Requisitions</h3>
                    <h1>25</h1>
                </div>
                <div class="date-scope">
                    <small>Last mm/dd/yy</small>
                </div>
            </div>
        </div>
        <div class="quotations">
            <span class="material-icons-sharp">inventory</span>
            <div class="middle">
                <div class="left">
                    <h3>Total Items</h3>
                    <h1>10</h1>
                </div>
                <div class="date-scope">
                    <small>Last mm/dd/yy</small>
                </div>
            </div>
        </div>
    </div>

    <div class="recent-orders">
        <h2>Recent Purchase Orders</h2>
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>Purchase Order #</th>
                        <th>Supplier</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>123</td>
                        <td>John Doe</td>
                        <td>Due</td>
                        <td class="warning">Pending</td>
                        <td><button class="primary" type="button" onclick="sayHi()">View Details</button></td>
                    </tr>
                    <tr>
                        <td>123</td>
                        <td>John Doe</td>
                        <td>Refunded</td>
                        <td class="danger">Cancelled</td>
                        <td><button class="primary" type="button" onclick="sayHi()">View Details</button></td>
                    </tr>
                    <tr>
                        <td>123</td>
                        <td>John Doe</td>
                        <td>Paid</td>
                        <td class="success">Fulfilled</td>
                        <td><button class="primary" type="button" onclick="sayHi()">View Details</button></td>
                    </tr>
                    <tr>
                        <td>123</td>
                        <td>John Doe</td>
                        <td>Due</td>
                        <td class="warning">Pending</td>
                        <td><button class="primary" type="button" onclick="sayHi()">View Details</button></td>
                    </tr>
                    <tr>
                        <td>123</td>
                        <td>John Doe</td>
                        <td>Due</td>
                        <td class="warning">Pending</td>
                        <td><button class="primary" type="button" onclick="sayHi()">View Details</button></td>
                    </tr>
                    <tr>
                        <td>123</td>
                        <td>John Doe</td>
                        <td>Due</td>
                        <td class="warning">Pending</td>
                        <td><button class="primary" type="button" onclick="sayHi()">View Details</button></td>
                    </tr>
                    <tr>
                        <td>123</td>
                        <td>John Doe</td>
                        <td>Due</td>
                        <td class="warning">Pending</td>
                        <td><button class="primary" type="button" onclick="sayHi()">View Details</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <a href="#">Show all</a>
    </div>
</x-layout>
