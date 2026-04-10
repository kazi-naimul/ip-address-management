export interface ApiResponse<T = unknown> {
  status: boolean;
  code: number;
  message: string;
  data?: T;
  errors?: Record<string, unknown>;
}

export interface UserData {
  id: number;
  name: string;
  email: string;
}

export interface IpAddressRecord {
  id: number;
  ip_address: string;
  label: string;
  created_by: number;
  created_at: string;
  updated_at: string;
  creator?: UserData;
}

export interface AuditLogEntry {
  id: number;
  user_id: number;
  action: string;
  model_type: string | null;
  model_id: number | null;
  details: Record<string, unknown>;
  created_at: string;
  user?: UserData;
}

export interface IpAddressHistory {
  ip_address: IpAddressRecord;
  changes: AuditLogEntry[];
}

export interface LoginResponse {
  token: string;
}

export interface CreateIpPayload {
  ip_address: string;
  label: string;
}

export interface UpdateIpPayload {
  id: number;
  label: string;
}
