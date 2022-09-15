<link rel="stylesheet" href="{{ asset('css/priority.css') }}">
<div class="priority">
    <div class="item priority">
        <div class="icon">
            <span class="material-icons-sharp">label</span>
        </div>
        <form action="" id="req-details-form">
            @csrf
            <input type="hidden" value="{{ csrf_token() }}" id="token">
            <div class="right">
                <h3>Priority level</h3>
                <select class="primary" id="priority">
                    <option value="default">-- Choose one -- </option>
                    <option value="high">High (Urgent)</option>
                    <option value="normal">Normal</option>
                    <option value="low">Low (Not Urgent)</option>
                </select>
            </div>
        </form>
    </div>
    <div class="item">
        @auth
            <button id="submit" form="req-details-form" name="submit" value="{{ auth()->user()->id }}">
                <span class="material-icons-sharp">send</span>
                <h3>Submit</h3>
            </button>
        @endauth
    </div>

</div>
