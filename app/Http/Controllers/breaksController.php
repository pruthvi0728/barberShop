<?php

namespace App\Http\Controllers;

use App\Models\Breaks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class breaksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'List of breaks',
            'data' => Breaks::select('name', 'from_time as fromTime', 'to_time as toTime')->get()
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Not found',
            'data' => []
        ], 404);
    }

    public function validationCheck($request){
        return Validator::make( $request->all(), [
            'name' => 'required',
            'fromTime' => 'required|date_format:H:i:s',
            'toTime' => 'required|date_format:H:i:s'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validationCheck($request);

        if($validator->fails())
        {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->getMessageBag(),
                'data' => [],
            ], 422);
        }

        Breaks::create([
            'name' => $request->name,
            'from_time' => $request->fromTime,
            'to_time' => $request->toTime
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Break created successfully',
            'data' => []
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $break = Breaks::find($id);

        if(!$break){
            return response()->json([
                'status' => 'error',
                'message' => 'Break data not found',
                'data' => []
            ], 404);
        }

        $breakData = [
            'name' => $break->name,
            'fromTime' => $break->from_time,
            'toTime' => $break->to_time
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Break data fetched successfully',
            'data' => $breakData
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->show($id);
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
        $validator = $this->validationCheck($request);

        if($validator->fails())
        {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->getMessageBag(),
                'data' => [],
            ], 422);
        }

        $checkBreakPresent = Breaks::find($id);

        if(empty($checkBreakPresent)){
            return response()->json([
                'status' => 'error',
                'messages' => 'Break not found.',
                'data' => [],
            ], 404);
        }

        $checkBreakPresent->update([
            'name' => $request->name,
            'from_time' => $request->fromTime,
            'to_time' => $request->toTime
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Break updated successfully',
            'data' => []
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $break = Breaks::find($id);

        if(!$break){
            return response()->json([
                'status' => 'error',
                'message' => 'Break data not found',
                'data' => []
            ], 404);
        }

        $break->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Break deleted successfully',
            'data' => []
        ], 200);
    }
}
