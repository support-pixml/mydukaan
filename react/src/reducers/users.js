import { userConstants } from "../actions/constants";

const initState = {
    users: [],
    loading: false,
    error: null,
    message: null
};

export default (state = initState, action) => {
    switch (action.type) {
        case userConstants.USER_SUCCESS:
            return state = {
                ...state,
                users: action.payload.users
            }  
        case userConstants.ADD_USER_SUCCESS:
            return state = {
                ...state,
                error: null,
                message: action.payload.message
            }  
        case userConstants.ADD_USER_FAILURE:
            return state = {
                ...state,
                error: action.payload.error
            }  
        case userConstants.UPDATE_USER_SUCCESS:
            return state = {
                ...state,
                error: null,
                message: action.payload.message
            }  
        case userConstants.UPDATE_USER_FAILURE:
            return state = {
                ...state,
                error: action.payload.error
            }  
        case userConstants.DELETE_USER_SUCCESS:
            return state = {
                ...state,
            }  
        case userConstants.RESET_RESPONSE:
            return state = {
                ...state,
                error: null,
                message: null
            }  
        default:
            return state;
    }
}