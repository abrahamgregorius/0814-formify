<?php

namespace App\Http\Controllers;

use App\Models\AllowedDomain;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = Form::where('creator_id', auth()->user()->id)->get();

        return response()->json([
            'message' => 'Get all forms success',
            'forms' => $forms
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:forms,slug|regex:/^[A-Za-z0-9.-]+$/',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $data = $request->all();
        $data['creator_id'] = auth()->user()->id;
        unset($data["allowed_domains"]);
        $form = Form::create($data);

        foreach($request->allowed_domains as $al) {
            AllowedDomain::create([
                'form_id' => $form->id,
                'domain' => $al,
            ]);
        }

        return response()->json([
            'message' => 'Create form success',
            'form' => [
                'name' => $form->name,
                'slug' => $form->slug,
                'description' => $form->description,
                'limit_one_response' => $form->limit_one_response ? true : false,
                'creator_id' => $form->creator_id,
            ]
        ]);
    
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        if(!$form) {
            return response()->json([
                'message' => 'Form not found'
            ], 404);
        }

        $form["allowed_domains"] = collect(AllowedDomain::where('form_id', $form->id)->get())->pluck('domain')->toArray();
        $form["questions"] = $form->questions;
        
        $domains = $form["allowed_domains"];
        $user_dom = explode("@", auth()->user()->email)[1];

        if(!in_array($user_dom, $domains)) {
            return response()->json([
                'message' => "Forbidden access"
            ], 403);
        }

        return response()->json([
            'message' => 'Get form success',
            'form' => $form
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Form $form)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form)
    {
        //
    }
}
