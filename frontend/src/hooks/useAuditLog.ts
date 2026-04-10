import { useGetAuditLogsQuery, useGetIpAddressHistoryQuery } from "@/store/services/auditLogApiSlice";
import { AuditLogEntry } from "@/types/apiTypes";

export const useAuditLog = () => {
  const { data, isLoading, isError } = useGetAuditLogsQuery();

  const rawData = data?.data as unknown as Record<string,unknown>;
  const entries: AuditLogEntry[] =
    Array.isArray(rawData) ? rawData : (Array.isArray(rawData?.data) ? rawData.data : []);

  return {
    entries,
    isLoading,
    isError,
  };
};

export const useIpAddressHistory = (ipId: number | null) => {
  const { data, isLoading } = useGetIpAddressHistoryQuery(ipId!, {
    skip: ipId === null,
  });

  return {
    history: data?.data ?? null,
    isLoading,
  };
};
