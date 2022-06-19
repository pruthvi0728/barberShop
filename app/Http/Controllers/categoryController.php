<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class categoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categories::all();

        $categoriesData = [];
        foreach($categories as $category){
            $categoriesData[] = [
                'categoryId' => $category->id,
                'name' => $category->name,
                'slotTime' => $category->time_of_slot,
                'cleanUpTime' => $category->clean_up_time,
                'maxClientPerSlot' => $category->max_client,
                'futureDaysBook' => $category->future_days_to_book,
                'mondayToFridayOpeningTime' => $category->mon_fri_from_time,
                'mondayToFridayClosingTime' => $category->mon_fri_to_time,
                'saturdayOpeningTime' => $category->sat_from_time,
                'saturdayClosingTime' => $category->sat_to_time
            ];
        }
        return response()->json([
            'status' => 'success',
            'message' => 'List of categories',
            'data' => $categoriesData
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return response()->json([
            'status' => 'error',
            'message' => 'Not found',
            'data' => []
        ], 404);
    }

    public function validationCheck($request){
        return Validator::make( $request->all(), [
            'name' => 'required',
            'slotTime' => 'required|numeric|gt:0|mod5',
            'cleanUpTime' => 'required|numeric|gt:0|mod5',
            'maxClientPerSlot' => 'required|numeric',
            'futureDaysBook' => 'required|numeric',
            'mondayToFridayOpeningTime' => 'required',
            'mondayToFridayClosingTime' => 'required',
            'saturdayOpeningTime' => 'required',
            'saturdayClosingTime' => 'required'
        ], [
            'mod5' => 'Number should be like 5, 10, 15...'
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

        $checkAlreadyPresent = Categories::where('name', $request->name)->get()->count();

        if($checkAlreadyPresent > 0){
            return response()->json([
                'status' => 'error',
                'messages' => 'Category already present.',
                'data' => [],
            ], 409);
        }

        Categories::create([
            'name' => $request->name,
            'time_of_slot' => $request->slotTime,
            'clean_up_time' => $request->cleanUpTime,
            'max_client' => $request->maxClientPerSlot,
            'future_days_to_book' => $request->futureDaysBook,
            'mon_fri_from_time' => $request->mondayToFridayOpeningTime,
            'mon_fri_to_time' => $request->mondayToFridayClosingTime,
            'sat_from_time' => $request->saturdayOpeningTime,
            'sat_to_time' => $request->saturdayClosingTime
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
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
        $category = Categories::find($id);

        if(!$category){
            return response()->json([
                'status' => 'error',
                'message' => 'Category data not found',
                'data' => []
            ], 404);
        }

        $categoriesData = [
            'categoryId' => $category->id,
            'name' => $category->name,
            'slotTime' => $category->time_of_slot,
            'cleanUpTime' => $category->clean_up_time,
            'maxClientPerSlot' => $category->max_client,
            'futureDaysBook' => $category->future_days_to_book,
            'mondayToFridayOpeningTime' => $category->mon_fri_from_time,
            'mondayToFridayClosingTime' => $category->mon_fri_to_time,
            'saturdayOpeningTime' => $category->sat_from_time,
            'saturdayClosingTime' => $category->sat_to_time
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Category data fetched successfully',
            'data' => $categoriesData
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

        $checkCategoryPresent = Categories::find($id);

        if(empty($checkCategoryPresent)){
            return response()->json([
                'status' => 'error',
                'messages' => 'Category not found.',
                'data' => [],
            ], 404);
        }

        $checkCategoryPresent->update([
            'name' => $request->name,
            'time_of_slot' => $request->slotTime,
            'clean_up_time' => $request->cleanUpTime,
            'max_client' => $request->maxClientPerSlot,
            'future_days_to_book' => $request->futureDaysBook,
            'mon_fri_from_time' => $request->mondayToFridayOpeningTime,
            'mon_fri_to_time' => $request->mondayToFridayClosingTime,
            'sat_from_time' => $request->saturdayOpeningTime,
            'sat_to_time' => $request->saturdayClosingTime
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
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
        $category = Categories::find($id);

        if(!$category){
            return response()->json([
                'status' => 'error',
                'message' => 'Category data not found',
                'data' => []
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully',
            'data' => []
        ], 200);
    }
}
