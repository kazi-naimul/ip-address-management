import { publicApiSlice } from "./baseQuery";
import type { ApiResponse, LoginResponse } from "@/types/apiTypes";

interface LoginParams {
  email: string;
  password: string;
}

const authApiSlice = publicApiSlice.injectEndpoints({
  endpoints: (build) => ({
    login: build.mutation<ApiResponse<LoginResponse>, LoginParams>({
      query: (credentials) => ({
        url: "/login",
        method: "POST",
        body: credentials,
      }),
    }),
  }),
});

export const { useLoginMutation } = authApiSlice;
