import {
  createApi,
  fetchBaseQuery,
} from "@reduxjs/toolkit/query/react";
import { AUTH_CONFIG } from "@/constants/globalConstants";

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000/api";

// Base query that attaches the bearer token to every request
const baseQueryWithAuth = fetchBaseQuery({
  baseUrl: apiBaseUrl,
  prepareHeaders: (headers) => {
    const token = localStorage.getItem(AUTH_CONFIG.accessToken);
    if (token) {
      headers.set("Authorization", `Bearer ${token}`);
    }
    headers.set("Accept", "application/json");
    headers.set("Content-Type", "application/json");
    return headers;
  },
});

// API slice for authenticated endpoints (IP addresses, audit logs)
export const apiSlice = createApi({
  reducerPath: "api",
  baseQuery: baseQueryWithAuth,
  tagTypes: ["IpAddress", "AuditLog"],
  endpoints: () => ({}),
});

// Separate slice for public endpoints (login) - no token needed
export const publicApiSlice = createApi({
  reducerPath: "publicApi",
  baseQuery: fetchBaseQuery({
    baseUrl: apiBaseUrl,
    prepareHeaders: (headers) => {
      headers.set("Accept", "application/json");
      headers.set("Content-Type", "application/json");
      return headers;
    },
  }),
  endpoints: () => ({}),
});
