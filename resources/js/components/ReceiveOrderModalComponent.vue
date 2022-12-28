<template>
    <!-- Receive Modal -->
    <!-- <div id="receive-modal" class="copy-modal d-flex">
        <div class="copy-modal-content">
            <h2 class="w-100 m-auto text-center">Receiving Purchased Order</h2>
            <h6 class="m-auto w-50 m-auto mt-2 text-center">Select an order, and receive.
                Selected items will be added to your inventory.</h6>

            <div class="input-group mt-4 mb-3 d-flex rounded">
                <label class="input-group-text text-center bg-dark-variant text-dark p-2" for="inputGroupSelect01">
                    Purchased Orders
                </label>
                <select class="form-select p-2" id="receive-purchase-orders">
                    @unless($purchasedOrders->isEmpty())
                    <option value="default">Choose one</option>
                    @foreach ($purchasedOrders as $purchasedOrder)
                    <option value="{{ $purchasedOrder->id }}" class="w-100 m-auto text-center">PO
                        #{{ $purchasedOrder -> id }}
                    </option>
                    @endforeach
                    @endunless
                </select>
                <div class="input-group-text gap-1">
                    <input class="form-check-input mt-0 p-2 ms-2" type="checkbox" value="" name="" id="received">
                    <label class="input-group-text text-center bg-dark-variant text-dark p-2" for="received">
                        Received
                    </label>
                </div>
            </div>

            <div class="input-group mt-3 mb-3 d-flex rounded">
                <label class="input-group-text text-center bg-dark-variant text-dark p-2" for="inputGroupSelect01">Order
                    items</label>
                <select class="form-select p-2 w-25" id="inputGroupSelect01">
                    <option selected>Choose item</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div>
            <div class="input-group rounded mb-4">
                <input type="text" class="form-control p-2 w-75" placeholder="Qty">
                <span class="input-group-text p-2">out of x</span>
                <button class="btn text-white p-2 bg-warning-mine" type="button" id="button-addon2">Max</button>
            </div>

            <div class="action w-100 mb-0 m-auto d-flex justify-content-center">
                <button class="go-back w-25" type="button" id="go-back">
                    <h3>Go back</h3>
                </button>
                <button class="confirm w-25" type="button">
                    <h3>Confirm</h3>
                </button>
            </div>
        </div>
    </div> -->
</template>

<script>
// var go_back = document.getElementById("go-back");

// const receiveModal = document.getElementById("receive-modal");

// go_back.onclick = function () {
//     receiveModal.style.display = "none";
// }

// window.onclick = function (event) {
//     if (event.target == receiveModal) receiveModal.style.display = "none";
// }

export default {
    data() {
        return {

        }
    },
}
</script>