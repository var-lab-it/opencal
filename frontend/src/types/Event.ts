import {EventType} from "./EventType";

export type Event = {
    id: number,
    eventType: EventType,
    startTime: string,
    endTime: string,
    day: string,
    participantName: string,
    participantEmail: string,
    participantMessage: string,
    cancellationHash: string,
    cancelledByAttendee: boolean,
}
