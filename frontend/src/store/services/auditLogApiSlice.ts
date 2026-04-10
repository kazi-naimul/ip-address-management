import { apiSlice } from "./baseQuery";
import type { ApiResponse, AuditLogEntry, IpAddressHistory } from "@/types/apiTypes";

const auditLogApiSlice = apiSlice.injectEndpoints({
  endpoints: (build) => ({
    getAuditLogs: build.query<ApiResponse<AuditLogEntry[]>, void>({
      query: () => "/audit-logs",
      providesTags: ["AuditLog"],
    }),

    getIpAddressHistory: build.query<ApiResponse<IpAddressHistory>, number>({
      query: (id) => `/audit-logs/ip-address/${id}`,
      providesTags: (_result, _error, id) => [{ type: "AuditLog", id }],
    }),
  }),
});

export const {
  useGetAuditLogsQuery,
  useGetIpAddressHistoryQuery,
} = auditLogApiSlice;
