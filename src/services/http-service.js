import apiClient from "./api-client";


class HttpService {
    endpoint;

    constructor(endpoint) {
        this.endpoint = endpoint;
    }

    getAll() {
        const controller = new AbortController();
        const request = apiClient.get(this.endpoint, {
            signal: controller.signal,
        });
        return { request, cancel: () => controller.abort() };
    }

    get(id) {
        const controller = new AbortController();
        const request = apiClient.get(this.endpoint + `?user_id=${id}`, {
            signal: controller.signal,
        });
        return { request, cancel: () => controller.abort() };
    }

    getWithParams(params) {
        // will receive a string of parameters
        console.log("endpoint: ", this.endpoint + '?' + params)

        const controller = new AbortController();
        const request = apiClient.get(this.endpoint + `?${params}`, {
            signal: controller.signal,
        });
        return { request, cancel: () => controller.abort() };
    }

    delete(id) {
        return apiClient.delete(this.endpoint + "/" + id);
    }

    create(entity) {
        return apiClient.post(this.endpoint, entity);
    }

    createOrUpdate(entity) {
        return apiClient.post(this.endpoint, entity);
    }

    update(entity) {
    return apiClient.patch(this.endpoint + '/' + entity);
}

updateAllWithParams(params, entity) {
    return apiClient.post(this.endpoint + params, entity);
}
}

const create = (endpoint) => new HttpService(endpoint);

export default create;