import React, { useState } from "react";
import Button from "@material-ui/core/Button";
import ButtonGroup from "@material-ui/core/ButtonGroup";
import { addProduct, decrease, increase, removeProduct, addProductOption, increaseOption, decreaseOption, removeProductOption } from "../../../actions/cart";
import { useDispatch } from "react-redux";

const CartButton = ({product, cartItems, option}) => {
    const [counter, setCounter] = useState(0);
    const dispatch = useDispatch();

    const isInCart = (product) => {
        if(!product.option_name)
            return !!cartItems.find(item => item.long_id === product.long_id);
        else
            return !!cartItems.find(item => item.long_id === product.long_id && item.product_option_id === product.product_option_id);
    }

    // const findProductInCart = cartItems.find(item => item.long_id === product.long_id);
    // if(findProductInCart)
    // {
    //     setCounter(findProductInCart.quantity);
    // }

    const handleIncrement = () => {
        let addedProd = Object.assign({}, product);

        if(isInCart(addedProd))
        {
            dispatch(increase(addedProd));
        }
        else
        {
            dispatch(addProduct(addedProd));
        }
        setCounter(counter+1);
    };

    const handleOptionIncrement = () => {
        let addedProd = Object.assign({}, option);

        if(isInCart(addedProd))
        {
            dispatch(increaseOption(addedProd));
        }
        else
        {
            dispatch(addProductOption(addedProd));
        }
        setCounter(counter+1);
    };

    const handleDecrement = () => {
        if(isInCart(product))
        {
            dispatch(decrease(product));
            setCounter(counter-1);            
            if(counter == 1)
            {
                dispatch(removeProduct(product));
            }
        }
    };
    
    const handleOptionDecrement = () => {
        let Prod = Object.assign({}, option);
        if(isInCart(Prod))
        {
            dispatch(decreaseOption(Prod));
            setCounter(counter-1);
            
            if(counter == 1)
            {
                dispatch(removeProductOption(Prod));
            }
        }
    };

    const displayCounter = counter > 0;    

    if(product)
    {
        // const findProductInCart = cartItems.find(item => item.long_id === product.long_id);
        // if(findProductInCart)
        // {

        // }
        return (
            <ButtonGroup size="small" color="secondary" aria-label="small outlined button group" className="float-right">
                {displayCounter && <Button onClick={handleDecrement}>-</Button>}                
                {displayCounter && <Button disabled>{counter}</Button>}
                <Button onClick={handleIncrement}>+</Button>
            </ButtonGroup>
            );
    }
    else if(option)
    {
        return (
            <ButtonGroup size="small" color="secondary" aria-label="small outlined button group" className="float-right">
                {displayCounter && <Button onClick={handleOptionDecrement}>-</Button>}
                {displayCounter && <Button disabled>{counter}</Button>}
                <Button onClick={handleOptionIncrement}>+</Button>
            </ButtonGroup>
        );
    }
    else
    {
        return (
            <div>No Stock</div>
        )
    } 
}

export default CartButton;
