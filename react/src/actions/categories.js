import * as api from '../api';
import { FETCH_ALL } from './constants';

export const getCategories = () => async (dispatch) => {
    try {
        const {data} = await api.fetchCategories();
        dispatch({ type: FETCH_ALL, payload: data });
    } catch (error) {
        console.log(error);
    }
}