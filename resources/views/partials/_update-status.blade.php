<link rel="stylesheet" href="{{ asset('css/update-status.css') }}">
<div class="requisition-info">
    <h2>Update Requisition</h2>
    <div class="details">
        <form id="update-status-form" method="POST" action="{{ route('requisition.update') }}">
            @csrf
            @method('PUT')
            <div class="detail mb-3">
                <div class="d-flex gap-3">
                    <span class="material-icons-sharp primary-variant">request_page</span>
                    <div class="right-side">
                        <h3>
                            <select id="reqs" name="requisition" required
                                class="@error('requisition') is-invalid @enderror">
                                <option value=""> -- Please Choose one -- </option>
                                @unless($requisitions->isEmpty())
                                    @foreach ($requisitions as $requisition)
                                        <option value="{{ $requisition->req_id }}">
                                            Req no.{{ $requisition->req_id }}
                                        </option>
                                    @endforeach
                                @endunless
                            </select>
                            @error('requisition')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </h3>
                        <small class="text-muted">Requisition No.</small>
                    </div>
                </div>
            </div>
            <div class="detail mb-3">
                <div class="d-flex gap-3">
                    <span class="material-icons-sharp primary-variant">store</span>
                    <div class="right-side">
                        <h3>
                            <select id="suppliers" name="" required
                                class="@error('supplier') is-invalid @enderror">
                                <option value="">-- Please Choose one --</option>
                                @unless($suppliers->isEmpty())
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                    @endforeach
                                @endunless
                            </select>
                            @error('supplier')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <input type="hidden" id="sup_x" name="supplier" value="">
                        </h3>
                        <small class="text-muted">Supplier</small>
                    </div>
                </div>
            </div>
            <div class="detail mb-3">
                <div class="d-flex gap-3">
                    <span class="material-icons-sharp primary-variant">local_shipping</span>
                    <div class="right-side">
                        <h3>
                            <select id="addxs" required class="@error('address') is-invalid @enderror">
                                <option value="">-- Please Choose one --</option>
                                @unless($delivery_address->isEmpty())
                                    @foreach ($delivery_address as $address)
                                        <option value="{{ $address->id }}">{{ $address->address }}</option>
                                    @endforeach
                                @endunless
                            </select>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <input type="hidden" id="addx" name="address" value="">
                        </h3>
                        <small class="text-muted">Delivery address</small>
                    </div>
                </div>
            </div>
            <div class="detail mb-3">
                <div class="d-flex gap-3">
                    <span class="material-icons-sharp primary-variant">person</span>
                    <div class="right-side">
                        <h3 id="evaluator"> {{ __('None yet') }} </h3>
                        <small class="text-muted">Evaluator from</small>
                    </div>
                </div>
            </div>
            @if (Auth::user()->department <= 3)
                <div class="detail mb-3" id="req-decision">
                    <div class="d-flex gap-3">
                        <div class="right-side w-100">
                            <h3>
                                <textarea name="" id="requisition-comment" cols="30" rows="2" placeholder="Message (optional)"></textarea>
                            </h3>
                        </div>
                    </div>
                    <div class="update d-flex gap-2">
                        <button type="submit" class="w-50 reject" name="decision" disabled value="rejected">
                            <span class="material-icons-sharp">cancel</span>
                            <h3>Reject</h3>
                        </button>
                        <button type="submit" class="w-50 release" name="decision" disabled value="released">
                            <span class="material-icons-sharp">check_circle</span>
                            <h3>Approve</h3>
                        </button>
                    </div>
                </div>

                <div class="detail d-none" id="callback">
                    <p class="text-center bg-primary-mine w-100 px-2 py-3 rounded-5 text-white" id="callback_response">
                    </p>
                </div>
            @endif
        </form>
    </div>
    <script>
        $(document).ready(function() {
            const BRANCH_MANAGER = '1';
            const SCHOOL_DIRECTOR = '2';
            const PROPERTY_CUSTODIAN = '3';

            const auth = '{{ Auth::user()->department }}';
            let reqs = new Array();

            $('#suppliers').prop('disabled', true);
            $('#addxs').prop('disabled', true);

            $.ajax({
                url: "{{ url('/api/get/requisitions') }}",
                type: 'GET',
                dataType: 'json',
                success: function(result) {
                    result.forEach(element => {
                        reqs.push(element);
                    });
                }
            });

            $(document).on('change', '#reqs', function() {
                $('#suppliers').prop('disabled', true);
                $('#suppliers').val('').change();
                $('#evaluator').text('None yet')
                $('#sup_x').val('');

                if ($(this).val() != '') {
                    reqs.map(element => {
                        if (element.req_id == this.value) {
                            if (element.stage == 0) {
                                $('#suppliers').prop('disabled', false);
                                $('#addxs').prop('disabled', false);
                                $('#approval').prop('disabled', false);
                            } else if (element.stage > 0 || element.stage == -1) {
                                $('#evaluator').text(element.evaluator.name + ' (' + element
                                    .evaluator.department + ')');
                                $('#suppliers').prop('disabled', true);
                                $('#suppliers').val(element.supplier);
                                $('#sup_x').val($('#suppliers option:selected').val());

                                $('#addxs').prop('disabled', true);
                                $('#addxs').val(element.delivery_address);
                                $('#addx').val($('#addxs option:selected').val());

                                if (element.stage == 3 || element.stage == -1) {
                                    $('.reject').prop('disabled', true);
                                    $('.release').prop('disabled', true);
                                } else {
                                    $('.reject').prop('disabled', false);
                                    $('.release').prop('disabled', false);
                                }


                            } else {
                                $('#suppliers').prop('disabled', false);
                            }

                            if (element.stage == 0 && auth != PROPERTY_CUSTODIAN) {
                                $('#req-decision').addClass('d-none');
                                $('#callback').removeClass('d-none');
                                $('#callback_response').text(
                                    "Waiting for Property Custodian's Approval");
                            } else if (element.stage == 1 && auth != SCHOOL_DIRECTOR) {
                                $('#req-decision').addClass('d-none');
                                $('#callback').removeClass('d-none');
                                $('#callback_response').text(
                                    "Waiting for School Director's Approval");
                            } else if (element.stage == 2 && auth != BRANCH_MANAGER) {
                                $('#req-decision').addClass('d-none');
                                $('#callback').removeClass('d-none');
                                $('#callback_response').text(
                                    "Waiting for Branch Manager's Approval");
                            } else {
                                $('#req-decision').removeClass('d-none');
                                $('#callback').addClass('d-none');
                                $('#callback_response').text('');
                            }
                        }
                    });
                }
            });

            $(document).on('change', '#suppliers', function() {
                if ($('#suppliers') != '') {
                    $('#sup_x').val($('#suppliers option:selected').val());
                }
            });

            $(document).on('change', '#addxs', function() {
                if ($('#addxs') != '') {
                    $('#addx').val($('#addxs option:selected').val());
                    $('.reject').prop('disabled', false);
                    $('.release').prop('disabled', false);
                } else {
                    $('.reject').prop('disabled', true);
                    $('.release').prop('disabled', true);
                }
            });
        });
    </script>
</div>
