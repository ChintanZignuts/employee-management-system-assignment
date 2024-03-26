<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class CompanyEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allemployee = User::whereIn('type', ['E', 'CA'])
        ->get();

    return response()->json($allemployee );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated=$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'type' => 'string|in:E',
            'company_id' => [ 
                'exists:companies,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->user()->type === 'CA') {
                        if ($value !== $request->user()->company_id) {
                            $fail('Company admin can only create employees for their own company.');
                        }
                    } else if ($request->user()->type !== 'SA') {
                        $fail('Unauthorized to create employees.');
                    }
                }, 
            ],
        ]);
        if ($validated->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make("password"),
            'type' => 'E',
            'company_id' => $request->user()->type === 'CA' ? $request->user()->company_id : $validated['company_id'],
        ]);
    
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
