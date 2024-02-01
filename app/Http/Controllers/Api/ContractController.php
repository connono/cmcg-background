<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\User;
use App\Http\Resources\ContractResource;

class ContractController extends Controller
{
    public function index(Request $request, Contract $contract){
        $query = $contract->query();

        if (!is_null($request->contract_name)) {
            $query = $query->where('contract_name', 'like', '%'.$request->contract_name.'%');
        }

        if (!is_null($request->series_number)) {
            $query = $query->where('series_number', 'like', '%'.$request->series_number.'%');
        }

        if (!is_null($request->category)) {
            $query = $query->where('category', $request->category);
        }

        if (!is_null($request->source)) {
            $query = $query->where('source', $request->source);
        }

        if (!is_null($request->isImportant)) {
            $query = $query->where('isImportant', $request->isImportant);
        }

        $contracts = $query->get();
        
        return  ContractResource::collection($contracts);
    }

    public function getItem(Request $request, Contract $contract){
        $contract = Contract::find($request->id);
        return new ContractResource($contract);
    }

    public function store(Request $request){
        $contract = Contract::create([
            'contract_name' => $request->contract_name,
            'category' => $request->category,
            'contractor' => $request->contractor,
            'source' => $request->source,
            'price' => $request->price,
            'contract_file' =>  $request->contract_file,
            'isImportant' => $request->isImportant,
            'comment' => $request->comment,
            'isComplement' => $request->isComplement,
        ]);
        $series_code = 0;
        $len = 5;
        $series_number = date('Y') . date('m') . $request->category . sprintf("%0{$len}d", $series_code);
        while(Contract::where('series_number', '=', $series_number)->exists()) {
            $series_code++;
            $series_number = date('Y') . date('m') . $request->category . sprintf("%0{$len}d", $series_code);
        }
        $contract->series_number = $series_number;
        $contract->save();
        // $manager = User::find($request->manager_id);
        // $contract->manager()->save($manager);
        // $manage_dean = User::find($request->manage_dean_id);
        // $contract->manage_dean()->save($manage_dean);
        // $dean = User::find($request->dean_id);
        // $contract->dean()->save($dean);

        return new ContractResource($contract);
    }

    public function storeDocx(Request $request, Contract $contract){
        $attributes = $request->only(['contract_docx']);
        $contract->update($attributes);
        return new ContractResource($contract);
    }
    
    public function delete(Request $request, Contract $contract) {
        $contract->delete();
        return response()->json([])->setStatusCode(200);
    }
}
