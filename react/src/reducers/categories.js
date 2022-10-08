import { categoryConstants } from "../actions/constants";

const initState = {
    categories: [],
    loading: false,
    message: null
};

export default (state = initState, action) => {
    switch (action.type) {
        case categoryConstants.CATEGORY_SUCCESS:
            return state = {
                ...state,
                categories: action.payload.categories,
            }  
        case categoryConstants.DELETE_CATEGORY_SUCCESS:
            return state = {
                ...state,
                message: action.payload
            }  
        case categoryConstants.ADD_CATEGORY_REQUEST:
            return state = {
                ...state,
                message: null,
            }  
        case categoryConstants.ADD_CATEGORY_SUCCESS:
            return state = {
                ...state,
                message: action.payload
            }  
        case categoryConstants.EDIT_CATEGORY_SUCCESS:
            return state = {
                ...state,
                message: action.payload
            }  
        case categoryConstants.RESET_RESPONSE:
            return state = {
                ...state,
                message: null
            } 
        default:
            return state;
    }
}