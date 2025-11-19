import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import payoutApi from "@/lib/apis/payouts";

export const usePayouts = (filters = {}) => {
    return useQuery({
        queryKey: ["payouts", filters],
        queryFn: () => payoutApi.get(filters).then((res) => res.data),
        keepPreviousData: true,
    });
};

export const useAddPayout = () => {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: (data) => payoutApi.add(data).then((res) => res.data),
        onSuccess: () => {
            qc.invalidateQueries(["payouts"]);
        },
    });
};

export const useUpdatePayout = () => {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: ({ id, data }) =>
            payoutApi.update(id, data).then((res) => res.data),
        onSuccess: () => {
            qc.invalidateQueries(["payouts"]);
        },
    });
};

export const useDeletePayout = () => {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: (id) => payoutApi.remove(id).then((res) => res.data),
        onSuccess: () => {
            qc.invalidateQueries(["payouts"]);
        },
    });
};
