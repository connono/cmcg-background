<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComsumableItemApplyRecord;
use App\Http\Resources\ComsumableItemApplyRecordResource;

class ComsumableItemApplyRecordController extends Controller
{
    public function store(Request $request) {
        $record = ComsumableItemApplyRecord::create([
            'department' => $request->department,
            'name'=> $request->name,
            'specification'=> $request->specification,
            'production_id'=> $request->production_id,
            'price'=> $request->price,
            'registration_number'=> $request->registration_number,
            'category_ZJ'=> $request->category_ZJ,
            'parent_directory'=> $request->parent_directory,
            'child_directory'=> $request->child_directory,
            'type'=> $request->type,
        ]);

        return new ComsumableItemApplyRecordResource($record);
    }

    public function index(Request $request, ComsumableItemApplyRecord $record) {
        $query = $record->query();
        $records = $query->paginate();
        return ComsumableItemApplyRecordResource::collection($records);
    }
    
    public function getItem(Request $request, ComsumableItemApplyRecord $record) {
        $record = ComsumableItemApplyRecord::find($request->id);
        return new ComsumableItemApplyRecordResource($record);
    }

    public function delete(Request $request, ComsumableItemApplyRecord $record) {
        $record->delete();
        return response()->json([])->setStatusCode(200);
    }
}
