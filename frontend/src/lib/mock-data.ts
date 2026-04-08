export interface IpRecord {
  id: string;
  ipAddress: string;
  label: string;
  createdAt: string;
  updatedAt: string;
  createdBy: string;
}

export interface AuditEntry {
  id: string;
  timestamp: string;
  action: "login" | "add" | "modify";
  user: string;
  details: string;
}

export const mockIpRecords: IpRecord[] = [
  { id: "1", ipAddress: "202.92.249.111", label: "gifts.ad-group.com.au", createdAt: "2026-04-01T09:00:00Z", updatedAt: "2026-04-01T09:00:00Z", createdBy: "admin@company.com" },
  { id: "2", ipAddress: "10.0.0.1", label: "Gateway Router", createdAt: "2026-04-02T10:30:00Z", updatedAt: "2026-04-02T10:30:00Z", createdBy: "admin@company.com" },
  { id: "3", ipAddress: "192.168.1.100", label: "BFBC2 Server", createdAt: "2026-04-03T14:15:00Z", updatedAt: "2026-04-05T11:00:00Z", createdBy: "tech@company.com" },
  { id: "4", ipAddress: "172.16.0.50", label: "Spare", createdAt: "2026-04-04T08:45:00Z", updatedAt: "2026-04-04T08:45:00Z", createdBy: "admin@company.com" },
  { id: "5", ipAddress: "10.10.10.1", label: "DNS Primary", createdAt: "2026-04-05T16:20:00Z", updatedAt: "2026-04-07T09:30:00Z", createdBy: "tech@company.com" },
];

export const mockAuditLog: AuditEntry[] = [
  { id: "1", timestamp: "2026-04-07T09:30:00Z", action: "modify", user: "tech@company.com", details: 'Changed label of 10.10.10.1 from "DNS Server" to "DNS Primary"' },
  { id: "2", timestamp: "2026-04-05T11:00:00Z", action: "modify", user: "tech@company.com", details: 'Changed label of 192.168.1.100 from "Game Server" to "BFBC2 Server"' },
  { id: "3", timestamp: "2026-04-05T16:20:00Z", action: "add", user: "tech@company.com", details: 'Added IP 10.10.10.1 with label "DNS Server"' },
  { id: "4", timestamp: "2026-04-04T08:45:00Z", action: "add", user: "admin@company.com", details: 'Added IP 172.16.0.50 with label "Spare"' },
  { id: "5", timestamp: "2026-04-03T14:15:00Z", action: "add", user: "tech@company.com", details: 'Added IP 192.168.1.100 with label "Game Server"' },
  { id: "6", timestamp: "2026-04-03T14:00:00Z", action: "login", user: "tech@company.com", details: "User logged in" },
  { id: "7", timestamp: "2026-04-02T10:30:00Z", action: "add", user: "admin@company.com", details: 'Added IP 10.0.0.1 with label "Gateway Router"' },
  { id: "8", timestamp: "2026-04-01T09:00:00Z", action: "add", user: "admin@company.com", details: 'Added IP 202.92.249.111 with label "gifts.ad-group.com.au"' },
  { id: "9", timestamp: "2026-04-01T08:55:00Z", action: "login", user: "admin@company.com", details: "User logged in" },
];

export function validateIpAddress(ip: string): boolean {
  const parts = ip.split(".");
  if (parts.length !== 4) return false;
  return parts.every((part) => {
    const num = parseInt(part, 10);
    return !isNaN(num) && num >= 0 && num <= 255 && part === num.toString();
  });
}
