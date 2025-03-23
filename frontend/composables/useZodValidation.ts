import * as z from "zod";

export const passwordSchema = z
    .string()
    .min(8, {message: "Password must be at least 8 characters long"})
    .refine((password) => /[A-Z]/.test(password), {
        message: "Password must contain at least one uppercase letter",
    })
    .refine((password) => /[a-z]/.test(password), {
        message: "Password must contain at least one lowercase letter",
    })
    .refine((password) => /[0-9]/.test(password), {message: "Password must contain at least one number"})

