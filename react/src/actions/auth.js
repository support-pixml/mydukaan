import * as api from '../api';
import { authConstants, SIGN_UP } from './constants';

export const signin = (formData) => async (dispatch) => {
    try {
        const { data } = await api.SignIn(formData);
        dispatch({ type: authConstants.LOGIN_REQUEST, data });
    } catch (error) {
        console.log(error);
        dispatch({
            type: authConstants.LOGIN_FAILURE, 
            payload: error
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