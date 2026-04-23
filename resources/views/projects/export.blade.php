<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>IT Asset Report - {{ $project->project_name }}</title>
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
            padding: 5px;
            text-align: left;
        }
        .section-header {
            font-weight: bold;
            text-align: center;
            border: none !important;
            padding-bottom: 5px;
            font-size: 12px;
        }
        .col-header {
            background-color: #e6edd9; /* Light green from template */
            font-weight: normal; /* In template it looks like empty rows or subtle headers */
            text-align: center !important;
        }
        .data-table-wrapper {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
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
        IT ASSET REPORT
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">PROJECT</td>
            <td>: {{ $project->project_name }}</td>
        </tr>
        <tr>
            <td class="meta-label">DATE</td>
            <td>: {{ date('F d, Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">STATUS</td>
            <td>: {{ $project->status }}</td>
        </tr>
    </table>

    <!-- Active Allocations Section -->
    <table class="data-table">
        <tr>
            <td colspan="4" class="section-header">ACTIVELY ALLOCATED ASSETS</td>
        </tr>
        <tbody class="data-table-wrapper">
            <tr>
                <th class="col-header" style="width: 25%">Asset Tag</th>
                <th class="col-header" style="width: 35%">Asset Name</th>
                <th class="col-header" style="width: 25%">Employee</th>
                <th class="col-header" style="width: 15%">Check-out Date</th>
            </tr>
            @forelse($activeAllocations as $alloc)
            <tr>
                <td style="text-align: center;">{{ $alloc->asset->tag_number }}</td>
                <td>{{ $alloc->asset->name }}</td>
                <td>{{ $alloc->employee->full_name ?? ($alloc->employee->first_name ?? "-") }}</td>
                <td style="text-align: center;">{{ $alloc->check_out_date->format('m/d/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #666; height: 30px;">There are no actively allocated assets.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Transferred Assets Section -->
    <table class="data-table">
        <tr>
            <td colspan="4" class="section-header">TRANSFERRED ASSETS HISTORY</td>
        </tr>
        <tbody class="data-table-wrapper">
            <tr>
                <th class="col-header" style="width: 20%">Asset Tag</th>
                <th class="col-header" style="width: 30%">Asset Name</th>
                <th class="col-header" style="width: 20%">Previous Assignee</th>
                <th class="col-header" style="width: 15%">Transferred To</th>
                <th class="col-header" style="width: 15%">Transfer Date</th>
            </tr>
            @forelse($transferredAllocations as $alloc)
            <tr>
                <td style="text-align: center;">{{ $alloc->asset->tag_number }}</td>
                <td>{{ $alloc->asset->name }}</td>
                <td>{{ $alloc->employee->full_name ?? ($alloc->employee->first_name ?? "-") }}</td>
                <td>{{ $alloc->transferred_to_name }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($alloc->actual_return_date)->format('m/d/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #666; height: 30px;">There are no transferred assets.</td>
            </tr>
            @endforelse
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
