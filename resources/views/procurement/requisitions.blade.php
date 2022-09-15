<x-layout :section='$section'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">
    <h1>Requisitions</h1>
    <div class="date">
        <input type="date" name="" value="">
    </div>

    <div class="items-table"id="items-table">
        <table>
            <thead>
                <th>Req No.</th>
                <th>Priority</th>
                <th width="">Description</th>
                <th>Status</th>
            </thead>
            <tbody>
                @unless($requisitions->isEmpty())
                    @foreach ($requisitions as $requisition)
                        @php
                            $status = Str::upper($requisition->status);
                        @endphp
                        <tr>
                            <td>{{ $requisition->req_id }}</td>
                            <td>{{ $requisition->priority }}</td>
                            <td>{{ $requisition->description }}</td>
                            <td
                                @if ($status == 'PENDING') class="warning"
                                    @elseif($status == 'APPROVED') class="success"
                                    @else class="danger" @endif>
                                {{ $requisition->status }}
                            </td>
                            <td> <button class="primary" type="button" value="{{ $requisition->req_id }}"
                                    onclick="clicked(this.value)">View details</button>
                            </td>
                            <td><button class="text-muted copy"> <span>(Copy)</span></button></td>
                        </tr>
                    @endforeach
                @endunless
            </tbody>
        </table>
    </div>
    <script>
        function clicked(value) {
            console.log(value);
        }
    </script>
</x-layout>
