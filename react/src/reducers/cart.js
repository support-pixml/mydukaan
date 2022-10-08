import { cartConstants } from "../actions/constants";

export const sumItems = cartItems => {
    Storage(cartItems);
    let itemCount = cartItems.reduce((total, product) => total + product.quantity, 0);
    let total = cartItems.reduce((total, product) => total + product.price * product.quantity, 0).toFixed(2);
    return { itemCount, total }
}
const Storage = (cartItems) => {
    localStorage.setItem('cart', JSON.stringify(cartItems.length > 0 ? cartItems: []));
}

const storage = localStorage.getItem('cart') ? JSON.parse(localStorage.getItem('cart')) : [];
const initialState = { cartItems: storage, ...sumItems(storage), checkout: false };

export const CartReducer = (state=initialState, action) => {
    switch (action.type) {
        case cartConstants.ADD_ITEM:
            if (!state.cartItems.find(item => item.long_id === action.payload.addedProd.long_id)) {
                state.cartItems.push({
                    ...action.payload.addedProd,
                    quantity: 1,
                    price: action.payload.productPrice.price
                })
            }
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.REMOVE_ITEM:    
            return {
                ...state,
                ...sumItems(state.cartItems.filter(item => item.long_id !== action.payload.long_id)),
                cartItems: [...state.cartItems.filter(item => item.long_id !== action.payload.long_id)]
            }
        case cartConstants.REMOVE_OPTION_ITEM:           
            return {
                ...state,
                ...sumItems(state.cartItems.filter(item => item.product_option_id !== action.payload.product_option_id)),
                cartItems: [...state.cartItems.filter(item => item.product_option_id !== action.payload.product_option_id)]
            }
        case cartConstants.INCREASE:
            state.cartItems[state.cartItems.findIndex(item => item.long_id === action.payload.long_id)].quantity++;
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.UPDATE_ITEM:
            state.cartItems[state.cartItems.findIndex(item => item.long_id === action.payload.product.long_id)].quantity = action.payload.qty;
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.INCREASE_OPTION:
            state.cartItems[state.cartItems.findIndex(item => item.long_id === action.payload.long_id && item.product_option_id === action.payload.product_option_id)].quantity++
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }

        case cartConstants.ADD_OPTION:
            if (!state.cartItems.find(item => item.long_id === action.payload.long_id && item.product_option_id === action.payload.product_option_id)) {
                state.cartItems.push({
                    ...action.payload,
                    quantity: 1
                })
            }
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.DECREASE:
            state.cartItems[state.cartItems.findIndex(item => item.long_id === action.payload.long_id)].quantity--
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.DECREASE_OPTION:
            state.cartItems[state.cartItems.findIndex(item => item.long_id === action.payload.long_id && item.product_option_id === action.payload.product_option_id)].quantity--
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.CHECKOUT_SUCCESS:
            return {
                ...state,
                cartItems: [],
                checkout: true,
                ...sumItems([]),
                response: action.payload
            }
        case cartConstants.CLEAR:
                return {
                    cartItems: [],
                    ...sumItems([]),
                    response: null
                }
        default:
            return state

    }
}