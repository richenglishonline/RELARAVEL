import api from "../api";

const get = (filters = {}) => api.get("/payout", { params: filters });

const add = (data) => api.post("/payout", data);

const update = (id, data) => api.put(`/payout/${id}`, data);

const remove = (id) => api.delete(`/payout/${id}`);

export default {
    get,
    add,
    update,
    remove,
};
