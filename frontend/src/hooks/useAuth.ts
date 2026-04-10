import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAppDispatch, useAppSelector } from "@/store/hook";
import { setCredentials, clearCredentials, selectCurrentUser, selectIsAuthenticated } from "@/store/features/auth/authSlice";
import { useLoginMutation } from "@/store/services/authApiSlice";
import { AUTH_CONFIG } from "@/constants/globalConstants";

const useAuth = () => {
  const dispatch = useAppDispatch();
  const navigate = useNavigate();

  const user = useAppSelector(selectCurrentUser);
  const isAuthenticated = useAppSelector(selectIsAuthenticated);

  const [loading, setLoading] = useState(false);
  const [loginMutation] = useLoginMutation();

  const login = async (
    email: string,
    password: string,
    onError?: (message: string) => void
  ) => {
    setLoading(true);
    try {
      const result = await loginMutation({ email, password }).unwrap();

      if (!result.data?.token) {
        onError?.("Login failed. Please try again.");
        return false;
      }

      const token = result.data.token;

      // Persist the token so protected requests work
      localStorage.setItem(AUTH_CONFIG.accessToken, token);

      const userData = { id: 0, name: email.split("@")[0], email };
      localStorage.setItem(AUTH_CONFIG.userData, JSON.stringify(userData));

      dispatch(setCredentials({ user: userData, token }));
      navigate("/dashboard");
      return true;
    } catch (err: unknown) {
      const message =
        (err as { data?: { message?: string } })?.data?.message ?? "Invalid credentials. Please try again.";
      onError?.(message);
      return false;
    } finally {
      setLoading(false);
    }
  };

  const logout = () => {
    localStorage.removeItem(AUTH_CONFIG.accessToken);
    localStorage.removeItem(AUTH_CONFIG.userData);
    dispatch(clearCredentials());
    navigate("/");
  };

  return {
    user,
    userEmail: user?.email ?? null,
    isAuthenticated,
    loading,
    login,
    logout,
  };
};

export default useAuth;
