<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $rl->rl_no }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11pt; color: #000; margin: 0; padding: 0; }
        
        /* HEADER / KOP SURAT */
        .header-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 10px; }
        .logo-container { width: 100px; text-align: left; vertical-align: top; }
        .logo { width: 80px; height: auto; }
        .company-info { text-align: center; vertical-align: middle; padding-right: 80px; } /* Padding kanan biar text center visual pas */
        .company-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .company-address { font-size: 9pt; margin-top: 5px; line-height: 1.2; }

        /* JUDUL DOKUMEN */
        .doc-title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-top: 20px; }
        .doc-number { text-align: center; font-size: 11pt; margin-bottom: 25px; }

        /* INFO TABLE (Header RL) */
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; font-size: 10pt; }
        .label { font-weight: bold; width: 15%; }
        .sep { width: 2%; }
        .val { width: 40%; }

        /* ITEMS TABLE */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 10pt; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 6px; }
        .items-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .col-no { width: 5%; }
        .col-item { width: 45%; }
        .col-qty { width: 10%; }
        .col-uom { width: 10%; }
        .col-rem { width: 30%; }

        /* SIGNATURE SECTION */
        .signature-table { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        .sig-box { width: 33%; text-align: center; vertical-align: top; }
        .sig-title { font-weight: bold; font-size: 10pt; margin-bottom: 60px; }
        .sig-name { font-weight: bold; text-decoration: underline; font-size: 10pt; }
        .sig-pos { font-size: 9pt; font-style: italic; }
        
        /* APPROVAL STAMP (Visual) */
        .approved-stamp {
            border: 2px solid green;
            color: green;
            padding: 5px 10px;
            display: inline-block;
            transform: rotate(-5deg);
            font-weight: bold;
            font-size: 10pt;
            border-radius: 5px;
            margin-top: 20px;
            margin-bottom: 20px;
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
                        Rukan Mangga Dua Square Blok H No. 22<br>
                        Jl. Gunung Sahari Raya, Jakarta Utara - Indonesia<br>
                        Telp: (021) 6231 1234 | Email: info@amarin.group
                    @elseif($rl->company->company_code == 'ACS')
                        Rukan Mangga Dua Square Blok H No. 22 (Crewing Div)<br>
                        Jl. Gunung Sahari Raya, Jakarta Utara<br>
                        License: SIUPPAK No. 123.456.789
                    @else
                        Gedung Caraka Lt. 3, Jl. Yos Sudarso No. 88<br>
                        Tanjung Priok, Jakarta Utara<br>
                        Logistics & Freight Forwarding Services
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-title">REQUISITION LETTER</div>
    <div class="doc-number">Ref No: {{ $rl->rl_no }}</div>

    <table class="info-table">
        <tr>
            <td class="label">From Dept</td><td class="sep">:</td><td class="val">{{ $rl->requester->department->department_name }}</td>
            <td class="label">Date</td><td class="sep">:</td><td class="val">{{ \Carbon\Carbon::parse($rl->request_date)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Requester</td><td class="sep">:</td><td class="val">{{ $rl->requester->full_name }}</td>
            <td class="label">To Dept</td><td class="sep">:</td><td class="val">Purchasing / Procurement</td>
        </tr>
        <tr>
            <td class="label">Subject</td><td class="sep">:</td><td class="val" colspan="4">{{ $rl->subject }}</td>
        </tr>
        @if($rl->remark)
        <tr>
            <td class="label">Note</td><td class="sep">:</td><td class="val" colspan="4">{{ $rl->remark }}</td>
        </tr>
        @endif
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-item">Description / Item Name</th>
                <th class="col-qty">Qty</th>
                <th class="col-uom">UOM</th>
                <th class="col-rem">Stock / Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rl->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <b>{{ $item->item_name }}</b>
                    @if($item->description) <br><i style="font-size: 9pt;">{{ $item->description }}</i> @endif
                </td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-center">{{ $item->uom }}</td>
                <td>
                    @if($item->status_item == 'SUPPLIED') 
                        <span style="color: green;">(Supplied)</span>
                    @endif
                </td>
            </tr>
            @endforeach
            @for($i = 0; $i < (5 - count($rl->items)); $i++)
            <tr>
                <td style="padding: 15px;">&nbsp;</td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td class="sig-box">
                <div class="sig-title">Requested By,</div>
                
                <div class="sign-space">
                    </div>

                <div class="sig-name">{{ $rl->requester->full_name }}</div>
                <div class="sig-pos">{{ $rl->requester->position->position_name ?? 'Staff' }}</div>
            </td>

            @php
                // Cari data approval level 1
                $mgrApproval = $rl->approvalQueues->where('level_order', 1)->first();
            @endphp
            <td class="sig-box">
                <div class="sig-title">Reviewed By,</div>

                <div class="sign-space">
                    @if($mgrApproval && $mgrApproval->status == 'APPROVED')
                        <div class="approved-stamp">APPROVED<br><span style="font-size:8pt">{{ $mgrApproval->approved_at ? \Carbon\Carbon::parse($mgrApproval->approved_at)->format('d-M-Y') : '' }}</span></div>
                    @endif
                </div>

                <div class="sig-name">{{ $mgrApproval->approver->full_name ?? '( Manager )' }}</div>
                <div class="sig-pos">Dept. Head / Manager</div>
            </td>

            @php
                $dirApproval = $rl->approvalQueues->where('level_order', 2)->first();
            @endphp
            <td class="sig-box">
                <div class="sig-title">Approved By,</div>

                <div class="sign-space">
                    @if($dirApproval && $dirApproval->status == 'APPROVED')
                        <div class="approved-stamp">APPROVED<br><span style="font-size:8pt">{{ $dirApproval->approved_at ? \Carbon\Carbon::parse($dirApproval->approved_at)->format('d-M-Y') : '' }}</span></div>
                    @endif
                </div>

                <div class="sig-name">{{ $dirApproval->approver->full_name ?? '( Director )' }}</div>
                <div class="sig-pos">Director</div>
            </td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 20px; left: 0; right: 0; text-align: center; font-size: 8pt; color: #888; border-top: 1px solid #ccc; padding-top: 5px;">
        Document Generated by RL Monitoring System | Printed on: {{ now()->format('d M Y H:i') }} | Page 1 of 1
    </div>

</body>
</html>