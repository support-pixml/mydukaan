import { CREATE, FETCH_ALL } from "../actions/constants";

export default (categories = [], action) => {
    switch (action.type) {
        case FETCH_ALL:
            return action.payload;    3
        case CREATE:
            return [...categories, action.payload];
        default:
            return categories;
    }
}