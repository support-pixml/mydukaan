import * as api from '../api';
import { productConstants } from './constants';

export const addProduct = (product) => async (dispatch) => {
     dispatch({
        type: productConstants.ADD_PRODUCT_REQUEST, 
    });
    try {
        const {data} = await api.createProduct(product);
        dispatch({ type: productConstants.ADD_PRODUCT_SUCCESS, payload: data });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.ADD_PRODUCT_FAILURE,
            payload: error
        });
    }
}

export const getAllProducts = () => async (dispatch) => {
    dispatch({
        type: productConstants.PRODUCT_REQUEST, 
    });
    try {
        const {data} = await api.fetchAllProducts();
        dispatch({
            type: productConstants.PRODUCT_SUCCESS, 
            payload: {products: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.PRODUCT_FAILURE,
            payload: error
        });
    }
}

