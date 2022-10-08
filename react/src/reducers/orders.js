import { orderConstants } from "../actions/constants";

const initState = {
    orders: [],
    loading: false,
    message: null,
    exports: []
};

export default (state = initState, action) => {
    switch (action.type) {
        case orderConstants.ORDERS_SUCCESS:
            return state = {
                ...state,
                orders: action.payload.orders,
            }  
        case orderConstants.ORDERS_REQUEST:
            return state = {
                ...state,
                loading: true,
                message: null
            }       
        case orderConstants.ORDERS_FAILURE:
            return state = {
                ...state,
                loading: false,
                orders: action.payload,
            }
        case orderConstants.TEMP_ORDERS_SUCCESS:
            return state = {
                ...state,
                orders: action.payload.orders,
            }  
        case orderConstants.TEMP_ORDERS_REQUEST:
            return state = {
                ...state,
                loading: true,
                message: null
            }       
        case orderConstants.TEMP_ORDERS_FAILURE:
            return state = {
                ...state,
                loading: false,
                orders: action.payload,
            }
        case orderConstants.DELETE_ORDER_SUCCESS:
            return state = {
                ...state,
                message: action.payload.message
            }       
        case orderConstants.CONFIRM_ORDER_SUCCESS:
            return state = {
                ...state,
                message: action.payload.message
            }       
        case orderConstants.EDIT_TEMP_ORDER_SUCCESS:
            return state = {
                ...state,
                message: action.payload.message
            }       
        case orderConstants.EDIT_RESERVED_ORDER_SUCCESS:
            return state = {
                ...state,
                message: action.payload.message
            }       
        case orderConstants.DELETE_TEMP_ORDERS_SUCCESS:
            return state = {
                ...state,
                message: action.payload.message
            }       
        case orderConstants.EXPORT_ORDERS_REQUEST:
            return state = {
                ...state,
                loading: true,
                message: null,
                exports: null,
            }
        case orderConstants.EXPORT_ORDERS_SUCCESS:
            return state = {
                ...state,
                loading: false,
                message: null,
                exports: action.payload.orders
            }
        case orderConstants.EXPORT_ORDERS_FAILURE:
            return state = {
                ...state,
                loading: false,
                message: null,
                exports: null,
            }
        case orderConstants.CLEAR_MESSAGE:
            return state = {
                ...state,
                loading: false,
                message: null,
                exports: null,
            }
        default:
            return state;
    }
}