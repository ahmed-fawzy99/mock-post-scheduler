export function handleError(error, isFetchError = false) {
    if (error?.status >= 500) {
        // if error.message is in this format: [POST/GET/PATCH/DELETE] {URL}/api/endpoint 500 (Internal Server Error), return only status and message
        const regex = /\[\w+\]\s".*?":\s(\d{3}\s.+)/;
        if (regex.test(error.message)) {
            const match = error.message.match(regex);
            return match[1]; // Extract and return only the status and message
        }
        return error.message
    }
    return isFetchError ? error.errors : (error.response?._data?.errors || []);
}