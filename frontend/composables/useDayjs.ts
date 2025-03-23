import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import relativeTime from 'dayjs/plugin/relativeTime'
import localizedFormat from 'dayjs/plugin/localizedFormat'
import customParseFormat from 'dayjs/plugin/customParseFormat'

dayjs.extend(utc)
dayjs.extend(timezone)
dayjs.extend(relativeTime)
dayjs.extend(localizedFormat)
dayjs.extend(customParseFormat)

// dayjs.tz.setDefault('UTC')

export function useDayjs() {
    // Common date formatting functions
    const formatDate = (date: string | Date, format = 'D MMM YYYY h:mm A') => {
        return dayjs(date).format(format)
    }

    const formatDateNoTime = (date: string | Date, format = 'D MMM YYYY') => {
        return dayjs(date).format(format)
    }

    const formatDateTime = (date: string | Date, format = 'YYYY-MM-DD h:mm:ss A') => {
        return dayjs(date).format(format)
    }

    const fromNow = (date: string | Date) => {
        return dayjs(date).fromNow()
    }

    const isValid = (date: string | Date) => {
        return dayjs(date).isValid()
    }

    const toUTC = (date: string | Date) => {
        return dayjs(date).utc()
    }

    const toTimezone = (date: string | Date, timezone: string) => {
        return dayjs(date).tz(timezone)
    }

    // You can add more utility functions as needed

    return {
        dayjs,
        formatDate,
        formatDateNoTime,
        formatDateTime,
        fromNow,
        isValid,
        toUTC,
        toTimezone,
    }
}