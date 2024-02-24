export type TGameType = 'calendar';

export interface IResponse<T> {
    data: T;
    status: 'error' | 'done'
    message: string;
}

export interface IItem {
    identifier: string;
    meta: string;
    date: string;
    score: string;
}

export interface ICalendarMeta {
    name: string;
}
