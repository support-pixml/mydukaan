import * as api from '../api';
import { orderConstants } from './constants';

export const getAllOrders = (filterData) => async (dispatch) => {
    dispatch({
        type: orderConstants.ORDERS_REQUEST,
    });
    try {
        const {data} = await api.getOrders(filterData);
        dispatch({
            type: orderConstants.ORDERS_SUCCESS,
            payload: {orders: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: orderConstants.ORDERS_FAILURE,
            payload: error
        });
    }
}

export const getTempOrders = () => async (dispatch) => {
    dispatch({
        type: orderConstants.TEMP_ORDERS_REQUEST,
    });
    try {
        const {data} = await api.getTempOrders();
        dispatch({
            type: orderConstants.TEMP_ORDERS_SUCCESS,
            payload: {orders: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: orderConstants.TEMP_ORDERS_FAILURE,
            payload: error
        });
    }
}

export const deleteOrder = (orderId) => async (dispatch) => {
    dispatch({
        type: orderConstants.DELETE_ORDER_REQUEST,
    });
    try {
        const {data} = await api.deleteOrder(orderId);
        dispatch({
            type: orderConstants.DELETE_ORDER_SUCCESS,
            payload: {message: data}
        });
        const response = await api.getOrders();
        dispatch({
            type: orderConstants.ORDERS_SUCCESS,
            payload: {orders: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: orderConstants.DELETE_ORDER_FAILURE,
            payload: error
        });
    }
}

export const confirmOrder = (orderId) => async (dispatch) => {
    dispatch({
        type: orderConstants.CONFIRM_ORDER_REQUEST,
    });
    try {
        const {data} = await api.confirmOrder(orderId);
        dispatch({
            type: orderConstants.CONFIRM_ORDER_SUCCESS,
            payload: {message: data}
        });
        const response = await api.getTempOrders();
        dispatch({
            type: orderConstants.TEMP_ORDERS_SUCCESS,
            payload: {orders: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: orderConstants.CONFIRM_ORDER_FAILURE,
            payload: error
        });
    }
}

export const deleteTempOrder = (orderId) => async (dispatch) => {
    dispatch({
        type: orderConstants.DELETE_TEMP_ORDERS_REQUEST,
    });
    try {
        const {data} = await api.deleteTempOrder(orderId);
        dispatch({
            type: orderConstants.DELETE_TEMP_ORDERS_SUCCESS,
            payload: {message: data}
        });
        const response = await api.getTempOrders();
        dispatch({
            type: orderConstants.TEMP_ORDERS_SUCCESS,
            payload: {orders: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: orderConstants.DELETE_TEMP_ORDERS_FAILURE,
            payload: error
        });
    }
}

export const editTempOrder = (tempOrderData) => async (dispatch) => {
    dispatch({
        type: orderConstants.EDIT_TEMP_ORDER_REQEUST,
    });
    try {
        const {data} = await api.editTempOrder(tempOrderData);
        dispatch({
            type: orderConstants.EDIT_TEMP_ORDER_SUCCESS,
            payload: {message: data}
        });
        const response = await api.getTempOrders();
        dispatch({
            type: orderConstants.TEMP_ORDERS_SUCCESS,
            payload: {orders: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: orderConstants.EDIT_TEMP_ORDER_FAILURE,
            payload: error
        });
    }
}

export const editReserveOrder = (reserveOrderData) => async (dispatch) => {
    dispatch({
        type: orderConstants.EDIT_RESERVED_ORDER_REQEUST,
    });
    try {
        const {data} = await api.editReserveOrder(reserveOrderData);
        console.log('message', data);
        dispatch({
            type: orderConstants.EDIT_RESERVED_ORDER_SUCCESS,
            payload: {message: data}
        });
        const response = await api.getOrders();
        dispatch({
            type: orderConstants.ORDERS_SUCCESS,
            payload: {orders: response.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: orderConstants.EDIT_RESERVED_ORDER_FAILURE,
            payload: error
        });
    }
}

export const exportOrders = (filterData) => async (dispatch) => {
    dispatch({
        type: orderConstants.EXPORT_ORDERS_REQUEST, 
    });
    try {
        const {data} = await api.exportOrders(filterData);  
        dispatch({
            type: orderConstants.EXPORT_ORDERS_SUCCESS, 
            payload: {orders: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: orderConstants.EXPORT_ORDERS_FAILURE,
            payload: error
        });
    }
}