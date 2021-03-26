import * as api from '../api';
import { CREATE, FETCH_ALL } from './constants';

export const getCategories = () => async (dispatch) => {
    try {
        const {data} = await api.fetchCategories();
        dispatch({ type: FETCH_ALL, payload: data });
    } catch (error) {
        console.log(error);
    }
}

export const addCategory = (category) => async (dispatch) => {
    try {
        const {data} = await api.createCategory(category);
        console.log(data);
        dispatch({ type: CREATE, payload: data });
    } catch (error) {
        console.log(error);
    }
}