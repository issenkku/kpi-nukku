<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Indicator;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndicatorApiController extends Controller
{
    protected function ensureAdmin(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('super_admin');
    }

    public function store(Request $request)
    {
        if (! $this->ensureAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100',
            'max_score' => 'required|numeric|min:0',
        ]);

        // Satisfy NOT NULL constraints
        $standard = Standard::firstOrCreate(['name' => 'Default']);
        $category = Category::firstOrCreate([
            'name' => 'General',
            'standard_id' => $standard->id,
        ]);

        $indicator = Indicator::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'max_score' => $data['max_score'],
            'score_acc' => 0,
            'status' => 1,
            'deadline' => now()->toDateString(),
            'categorie_id' => $category->id,
        ]);

        return response()->json($indicator, 201);
    }

    public function update(Request $request, Indicator $indicator)
    {
        if (! $this->ensureAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:100',
            'max_score' => 'sometimes|required|numeric|min:0',
        ]);

        $indicator->update($data);
        return response()->json($indicator, 200);
    }

    public function destroy(Indicator $indicator)
    {
        if (! $this->ensureAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $indicator->delete();
        return response()->noContent();
    }
}

