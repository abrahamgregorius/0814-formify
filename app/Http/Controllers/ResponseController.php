<?php

namespace App\Http\Controllers;

use App\Models\AllowedDomain;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        $form = Form::firstWhere('slug', $slug);

        if(!$form) {
            return response()->json([
                'message' => 'Form not found'
            ], 404);
        }

        if($form->creator_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Forbidden access'
            ], 403);
        }

        $responses = Response::where('form_id', $form->id)->get();
        
        return response()->json([
            'message' => 'Get responses success',
            'responses' => $responses->map(function($re) {
                $re["user"] = $re->user;
                $re["answers"] = $re->answers;
                return $re;
            })
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
    public function store(Request $request, $slug)
    {
        $form = Form::firstWhere('slug', $slug);

        // Form not found
        if(!$form) {
            return response()->json([
                'message' => 'Form not found'
            ], 404);
        }

        // Invalid fields
        $validator = Validator::make($request->all(), [
            'answers' => 'array|required_array_keys',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        // User is not invited
        $domains = collect(AllowedDomain::where('form_id', $form->id)->get())->pluck('domain')->toArray();
        $user_dom = explode("@", auth()->user()->email)[1];

        if(!in_array($user_dom, $domains)) {
            return response()->json([
                'message' => "Forbidden access"
            ], 403);
        }


        $response = Response::where('user_id', auth()->user()->id)->firstWhere('form_id', $form->id);
        if($response) {
            return response()->json([
                'message' => 'You can not submit form twice'
            ], 422);
        }

        $response = Response::create([
            'form_id' => $form->id,
            'user_id' => auth()->user()->id,
            'date' => Date::now()
        ]);


        foreach($request->answers as $re) {
            Answer::create([
                'response_id' => $response->id,
                'question_id' => $re["question_id"],
                'value' => $re["value"]
            ]);
        }

        return response()->json([
            'message' => "Submit response success"
        ]);
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Response $response)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Response $response)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Response $response)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Response $response)
    {
        //
    }
}
