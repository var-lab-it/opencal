import {EventType, EventTypes} from "../types/EventType";
import apiClient from "./api";
import {Availability} from "../types/Availability";
import {Booking} from "../types/Booking";
import {Event} from "../types/Event";

export async function getEventTypes(email: string): Promise<EventTypes> {
    try {
        const response = await apiClient.get(`/event_types?&host.email=${email}`);
        return response.data;
    } catch (error) {
        console.error(error);
        throw error;
    }
}

export async function getOneEventType(email: string, slug: string): Promise<EventType> {
    try {
        const response = await apiClient.get(`/event_types?&host.email=${email}&slug=${slug}`);
        return response.data[0];
    } catch (error) {
        console.error(error);
        throw error;
    }
}

export async function getDayAvailabilities(email: string, dayAsString: string, eventTypeId: number): Promise<Availability> {
    try {
        const response = await apiClient.get(`/availability/day/?email=${email}&date=${dayAsString}&event_type_id=${eventTypeId}`);
        return response.data;
    } catch (error) {
        console.error(error);
        throw error;
    }
}

export async function submitBooking(booking: Booking): Promise<EventTypes> {
    try {
        const response = await apiClient.post(`/events`, {
            participantName: booking.participantName,
            participantEmail: booking.participantEmail,
            participantMessage: booking.participantMessage,
            eventType: `/event_types/${booking.eventType.id}`,
            day: booking.day,
            startTime: booking.startTime,
            endTime: booking.endTime,
        });
        return response.data;
    } catch (error) {
        console.error(error);
        throw error;
    }
}

export async function getOneEvent(eventId: number): Promise<Event> {
    try {
        const response = await apiClient.get(`/events/${eventId}`);
        return response.data;
    } catch (error) {
        console.error(error);
        throw error;
    }
}

export async function cancelBooking(eventId: number, cancellationHash: string): Promise<null> {
    try {
        const response = await apiClient.post(`/events/${eventId}/cancel`, {
            cancellationHash: cancellationHash,
        });
        return response.data;
    } catch (error) {
        console.error(error);
        throw error;
    }
}
