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
                <select class="primary" id="priority" required>
                    <option value="">-- Choose one -- </option>
                    <option value="High">High (Urgent)</option>
                    <option value="Normal">Normal</option>
                    <option value="Low">Low (Not Urgent)</option>
                </select>
            </div>
        </form>
    </div>
    <div class="item submit">
        @auth
            <button id="submit" form="req-details-form" name="submit" value="{{ auth()->user()->name }}">
                <input type="hidden" id="userId" value="{{ auth()->user()->id }}">
                <span class="material-icons-sharp">send</span>
                <h3>Submit</h3>
            </button>
        @endauth
    </div>
</div>
