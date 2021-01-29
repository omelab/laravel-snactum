import axios from 'axios';

const apiClient = axios.create({
    baseURL: 'http://api.sanctum.test',
    withCredentials: true,
});

export default apiClient;