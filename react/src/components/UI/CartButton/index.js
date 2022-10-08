import React, { useEffect, useState } from "react";
import Button from "@material-ui/core/Button";
import ButtonGroup from "@material-ui/core/ButtonGroup";
import { addProduct, decrease, increase, removeProduct, updateProductQty } from "../../../actions/cart";
import { useDispatch } from "react-redux";
import { makeStyles, TextField } from "@material-ui/core";

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

    const handleQtyChange = (e) => {
        if(parseInt(e.target.value) == 0)
        {
            dispatch(removeProduct(product));
        }
        else if(e.target.value == '')
        {
            setCounter(0);
        }
        else if(parseInt(e.target.value) > product.total_stock)
        {
            setCounter(parseInt(product.total_stock));
            let qty = parseInt(product.total_stock);
            dispatch(updateProductQty({product, qty}));
        }
        else
        {
            setCounter(parseInt(e.target.value));
            let qty = parseInt(e.target.value);
            dispatch(updateProductQty({product, qty}));
        }
    }

    if(new Date(customer.expiry_date).getTime() < new Date().getTime())
    {
        return false;
    }

    if(product.total_stock !== 0)
    {
        return (
            <ButtonGroup size="small" color="primary" aria-label="small outlined button group">
                {isInCart(product) && <Button onClick={handleDecrement}>-</Button>}                
                {isInCart(product) && <TextField value={counter} style={{width: 75}} onChange={handleQtyChange} />}
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
