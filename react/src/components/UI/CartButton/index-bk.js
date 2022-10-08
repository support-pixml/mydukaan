import React, { useEffect, useState } from "react";
import Button from "@material-ui/core/Button";
import ButtonGroup from "@material-ui/core/ButtonGroup";
import { addProduct, decrease, increase, removeProduct } from "../../../actions/cart";
import { useDispatch } from "react-redux";
import { makeStyles } from "@material-ui/core";

const useStyles = makeStyles((theme) => ({
    qtyBtn: {
        color: '#000000 !important',
        fontWeight: '700'
    },
}));

const CartButton = ({product, cartItems, auth, productPrice}) => {
    const [counter, setCounter] = useState(0);
    const [disabled, setDisabled] = useState(false);
    const dispatch = useDispatch();
    const classes = useStyles();

    const isInCart = (product) => {
        return !!cartItems.find(item => item.long_id === product.long_id);
    }

    useEffect(() => {
        setDisabled(false);
        if(product)
        {
            const findQty = cartItems.find(item => item.long_id === product.long_id);
            if(findQty)
            {
                setCounter(findQty.quantity);
                if(findQty.quantity == product.total_stock && auth.user.role !== '4') { setDisabled(true) } else setDisabled(false);
                // setInputDisabled(true);
            }
            else
            {
                // setInputDisabled(false);
            }
        }
        
    }, [cartItems]);

    const handleIncrement = () => {
        let addedProd = Object.assign({}, product);

        if(isInCart(addedProd))
        {
            dispatch(increase(addedProd));
        }
        else
        {
            dispatch(addProduct({addedProd, productPrice}));
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

    if(new Date(customer.expiry_date).getTime() < new Date().getTime())
    {
        return false;
    }

    if(product.total_stock !== 0)
    {
        return (
            <ButtonGroup size="small" color="primary" aria-label="small outlined button group">
                {isInCart(product) && <Button onClick={handleDecrement}>-</Button>}                
                {isInCart(product) && <Button disabled className={classes.qtyBtn}>{counter}</Button>}
                <Button disabled={disabled} onClick={handleIncrement}>+</Button>
            </ButtonGroup>
            );
    }
    else
    {
        return (
            <div className="text-danger">No Stock</div>
        )
    } 
}

export default CartButton;
