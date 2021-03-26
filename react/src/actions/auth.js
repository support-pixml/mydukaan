import * as api from '../api';
import { SIGN_IN, SIGN_UP } from './constants';

export const signin = (formData) => async (dispatch) => {
    try {
        const { data } = await api.SignIn(formData);
        dispatch({ type: SIGN_IN, data });
        // router.push('/');
    } catch (error) {
        console.log(error);
    }
};

export const signup = (formData) => async (dispatch) => {
    try {
        const { data } = await api.SignUp(formData);
        console.log(data);
        dispatch({ type: SIGN_UP, data });
        // router.push('/');
    } catch (error) {
        console.log(error);
    }
};