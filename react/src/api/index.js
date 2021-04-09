import axios from 'axios';

const API = axios.create({baseURL: 'http://localhost:8080'});

API.interceptors.request.use((req) => {
    if(localStorage.getItem('access_token'))
    {
        req.headers.Authorization = `Bearer ${localStorage.getItem('access_token')}`;
    }

    return req;
});

export const SignIn = (formData) => API.post('/signin', formData);
export const SignUp = (formData) => API.post('/signup', formData);
export const SignOut = () => API.post('/signout');

export const fetchCategories = () => API.get('/api/get_categories');
export const createCategory = (newCategory) => API.post('/api/add_category', newCategory);

export const createProduct = (newProduct) => API.post('/api/add_product', newProduct);
export const fetchAllProducts = () => API.get('/api/get_all_products');