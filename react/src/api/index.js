import axios from 'axios';

const API = axios.create({baseURL: 'http://localhost:8080'});

export const SignIn = (formData) => API.post('/api/signin', formData);
export const SignUp = (formData) => API.post('/api/signup', formData);

export const fetchCategories = () => API.get('/api/get_categories');
export const createCategory = (newCategory) => API.post('/api/add_category', newCategory);

export const createProduct = (newProduct) => API.post('/api/add_product', newProduct);
export const fetchAllProducts = () => API.get('/api/get_all_products');