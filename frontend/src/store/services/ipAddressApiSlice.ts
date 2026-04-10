import { apiSlice } from "./baseQuery";
import type {
  ApiResponse,
  IpAddressRecord,
  CreateIpPayload,
  UpdateIpPayload,
} from "@/types/apiTypes";

const ipAddressApiSlice = apiSlice.injectEndpoints({
  endpoints: (build) => ({
    getIpAddresses: build.query<ApiResponse<IpAddressRecord[]>, void>({
      query: () => "/ip-addresses",
      providesTags: ["IpAddress"],
    }),

    getIpAddress: build.query<ApiResponse<IpAddressRecord>, number>({
      query: (id) => `/ip-addresses/${id}`,
      providesTags: (_result, _error, id) => [{ type: "IpAddress", id }],
    }),

    createIpAddress: build.mutation<ApiResponse<IpAddressRecord>, CreateIpPayload>({
      query: (body) => ({
        url: "/ip-addresses",
        method: "POST",
        body,
      }),
      invalidatesTags: ["IpAddress", "AuditLog"],
    }),

    updateIpAddress: build.mutation<ApiResponse<IpAddressRecord>, UpdateIpPayload>({
      query: ({ id, label }) => ({
        url: `/ip-addresses/${id}`,
        method: "PUT",
        body: { label },
      }),
      invalidatesTags: ["IpAddress", "AuditLog"],
    }),
  }),
});

export const {
  useGetIpAddressesQuery,
  useGetIpAddressQuery,
  useCreateIpAddressMutation,
  useUpdateIpAddressMutation,
} = ipAddressApiSlice;
