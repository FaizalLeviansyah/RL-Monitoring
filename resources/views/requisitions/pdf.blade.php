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
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 10pt; }
        .items-table th { border: 1px solid #000; padding: 8px; background-color: #e0e0e0; text-align: center; font-weight: bold; }
        .items-table td { border: 1px solid #000; padding: 8px; }

        .text-center { text-align: center; }

        /* SIGNATURE */
        .signature-table { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        .sig-box { width: 33%; text-align: center; vertical-align: top; position: relative; } /* Tambah relative disini */
        .sig-title { font-weight: bold; font-size: 9pt; margin-bottom: 60px; } /* Jarak TTD */
        .sig-name { font-weight: bold; text-decoration: underline; font-size: 9pt; text-transform: uppercase; }
        .sig-pos { font-size: 8pt; font-style: italic; margin-top: 2px; }

        /* STEMPEL APPROVED */
        .stamp-box { position: absolute; top: 30px; left: 0; right: 0; text-align: center; z-index: -1; } /* z-index agar di belakang teks jika perlu, atau sesuaikan */
        .approved-text { border: 2px solid green; color: green; padding: 2px 8px; display: inline-block; transform: rotate(-5deg); font-weight: bold; font-size: 8pt; border-radius: 4px; background: rgba(255,255,255,0.8); }

        /* Footer */
        .footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 7pt; color: #aaa; }
    </style>
</head>
<body>

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
            <td class="label"></td><td class="sep"></td><td class="val"></td>
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
            @if(isset($rl->items) && count($rl->items) > 0)
                @foreach($rl->items as $index => $item)
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
                    <td>{{ $item->remark ?? '' }}</td> {{-- Added remark if available in item --}}
                </tr>
                @endforeach
                {{-- Fill Empty Rows for Layout Consistency --}}
                @for($i = 0; $i < (5 - count($rl->items)); $i++)
                <tr><td style="padding:12px;">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                @endfor
            @else
                 <tr><td colspan="7" class="text-center" style="padding: 20px;">No Items</td></tr>
            @endif
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td class="sig-box">
                <div class="sig-title">Requested By,</div>
                <div class="sig-name">{{ $rl->requester->full_name }}</div>
                <div class="sig-pos">{{ $rl->requester->position->position_name ?? 'Staff' }}</div>
            </td>

            <td class="sig-box">
                <div class="sig-title">Reviewed By,</div>

                @php
                    // Logika: Cek tabel approvalQueue DULU (Real). Kalau kosong, pakai data $manager (Plan).
                    // Variabel $manager DIKIRIM DARI CONTROLLER (printPdf / previewTemp)

                    $mgrApp = null;
                    if(isset($rl->approvalQueues)){
                        $mgrApp = $rl->approvalQueues->where('level_order', 1)->first();
                    }

                    // Tentukan Nama
                    if ($mgrApp) {
                        $mgrName = $mgrApp->approver->full_name; // Nama dari yang approve beneran
                        $mgrPos = $mgrApp->approver->position->position_name ?? 'Manager';
                    } else {
                        $mgrName = $manager->full_name ?? '( ........................... )'; // Nama Rencana
                        $mgrPos = $manager->position->position_name ?? 'Manager';
                    }
                @endphp

                {{-- Stampel Jika Approved --}}
                @if($mgrApp && $mgrApp->status == 'APPROVED')
                    <div class="stamp-box">
                        <div class="approved-text">APPROVED<br>{{ \Carbon\Carbon::parse($mgrApp->approved_at)->format('d/m/Y') }}</div>
                    </div>
                @endif

                <div class="sig-name">{{ $mgrName }}</div>
                <div class="sig-pos">{{ $mgrPos }}</div>
            </td>

            <td class="sig-box">
                <div class="sig-title">Approved By,</div>

                @php
                    $dirApp = null;
                    if(isset($rl->approvalQueues)){
                        $dirApp = $rl->approvalQueues->where('level_order', 2)->first();
                    }

                    // Tentukan Nama
                    if ($dirApp) {
                        $dirName = $dirApp->approver->full_name;
                        $dirPos = $dirApp->approver->position->position_name ?? 'Director';
                    } else {
                        $dirName = $director->full_name ?? '( ........................... )';
                        $dirPos = $director->position->position_name ?? 'Director';
                    }
                @endphp

                {{-- Stampel Jika Approved --}}
                @if($dirApp && $dirApp->status == 'APPROVED')
                    <div class="stamp-box">
                        <div class="approved-text">APPROVED<br>{{ \Carbon\Carbon::parse($dirApp->approved_at)->format('d/m/Y') }}</div>
                    </div>
                @endif

                <div class="sig-name">{{ $dirName }}</div>
                <div class="sig-pos">{{ $dirPos }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Printed System: {{ now()->format('d/m/Y H:i') }} | Page 1/1
    </div>
</body>
</html>
