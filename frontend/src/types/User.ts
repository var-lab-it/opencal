import {Team} from "./Team";

export type User = {
    id: number,
    email: string,
    givenName: string,
    familyName: string,
    roles: string[],
    teams: Array<Team>,
    managedTeams: Array<Team>,
}
