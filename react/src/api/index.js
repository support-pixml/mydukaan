import axios from 'axios';

const API = axios.create({baseURL: 'http://localhost:8080'});

export const SignIn = (formData) => API.post('/api/signin', formData);
export const SignUp = (formData) => API.post('/api/signup', formData);

export const fetchCategories = () => API.get('/api/get_categories');