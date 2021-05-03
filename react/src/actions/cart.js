import { CartReducer, sumItems } from '../reducers/cart';
import { cartConstants } from './constants';
import * as api from '../api';

const storage = localStorage.getItem('cart') ? JSON.parse(localStorage.getItem('cart')) : [];
const initialState = { cartItems: storage, ...sumItems(storage), checkout: false };

export const increase = (payload) => async (dispatch) => {
    dispatch({
        type: cartConstants.INCREASE, payload
    });
}

export const decrease = (payload) => async (dispatch) => {
    dispatch({
        type: cartConstants.DECREASE, payload
    });
}

export const addProduct = (payload) => async (dispatch) => {
    dispatch({
        type: cartConstants.ADD_ITEM, payload
    });
}

export const increaseOption = (payload) => async (dispatch) => {
    dispatch({
        type: cartConstants.INCREASE_OPTION, payload
    });
}

export const addProductOption = (payload) => async (dispatch) => {
    dispatch({
        type: cartConstants.ADD_OPTION, payload
    });
} 

export const decreaseOption = (payload) => async (dispatch) => {
    dispatch({
        type: cartConstants.DECREASE_OPTION, payload
    });
}

export const removeProduct = (payload) => async (dispatch) => {
    dispatch({
        type: cartConstants.REMOVE_ITEM, payload
    });
}

export const removeProductOption = (payload) => async (dispatch) => {
    dispatch({
        type: cartConstants.REMOVE_OPTION_ITEM, payload
    });
}

export const clearCart = () => async (dispatch) => {
    dispatch({
        type: cartConstants.CLEAR,
    });
}

export const checkOut = (orderData) => async (dispatch) => {
    dispatch({
        type: cartConstants.CHECKOUT_REQUEST,
    });
    try {
        console.log('place_order', orderData);
        const {data} = await api.placeOrder(orderData);
        dispatch({ type: cartConstants.CHECKOUT_SUCCESS, payload: data });
    } catch (error) {
        console.log(error);
        dispatch({
            type: cartConstants.CHECKOUT_FAILURE,
            payload: error
        });
    }
}

