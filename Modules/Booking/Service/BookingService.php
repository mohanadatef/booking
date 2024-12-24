<?php

namespace Modules\Booking\Service;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\Booking\Repositories\BookingRepository;
use Modules\Basic\Service\BasicService;

/**
 * Class BookingService
 * This class provides services related to stadium operations,
 * extending the functionality of BasicService.
 */
class BookingService extends BasicService
{
    protected BookingRepository $repo;

    /**
     * BookingService constructor.
     * Initializes the BookingService with a BookingRepository instance.
     *
     * @param BookingRepository $repository
     */
    public function __construct(BookingRepository $repository)
    {
        $this->repo = $repository;
    }

    /**
     * Retrieves available time slots based on the request parameters
     * by filtering out booked slots from a generated list of slots.
     *
     * @param Request $request HTTP request containing booking information.
     * @return array Filtered array of available time slots.
     */
    public function availableSlots(Request $request)
    {
        $slots = $this->generateSlots(); // Helper function to generate slots
        $bookedSlots = $this->repo->findBy($request, column:['start_time', 'end_time']);
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
        return $availableSlots;
    }

    /**
     * Generates a list of time slots between specified start and end times.
     *
     * @param string $start Starting time for the slots.
     * @param string $end Ending time for the slots.
     * @param int $slotDuration Duration of each slot in minutes.
     * @return array An array of time slots with start and end times.
     */
    private function generateSlots($start = '00:00:00', $end = '24:00:00', $slotDuration = 30)
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
     * Stores a new booking based on the request parameters,
     * ensuring that the booking meets minimum and maximum duration requirements
     * and checking for overlapping bookings.
     *
     * @param Request $request HTTP request containing booking details.
     * @return array|false Response containing booking confirmation or an error message.
     */
    public function store(Request $request)
    {
        $start = new Carbon($request->start_time);
        $end = new Carbon($request->end_time);
        if($start->diffInMinutes($end) < 60)
        {
            return ['message' => 'Min booking duration is 60 minutes'];
        }
        if($start->diffInMinutes($end) > 90)
        {
            return ['message' => 'Max booking duration is 90 minutes'];
        }
        // Check for overlapping bookings
        $existingBooking = Booking::where('pitch_id', $request->pitch_id)
            ->where('date', $request->date)
            ->where('start_time', $request->start_time)
            ->exists();
        if($existingBooking)
        {
            return ['message' => 'Slot already booked'];
        }
        $data = $this->repo->save($request);
        if ($data) {
            return $data;
        }
        return false;
    }
}
