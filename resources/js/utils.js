import axios from 'axios';

const baseURL = import.meta.env.VITE_APP_URL;

const axiosConfig = {
    baseURL,
    withCredentials: false
};

const http = axios.create(axiosConfig);

export { http };

export const openModal = function(product_id, type = 'buy') {
    let modal = document.getElementById("modal");
    modal.classList.remove('hidden');
    return false;
}
