<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Plan;
use Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $plans = Plan::where('user_id',Auth::id())->pluck('id');

        $feedbacks = Feedback::whereIn('plan_id',$plans)->with('plans')->get();

        return response()->json(['message' => 'feedbacks List', 'body' => $feedbacks], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'feedback' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
            'rate' => 'required|integer|max:10',
        ]);
            
        $feedback = Feedback::create([
            'feedback' => $request->feedback,
            'plan_id' => $request->plan_id,
            'rate' => isset($request->rate) ? $request->rate : 0
        ]);

        return response()->json(['message' => 'feedback created successfully', 'body' => $feedback], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $feedback = Feedback::find($id);

        if(isset($feedback))
            return response()->json(['message' => 'Here is your choosen feedback', 'body' => $feedback], 200);

        return response()->json(['message' => 'feedback not fount!', 'body' => [] ], 400);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $feedback = Feedback::find($id);

        if(!isset($feedback))
            return response()->json(['message' => 'feedback not found', 'body' => $feedback], 200);

        if($request->has('feedback'))
            $feedback->feedback = $request->feedback;
        
        if($request->has('rate'))
            $feedback->rate = $request->rate;

        $feedback->save();

        return response()->json(['message' => 'feedback updated successfully', 'body' => $feedback], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $feedback = Feedback::find($id);

        if(isset($feedback)){
            $feedback->delete();
            return response()->json(['message' => 'feedback deleted successfully', 'body' => $feedback], 200);
        }

        return response()->json(['message' => 'feedback not fount!', 'body' => [] ], 400);

    }
}
