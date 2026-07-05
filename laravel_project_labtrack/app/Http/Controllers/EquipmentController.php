<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    public function index()
    {
        $search = request('search');
        $category = request('category');
        $status = request('status');

        $query = DB::table('equipment')
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
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('equipment.equipment_id', 'like', '%' . $search . '%')
                  ->orWhere('equipment.equipment_name', 'like', '%' . $search . '%');
            });
        }

        if (!empty($category)) {
            $query->where('equipment.category_id', $category);
        }

        if (!empty($status)) {
            $query->where('equipment.status', $status);
        }

        $equipment = $query->orderBy('equipment.purchase_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        $categories = DB::table('categories')
            ->select('category_id', 'category_name')
            ->orderBy('category_name', 'asc')
            ->get();

        return view('equipment.index', compact('equipment', 'categories'));
    }

    public function create()
    {
        $categories = DB::table('categories')
            ->orderBy('category_name', 'asc')
            ->get();

        $labs = DB::table('labs')
            ->orderBy('lab_name', 'asc')
            ->get();

        return view('equipment.create', compact('categories', 'labs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id'   => 'required|string|max:10|unique:equipment,equipment_id',
            'equipment_name' => 'required|string|max:150',
            'category_id'    => 'required|exists:categories,category_id',
            'lab_id'         => 'required|exists:labs,lab_id',
            'quantity'       => 'required|integer|min:1',
        ]);

        try {
            DB::statement('BEGIN add_equipment(:equipment_id, :equipment_name, :category_id, :lab_id, :quantity); END;', [
                'equipment_id'   => $validated['equipment_id'],
                'equipment_name' => $validated['equipment_name'],
                'category_id'    => $validated['category_id'],
                'lab_id'         => $validated['lab_id'],
                'quantity'       => $validated['quantity'],
            ]);

            return redirect()->route('equipment.index')
                ->with('success', 'Equipment added successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $equipment = DB::table('equipment')
            ->where('equipment_id', $id)
            ->first();

        if (!$equipment) {
            abort(404);
        }

        $categories = DB::table('categories')
            ->select('category_id', 'category_name')
            ->orderBy('category_name', 'asc')
            ->get();

        $labs = DB::table('labs')
            ->select('lab_id', 'lab_name')
            ->orderBy('lab_name', 'asc')
            ->get();

        return view('equipment.edit', compact('equipment', 'categories', 'labs'));
    }


    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:150',
            'category_id'    => 'required|exists:categories,category_id',
            'lab_id'         => 'required|exists:labs,lab_id',
            'quantity'       => 'required|integer|min:1',
        ]);

        try {
            DB::statement('BEGIN
                update_equipment(
                    :equipment_id,
                    :equipment_name,
                    :category_id,
                    :lab_id,
                    :quantity
                );
            END;', [
                'equipment_id'   => $id,
                'equipment_name' => $validated['equipment_name'],
                'category_id'    => $validated['category_id'],
                'lab_id'         => $validated['lab_id'],
                'quantity'       => $validated['quantity'],
            ]);

            return redirect()->route('equipment.index')
                ->with('success', 'Equipment updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::statement('BEGIN
                delete_equipment(
                    :equipment_id
                );
            END;', [
                'equipment_id' => $id,
            ]);

            return redirect()->route('equipment.index')
                ->with('success', 'Equipment deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
