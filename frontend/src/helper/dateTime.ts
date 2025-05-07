import type {CalendarDay} from "v-calendar/dist/types/src/utils/page";

export function formatCalendarDayToString(day: CalendarDay): string {
    const year = day.year;
    const month = String(day.month).padStart(2, '0');
    const date = String(day.day).padStart(2, '0');
    return `${year}-${month}-${date}`;
}
