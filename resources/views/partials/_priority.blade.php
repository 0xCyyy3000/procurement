<link rel="stylesheet" href="{{ asset('css/priority.css') }}">
<div class="priority">
    <div class="item priority">
        <div class="icon">
            <span class="material-icons-sharp">label</span>
        </div>
        <div class="right">
            <h3>Priority level</h3>
            <select class="primary" id="priority-level">
                <option value="default">-- Choose one -- </option>
                <option>High (Urgent)</option>
                <option>Normal</option>
                <option>Low (Not Urgent)</option>
            </select>
        </div>
    </div>
    <div class="item submit">
        <button type="submit" form="req-details-form" name="button">
            <span class="material-icons-sharp">send</span>
            <h3>Submit</h3>
        </button>
    </div>

</div>
