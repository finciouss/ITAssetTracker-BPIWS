<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Helpers\ExcelTemplate;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('SearchString')) {
            $search = $request->input('SearchString');
            $query->where(function ($q) use ($search) {
                $q->where('tag_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('StatusFilter')) {
            $query->where('status', $request->input('StatusFilter'));
        }

        $sortDate = $request->input('sort_date');
        $sortStatus = $request->input('sort_status');

        if ($sortDate === 'oldest') {
            $query->orderBy('purchase_date', 'asc');
        } elseif ($sortDate === 'newest') {
            $query->orderBy('purchase_date', 'desc');
        } elseif ($sortStatus === 'asc') {
            $query->orderBy('status', 'asc');
        } elseif ($sortStatus === 'desc') {
            $query->orderBy('status', 'desc');
        } else {
            $query->orderBy('purchase_date', 'desc');
        }

        $assets = $query->get();
        return view('assets.index', compact('assets'));
    }

    public function exportPdf(Request $request)
    {
        $query = Asset::query();
        
        // Preserve any search filters
        if ($request->filled('SearchString')) {
            $search = $request->input('SearchString');
            $query->where(function ($q) use ($search) {
                $q->where('tag_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('StatusFilter')) {
            $query->where('status', $request->input('StatusFilter'));
        }

        // Ordered by status exactly as requested using Postgres compatible CASE
        $assets = $query->orderByRaw("
                            CASE status 
                                WHEN 'InStock' THEN 1 
                                WHEN 'Allocated' THEN 2 
                                WHEN 'Maintenance' THEN 3 
                                WHEN 'Retired' THEN 4 
                                ELSE 5 
                            END
                        ")
                        ->orderBy('purchase_date', 'desc')
                        ->get();
                        
        $summary = [
            'TotalStock'       => $assets->sum('stock'),
            'TotalAvailable'   => $assets->sum(fn($a) => $a->availableStock()),
            'TotalAllocated'   => $assets->sum(fn($a) => $a->allocatedQuantity()),
            'TotalMaintenance' => $assets->sum('maintenance_quantity'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('assets.export', compact('assets', 'summary'))
                    ->setPaper('a4', 'landscape');

        return $pdf->stream("IT_Asset_Master_Report.pdf");
    }

    public function exportExcel(Request $request)
    {
        $query = Asset::query();
        if ($request->filled('SearchString')) {
            $search = $request->input('SearchString');
            $query->where(function ($q) use ($search) {
                $q->where('tag_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('StatusFilter')) {
            $query->where('status', $request->input('StatusFilter'));
        }
        $assets = $query->orderByRaw("
            CASE status
                WHEN 'InStock' THEN 1
                WHEN 'Allocated' THEN 2
                WHEN 'Maintenance' THEN 3
                WHEN 'Retired' THEN 4
                ELSE 5
            END
        ")->orderBy('purchase_date', 'desc')->get();

        $excel = new ExcelTemplate();
        $row = $excel->writeCompanyHeader('IT ASSET MASTER REPORT');

        $totalStock     = $assets->sum('stock');
        $totalAvailable = $assets->sum(fn($a) => $a->availableStock());
        $totalAllocated = $assets->sum(fn($a) => $a->allocatedQuantity());
        $totalMaint     = $assets->sum('maintenance_quantity');

        $row = $excel->writeMetadataBlock($row, [
            'DATE'            => now()->timezone('Asia/Jakarta')->format('d F Y'),
            'TOTAL ASSETS'    => $assets->count() . ' asset type(s)',
            'TOTAL STOCK'     => $totalStock . ' unit(s)',
            'AVAILABLE'       => $totalAvailable . ' unit(s)',
            'ALLOCATED'       => $totalAllocated . ' unit(s)',
            'IN MAINTENANCE'  => $totalMaint . ' unit(s)',
        ]);

        $row = $excel->writeSectionTitle($row, 'ALL REGISTERED ASSETS');
        $dataRow = $excel->writeTableHeader($row, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Category',
            'H' => 'Total Stock',
        ]);

        // We'll use columns B-H; add extra cols for stock breakdown manually
        // Since ExcelTemplate is limited, use direct sheet access via the helper
        // For now map: B=No, C=TagNo, E=Name, F=Category, H=Stock info string
        $no = 1;
        foreach ($assets as $asset) {
            $avail = $asset->availableStock();
            $alloc = $asset->allocatedQuantity();
            $maint = $asset->maintenance_quantity;
            $excel->writeDataRow($dataRow, [
                'B' => $no,
                'C' => $asset->tag_number,
                'E' => $asset->name,
                'F' => $asset->category ?? '',
                'H' => "Total: {$asset->stock} | Avail: {$avail} | Alloc: {$alloc} | Maint: {$maint}",
            ]);
            $dataRow++;
            $no++;
        }

        $excel->writeSignatureBlock($dataRow + 3);

        return $excel->download('IT_Asset_Master_Report.xlsx');
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag_number'     => 'required|string|max:50|unique:assets,tag_number',
            'name'           => 'required|string|max:200',
            'specifications' => 'nullable|string|max:1000',
            'purchase_date'  => 'required|date',
            'stock'          => 'required|integer|min:1',
            'category'       => 'nullable|string|max:100',
            'cost'           => 'nullable|numeric|min:0',
        ]);

        // New assets always start as InStock
        $validated['status'] = Asset::STATUS_IN_STOCK;
        $validated['maintenance_quantity'] = 0;

        Asset::create($validated);

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['allocations.employee', 'allocations.project', 'logs']);
        return view('assets.detail', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'tag_number'     => 'required|string|max:50|unique:assets,tag_number,' . $asset->id,
            'name'           => 'required|string|max:200',
            'specifications' => 'nullable|string|max:1000',
            'purchase_date'  => 'required|date',
            'stock'          => 'required|integer|min:1',
            'category'       => 'nullable|string|max:100',
            'cost'           => 'nullable|numeric|min:0',
        ]);

        // Validate stock not less than already allocated + maintenance
        $minStock = $asset->allocatedQuantity() + $asset->maintenance_quantity;
        if ($validated['stock'] < $minStock) {
            return back()->withErrors(['stock' => "Stock cannot be less than currently committed units ({$minStock})."]) ->withInput();
        }

        // Retire is a special manual override
        if ($request->input('retire') === '1') {
            $validated['status'] = Asset::STATUS_RETIRED;
        }

        $asset->update($validated);
        $asset->autoUpdateStatus(); // re-evaluate if not retiring

        return redirect()->route('assets.show', $asset)->with('success', 'Asset updated successfully.');
    }

    /**
     * Update maintenance_quantity for an asset.
     */
    public function updateMaintenance(Request $request, Asset $asset)
    {
        $request->validate([
            'maintenance_quantity' => 'required|integer|min:0',
        ]);

        $qty = (int) $request->input('maintenance_quantity');
        $maxMaintenance = $asset->stock - $asset->allocatedQuantity();

        if ($qty > $maxMaintenance) {
            return back()->withErrors(['maintenance_quantity' =>
                "Cannot put {$qty} units in maintenance. Only {$maxMaintenance} units are currently available (not allocated)."
            ])->withInput();
        }

        $asset->maintenance_quantity = $qty;
        $asset->save();
        $asset->autoUpdateStatus();

        return back()->with('success', "Maintenance stock updated to {$qty} unit(s).");
    }

    public function delete(Asset $asset)
    {
        return view('assets.delete', compact('asset'));
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }
}
