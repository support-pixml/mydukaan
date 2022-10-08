import * as api from '../api';
import { authConstants, cartConstants, SIGN_UP } from './constants';

export const signin = (formData) => async (dispatch) => {
    try {
        const response = await api.SignIn(formData);
        console.log('sign in', response);
        dispatch({ type: authConstants.LOGIN_REQUEST, data: response.data });
    } catch (error) {
        console.log('action error', error);
        dispatch({
            type: authConstants.LOGIN_FAILURE, 
            payload: {error: Promise.reject(error)}
        });
    }
};

export const isUserLoggedIn = () => async (dispatch) => {
    const token = localStorage.getItem('access_token');
    if(token)
    {
        const user = JSON.parse(localStorage.getItem('user'));
        dispatch({
            type: authConstants.LOGIN_REQUEST, 
            data: {access_token:token, user}
        });
    }
    else
    {
        dispatch({
            type: authConstants.LOGIN_FAILURE, 
            payload: { error: 'Failed to login.'}
        });
    }    
}

export const signout = () => async (dispatch) => {
    dispatch({
        type: authConstants.LOGOUT_REQUEST
    })
    try {
        // const {data} = await api.adminSignOut();
        dispatch({
            type: authConstants.LOGOUT_SUCCESS
        })
        dispatch({
            type: cartConstants.CLEAR,
        });
    } catch (error) {
        dispatch({
            type: authConstants.LOGOUT_FAILURE,
            payload: { error }
        })
    }
}

export const signup = (formData) => async (dispatch) => {
    try {
        const { data } = await api.SignUp(formData);
        console.log(data);
        dispatch({ type: SIGN_UP, data });
    } catch (error) {
        console.log(error);
    }
};