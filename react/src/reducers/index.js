import { combineReducers } from "redux";
import categories from './categories';
import products from './products';
import authReducer from './auth';

const rootReducer = combineReducers({
    auth: authReducer, categories, products
});

export default rootReducer;