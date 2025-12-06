<!DOCTYPE html>
<html>
<head>
    <title>{{ $rl->rl_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
        
        /* KOP SURAT */
        .header-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 80px; height: auto; }
        .company-info { text-align: center; vertical-align: middle; }
        .company-name { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .company-address { font-size: 10px; margin-top: 5px; }
        
        /* JUDUL DOKUMEN */
        .doc-title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .doc-number { text-align: center; font-size: 12px; margin-bottom: 20px; }

        /* TABEL INFO */
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .label { font-weight: bold; width: 15%; }
        .sep { width: 2%; }
        .val { width: 33%; }

        /* TABEL BARANG */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 8px; }
        .items-table th { background-color: #e0e0e0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* TANDA TANGAN */
        .signature-table { width: 100%; margin-top: 50px; }
        .signature-box { text-align: center; vertical-align: top; width: 33%; }
        .sign-space { height: 70px; }
        .sign-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="15%">
                <img src="{{ public_path('images/' . $rl->company->logo_path) }}" class="logo">
            </td>
            <td class="company-info">
                <h1 class="company-name">{{ $rl->company->company_name }}</h1>
                <div class="company-address">
                    @if($rl->company->company_code == 'ASM')
                        Rukan Mangga Dua Square Blok H No. 22, Jl. Gunung Sahari Raya, Jakarta Utara
                    @elseif($rl->company->company_code == 'ACS')
                        Rukan Mangga Dua Square Blok H No. 22, Jl. Gunung Sahari Raya (Crewing Div)
                    @else
                        Alamat Kantor Pusat Caraka Tirta Pratama
                    @endif
                    <br> Telp: (021) 12345678 | Email: info@amarin.com
                </div>
            </td>
            <td width="15%"></td>
        </tr>
    </table>

    <div class="doc-title">REQUISITION LETTER</div>
    <div class="doc-number">No: {{ $rl->rl_no }}</div>

    <table class="info-table">
        <tr>
            <td class="label">Requester</td><td class="sep">:</td><td class="val">{{ $rl->requester->full_name }}</td>
            <td class="label">To Dept</td><td class="sep">:</td><td class="val">{{ $rl->to_department ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Department</td><td class="sep">:</td><td class="val">{{ $rl->requester->department->department_name }}</td>
            <td class="label">Date</td><td class="sep">:</td><td class="val">{{ \Carbon\Carbon::parse($rl->request_date)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Subject</td><td class="sep">:</td><td class="val" colspan="4">{{ $rl->subject }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Description / Item Name</th>
                <th width="15%">Qty</th>
                <th width="15%">UOM</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rl->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <b>{{ $item->item_name }}</b>
                    @if($item->description) <br><small>Spec: {{ $item->description }}</small> @endif
                </td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-center">{{ $item->uom }}</td>
                <td>{{ $item->remark ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($rl->remark)
    <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
        <strong>Note / Rationale:</strong><br>
        {{ $rl->remark }}
    </div>
    @endif

    <table class="signature-table">
        <tr>
            <td class="signature-box">
                Requested By,<br>
                <div class="sign-space"></div>
                <span class="sign-name">{{ $rl->requester->full_name }}</span><br>
                Requester
            </td>
            <td class="signature-box">
                Reviewed By,<br>
                <div class="sign-space">
                    @php 
                        $mgr = $rl->approvalQueues->where('level_order', 1)->first(); 
                    @endphp
                    @if($mgr && $mgr->status == 'APPROVED')
                        <br><img src="{{ public_path('images/approved_stamp.png') }}" style="height:50px; opacity: 0.5;"> 
                        @endif
                </div>
                <span class="sign-name">( Manager IT )</span><br>
                Dept. Head
            </td>
            <td class="signature-box">
                Approved By,<br>
                <div class="sign-space"></div>
                <span class="sign-name">( Director )</span><br>
                Director
            </td>
        </tr>
    </table>

    <div style="position: absolute; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777;">
        Dokumen ini digenerate otomatis oleh RL Monitoring System. <br>
        Silakan cetak, tandatangani, lalu scan dan upload kembali untuk arsip digital.
    </div>

</body>
</html>