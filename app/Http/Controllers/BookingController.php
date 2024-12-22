<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function availableSlots(Request $request, $stadiumId, $pitchId)
    {
        $date = $request->input('date');
        $slots = $this->generateSlots(); // Helper function to generate slots

        $bookedSlots = Booking::where('pitch_id', $pitchId)
            ->where('date', $date)
            ->get(['start_time', 'end_time']);

        $availableSlots = array_filter($slots, function ($slot) use ($bookedSlots) {
            foreach ($bookedSlots as $booked) {
                if ($slot['start_time'] === $booked->start_time && $slot['end_time'] === $booked->end_time) {
                    return false;
                }
            }
            return true;
        });
        return response()->json($availableSlots);
    }

    public function bookPitch(Request $request, $stadiumId, $pitchId)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check for overlapping bookings
        $existingBooking = Booking::where('pitch_id', $pitchId)
            ->where('date', $data['date'])
            ->where('start_time', $data['start_time'])
            ->exists();

        if ($existingBooking) {
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
