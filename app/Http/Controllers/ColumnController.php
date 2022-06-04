<?php

namespace App\Http\Controllers;

use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends Controller
{

    public function index()
    {
        try {
            $columns = Column::with('cards')->get();
            return response()->json(['status' => 200, 'columns' => $columns]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $result = $user->columns()->create($request->all());
            return response()->json(['status' => 200, 'message' => 'Column added successfully', 'column' => $result->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, Column $column)
    {
        try {
            $column->update($request->all());
            return response()->json(['status' => 200, 'message' => 'Column updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Column $column)
    {
        try {
            $column->delete();
            return response()->json(['status' => 200, 'message' => 'Column deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function sync(Request $request)
    {
        $this->validate(request(), [
            'columns' => ['required', 'array']
        ]);

        foreach ($request->columns as $column) {
            foreach ($column['cards'] as $i => $card) {
                $order = $i + 1;
                if ($card['column_id'] !== $column['id'] || $card['order'] !== $order) {
                    request()->user()->cards()
                        ->find($card['id'])
                        ->update(['column_id' => $column['id'], 'order' => $order]);
                }
            }
        }

        return $request->user()->columns()->with('cards')->get();
    }
}
