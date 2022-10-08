import { customerConstants } from "../actions/constants";

const initState = {
    customer: null,
    loading: false,
    error: null,
    message: null
};

export default (state = initState, action) => {
    switch (action.type) {
        case customerConstants.CUSTOMER_REQUEST:
            return state = {
                ...state,
                loading: true,
                error: null,
                message: null
            }  
        case customerConstants.CUSTOMER_SUCCESS:
            return state = {
                ...state,
                customer: action.payload.customer,
                loading: false,
                error: null,
                message: null
            }  
        case customerConstants.CUSTOMER_FAILURE:
            return state = {
                ...state,
                loading: false,
            }  
        default:
            return state;
    }
}