import { useMemo } from "react";
import { ICalendarMeta, IItem } from "../types";

export function Calendar({item, index}: { item: IItem, index: number }) {

    const meta: ICalendarMeta = useMemo(() => {
        try {
            const parsed = JSON.parse(item.meta);
            return { name: typeof parsed?.name === 'string' && parsed.name ? parsed.name : item.identifier };
        } catch {
            return { name: item.identifier };
        }
    }, [item])

    return (
        <>
            <div>
                {index + 1}
            </div>
            <a className="player-name" href={`https://online.sbis.ru/person/${encodeURIComponent(item.identifier)}`} target="_blank" rel="noopener noreferrer">
                {meta.name}
            </a>
            <div>
                {item.score}
            </div>
        </>
    );
}
