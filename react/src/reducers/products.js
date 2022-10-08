import { productConstants } from "../actions/constants";

const initState = {
    products: [],
    products_list: [],
    loading: false,
    error: null,
    message: null,
    exports: [],
    demoExports: [],
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
                message: action.payload
            }
        case productConstants.DELETE_PRODUCT_SUCCESS:
            return state = {
                ...state,
                loading: false,
                message: action.payload
            }
        case productConstants.ADD_PRODUCT_STOCK_REQUEST:
            return state = {
                ...state,
                loading: true
            }
        case productConstants.ADD_PRODUCT_STOCK_SUCCESS:
            return state = {
                ...state,
                loading: false,
                message: action.payload
            }
        case productConstants.GET_PRODUCT_REQUEST:
            return state = {
                ...state,
                loading: true
            }
        case productConstants.GET_PRODUCT_SUCCESS:
            return state = {
                ...state,
                loading: false,
                products_list: action.payload.products
            }
        case productConstants.GET_PRODUCT_FAILURE:
            return state = {
                ...state,
                loading: false,
                products_list: action.payload
            }
        case productConstants.UPDATE_PRODUCT_REQUEST:
            return state = {
                ...state,
                loading: true,
                message: null
            }
        case productConstants.UPDATE_PRODUCT_SUCCESS:
            return state = {
                ...state,
                loading: false,
                message: action.payload.message
            }
        case productConstants.EXPORT_PRODUCTS_REQUEST:
            return state = {
                ...state,
                loading: true,
                message: null,
                exports: null,
            }
        case productConstants.EXPORT_PRODUCTS_SUCCESS:
            return state = {
                ...state,
                loading: false,
                message: null,
                exports: action.payload.products
            }
        case productConstants.EXPORT_PRODUCTS_FAILURE:
            return state = {
                ...state,
                loading: false,
                message: null,
                exports: null,
            }
        case productConstants.DEMO_EXPORT_PRODUCTS_REQUEST:
            return state = {
                ...state,
                loading: true,
                message: null,
                exports: null,
            }
        case productConstants.DEMO_EXPORT_PRODUCTS_SUCCESS:
            return state = {
                ...state,
                loading: false,
                message: null,
                demoExports: action.payload.products
            }
        case productConstants.DEMO_EXPORT_PRODUCTS_FAILURE:
            return state = {
                ...state,
                loading: false,
                message: null,
                exports: null,
            }
        case productConstants.IMPORT_PRODUCTS_REQUEST:
            return state = {
                ...state,
                loading: true,
                message: null,
            }
        case productConstants.IMPORT_PRODUCTS_SUCCESS:
            return state = {
                ...state,
                loading: false,
                message: action.payload.message,
            }
        case productConstants.IMPORT_PRODUCTS_FAILURE:
            return state = {
                ...state,
                loading: false,
                message: null,
            }
        case productConstants.EXPORT_PRODUCT_STOCKS_REQUEST:
            return state = {
                ...state,
                loading: true,
                message: null,
            }
        case productConstants.EXPORT_PRODUCT_STOCKS_SUCCESS:
            return state = {
                ...state,
                loading: false,
                productStocks: action.payload.productstocks,
                message: null,
            }
        case productConstants.EXPORT_PRODUCT_STOCKS_FAILURE:
            return state = {
                ...state,
                loading: false,
                message: null,
            }
        case productConstants.CLEAR_MESSAGE:
            return state = {
                ...state,
                loading: false,
                message: null,
                error: null
            }
        default:
            return state;
    }
}