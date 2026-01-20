<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\MaterialRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalItems = InventoryItem::active()->count();
        $lowStockItems = InventoryItem::active()->lowStock()->count();
        $outOfStockItems = InventoryItem::active()->outOfStock()->count();
        $pendingRequests = MaterialRequest::where('status', 'pending')->count();
        
        // Get low stock items for alert widget
        $lowStockAlerts = InventoryItem::with('category')->active()
            ->lowStock()
            ->orderBy('current_stock', 'asc')
            ->limit(10)
            ->get();
        
        // Get recent stock movements
        $recentMovements = StockMovement::with(['inventoryItem.category', 'creator'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Get pending material requests
        // Get pending material requests
        $pendingMaterialRequests = MaterialRequest::with(['job', 'requester', 'inventoryItem.category'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('storekeeper.dashboard', compact(
            'totalItems',
            'lowStockItems',
            'outOfStockItems',
            'pendingRequests',
            'lowStockAlerts',
            'recentMovements',
            'pendingMaterialRequests'
        ));
    }
}
