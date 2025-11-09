<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Continent;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContinentController extends Controller
{
    /**
     * 🏠 Main View
     */
    public function index()
    {
        return view('admin.continents.index');
    }

    /**
     * 📊 AJAX Data Endpoint for DataTables
     */
    public function data(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $query = Continent::query();

        // ✅ Filter by status (if provided)
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('dark_img', function ($row) {
                return $row->dark_img
                    ? '<img src="'.asset('storage/'.$row->dark_img).'" width="50" class="img-thumbnail shadow-sm">'
                    : '—';
            })
            ->addColumn('light_img', function ($row) {
                return $row->light_img
                    ? '<img src="'.asset('storage/'.$row->light_img).'" width="50" class="img-thumbnail shadow-sm">'
                    : '—';
            })
            ->addColumn('status', function ($row) {
                $badge = $row->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
                return '<button class="btn btn-sm toggle-status" data-id="'.$row->id.'" data-status="'.$row->status.'">'.$badge.'</button>';
            })
            ->addColumn('actions', function ($row) {
                return '
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary btn-sm edit-btn" data-id="'.$row->id.'">✏️ Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="'.$row->id.'">🗑 Delete</button>
                    </div>
                ';
            })
            ->rawColumns(['dark_img', 'light_img', 'status', 'actions'])
            ->make(true);
    }

    /**
     * 🟢 Toggle Status (AJAX)
     */
    public function toggleStatus($id)
    {
        $continent = Continent::findOrFail($id);
        $continent->status = !$continent->status;
        $continent->save();

        return response()->json([
            'success' => true,
            'status' => $continent->status,
        ]);
    }

    /**
     * ✏️ Update Continent (AJAX)
     */
    public function update(Request $request, $id)
    {
        $continent = Continent::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:5',
            'en_name' => 'required|string|max:255',
            'ar_name' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $continent->update($validated);

        return response()->json(['success' => true, 'message' => 'Continent updated successfully!']);
    }

    /**
     * 🗑 Delete Continent (AJAX)
     */
    public function destroy($id)
    {
        $continent = Continent::findOrFail($id);
        $continent->delete();

        return response()->json(['success' => true, 'message' => 'Continent deleted successfully!']);
    }
}
