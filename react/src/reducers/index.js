import { combineReducers } from "redux";
import categoryReducer from './categories';
import productReducer from './products';
import authReducer from './auth';
import orderReducer from './orders';
import userReducer from './users';
import customerReducer from './customer';
import errorReducer from './error';
import { CartReducer } from "./cart";


const rootReducer = combineReducers({
    auth: authReducer, 
    category: categoryReducer, 
    product: productReducer,
    cart: CartReducer,
    order: orderReducer,
    user: userReducer,
    customer: customerReducer,
    error: errorReducer
});

export default rootReducer;