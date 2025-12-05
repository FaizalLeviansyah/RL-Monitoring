<!DOCTYPE html>
<html>
<head>
    <title>Requisition Letter - {{ $rl->rl_no }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f0f0f0; }
        .info-table { width: 100%; border: none; margin-bottom: 20px; }
        .info-table td { border: none; padding: 2px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($rl->company->company_name ?? 'COMPANY NAME') }}</h1>
        <h3>REQUISITION LETTER</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>No. RL</strong></td>
            <td width="35%">: {{ $rl->rl_no }}</td>
            <td width="15%"><strong>Date</strong></td>
            <td width="35%">: {{ \Carbon\Carbon::parse($rl->request_date)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Requester</strong></td>
            <td>: {{ $rl->requester->full_name }}</td>
            <td><strong>Department</strong></td>
            <td>: {{ $rl->requester->department->department_name }}</td>
        </tr>
        <tr>
            <td><strong>Subject</strong></td>
            <td colspan="3">: {{ $rl->subject }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Item Name</th>
                <th>Description</th>
                <th width="10%">Qty</th>
                <th width="10%">UOM</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rl->items as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->description }}</td>
                <td style="text-align: center;">{{ $item->qty }}</td>
                <td style="text-align: center;">{{ $item->uom }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; text-align: center;" width="33%">
                    Created By,<br><br><br><br>
                    <strong>{{ $rl->requester->full_name }}</strong>
                </td>
                <td style="border: none; text-align: center;" width="33%">
                    Approved By,<br><br><br><br>
                    <strong>( Manager )</strong>
                </td>
                <td style="border: none; text-align: center;" width="33%">
                    Acknowledged By,<br><br><br><br>
                    <strong>( Director )</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
