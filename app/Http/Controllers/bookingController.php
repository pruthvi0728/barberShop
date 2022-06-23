<?php

namespace App\Http\Controllers;

use App\Models\Breaks;
use App\Models\Categories;
use App\Models\Customers;
use App\Models\Holidays;
use App\Models\slotsBooked;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class bookingController extends Controller
{
    public function createBooking(Request $request)
    {
        // Normal validation checks
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'firstName' => 'required',
            'lastName' => 'required',
            'slotFrom' => 'required|date_format:Y-m-d H:i',
            'categoryId' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->getMessageBag(),
                'data' => []
            ], 422);
        }

        $carbonDateOfSlotFrom = Carbon::parse($request->slotFrom);

        if($carbonDateOfSlotFrom->format('Y-m-d H:i:s') < Carbon::now()->format('Y-m-d H:i:s')){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking date should be grater than current time.',
                'data' => []
            ], 422);
        }

        $checkSlotDay = $carbonDateOfSlotFrom->dayOfWeek;

        // check for slotFrom is on sunday
        if(!$checkSlotDay){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking slots are not available on Sundays.',
                'data' => []
            ], 422);
        }

        $categoryData = Categories::find($request->categoryId);

        // check for category
        if(empty($categoryData)){
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
                'data' => []
            ], 404);
        }

        $holidayCheck = Holidays::where('date', $carbonDateOfSlotFrom->format('Y-m-d'))->first();

        if($holidayCheck){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking slots are not available becuase of '.$holidayCheck->name.'.',
                'data' => []
            ], 422);
        }

        // check For Date Validation
        $slotTimeInMinutes = (int) $carbonDateOfSlotFrom->format('i');

        if($slotTimeInMinutes % 5){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking slots minute should be multiple of 5',
                'data' => []
            ], 422);
        }

        $futureDate = Carbon::now()->addDays($categoryData->future_days_to_book)->format('Y-m-d');

        if($carbonDateOfSlotFrom->format('Y-m-d') > $futureDate){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking slots date should be equal or less than '.$futureDate,
                'data' => []
            ], 422);
        }

        if($checkSlotDay == 6){
            $openingTime = $categoryData->sat_from_time;
            $closingTime = $categoryData->sat_to_time;
        }else{
            $openingTime = $categoryData->mon_fri_from_time;
            $closingTime = $categoryData->mon_fri_to_time;
        }

        if($carbonDateOfSlotFrom->format('H:i:s') < $openingTime){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking slot time should be equal or greater than '.$openingTime,
                'data' => []
            ], 422);
        }

        if((clone $carbonDateOfSlotFrom)->addMinutes($categoryData->time_of_slot)->format('H:i:s') > $closingTime){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking slot time should be less than '.$closingTime,
                'data' => []
            ], 422);
        }

        $checkBreakTime = Breaks::where('from_time', '<=' , $carbonDateOfSlotFrom->format('H:i:s'))->where('to_time', '>', $carbonDateOfSlotFrom->format('H:i:s'))->get()->count();

        if($checkBreakTime > 0){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking slot time is in break',
                'data' => []
            ], 422);
        }

        $largeBreakTime = Breaks::where('from_time', '>', $carbonDateOfSlotFrom->format('H:i:s'))->first();

        if(!empty($largeBreakTime) && (clone $carbonDateOfSlotFrom)->addMinutes($categoryData->time_of_slot)->format('H:i:s') > $largeBreakTime->from_time){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking slot time is in upcomming break',
                'data' => []
            ], 422);
        }

        $slotDetails = slotsBooked::where('from', $carbonDateOfSlotFrom->format('Y-m-d H:i:s'))->where('category_id', $categoryData->id)->first();

        if(!empty($slotDetails) && $slotDetails->client_count+1 > $categoryData->max_client){
            return response()->json([
                'status' => 'error',
                'message' => 'Booking slot is full.',
                'data' => []
            ], 422);
        }else{
            $takeCountSlotsOfBetween = slotsBooked::where('from', '<=', $carbonDateOfSlotFrom->format('Y-m-d H:i:s'))->where('to', '>', $carbonDateOfSlotFrom->format('Y-m-d H:i:s'))->where('category_id', $categoryData->id)->get()->count();

            if($takeCountSlotsOfBetween > 0){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking slot is conflicts to other slot.',
                    'data' => []
                ], 422);
            }
        }

        if($slotDetails){
            $checkCustomerAlreadyInSlot = Customers::where('email', $request->email)->where('slot_booked_id', $slotDetails->id)->get()->count();
            if($checkCustomerAlreadyInSlot > 0){
                return response()->json([
                    'status' => 'error',
                    'message' => 'User is already in this slot.',
                    'data' => []
                ], 422);
            }
            $slotDetails->update([
                'client_count' => $slotDetails->client_count + 1
            ]);
            $slotId = $slotDetails->id;
        }else{
            $slotData = slotsBooked::create([
                'from' => $carbonDateOfSlotFrom->format('Y-m-d H:i:s'),
                'to' => (clone $carbonDateOfSlotFrom)->addMinutes($categoryData->time_of_slot + $categoryData->clean_up_time)->format('Y-m-d H:i:s'),
                'client_count' => 1,
                'category_id' => $categoryData->id
            ]);
            $slotId = $slotData->id; 
        }

        Customers::create([
            'email' => $request->email,
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'slot_booked_id' => $slotId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking created successfully',
            'data' => []
        ], 200);
    }

    public function details(Request $request)
    {
        $categories = Categories::all();

        $categoriesData = [];
        foreach($categories as $category){
            $slotDetails = slotsBooked::where('category_id', $category->id)->where('from', '>', Carbon::now()->format('Y-m-d H:i:s'))->orderBy('from', 'DESC')->get();
            $slotData = [];
            foreach($slotDetails as $slot){
                $slotData[] = [
                    'from' => $slot->from,
                    'to' => $slot->to,
                    'clientCount' => $slot->client_count,
                    'availableCount' => $category->max_client - $slot->client_count
                ];
            }
            $categoriesData[] = [
                'categoryId' => $category->id,
                'name' => $category->name,
                'slotTime' => $category->time_of_slot,
                'cleanUpTime' => $category->clean_up_time,
                'TotalSlotTime' => $category->time_of_slot + $category->clean_up_time,
                'maxClientPerSlot' => $category->max_client,
                'futureDaysBook' => $category->future_days_to_book,
                'mondayToFridayOpeningTime' => $category->mon_fri_from_time,
                'mondayToFridayClosingTime' => $category->mon_fri_to_time,
                'saturdayOpeningTime' => $category->sat_from_time,
                'saturdayClosingTime' => $category->sat_to_time,
                'bookedSlots' => $slotData
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data fetched successfully',
            'data' => [
                'events' => $categoriesData,
                'breaks' => Breaks::select('name', 'from_time as fromTime', 'to_time as toTime')->get(),
                'holidays' => Holidays::select('name', 'date')->where('date', '>=', Carbon::now()->format('Y-m-d'))->get()
            ]
        ]);
    }
}
