<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        //
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

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'choice_type' => 'required|in:short answer,paragraph,date,multiple choice,dropdown,checkboxes',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $data = $request->all();
        $data["choices"] = implode(",", $request->choices);
        $data["form_id"] = $form->id;

        $q = Question::create($data);
        
        return response()->json([
            'message' => "Add question success",
            'question' => $q 
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question, $slug, $id)
    {
        $form = Form::firstWhere('slug', $slug);

        if(!$form) {
            return response()->json([
                'message' => 'Form not found'
            ], 404);
        }

        $question = Question::where('id', $id)->firstWhere('form_id', $form->id);
    
        if(!$question) {
            return response()->json([
                'message' => 'Question not found'
            ], 404);
        }

        if($form->creator_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Forbidden access'
            ], 403);
        }

        $question->delete();

        return response()->json([
            'message' => 'Remove question success'
        ]);
    }
}
