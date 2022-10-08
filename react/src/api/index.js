import axios from 'axios';
import { errorConstants } from '../actions/constants';
import store from '../store';

// const API = axios.create({
//     baseURL: `https://${customer.url_title}.d-store.co.in/`,
//     headers: {
//         'Access-Control-Allow-Origin' : '*',
//         }});
const API = axios.create({baseURL: `http://localhost:8080`});

API.interceptors.request.use((req) => {
    if(localStorage.getItem('access_token'))
    {
        req.headers.Authorization = `Bearer ${localStorage.getItem('access_token')}`;
    }
    return req;
});

API.interceptors.response.use((res) => {
    return res;
}, (error) => {
    if(error)
    {
        const errorObj = error.response.data.messages;
        let errorMessage = '';
        let loop = true;
        Object.keys(errorObj).forEach(key=>{
            if(loop)
            {
                errorMessage = errorObj[key];
                loop = false;
            }
            else
                errorMessage = errorMessage +', '+errorObj[key];
        });
        store.dispatch({
            type: errorConstants.ERROR_MESSAGE, 
            payload: errorMessage
        });
    }
    else
    {
        store.dispatch({
            type: errorConstants.ERROR_CLEAR
        });
    }
    // if(status === 500 || status === 400)
    // {
    //     localStorage.clear();
    //     store.dispatch({type: authConstants.LOGOUT_SUCCESS});
    // }
});

export const SignIn = (formData) => API.post('/signin', formData);
export const SignUp = (formData) => API.post('/signup', formData);
export const SignOut = () => API.post('/signout');

export const fetchCategories = () => API.get('/api/get_categories');
export const createCategory = (newCategory) => API.post('/api/add_category', newCategory);
export const updateCategory = (categoryData) => API.post('/api/update_category', categoryData);
export const deleteCategory = (categoryId) => API.post(`/api/delete_category/${categoryId}`);

export const createProduct = (newProduct) => API.post('/api/add_product', newProduct);
export const addProductStock = (stockData) => API.post('/api/add_product_stock', stockData);
export const updateProduct = (product_id, Product) => API.post(`/api/update_product/${product_id}`, Product);
export const deleteProduct = (product_id) => API.post(`/api/delete_product/${product_id}`);
export const fetchAllProducts = () => API.get('/api/get_all_products');
export const fetchSearchedProducts = (searchData) => API.post('/api/get_searched_products', searchData);
export const fetchProducts = () => API.get('/api/get_products');
export const exportProducts = () => API.get('/api/export_products');
export const demoExportProducts = () => API.get('/api/demo_export_products');
export const importProducts = (productsFile) => API.post('/api/import_products', productsFile);
export const getProductStocks = (product_id) => API.get(`/api/get_product_stocks_details/${product_id}`);

export const placeOrder = (orderData) => API.post('/api/place_order', orderData);
export const getOrders = (filterData) => API.post('/api/get_orders', filterData);
export const getTempOrders = () => API.post('/api/get_temp_orders');
export const deleteOrder = (orderId) => API.post(`/api/delete_order/${orderId}`);
export const deleteTempOrder = (orderId) => API.post(`/api/delete_temp_order/${orderId}`);
export const confirmOrder = (orderId) => API.post(`/api/confirm_temp_order/${orderId}`);
export const exportOrders = (filterData) => API.post('/api/export_orders', filterData);
export const editTempOrder = (tempOrderData) => API.post('/api/edit_temp_order', tempOrderData);
export const editReserveOrder = (reserveOrderData) => API.post('/api/edit_reserve_order', reserveOrderData);
export const confirmReserveOrder = (orderId) => API.post(`/api/confirm_reserve_order/${orderId}`);

export const getUsers = () => API.get('/api/get_users');
export const deleteUser = (userId) => API.post(`/api/delete_user/${userId}`);
export const updateUser = (userData) => API.post('/api/update_user', userData);

export const getCustomerData = (customerLongId) => API.post(`/api/get_customer_data/${customerLongId}`);