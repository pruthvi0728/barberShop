<?php

namespace App\Http\Controllers;

use App\Models\Holidays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class holidayController extends Controller
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
            'message' => 'List of holidays',
            'data' => Holidays::select('name', 'date')->get()
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
            'date' => 'required|date_format:Y-m-d'
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

        Holidays::create([
            'name' => $request->name,
            'date' => $request->date
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Holiday created successfully',
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
        $holiday = Holidays::find($id);

        if(!$holiday){
            return response()->json([
                'status' => 'error',
                'message' => 'Holiday data not found',
                'data' => []
            ], 404);
        }

        $holidayData = [
            'name' => $holiday->name,
            'date' => $holiday->date
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Holiday data fetched successfully',
            'data' => $holidayData
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

        $checkHolidayPresent = Holidays::find($id);

        if(empty($checkHolidayPresent)){
            return response()->json([
                'status' => 'error',
                'messages' => 'Holiday not found.',
                'data' => [],
            ], 404);
        }

        $checkHolidayPresent->update([
            'name' => $request->name,
            'date' => $request->date
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Holiday updated successfully',
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
        $holiday = Holidays::find($id);

        if(!$holiday){
            return response()->json([
                'status' => 'error',
                'message' => 'Holiday data not found',
                'data' => []
            ], 404);
        }

        $holiday->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Holiday deleted successfully',
            'data' => []
        ], 200);
    }
}
