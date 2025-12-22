<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $rl->rl_no }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10pt; color: #000; margin: 0; padding: 0; }

        /* KOP SURAT */
        .header-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 10px; padding-bottom: 5px; }
        .logo-container { width: 80px; text-align: left; vertical-align: middle; }
        .logo { width: 70px; height: auto; }
        .company-info { text-align: center; vertical-align: middle; padding-right: 80px; }
        .company-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
        .company-address { font-size: 9pt; margin-top: 5px; line-height: 1.3; }

        .doc-title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-top: 15px; letter-spacing: 1px; }
        .doc-number { text-align: center; font-size: 10pt; margin-bottom: 20px; font-weight: bold; }

        /* INFO BOX */
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 3px 0; vertical-align: top; font-size: 9pt; }
        .label { font-weight: bold; width: 16%; }
        .sep { width: 2%; }
        .val { width: 32%; }

        /* TABLE ITEMS */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10pt; }
        .items-table th { border: 1px solid #000; padding: 8px; background-color: #e0e0e0; text-align: center; font-weight: bold; }
        .items-table td { border: 1px solid #000; padding: 8px; }

        /* Summary Row */
        .summary-row td { border-top: 2px solid #000; background-color: #f9f9f9; font-weight: bold; }

        .text-center { text-align: center; }

        /* SIGNATURE */
        .signature-table { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        .sig-box { width: 33%; text-align: center; vertical-align: top; position: relative; }
        .sig-title { font-weight: bold; font-size: 9pt; margin-bottom: 60px; }
        .sig-name { font-weight: bold; text-decoration: underline; font-size: 9pt; text-transform: uppercase; }
        .sig-pos { font-size: 8pt; font-style: italic; margin-top: 2px; }

        /* Footer */
        .footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 7pt; color: #aaa; border-top: 1px solid #eee; padding-top: 5px; }
        .doc-id { font-family: 'Courier New', monospace; letter-spacing: 1px; }
    </style>
</head>
<body>

    {{-- LOGIC PHP: FORMAT JABATAN --}}
    @php
        function formatJabatan($user) {
            if (!$user) return '-';

            $posName = $user->position->position_name ?? '';
            $deptName = $user->department->department_name ?? '';

            // List Jabatan Tinggi (Tanpa Dept)
            $highLevel = ['Director', 'Managing Director', 'Deputy Managing Director', 'General Manager', 'President Director'];

            if (in_array($posName, $highLevel)) {
                return $posName;
            }

            // Singkatan Departemen
            $shortDept = $deptName;
            if (stripos($deptName, 'Information Technology') !== false) $shortDept = 'IT';
            if (stripos($deptName, 'Human Resource') !== false) $shortDept = 'HR';
            if (stripos($deptName, 'General Affair') !== false) $shortDept = 'GA';
            if (stripos($deptName, 'Finance') !== false) $shortDept = 'Finance';
            if (stripos($deptName, 'Accounting') !== false) $shortDept = 'Acct';
            if (stripos($deptName, 'Procurement') !== false) $shortDept = 'Purchasing';
            if (stripos($deptName, 'Operational') !== false) $shortDept = 'Ops';
            if (stripos($deptName, 'Technical') !== false) $shortDept = 'Technical';

            // Gabungkan: IT Staff / HR Manager
            return $shortDept . ' ' . $posName;
        }
    @endphp

    <table class="header-table">
        <tr>
            <td class="logo-container">
                @if(isset($rl->company) && $rl->company->logo_path && file_exists(public_path('images/' . $rl->company->logo_path)))
                    <img src="{{ public_path('images/' . $rl->company->logo_path) }}" class="logo">
                @elseif(isset($rl->company))
                   <b>{{ $rl->company->company_code }}</b>
                @endif
            </td>
            <td class="company-info">
                @if(isset($rl->company))
                    <h1 class="company-name">{{ $rl->company->company_name }}</h1>
                    <div class="company-address">
                        @if($rl->company->company_code == 'ASM')
                            Citra Tower Jl. Benyamin Sueb Kav 6A, Lt. 08 Unit K-L<br>
                            Kemayoran, Jakarta Pusat 10630 - Indonesia
                        @elseif($rl->company->company_code == 'CTP')
                            Jl. Mangga Dua Raya Blok JJ/KK No.39, Mangga Dua Selatan<br>
                            Sawah Besar, Jakarta Pusat 14430 - Indonesia
                        @else
                            Rukan Mangga Dua Square Blok H No. 22<br>
                            Jl. Gunung Sahari Raya, Jakarta Utara - Indonesia
                        @endif
                    </div>
                @else
                    <h1 class="company-name">COMPANY NAME</h1>
                @endif
            </td>
        </tr>
    </table>

    <div class="doc-title">REQUISITION LETTER</div>
    <div class="doc-number">No: {{ $rl->rl_no }}</div>

    <table class="info-table">
        <tr>
            <td class="label">From Dept</td><td class="sep">:</td><td class="val">{{ $rl->requester->department->department_name ?? '-' }}</td>
            <td class="label">Priority</td><td class="sep">:</td>
            <td class="val">
                @if($rl->priority == 'Top Urgent') <b style="color:red">TOP URGENT</b> @else {{ $rl->priority }} @endif
            </td>
        </tr>
        <tr>
            <td class="label">Requester</td><td class="sep">:</td><td class="val">{{ $rl->requester->full_name }}</td>
            <td class="label">Required Date</td><td class="sep">:</td><td class="val">{{ $rl->required_date ? \Carbon\Carbon::parse($rl->required_date)->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">To Dept</td><td class="sep">:</td><td class="val">Purchasing / Procurement</td>
            <td class="label">Status</td><td class="sep">:</td><td class="val" style="text-transform:uppercase;">{{ str_replace('_', ' ', $rl->status_flow) }}</td>
        </tr>
        <tr>
            <td class="label">Subject</td><td class="sep">:</td><td class="val" colspan="4" style="font-weight:bold; font-style:italic;">"{{ $rl->subject }}"</td>
        </tr>
        <tr>
            <td class="label" style="vertical-align: top;">Note / Remark</td>
            <td class="sep" style="vertical-align: top;">:</td>
            <td class="val" colspan="4" style="font-style: italic; color: #444;">
                {{ $rl->remark ?? '-' }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Item Name & Description</th>
                <th style="width: 15%;">Part No.</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 10%;">UOM</th>
                <th style="width: 10%;">Stock</th>
                <th style="width: 15%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQty = 0; @endphp
            @if(isset($rl->items) && count($rl->items) > 0)
                @foreach($rl->items as $index => $item)
                @php $totalQty += $item->qty; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <b>{{ $item->item_name }}</b>
                        @if($item->description) <br><span style="color:#555; font-size:8pt;">{{ $item->description }}</span> @endif
                    </td>
                    <td class="text-center">{{ $item->part_number ?? '-' }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-center">{{ $item->uom }}</td>
                    <td class="text-center">{{ $item->stock_on_hand }}</td>
                    <td>{{ $item->remark ?? '' }}</td>
                </tr>
                @endforeach

                {{-- Fill Empty Rows --}}
                @for($i = 0; $i < (5 - count($rl->items)); $i++)
                <tr><td style="padding:12px;">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                @endfor

                {{-- INOVASI: BARIS TOTAL --}}
                <tr class="summary-row">
                    <td colspan="3" style="text-align: right; padding-right: 10px;">Total Items Requested:</td>
                    <td class="text-center">{{ $totalQty }}</td>
                    <td colspan="3"></td>
                </tr>
            @else
                 <tr><td colspan="7" class="text-center" style="padding: 20px;">No Items</td></tr>
            @endif
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td class="sig-box">
                <div class="sig-title">Requested By,</div>
                <br><br><br><br> <div class="sig-name">{{ $rl->requester->full_name }}</div>

                {{-- FIX: Panggil Helper formatJabatan --}}
                <div class="sig-pos">{{ formatJabatan($rl->requester) }}</div>
            </td>

            <td class="sig-box">
                <div class="sig-title">Reviewed By,</div>

                @php
                    // Logic Manager
                    $mgrApp = null;
                    if(isset($rl->approvalQueues)){
                        $mgrApp = $rl->approvalQueues->where('level_order', 1)->first();
                    }

                    if ($mgrApp) {
                        $mgrName = $mgrApp->approver->full_name;
                        $mgrUser = $mgrApp->approver; // Object User
                    } else {
                        $mgrName = $manager->full_name ?? '( ........................... )';
                        $mgrUser = $manager; // Object User (Plan)
                    }
                @endphp

                <br><br><br><br>

                <div class="sig-name">{{ $mgrName }}</div>

                {{-- FIX: Panggil Helper formatJabatan untuk Manager --}}
                <div class="sig-pos">{{ formatJabatan($mgrUser) }}</div>
            </td>

            <td class="sig-box">
                <div class="sig-title">Approved By,</div>

                @php
                    // Logic Director
                    $dirApp = null;
                    if(isset($rl->approvalQueues)){
                        $dirApp = $rl->approvalQueues->where('level_order', 2)->first();
                    }

                    if ($dirApp) {
                        $dirName = $dirApp->approver->full_name;
                        $dirUser = $dirApp->approver;
                    } else {
                        $dirName = $director->full_name ?? '( ........................... )';
                        $dirUser = $director;
                    }
                @endphp

                <br><br><br><br>

                <div class="sig-name">{{ $dirName }}</div>

                {{-- FIX: Panggil Helper formatJabatan untuk Director --}}
                <div class="sig-pos">{{ formatJabatan($dirUser) }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        {{-- INOVASI: DIGITAL FOOTPRINT --}}
        System Generated: {{ now()->format('d M Y H:i:s') }} |
        Doc ID: <span class="doc-id">{{ strtoupper(md5($rl->id . $rl->created_at)) }}</span> |
        Page 1/1
    </div>
</body>
</html>
