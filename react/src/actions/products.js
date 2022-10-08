import * as api from '../api';
import { productConstants } from './constants';

export const addProduct = (product) => async (dispatch) => {
    dispatch({
        type: productConstants.ADD_PRODUCT_REQUEST, 
    });
    try {
        const {data} = await api.createProduct(product);
        dispatch({ type: productConstants.ADD_PRODUCT_SUCCESS, payload: data });
        const response = await api.fetchProducts();
        dispatch({
            type: productConstants.GET_PRODUCT_SUCCESS, 
            payload: {products: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.ADD_PRODUCT_FAILURE,
            payload: error
        });
    }
}

export const addProductStock = (stockData) => async (dispatch) => {
    dispatch({
        type: productConstants.ADD_PRODUCT_STOCK_REQUEST, 
    });
    try {
        const {data} = await api.addProductStock(stockData);
        dispatch({ type: productConstants.ADD_PRODUCT_STOCK_SUCCESS, payload: data });
        const response = await api.fetchProducts();
        dispatch({
            type: productConstants.GET_PRODUCT_SUCCESS, 
            payload: {products: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.ADD_PRODUCT_STOCK_FAILURE,
            payload: error
        });
    }
}

export const getAllProducts = () => async (dispatch) => {
    
    dispatch({
        type: productConstants.PRODUCT_REQUEST, 
    });
    try {
        const response = await api.fetchAllProducts();
        dispatch({
            type: productConstants.PRODUCT_SUCCESS, 
            payload: {products: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.PRODUCT_FAILURE,
            payload: error
        });
    }
}

export const getSearchedProducts = (searchData) => async (dispatch) => {
    dispatch({
        type: productConstants.PRODUCT_REQUEST, 
    });
    try {
        const response = await api.fetchSearchedProducts(searchData);
        dispatch({
            type: productConstants.PRODUCT_SUCCESS, 
            payload: {products: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.PRODUCT_FAILURE,
            payload: error
        });
    }
}

export const getProducts = () => async (dispatch) => {
    dispatch({
        type: productConstants.GET_PRODUCT_REQUEST, 
    });
    try {
        const {data} = await api.fetchProducts();
        dispatch({
            type: productConstants.GET_PRODUCT_SUCCESS, 
            payload: {products: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.GET_PRODUCT_FAILURE,
            payload: error
        });
    }
}

export const updateProduct = (product_id, product) => async (dispatch) => {
    dispatch({
        type: productConstants.UPDATE_PRODUCT_REQUEST, 
    });
    try {
        const {data} = await api.updateProduct(product_id, product);
        dispatch({ type: productConstants.UPDATE_PRODUCT_SUCCESS, payload: {message: data} });
        const response = await api.fetchProducts();
        dispatch({
            type: productConstants.GET_PRODUCT_SUCCESS, 
            payload: {products: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.UPDATE_PRODUCT_FAILURE,
            payload: error
        });
    }
}

export const exportProducts = () => async (dispatch) => {
    dispatch({
        type: productConstants.EXPORT_PRODUCTS_REQUEST, 
    });
    try {
        const {data} = await api.exportProducts();  
        dispatch({
            type: productConstants.EXPORT_PRODUCTS_SUCCESS, 
            payload: {products: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.EXPORT_PRODUCTS_FAILURE,
            payload: error
        });
    }
}

export const demoExportProducts = () => async (dispatch) => {
    dispatch({
        type: productConstants.DEMO_EXPORT_PRODUCTS_REQUEST, 
    });
    try {
        const {data} = await api.demoExportProducts();  
        dispatch({
            type: productConstants.DEMO_EXPORT_PRODUCTS_SUCCESS, 
            payload: {products: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.DEMO_EXPORT_PRODUCTS_FAILURE,
            payload: error
        });
    }
}

export const importProducts = (form) => async (dispatch) => {
    dispatch({
        type: productConstants.IMPORT_PRODUCTS_REQUEST, 
    });
    try {
        const {data} = await api.importProducts(form);
        dispatch({
            type: productConstants.IMPORT_PRODUCTS_SUCCESS, 
            payload: {message: data}
        });
        const response = await api.fetchProducts();
        dispatch({
            type: productConstants.GET_PRODUCT_SUCCESS, 
            payload: {products: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.IMPORT_PRODUCTS_FAILURE,
            payload: error
        });
    }
}

export const deleteProduct = (productId) => async (dispatch) => {
    try {
        const {data} = await api.deleteProduct(productId);
        dispatch({ type: productConstants.DELETE_PRODUCT_SUCCESS, payload: data });
        const response = await api.fetchProducts();
        dispatch({
            type: productConstants.GET_PRODUCT_SUCCESS, 
            payload: {products: response.data}
        });
    } catch (error) {
        console.log(error);
    }
}

export const getProductStocks = (productId) => async (dispatch) => {
    dispatch({
        type: productConstants.EXPORT_PRODUCT_STOCKS_REQUEST, 
    });
    try {
        const {data} = await api.getProductStocks(productId);  
        dispatch({
            type: productConstants.EXPORT_PRODUCT_STOCKS_SUCCESS, 
            payload: {productstocks: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: productConstants.EXPORT_PRODUCT_STOCKS_FAILURE,
            payload: error
        });
    }
}