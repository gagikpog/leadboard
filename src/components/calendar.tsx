import { useMemo } from "react";
import { ICalendarMeta, IItem } from "../types";

export function Calendar({item, index}: { item: IItem, index: number }) {

    const meta: ICalendarMeta = useMemo(() => {
        return JSON.parse(item.meta);
    }, [item])

    return (
        <>
            <div>
                {index + 1}
            </div>
            <a href={`https://online.sbis.ru/person/${item.identifier}`} target="__blink">
                {meta.name}
            </a>
            <div>
                {item.score}
            </div>
        </>
    );
}
