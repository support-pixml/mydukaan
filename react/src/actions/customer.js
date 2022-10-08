import * as api from '../api';
import { customerConstants } from './constants';

export const getCustomerData = (customerLongId) => async (dispatch) => {
    dispatch({
        type: customerConstants.CUSTOMER_REQUEST, 
    });
    try {
        const {data} = await api.getCustomerData(customerLongId);
        dispatch({
            type: customerConstants.CUSTOMER_SUCCESS, 
            payload: {customer: data}
        });
    } catch (error) {
        console.log(error);
        dispatch({
            type: customerConstants.CUSTOMER_FAILURE, 
            payload: error
        });
    }
}