import { FETCH_ALL } from "../actions/constants";

export default (categories = [], action) => {
    switch (action.type) {
        case FETCH_ALL:
            return action.payload;    
        default:
            return categories;
    }
}