import { configureStore } from "@reduxjs/toolkit";
import { apiSlice, publicApiSlice } from "./services/baseQuery";
import authReducer from "./features/auth/authSlice";

export const store = configureStore({
  reducer: {
    [apiSlice.reducerPath]: apiSlice.reducer,
    [publicApiSlice.reducerPath]: publicApiSlice.reducer,
    auth: authReducer,
  },
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware({
      serializableCheck: false,
    }).concat([apiSlice.middleware, publicApiSlice.middleware]),
});

export type AppDispatch = typeof store.dispatch;
export type RootState = ReturnType<typeof store.getState>;
