<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PositionResource;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::all();

        if ($positions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Positions not found'], 404);
        }

        return response()->json([
            'success' => true,
            'positions' => PositionResource::collection($positions)
        ], 200);
    }
}