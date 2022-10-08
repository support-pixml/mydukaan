import * as api from '../api';
import { categoryConstants } from './constants';

export const getCategories = () => async (dispatch) => {
    dispatch({
        type: categoryConstants.CATEGORY_REQUEST, 
    });
    try {
        const {data} = await api.fetchCategories();
        dispatch({
            type: categoryConstants.CATEGORY_SUCCESS, 
            payload: {categories: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: categoryConstants.CATEGORY_FAILURE, 
            payload: error
        });
    }
}

export const deleteCategory = (categoryId) => async (dispatch) => {
    try {
        const {data} = await api.deleteCategory(categoryId);
        dispatch({ type: categoryConstants.DELETE_CATEGORY_SUCCESS, payload: data });
        const categories = await api.fetchCategories();
        dispatch({
            type: categoryConstants.CATEGORY_SUCCESS, 
            payload: {categories: categories.data}
        });
    } catch (error) {
        console.log(error);
    }
}

export const addCategory = (category) => async (dispatch) => {
    try {
        const {data} = await api.createCategory(category);
        dispatch({ type: categoryConstants.ADD_CATEGORY_SUCCESS, payload: data });
        const categories = await api.fetchCategories();
        dispatch({
            type: categoryConstants.CATEGORY_SUCCESS, 
            payload: {categories: categories.data}
        });
    } catch (error) {
        console.log(error);
    }
}

export const updateCategory = (category) => async (dispatch) => {
    try {
        const {data} = await api.updateCategory(category);
        dispatch({ type: categoryConstants.EDIT_CATEGORY_SUCCESS, payload: data });
        const categories = await api.fetchCategories();
        dispatch({
            type: categoryConstants.CATEGORY_SUCCESS, 
            payload: {categories: categories.data}
        });
    } catch (error) {
        console.log(error);
    }
}