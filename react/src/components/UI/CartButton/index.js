import React, { useState } from "react";
import Button from "@material-ui/core/Button";
import ButtonGroup from "@material-ui/core/ButtonGroup";
import { addProduct, increase } from "../../../actions/cart";
import { useDispatch } from "react-redux";

const CartButton = ({product, cartItems}) => {
    const [counter, setCounter] = useState(0);
    const dispatch = useDispatch();

    const isInCart = (product) => {
        return !!cartItems.find(item => item.long_id === product.long_id);
    }

    const handleIncrement = () => {
        console.log(product);
        if(isInCart(product))
        {
            console.log('1');
            dispatch(increase(product));
        }
        else
        {
            console.log('2');
            dispatch(addProduct(product));
        }
        setCounter(counter+1);
    };

    const handleDecrement = () => {
        setCounter(counter-1);
    };
    const displayCounter = counter > 0;    

    if(product.stock > 0)
    {
        return (
            <ButtonGroup size="small" color="secondary" aria-label="small outlined button group" className="float-right">
                <Button onClick={handleIncrement}>+</Button>
                {displayCounter && <Button disabled>{counter}</Button>}
                {displayCounter && <Button onClick={handleDecrement}>-</Button>}
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
