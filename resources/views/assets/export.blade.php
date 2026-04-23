<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>IT Asset Master Report</title>
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
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 2px;
            border: none;
        }
        .meta-label {
            font-weight: bold;
            width: 80px;
        }

        .summary-block {
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            width: 40%;
        }
        .summary-title {
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }
        .summary-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .summary-list li {
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }
        .summary-value {
            font-weight: bold;
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
        IT ASSET MASTER REPORT
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

    <div class="summary-block">
        <div class="summary-title">Inventory Summary</div>
        <table style="width: 100%; border: none; font-size: 10px;">
            <tr>
                <td style="border: none; padding: 2px;">Total Asset Types</td>
                <td style="border: none; padding: 2px; text-align: right; font-weight: bold;">{{ $assets->count() }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">Total Stock Units</td>
                <td style="border: none; padding: 2px; text-align: right; font-weight: bold;">{{ $summary['TotalStock'] }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">In Stock (Available)</td>
                <td style="border: none; padding: 2px; text-align: right; font-weight: bold;">{{ $summary['TotalAvailable'] }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">Allocated Units</td>
                <td style="border: none; padding: 2px; text-align: right; font-weight: bold;">{{ $summary['TotalAllocated'] }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">In Maintenance</td>
                <td style="border: none; padding: 2px; text-align: right; font-weight: bold;">{{ $summary['TotalMaintenance'] }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <tbody class="data-table-wrapper">
            <tr>
                <th class="col-header" style="width: 4%">#</th>
                <th class="col-header" style="width: 18%">Tag Number</th>
                <th class="col-header" style="width: 28%">Asset Name</th>
                <th class="col-header" style="width: 10%">Category</th>
                <th class="col-header" style="width: 8%; text-align:center">Total</th>
                <th class="col-header" style="width: 8%; text-align:center">Avail.</th>
                <th class="col-header" style="width: 8%; text-align:center">Alloc.</th>
                <th class="col-header" style="width: 8%; text-align:center">Maint.</th>
                <th class="col-header" style="width: 8%">Purchase Date</th>
                <th class="col-header" style="width: 10%">Status</th>
            </tr>
            @foreach($assets as $index => $asset)
            @php
                $alloc   = $asset->allocatedQuantity();
                $avail   = $asset->availableStock();
                $maint   = $asset->maintenance_quantity;
            @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-weight: bold; color: #4f46e5; text-align: center;">
                    {{ $asset->tag_number }}
                </td>
                <td>{{ $asset->name }}</td>
                <td style="text-align: center;">{{ $asset->category ?? '—' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $asset->stock }}</td>
                <td style="text-align: center; color: #10b981; font-weight: bold;">{{ $avail }}</td>
                <td style="text-align: center; color: #3b82f6; font-weight: bold;">{{ $alloc }}</td>
                <td style="text-align: center; color: #f59e0b; font-weight: bold;">{{ $maint }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('d/m/Y') }}</td>
                <td style="text-align: center;">
                    <div class="status-badge" style="color:
                        {{ $asset->status === 'InStock' ? '#10b981' : '' }}
                        {{ $asset->status === 'Allocated' ? '#3b82f6' : '' }}
                        {{ $asset->status === 'Maintenance' ? '#f59e0b' : '' }}
                        {{ $asset->status === 'Retired' ? '#ef4444' : '' }}
                    ;">
                        {{ $asset->status == 'InStock' ? 'In Stock' : $asset->status }}
                    </div>
                </td>
            </tr>
            @endforeach
            @if($assets->isEmpty())
            <tr>
                <td colspan="10" style="text-align: center; color: #666; height: 30px;">There are no assets to display.</td>
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
