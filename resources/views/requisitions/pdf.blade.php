<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $rl->rl_no }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10pt; color: #000; margin: 0; padding: 0; }
        
        /* HEADER / KOP SURAT */
        .header-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 10px; }
        .logo-container { width: 80px; text-align: left; vertical-align: top; }
        .logo { width: 60px; height: auto; }
        .company-info { text-align: center; vertical-align: middle; padding-right: 60px; }
        .company-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .company-address { font-size: 8pt; margin-top: 2px; line-height: 1.2; }

        /* JUDUL DOKUMEN */
        .doc-title { text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline; margin-top: 10px; }
        .doc-number { text-align: center; font-size: 10pt; margin-bottom: 20px; }

        /* INFO TABLE */
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-table td { padding: 3px 0; vertical-align: top; font-size: 9pt; }
        .label { font-weight: bold; width: 15%; }
        .sep { width: 2%; }
        .val { width: 33%; }

        /* ITEMS TABLE */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9pt; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 5px; }
        .items-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; text-transform: uppercase; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .col-no { width: 5%; }
        .col-item { width: 35%; }
        .col-part { width: 15%; }
        .col-qty { width: 8%; }
        .col-uom { width: 8%; }
        .col-stock { width: 8%; }
        .col-rem { width: 21%; }

        /* SIGNATURE SECTION */
        .signature-table { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .sig-box { width: 33%; text-align: center; vertical-align: top; }
        .sig-title { font-weight: bold; font-size: 9pt; margin-bottom: 50px; }
        .sig-name { font-weight: bold; text-decoration: underline; font-size: 9pt; }
        .sig-pos { font-size: 8pt; font-style: italic; margin-top: 2px; }
        
        /* STAMP */
        .approved-stamp {
            border: 2px solid green;
            color: green;
            padding: 2px 8px;
            display: inline-block;
            transform: rotate(-5deg);
            font-weight: bold;
            font-size: 9pt;
            border-radius: 4px;
            opacity: 0.8;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-container">
                @if($rl->company->logo_path && file_exists(public_path('images/' . $rl->company->logo_path)))
                    <img src="{{ public_path('images/' . $rl->company->logo_path) }}" class="logo">
                @else
                    <strong>LOGO</strong>
                @endif
            </td>
            <td class="company-info">
                <h1 class="company-name">{{ $rl->company->company_name }}</h1>
                <div class="company-address">
                    @if($rl->company->company_code == 'ASM')
                        Rukan Mangga Dua Square Blok H No. 22<br>Jl. Gunung Sahari Raya, Jakarta Utara<br>Email: asm@amarin.group
                    @elseif($rl->company->company_code == 'ACS')
                        Rukan Mangga Dua Square Blok H No. 22 (Crewing Div)<br>Jl. Gunung Sahari Raya, Jakarta Utara
                    @else
                        Gedung Caraka Lt. 3, Jl. Yos Sudarso No. 88<br>Tanjung Priok, Jakarta Utara
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-title">REQUISITION LETTER</div>
    <div class="doc-number">No: {{ $rl->rl_no }}</div>

    <table class="info-table">
        <tr>
            <td class="label">From Dept</td><td class="sep">:</td><td class="val">{{ $rl->requester->department->department_name }}</td>
            <td class="label">Priority</td><td class="sep">:</td>
            <td class="val">
                @if($rl->priority == 'Top Urgent')
                    <span style="color:red; font-weight:bold;">TOP URGENT</span>
                @else
                    {{ $rl->priority ?? 'Normal' }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Requester</td><td class="sep">:</td><td class="val">{{ $rl->requester->full_name }}</td>
            <td class="label">Required Date</td><td class="sep">:</td>
            <td class="val">{{ $rl->required_date ? \Carbon\Carbon::parse($rl->required_date)->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">To Dept</td><td class="sep">:</td><td class="val">Purchasing / Procurement</td>
            <td class="label">Status</td><td class="sep">:</td><td class="val">{{ $rl->status_flow }}</td>
        </tr>
        <tr>
            <td class="label">Subject</td><td class="sep">:</td><td class="val" colspan="4" style="font-weight:bold;">{{ $rl->subject }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-item">Item Description</th>
                <th class="col-part">Part No.</th>
                <th class="col-qty">Qty</th>
                <th class="col-uom">UOM</th>
                <th class="col-stock">Stock</th>
                <th class="col-rem">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rl->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <b>{{ $item->item_name }}</b>
                    @if($item->brand_preference) <br><i style="color:#555;">Brand: {{ $item->brand_preference }}</i> @endif
                </td>
                <td class="text-center">{{ $item->part_number ?? '-' }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-center">{{ $item->uom }}</td>
                <td class="text-center">{{ $item->stock_on_hand ?? 0 }}</td>
                <td>{{ $item->description }}</td>
            </tr>
            @endforeach
            @for($i = 0; $i < (5 - count($rl->items)); $i++)
            <tr><td style="padding:12px;">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td class="sig-box">
                <div class="sig-title">Requested By,</div>
                <div style="height: 40px;"></div> <div class="sig-name">{{ $rl->requester->full_name }}</div>
                <div class="sig-pos">{{ $rl->requester->position->position_name ?? 'Staff' }}</div>
            </td>

            @php
                $mgrApproval = $rl->approvalQueues->where('level_order', 1)->first();
            @endphp
            <td class="sig-box">
                <div class="sig-title">Reviewed By,</div>
                <div style="height: 40px; display: flex; align-items: center; justify-content: center;">
                    @if($mgrApproval && $mgrApproval->status == 'APPROVED')
                        <div class="approved-stamp">APPROVED<br><span style="font-size:7pt">{{ \Carbon\Carbon::parse($mgrApproval->approved_at)->format('d-M-Y') }}</span></div>
                    @endif
                </div>
                <div class="sig-name">{{ $mgrApproval->approver->full_name ?? '( Dept. Head )' }}</div>
                <div class="sig-pos">Dept. Head / Manager</div>
            </td>

            @php
                $dirApproval = $rl->approvalQueues->where('level_order', 2)->first();
            @endphp
            <td class="sig-box">
                <div class="sig-title">Approved By,</div>
                <div style="height: 40px; display: flex; align-items: center; justify-content: center;">
                    @if($dirApproval && $dirApproval->status == 'APPROVED')
                        <div class="approved-stamp">APPROVED<br><span style="font-size:7pt">{{ \Carbon\Carbon::parse($dirApproval->approved_at)->format('d-M-Y') }}</span></div>
                    @endif
                </div>
                <div class="sig-name">{{ $dirApproval->approver->full_name ?? '( Director )' }}</div>
                <div class="sig-pos">Director</div>
            </td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 7pt; color: #888;">
        Printed by System: {{ now()->format('d M Y H:i') }} | Page 1 of 1
    </div>

</body>
</html>