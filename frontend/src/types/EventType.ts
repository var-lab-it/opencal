export type EventType = {
    id: number,
    name: string,
    description: string,
    duration: number,
    slug: string,
    host: {
        givenName: string,
        familyName: string,
    }
}

export type EventTypes = Array<EventType>
