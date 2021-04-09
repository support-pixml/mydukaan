import { categoryConstants, CREATE } from "../actions/constants";

const initState = {
    categories: [],
    loading: false,
    error: null
};

export default (state = initState, action) => {
    switch (action.type) {
        case categoryConstants.CATEGORY_SUCCESS:
            return state = {
                ...state,
                categories: action.payload.categories
            }  
        // case FETCH_ALL:
        //     return action.payload;   
        // case CREATE:
        //     return [...categories, action.payload];
        default:
            return state;
    }
}