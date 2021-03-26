import * as api from '../api';
import { CREATE, FETCH_ALL } from './constants';

export const addProduct = (product) => async (dispatch) => {
    try {
        console.log(product);
        const {data} = await api.createProduct(product);
        console.log(data);
        dispatch({ type: CREATE, payload: data });
    } catch (error) {
        console.log(error);
    }
}

export const getAllProducts = () => async (dispatch) => {
    try {
        const {data} = await api.fetchAllProducts();
        dispatch({ type: FETCH_ALL, payload: data });
    } catch (error) {
        console.log(error);
    }
}