import { CREATE, productConstants } from "../actions/constants";

const initState = {
    products: [],
    loading: false,
    error: null
};

export default (state = initState, action) => {
    switch (action.type) {
        case productConstants.PRODUCT_SUCCESS:
            return state = {
                ...state,
                products: action.payload.products
            }  
        case productConstants.ADD_PRODUCT_REQUEST:
            return state = {
                ...state,
                loading: true
            }
        case productConstants.ADD_PRODUCT_SUCCESS:
            return state = {
                ...state,
                loading: false,
                products: action.payload
            }
        default:
            return state;
    }
}