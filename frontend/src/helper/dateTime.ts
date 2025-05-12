import type {CalendarDay} from "v-calendar/dist/types/src/utils/page";

export function formatCalendarDayToString(day: CalendarDay): string {
    const year = day.year;
    const month = String(day.month).padStart(2, '0');
    const date = String(day.day).padStart(2, '0');
    return `${year}-${month}-${date}`;
}

export function formatDateStringToDay(dateString: string | undefined): string {
    if (!dateString) return '';

    const date = new Date(dateString);

    if (isNaN(date.getTime())) return '';

    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();

    return `${day}.${month}.${year}`;
}

export function formatDateStringToTime(dateString: string | undefined): string {
    if (!dateString) return '';

    const date = new Date(dateString);

    if (isNaN(date.getTime())) return '';

    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');

    return `${hours}:${minutes}`;
}
