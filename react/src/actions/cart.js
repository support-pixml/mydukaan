import { CartReducer, sumItems } from '../reducers/cart';
import { cartConstants } from './constants';

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

export const removeProduct = (payload) => async (dispatch) => {
    dispatch({
        type: cartConstants.REMOVE_ITEM, payload
    });
}

export const clearCart = () => async (dispatch) => {
    dispatch({
        type: cartConstants.CLEAR,
    });
}

export const checkOut = () => async (dispatch) => {
    dispatch({
        type: cartConstants.CHECKOUT,
    });
}

