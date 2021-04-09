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
            console.log(state);
            if (!state.cartItems.find(item => item.long_id === action.payload.long_id)) {
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
        case cartConstants.REMOVE_ITEM:
            return {
                ...state,
                ...sumItems(state.cartItems.filter(item => item.id !== action.payload.id)),
                cartItems: [...state.cartItems.filter(item => item.id !== action.payload.id)]
            }
        case cartConstants.INCREASE:
            console.log(state);
            state.cartItems[state.cartItems.findIndex(item => item.long_id === action.payload.long_id)].quantity++
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.DECREASE:
            state.cartItems[state.cartItems.findIndex(item => item.id === action.payload.id)].quantity--
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.CHECKOUT:
            return {
                cartItems: [],
                checkout: true,
                ...sumItems([]),
            }
        case cartConstants.CLEAR:
                return {
                    cartItems: [],
                    ...sumItems([]),
                }
        default:
            return state

    }
}