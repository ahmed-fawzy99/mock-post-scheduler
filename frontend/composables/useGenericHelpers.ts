export const isObject = (value: unknown): value is Record<string, unknown> => {
    return value !== null && typeof value === 'object' && !Array.isArray(value)
}

export const getInitials = (name: string): string => {
    return name.split(' ').map((n) => n[0]).join('').toUpperCase()
}

export const capitalizeFirstLetter= (val) =>
    String(val).charAt(0).toUpperCase() + String(val).slice(1);



export function truncateText(str: string, num: number = 80): string {
    if (!str)
        return "";
    if (str.length > num)
        return str.slice(0, num) + "...";
    else
        return str;
}


export const scrollToTop = (): void => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
};


export const apiUrlBuilder = (endpoint: string, params: Array<string>): string => {
    return params.length > 0 ? `/${endpoint}?${params.join('&')}` : `/${endpoint}`;
}