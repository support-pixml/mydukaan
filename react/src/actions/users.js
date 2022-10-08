import * as api from '../api';
import { userConstants } from './constants';

export const getUsers = () => async (dispatch) => {
    dispatch({
        type: userConstants.USER_REQUEST, 
    });
    try {
        const {data} = await api.getUsers();
        dispatch({
            type: userConstants.USER_SUCCESS, 
            payload: {users: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: userConstants.USER_FAILURE, 
            payload: error
        });
    }
}

export const addUser = (formData) => async (dispatch) => {
    dispatch({
        type: userConstants.ADD_USER_REQUEST
    });
    try {
        const { data } = await api.SignUp(formData);
        dispatch({ type: userConstants.ADD_USER_SUCCESS, payload: {message: data} });
        const users = await api.getUsers();
        dispatch({
            type: userConstants.USER_SUCCESS, 
            payload: {users: users.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: userConstants.ADD_USER_FAILURE, 
            payload: error
        });
    }
};

export const updateUser = (formData) => async (dispatch) => {
    dispatch({
        type: userConstants.UPDATE_USER_REQUEST
    });
    try {
        const { data } = await api.updateUser(formData);
        dispatch({ type: userConstants.UPDATE_USER_SUCCESS, payload: {message: data} });
        const users = await api.getUsers();
        dispatch({
            type: userConstants.USER_SUCCESS, 
            payload: {users: users.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: userConstants.UPDATE_USER_FAILURE, 
            payload: error
        });
    }
};

export const removeUser = (userId) => async (dispatch) => {
    dispatch({
        type: userConstants.DELETE_USER_REQUEST
    });
    try {
        const { data } = await api.deleteUser(userId);
        dispatch({ type: userConstants.DELETE_USER_SUCCESS, payload: {message: data} });
        const users = await api.getUsers();
        dispatch({
            type: userConstants.USER_SUCCESS, 
            payload: {users: users.data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: userConstants.DELETE_USER_FAILURE, 
            payload: error
        });
    }
};
