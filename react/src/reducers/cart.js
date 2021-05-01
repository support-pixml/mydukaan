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
            console.log(state.cartItems);
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
                ...sumItems(state.cartItems.filter(item => item.long_id !== action.payload.long_id)),
                cartItems: [...state.cartItems.filter(item => item.long_id !== action.payload.long_id)]
            }
        case cartConstants.INCREASE:
            console.log(state);
            state.cartItems[state.cartItems.findIndex(item => item.long_id === action.payload.long_id)].quantity++

            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.INCREASE_OPTION:
            console.log('BEFORE MUTATION STATE' ,state);
            // // state.cartItems[state.cartItems.findIndex(item => item.long_id === action.payload.long_id).product_options]
                //  state.cartItems.forEach(function(e) {
                // (e.product_options.findIndex(c => c.product_id === action.payload.product_id)).add_quantity.parseInt()++;
                // })
                var tempObjectOne = Object.assign({},state);    
                    tempObjectOne.cartItems.forEach(function(e) {
                (e.product_options.findIndex(c => 
                    {
                     if(c.product_id === action.payload.product_id)
                        {
                            var tempObject = Object.assign({},c);    
                            tempObject.add_quantity = parseInt(tempObject.add_quantity)+1;                    
                            c = tempObject;
                        }
                    }
                 ));
                })
            state = tempObjectOne;
            console.log('AFTER MUTATION STATE' ,state);
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }

        case cartConstants.ADD_OPTION:
            console.log('add' ,state);
            // if (!state.cartItems.find(item => item.long_id === action.payload.long_id)) {
            //     state.cartItems.push({
            //         ...action.payload
            //     })
                state.cartItems.forEach(function(e) {
                if(!e.product_options.find(c => c.product_id === action.payload.product_id))
                {
                    e.product_options.push({
                        ...action.payload,
                        add_quantity: 1
                    });
                }
                })
            // }
            return {
                ...state,
                ...sumItems(state.cartItems),
                cartItems: [...state.cartItems]
            }
        case cartConstants.DECREASE:
            console.log(state);
            state.cartItems[state.cartItems.findIndex(item => item.long_id === action.payload.long_id)].quantity--
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