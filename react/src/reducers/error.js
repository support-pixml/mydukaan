import { errorConstants } from "../actions/constants";

const initState = {
    error: null
};

export default (state = initState, action) => {
    switch (action.type) {
        case errorConstants.ERROR_MESSAGE:
            return state = {
                ...state,
                error: action.payload
            }  
        case errorConstants.ERROR_CLEAR:
            return state = {
                ...state,
                error: null
            }  
        default:
            return state;
    }
}