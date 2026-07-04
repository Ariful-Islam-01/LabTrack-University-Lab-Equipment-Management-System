<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = DB::table('equipment')
            ->join('categories', 'equipment.category_id', '=', 'categories.category_id')
            ->join('labs', 'equipment.lab_id', '=', 'labs.lab_id')
            ->select(
                'equipment.equipment_id',
                'equipment.equipment_name',
                'equipment.available_quantity',
                'equipment.total_quantity',
                'equipment.status',
                'equipment.purchase_date',
                'categories.category_name',
                'labs.lab_name'
            )
            ->orderBy('equipment.purchase_date', 'desc')
            ->paginate(10);

        return view('equipment.index', compact('equipment'));
    }

    public function create()
    {
        abort(501);
    }

    public function store(Request $request)
    {
        abort(501);
    }

    public function edit(string $id)
    {
        abort(501);
    }

    public function update(Request $request, string $id)
    {
        abort(501);
    }

    public function destroy(string $id)
    {
        abort(501);
    }
}
