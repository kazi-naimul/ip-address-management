import { useState } from "react";
import { toast } from "sonner";
import {
  useGetIpAddressesQuery,
  useCreateIpAddressMutation,
  useUpdateIpAddressMutation,
} from "@/store/services/ipAddressApiSlice";
import type { IpAddressRecord } from "@/types/apiTypes";

const useIpAddress = () => {
  const { data, isLoading, isFetching, isError } = useGetIpAddressesQuery();
  const [createIpAddress, { isLoading: isCreating }] = useCreateIpAddressMutation();
  const [updateIpAddress, { isLoading: isUpdating }] = useUpdateIpAddressMutation();

  const [showAddDialog, setShowAddDialog] = useState(false);
  const [editRecord, setEditRecord] = useState<IpAddressRecord | null>(null);
  const [viewRecord, setViewRecord] = useState<IpAddressRecord | null>(null);

  const records = data?.data ?? [];

  const addIpAddress = async (ip_address: string, label: string) => {
    try {
      await createIpAddress({ ip_address, label }).unwrap();
      toast.success("IP address added successfully");
      setShowAddDialog(false);
      return true;
    } catch (err: unknown) {
      const message = (err as { data?: { message?: string } })?.data?.message ?? "Failed to add IP address";
      toast.error(message);
      return false;
    }
  };

  const updateLabel = async (id: number, label: string) => {
    try {
      await updateIpAddress({ id, label }).unwrap();
      toast.success("Label updated successfully");
      setEditRecord(null);
      return true;
    } catch (err: unknown) {
      const message = (err as { data?: { message?: string } })?.data?.message ?? "Failed to update label";
      toast.error(message);
      return false;
    }
  };

  const openEdit = (record: IpAddressRecord) => {
    setEditRecord(record);
  };

  const openView = (record: IpAddressRecord) => {
    setViewRecord(record);
  };

  return {
    records,
    isLoading,
    isFetching,
    isError,
    isCreating,
    isUpdating,
    showAddDialog,
    setShowAddDialog,
    editRecord,
    setEditRecord,
    viewRecord,
    setViewRecord,
    addIpAddress,
    updateLabel,
    openEdit,
    openView,
  };
};

export default useIpAddress;
