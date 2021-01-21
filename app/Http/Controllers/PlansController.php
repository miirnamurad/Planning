<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use Auth;

class PlansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //validations
        $request->validate([
            'search' => 'string',
            'start_date' => 'date|required_with:due_date',
            'due_date' => 'date|required_with:start_date',
        ]);

        $plans = Plan::with('feedbacks')->where('user_id',Auth::id())->orderBy('start_date');

        if($request->has('start_date') && $request->has('due_date'))
            $plans->where('start_date','>=',$request->start_date)->where('due_date','<=',$request->due_date);

        if($request->has('search'))
            $plans->where('name','LIKE' ,"%$request->search%");

        return response()->json(['message' => 'Your Plans List', 'body' => $plans->get()], 200);
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
            'name' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
        ]);

        $check_exist = Plan::where('user_id',Auth::id())
                            ->where('start_date','>=',$request->start_date)
                            ->Where('due_date','<=',$request->due_date)
                            ->get();

        if(count($check_exist) > 0)
            return response()->json(['message' => 'You have a plan during this period, please check again your schudle.', 'body' => null], 404);
            
        $plan = Plan::firstOrCreate([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'user_id' => Auth::id()
        ]);

        return response()->json(['message' => 'Plan created successfully', 'body' => $plan], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = Plan::with('feedbacks')->whereId($id)->first();

        if(isset($plan))
            return response()->json(['message' => 'Here is your choosen plan', 'body' => $plan], 200);

        return response()->json(['message' => 'Plan not fount!', 'body' => [] ], 400);
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
        $request->validate([
            'start_date' => 'date|required_with:due_date',
            'due_date' => 'date|required_with:start_date|after:start_date',
        ]);

        $plan = Plan::find($id);

        if(!isset($plan))
            return response()->json(['message' => 'Plan not found', 'body' => $plan], 200);

        if($request->has('start_date') && $request->has('due_date')){

            $check_exist = Plan::where('user_id',Auth::id())
                                ->where('start_date','>=',$request->start_date)
                                ->Where('due_date','<=',$request->due_date)
                                ->get();

            if(count($check_exist) > 0)
                return response()->json(['message' => 'You have a plan during this period, please check again your schudle.', 'body' => null], 404);

            $plan->start_date = $request->start_date;
            $plan->due_date = $request->due_date;
        }

        if($request->has('name'))
            $plan->name = $request->name;
        
        if($request->has('description'))
            $plan->description = $request->description;

        $plan->save();

        return response()->json(['message' => 'Plan updated successfully', 'body' => $plan], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plan = Plan::find($id);

        if(isset($plan)){
            $plan->delete();
            return response()->json(['message' => 'Plan deleted successfully', 'body' => $plan], 200);
        }

        return response()->json(['message' => 'Plan not fount!', 'body' => [] ], 400);
    }
}
