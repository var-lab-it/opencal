import {EventType} from "./EventType";

export type Booking = {
    participantName: string,
    participantEmail: string,
    participantMessage: string,
    eventType: EventType,
    startTime: string,
    endTime: string,
    day: string,
}
