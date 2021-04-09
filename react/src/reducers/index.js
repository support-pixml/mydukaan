import { combineReducers } from "redux";
import categoryReducer from './categories';
import productReducer from './products';
import authReducer from './auth';
import { CartReducer } from "./cart";


const rootReducer = combineReducers({
    auth: authReducer, 
    category: categoryReducer, 
    product: productReducer,
    cart: CartReducer
});

export default rootReducer;