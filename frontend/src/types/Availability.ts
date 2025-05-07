import {TimeSlots} from "./TimeSlot";

export type Availability = {
    day_of_week: string,
    event_type: [
        id: number,
        name: string,
    ],
    availabilities: TimeSlots,
}
