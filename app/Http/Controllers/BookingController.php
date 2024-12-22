<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class BookingController
 * Handles the management of bookings for pitches, including available slot retrieval and booking creation.
 */
class BookingController extends Controller
{
    /**
     * Retrieves available time slots for a specific pitch on a given date.
     *
     * @param Request $request The incoming request containing query parameters.
     * @param int $pitchId The unique identifier for the pitch.
     * @return \Illuminate\Http\JsonResponse A JSON response containing available time slots.
     */
    public function availableSlots(Request $request, $pitchId)
    {
        $date = $request->input('date');
        $slots = $this->generateSlots(); // Helper function to generate slots
        $bookedSlots = Booking::where('pitch_id', $pitchId)
            ->get(['start_time', 'end_time']);
        $availableSlots = array_filter($slots, function($slot) use ($bookedSlots)
        {
            foreach($bookedSlots as $booked)
            {
                if($slot['start_time'] === $booked->start_time || $slot['end_time'] === $booked->end_time || ($slot['start_time'] > $booked->start_time && $slot['start_time'] < $booked->end_time))
                {
                    return false;
                }
            }
            return true;
        });
        return response()->json($availableSlots);
    }

    /**
     * Generates a list of time slots between specified start and end times.
     *
     * @param string $start Starting time for the slots.
     * @param string $end Ending time for the slots.
     * @param int $slotDuration Duration of each slot in minutes.
     * @return array An array of time slots with start and end times.
     */
    private function generateSlots($start = '08:00:00', $end = '22:00:00', $slotDuration = 30)
    {
        $slots = [];
        $startTime = Carbon::createFromFormat('H:i:s', $start);
        $endTime = Carbon::createFromFormat('H:i:s', $end);
        while($startTime->addMinutes($slotDuration)->lte($endTime))
        {
            $slotStart = $startTime->copy()->subMinutes($slotDuration)->format('H:i:s');
            $slotEnd = $startTime->format('H:i:s');
            $slots[] = [
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
            ];
        }
        return $slots;
    }

    /**
     * Books a pitch for a specified time slot on a given date.
     *
     * @param Request $request The incoming request containing booking data.
     * @param int $pitchId The unique identifier for the pitch being booked.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the booking attempt.
     */
    public function bookPitch(Request $request, $pitchId)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
        ]);
        $start  = new Carbon($data['start_time']);
        $end    = new Carbon($data['end_time']);

        if($start->diffInMinutes($end) < 60 )
        {
            return response()->json(['message' => 'Min booking duration is 60 minutes'], 409);
        }

        if($start->diffInMinutes($end) > 90 )
        {
            return response()->json(['message' => 'Max booking duration is 90 minutes'], 409);
        }

        // Check for overlapping bookings
        $existingBooking = Booking::where('pitch_id', $pitchId)
            ->where('date', $data['date'])
            ->where('start_time', $data['start_time'])
            ->exists();
        if($existingBooking)
        {
            return response()->json(['message' => 'Slot already booked'], 409);
        }
        // Create booking
        Booking::create([
            'pitch_id' => $pitchId,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
        ]);
        return response()->json(['message' => 'Booking successful'], 201);
    }
}

