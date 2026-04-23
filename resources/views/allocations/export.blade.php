<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>IT Asset Allocations Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table {
            margin-bottom: 20px;
            border: none;
        }
        .header-table td {
            border: none;
            padding: 2px;
            vertical-align: top;
        }
        .logo-cell {
            width: 120px;
        }
        .logo-img {
            max-width: 100px;
            height: auto;
        }
        .company-info {
            font-size: 9px;
        }
        .company-name {
            font-weight: bold;
            font-size: 12px;
        }
        .company-specialist {
            font-weight: bold;
            font-size: 10px;
            margin-top: 2px;
            margin-bottom: 4px;
        }
        
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 20px 0;
        }

        .meta-table {
            width: 40%;
            margin-bottom: 30px;
        }
        .meta-table td {
            padding: 2px;
            border: none;
        }
        .meta-label {
            font-weight: bold;
            width: 80px;
        }

        .data-table {
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
            vertical-align: middle;
        }
        .section-header {
            font-weight: bold;
            text-align: center;
            border: none !important;
            padding-bottom: 5px;
            font-size: 12px;
        }
        .col-header {
            background-color: #e6edd9;
            font-weight: bold;
            text-align: center !important;
        }
        .data-table-wrapper {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .status-badge {
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            width: 100%;
            text-align: right;
        }
        .footer-lines {
            display: inline-block;
            text-align: center;
            margin-right: 20px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(file_exists(public_path('img/bauer-logo.jpeg')))
                    <img src="{{ public_path('img/bauer-logo.jpeg') }}" class="logo-img" alt="Bauer Logo">
                @else
                    <div style="width:100px;height:80px;background:#0d3d7a;color:#fcb614;font-size:24px;text-align:center;line-height:80px;font-weight:bold;">BAUER</div>
                @endif
            </td>
            <td class="company-info">
                <div class="company-name">PT. BAUER Pratama Indonesia</div>
                <div class="company-specialist">International Foundation Specialist</div>
                <div>Alamanda Tower 19th Floor Jalan TB Simatupang Kav. 23-24 Cilandak Barat Jakarta Selatan 12430 - Indonesia</div>
                <div>Telp : +62-21 2966 1988 (Hunting) Fax. : +62 21 2966 0188</div>
                <div>Workshop : Kp. Cipoicung Rt. 18 / Rw. 04 Desa Mekarsari Kec. Cileungsi Kab. Bogor</div>
                <div>Telp : +62-21-2923 2795</div>
            </td>
        </tr>
    </table>

    <div class="report-title">
        IT ASSET ALLOCATIONS REPORT
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">LOCATION</td>
            <td>: BPI WS</td>
        </tr>
        <tr>
            <td class="meta-label">DATE</td>
            <td>: {{ date('F d, Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <tbody class="data-table-wrapper">
            <tr>
                <th class="col-header" style="width: 5%">#</th>
                <th class="col-header" style="width: 35%">Asset</th>
                <th class="col-header" style="width: 30%">Assigned To</th>
                <th class="col-header" style="width: 15%">Check Out</th>
                <th class="col-header" style="width: 15%">Status</th>
            </tr>
            @foreach($allocations as $index => $alloc)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight: bold; color: #4f46e5;">{{ $alloc->asset->name }}</div>
                    <div style="font-size: 9px; color: #64748b;">{{ $alloc->asset->tag_number }}</div>
                </td>
                <td>
                    @if($alloc->employee)
                        <div style="font-weight: bold;">{{ $alloc->employee->full_name ?? $alloc->employee->first_name }}</div>
                        @if($alloc->project)
                        <div style="font-size: 9px; color: #64748b;">Project: {{ $alloc->project->project_name }}</div>
                        @endif
                    @elseif($alloc->project)
                        <div style="font-weight: bold;">{{ $alloc->project->project_name }}</div>
                        <div style="font-size: 9px; color: #64748b;">Project Only</div>
                    @else
                        <div style="font-weight: bold;">-</div>
                    @endif
                </td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($alloc->check_out_date)->format('m/d/Y') }}</td>
                <td style="text-align: center;">
                    @if($alloc->actual_return_date)
                        @if($alloc->is_transfer_out)
                            <div class="status-badge" style="color: #475569;">Transferred</div>
                            <div style="font-size: 9px; color: #64748b;">{{ \Carbon\Carbon::parse($alloc->actual_return_date)->format('m/d/Y') }}</div>
                        @else
                            <div class="status-badge" style="color: #64748b;">Returned</div>
                            <div style="font-size: 9px; color: #64748b;">{{ \Carbon\Carbon::parse($alloc->actual_return_date)->format('m/d/Y') }}</div>
                        @endif
                    @else
                        <div class="status-badge" style="color: #10b981;">Active</div>
                    @endif
                </td>
            </tr>
            @endforeach
            @if($allocations->isEmpty())
            <tr>
                <td colspan="5" style="text-align: center; color: #666; height: 30px;">There are no allocations to display.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-lines">
            <div>PUBLISHED BY</div>
            <div>WORKSHOP IT DEPARTMENT</div>
        </div>
    </div>

</body>
</html>
