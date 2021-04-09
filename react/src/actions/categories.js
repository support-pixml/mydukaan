import * as api from '../api';
import { categoryConstants, CREATE } from './constants';

export const getCategories = () => async (dispatch) => {
    dispatch({
        type: categoryConstants.CATEGORY_REQUEST, 
    });
    try {
        const {data} = await api.fetchCategories();
        // dispatch({ type: FETCH_ALL, payload: data });
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

export const addCategory = (category) => async (dispatch) => {
    try {
        const {data} = await api.createCategory(category);
        dispatch({ type: CREATE, payload: data });
    } catch (error) {
        console.log(error);
    }
}